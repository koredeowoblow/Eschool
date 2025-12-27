<?php

namespace App\Services\Fees;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Repositories\Fees\PaymentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PaymentService
{
    public function __construct(
        public PaymentRepository $payments,
        public InvoiceService $invoices,
    ) {}

    /**
     * List payments with filtering options
     */
    public function list(array $filters = [])
    {
        return $this->payments->list($filters);
    }

    /**
     * Get a specific payment by ID
     */
    public function get(int|string $id): Payment
    {
        return $this->payments->findById($id, ['invoice', 'student', 'processedBy']);
    }

    /**
     * Create a new payment
     */
    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            if (empty($data['payment_date'])) {
                $data['payment_date'] = now();
            }

            if (empty($data['status'])) {
                // RULE: Bank Transfer / Cash must be PENDING until confirmed
                if (in_array($data['method'] ?? '', [Payment::METHOD_BANK_TRANSFER, Payment::METHOD_CASH])) {
                    $data['status'] = Payment::STATUS_PENDING;
                } else {
                    $data['status'] = Payment::STATUS_COMPLETED;
                }
            }

            if (empty($data['receipt_number'])) {
                $data['receipt_number'] = $this->generateReceiptNumber();
            }

            // Tenancy: Inject school_id
            if (empty($data['school_id']) && Auth::check()) {
                $data['school_id'] = Auth::user()->school_id;
            }

            if ($user->hasRole('Student')) {
                // Fix: Resolve the actual Student ID from the User relationship
                $studentProfile = $user->student()->first();
                $data['student_id'] = $studentProfile?->id;

                if (!$data['student_id']) {
                    // DATA INTEGRITY VIOLATION: Orphaned User Record
                    // Policy: Soft-failure with Audit Log. Do not crash.
                    Log::warning("Data Integrity Violation: Orphaned User Record found when attempting payment.", [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'action' => 'PaymentService::create'
                    ]);

                    // Gracefully stop the transaction and return a user-friendly error.
                    throw new ValidationException(
                        Validator::make([], []),
                        ValidationException::withMessages([
                            'student_id' => ['Your user account is not correctly linked to a student profile. Please contact support.']
                        ])
                    );
                }
            }

            $payment = $this->payments->create($data);

            if ($payment->invoice_id) {
                // Security: Verify that the invoice belongs to the student
                $invoice = Invoice::find($payment->invoice_id);
                if ($invoice && $invoice->student_id !== $payment->student_id) {
                    throw new RuntimeException("Unauthorized: This invoice does not belong to the selected student.");
                }
                $this->invoices->recalculate($payment->invoice_id);
            }

            return $payment->fresh(['invoice', 'student', 'processedBy']);
        });
    }

    /**
     * Update an existing payment
     */
    public function update(int|string $id, array $data): Payment
    {
        return DB::transaction(function () use ($id, $data) {
            $model = $this->payments->update($id, $data);

            if ($model->invoice_id) {
                $this->invoices->recalculate($model->invoice_id);
            }

            return $model->fresh(['invoice', 'student', 'processedBy']);
        });
    }

    /**
     * Delete a payment
     */
    public function delete(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $payment = $this->get($id);
            $invoiceId = $payment->invoice_id;

            $deleted = $this->payments->delete($id);

            if ($deleted && $invoiceId) {
                $this->invoices->recalculate($invoiceId);
            }

            return $deleted;
        });
    }

    /**
     * Get payments for a specific student
     */
    public function getStudentPayments(int|string $studentId, array $filters = [])
    {
        $filters['student_id'] = $studentId;
        return $this->list($filters);
    }

    /**
     * Get payments for a specific invoice
     */
    public function getInvoicePayments(int|string $invoiceId, array $filters = [])
    {
        $filters['invoice_id'] = $invoiceId;
        return $this->list($filters);
    }

    /**
     * Generate a unique receipt number
     */
    protected function generateReceiptNumber(): string
    {
        $prefix = 'RCP-';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return $prefix . $date . '-' . $random;
    }

    /**
     * Verify a payment and update its status
     */
    public function verifyPayment(int|string $id, array $data, int|string $userId): Payment
    {
        return DB::transaction(function () use ($id, $data, $userId) {
            $payment = $this->get($id);

            if ($payment->status === Payment::STATUS_COMPLETED) {
                throw new RuntimeException("Payment is already completed.");
            }

            $updateData = [
                'status' => Payment::STATUS_COMPLETED,
                'processed_by' => $userId,
                'notes' => $data['notes'] ?? $payment->notes . ' (Verified)',
            ];

            $payment = $this->update($id, $updateData);

            return $payment;
        });
    }

    /**
     * Generate a payment receipt
     */
    public function generateReceipt(int|string $id): array
    {
        $payment = $this->get($id);
        $student = $payment->student;
        $invoice = $payment->invoice;

        if (!$student || !$invoice) {
            throw new RuntimeException("Student or Invoice records relevant to this payment are missing.");
        }

        return [
            'receipt_number' => $payment->receipt_number,
            'date' => $payment->payment_date->format('Y-m-d'),
            'student' => [
                'id' => $student->id,
                'name' => $student->user->name ?? 'Unknown',
                'grade' => $student->grade->name ?? 'Unknown',
                'section' => $student->section->name ?? 'Unknown',
            ],
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount,
                'paid_amount' => $invoice->paid_amount,
                'balance' => $invoice->total_amount - $payment->amount, // Simplified balance check
            ],
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'method' => $payment->method,
                'transaction_ref' => $payment->transaction_ref,
                'status' => $payment->status,
                'notes' => $payment->notes,
            ],
            'school' => [
                'name' => $student->school->name ?? 'Unknown',
                'address' => $student->school->address ?? 'Unknown',
                'phone' => $student->school->phone ?? 'Unknown',
                'email' => $student->school->email ?? 'Unknown',
            ],
        ];
    }

    /**
     * Get payment statistics for a school
     */
    public function getPaymentStats(int|string $schoolId, array $filters = []): array
    {
        $query = Payment::where('school_id', $schoolId);

        if (!empty($filters['date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['date_to']);
        }

        $query->where('status', Payment::STATUS_COMPLETED);

        $totalAmount = $query->sum('amount');
        $totalCount = $query->count();

        $methodStats = $query->select('method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('method')
            ->get()
            ->keyBy('method')
            ->toArray();

        $thirtyDaysAgo = now()->subDays(30)->startOfDay();
        $dailyPayments = Payment::where('school_id', $schoolId)
            ->where('status', Payment::STATUS_COMPLETED)
            ->where('payment_date', '>=', $thirtyDaysAgo)
            ->select(DB::raw('DATE(payment_date) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->toArray();

        return [
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'methods' => $methodStats,
            'daily_payments' => $dailyPayments,
        ];
    }
}
