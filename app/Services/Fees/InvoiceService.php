<?php

namespace App\Services\Fees;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Student;
use App\Repositories\Fees\InvoiceItemRepository;
use App\Repositories\Fees\InvoiceRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function __construct(
        public InvoiceRepository $invoices,
        public InvoiceItemRepository $items,
    ) {}

    /**
     * List invoices with filtering options
     */
    public function list(array $filters = [])
    {
        $query = Invoice::query();

        // Apply school filter
        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        } elseif (!Auth::user()->hasRole('super_admin')) {
            $query->where('school_id', Auth::user()->school_id ?? null);
        }

        // Security: Force Student Scoping
        if (Auth::user()->hasRole('Student')) {
            $query->where('student_id', Auth::user()->student()->value('id'));
        }

        // Apply other filters
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }
        if (!empty($filters['session_id'])) {
            $query->where('session_id', $filters['session_id']);
        }
        if (!empty($filters['term_id'])) {
            $query->where('term_id', $filters['term_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['due_before'])) {
            $query->whereDate('due_date', '<=', $filters['due_before']);
        }
        if (!empty($filters['due_after'])) {
            $query->whereDate('due_date', '>=', $filters['due_after']);
        }

        // Check for overdue invoices and update their status
        $this->updateOverdueInvoices();

        $perPage = $filters['per_page'] ?? 15;

        return $query->with(['student', 'items', 'payments'])
            ->latest('due_date')
            ->paginate($perPage);
    }

    /**
     * Get a specific invoice by ID
     */
    public function get(int|string $id): Invoice
    {
        $query = Invoice::where('id', $id)->with(['student', 'items', 'payments']);

        if (!Auth::user()->hasRole('super_admin')) {
            $query->where('school_id', Auth::user()->school_id);
        }

        return $query->firstOrFail();
    }

    /**
     * Create a new invoice
     */
    public function create(array $data, array $items = []): Invoice
    {
        return DB::transaction(function () use ($data, $items) {
            // Generate invoice number
            $data['invoice_number'] = $this->generateInvoiceNumber();

            // Create the invoice
            $invoice = $this->invoices->create($data);

            // Add items if provided
            foreach ($items as $item) {
                $this->addItem($invoice->id, $item);
            }

            // Recalculate totals and status
            $this->recalculate($invoice->id);

            return $invoice->fresh(['student', 'payments', 'items']);
        });
    }

    /**
     * Update an existing invoice
     */
    public function update(int|string $id, array $data): Invoice
    {
        $invoice = $this->get($id);
        $invoice->update($data);

        // Recalculate if relevant fields changed
        $this->recalculate($invoice->id);

        return $invoice->fresh(['student', 'payments', 'items']);
    }

    /**
     * Delete an invoice
     */
    public function delete(int|string $id): bool
    {
        $invoice = $this->get($id);

        // Check if invoice has payments
        if ($invoice->payments()->count() > 0) {
            // Soft delete only if it has payments
            return (bool) $invoice->delete();
        }

        // Hard delete items first
        $invoice->items()->delete();

        // Then delete the invoice
        return (bool) $invoice->delete();
    }

    /**
     * Add an item to an invoice
     */
    public function addItem(int|string $invoiceId, array $data): InvoiceItem
    {
        $invoice = $this->get($invoiceId);

        $payload = [
            'invoice_id' => $invoice->id,
            'fee_type_id' => $data['fee_type_id'] ?? null,
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'] ?? 0,
            'quantity' => $data['quantity'] ?? 1,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'discount_percentage' => $data['discount_percentage'] ?? 0,
            'school_id' => $invoice->school_id,
        ];

        $item = $this->items->create($payload);
        $this->recalculate($invoice->id);
        return $item;
    }

    /**
     * Remove an item from an invoice
     */
    public function removeItem(int|string $invoiceId, int|string $itemId): bool
    {
        $invoice = $this->get($invoiceId);

        $item = InvoiceItem::where('id', $itemId)
            ->where('invoice_id', $invoice->id)
            ->firstOrFail();

        $deleted = (bool) $item->delete();
        $this->recalculate($invoice->id);
        return $deleted;
    }

    /**
     * Recalculate invoice totals and status
     */
    public function recalculate(int|string $invoiceId): void
    {
        $invoice = $this->get($invoiceId);

        // Calculate total from items
        $items = InvoiceItem::where('invoice_id', $invoice->id)->get();
        $totalBeforeDiscount = 0;

        foreach ($items as $item) {
            $itemTotal = $item->amount * $item->quantity;
            $itemDiscount = 0;

            if ($item->discount_percentage > 0) {
                $itemDiscount += ($itemTotal * $item->discount_percentage / 100);
            }

            if ($item->discount_amount > 0) {
                $itemDiscount += $item->discount_amount;
            }

            $totalBeforeDiscount += $itemTotal - $itemDiscount;
        }

        // Apply invoice-level discounts
        $totalDiscount = 0;

        if ($invoice->discount_percentage > 0) {
            $totalDiscount += ($totalBeforeDiscount * $invoice->discount_percentage / 100);
        }

        if ($invoice->discount_amount > 0) {
            $totalDiscount += $invoice->discount_amount;
        }

        $finalTotal = max(0, $totalBeforeDiscount - $totalDiscount);

        $invoice->total_amount = $finalTotal;
        $invoice->save();

        $this->updateStatusFromPayments($invoice);
    }

    /**
     * Update invoice status based on payments
     */
    protected function updateStatusFromPayments(Invoice $invoice): void
    {
        $paid = Payment::where('invoice_id', $invoice->id)
            ->where('status', Payment::STATUS_COMPLETED)
            ->sum('amount');

        if ($paid <= 0) {
            $status = Invoice::STATUS_PENDING;
        } elseif ($paid >= $invoice->total_amount) {
            $status = Invoice::STATUS_PAID;
        } else {
            $status = Invoice::STATUS_PARTIAL;
        }

        // Check if invoice is overdue
        if ($status !== Invoice::STATUS_PAID && $invoice->due_date < now()->startOfDay()) {
            $status = Invoice::STATUS_OVERDUE;
        }

        $invoice->status = $status;
        $invoice->save();
    }

    /**
     * Update status of overdue invoices
     */
    protected function updateOverdueInvoices(): void
    {
        Invoice::where('status', '!=', Invoice::STATUS_PAID)
            ->where('status', '!=', Invoice::STATUS_CANCELLED)
            ->where('due_date', '<', now()->startOfDay())
            ->update(['status' => Invoice::STATUS_OVERDUE]);
    }

    /**
     * Generate a unique invoice number
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV-';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return $prefix . $date . '-' . $random;
    }

    /**
     * Generate invoices in bulk for multiple students
     */
    public function bulkGenerate(array $data): array
    {
        $results = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'invoices' => []
        ];

        return DB::transaction(function () use ($data, $results) {
            $feeTypes = FeeType::whereIn('id', $data['fee_type_ids'])->get();

            if ($feeTypes->isEmpty()) {
                return $results;
            }

            $studentsQuery = Student::query()->where('school_id', $data['school_id']);

            if (!empty($data['class_id'])) {
                $studentsQuery->where('class_id', $data['class_id']);
            }

            if (!empty($data['section_id'])) {

                $studentsQuery->where('section_id', $data['section_id']);
            }

            if (!empty($data['student_ids'])) {
                $studentsQuery->whereIn('id', $data['student_ids']);
            }

            $students = $studentsQuery->get();
            $results['total'] = $students->count();

            foreach ($students as $student) {
                try {
                    $invoiceData = [
                        'student_id' => $student->id,
                        'session_id' => $data['session_id'],
                        'term_id' => $data['term_id'],
                        'due_date' => $data['due_date'],
                        'notes' => $data['notes'] ?? null,
                        'school_id' => $data['school_id'],
                    ];

                    $items = [];
                    foreach ($feeTypes as $feeType) {
                        $items[] = [
                            'fee_type_id' => $feeType->id,
                            'description' => $feeType->name,
                            'amount' => $feeType->amount,
                            'quantity' => 1
                        ];
                    }

                    $invoice = $this->create($invoiceData, $items);
                    $results['invoices'][] = $invoice->id;
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                }
            }

            return $results;
        });
    }

    /**
     * Mark an invoice as paid manually
     */
    public function markAsPaid(int|string $id, array $data, int|string $userId): Invoice
    {
        return DB::transaction(function () use ($id, $data, $userId) {
            $invoice = $this->get($id);

            // Calculate remaining amount
            $paidAmount = $invoice->payments()
                ->where('status', Payment::STATUS_COMPLETED)
                ->sum('amount');

            $remainingAmount = $invoice->total_amount - $paidAmount;

            if ($remainingAmount <= 0) {
                throw new \RuntimeException("Invoice is already fully paid.");
            }

            // Create payment for remaining amount
            $paymentData = [
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'amount' => $remainingAmount,
                'method' => $data['payment_method'],
                'payment_date' => $data['payment_date'] ?? now(),
                'transaction_ref' => $data['transaction_ref'] ?? null,
                'notes' => $data['notes'] ?? 'Manually marked as paid',
                'status' => Payment::STATUS_COMPLETED,
                'school_id' => $invoice->school_id,
                'processed_by' => $userId,
                'receipt_number' => 'RCP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
            ];

            app(PaymentService::class)->create($paymentData);

            // Update invoice status
            $invoice->status = Invoice::STATUS_PAID;
            $invoice->save();

            return $invoice->fresh(['student', 'payments', 'items']);
        });
    }
}
