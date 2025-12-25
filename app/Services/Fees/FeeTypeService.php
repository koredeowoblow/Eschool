<?php

namespace App\Services\Fees;

use App\Repositories\Fees\FeeTypeRepository;
use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeeTypeService
{
    public function __construct(public FeeTypeRepository $repo) {}

    /**
     * List fee types with filtering options
     */
    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    /**
     * Get a fee type by ID
     */
    public function get(int|string $id): FeeType
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Fee type not found");
        }
        return $model;
    }

    /**
     * Create a new fee type
     */
    public function create(array $data): FeeType
    {
        return $this->repo->create($data);
    }

    /**
     * Update an existing fee type
     */
    public function update(int|string $id, array $data): FeeType
    {
        $model = $this->repo->update($id, $data);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Fee type not found");
        }
        return $model;
    }

    /**
     * Delete a fee type
     */
    public function delete(int|string $id): bool
    {
        // Check usage (Failsafe logic remains)
        $usageCount = InvoiceItem::where('fee_type_id', $id)->count();
        if ($usageCount > 0) {
            throw new \RuntimeException("Cannot delete fee type as it is used in {$usageCount} invoice items");
        }

        $this->get($id);
        return $this->repo->delete($id);
    }

    /**
     * Bulk create fee types
     */
    public function bulkCreate(array $feeTypes): array
    {
        return DB::transaction(function () use ($feeTypes) {
            $results = [
                'success' => 0,
                'failed' => 0,
                'fee_types' => []
            ];

            foreach ($feeTypes as $feeTypeData) {
                try {
                    $feeType = $this->create($feeTypeData);
                    $results['fee_types'][] = $feeType->id;
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    Log::error('Error in bulk create fee type: ' . $e->getMessage());
                }
            }

            return $results;
        });
    }

    /**
     * Get fee type usage statistics
     */
    public function getUsageStats(int|string $id): array
    {
        $feeType = $this->get($id);

        $invoiceItemsCount = InvoiceItem::where('fee_type_id', $id)->count();

        $totalAmount = InvoiceItem::where('fee_type_id', $id)
            ->sum(DB::raw('amount * quantity'));

        $invoiceIds = InvoiceItem::where('fee_type_id', $id)
            ->pluck('invoice_id')
            ->unique();

        $paidInvoicesCount = Invoice::whereIn('id', $invoiceIds)
            ->where('status', Invoice::STATUS_PAID)
            ->count();

        $pendingInvoicesCount = Invoice::whereIn('id', $invoiceIds)
            ->where('status', Invoice::STATUS_PENDING)
            ->count();

        $overdueInvoicesCount = Invoice::whereIn('id', $invoiceIds)
            ->where('status', Invoice::STATUS_OVERDUE)
            ->count();

        return [
            'fee_type' => $feeType,
            'usage_count' => $invoiceItemsCount,
            'total_amount' => $totalAmount,
            'paid_invoices_count' => $paidInvoicesCount,
            'pending_invoices_count' => $pendingInvoicesCount,
            'overdue_invoices_count' => $overdueInvoicesCount,
        ];
    }

    /**
     * Get fee types summary for a school
     */
    public function getFeeTypesSummary(string $schoolId = null): array
    {
        $schoolId = $schoolId ?? Auth::user()->school_id ?? null;

        $totalFeeTypes = FeeType::where('school_id', $schoolId)->count();

        $mostUsedFeeTypes = DB::table('fee_types')
            ->join('invoice_items', 'fee_types.id', '=', 'invoice_items.fee_type_id')
            ->where('fee_types.school_id', $schoolId)
            ->select('fee_types.id', 'fee_types.name', DB::raw('count(invoice_items.id) as usage_count'))
            ->groupBy('fee_types.id', 'fee_types.name')
            ->orderBy('usage_count', 'desc')
            ->limit(5)
            ->get();

        $highestAmountFeeTypes = FeeType::where('school_id', $schoolId)
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_fee_types' => $totalFeeTypes,
            'most_used_fee_types' => $mostUsedFeeTypes,
            'highest_amount_fee_types' => $highestAmountFeeTypes,
        ];
    }
}
