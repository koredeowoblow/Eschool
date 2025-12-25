<?php

namespace App\Services\Fees;

use App\Repositories\Fees\InvoiceItemRepository;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Auth;

class InvoiceItemService
{
    public function __construct(public InvoiceItemRepository $repo) {}

    public function list(array $filters = [])
    {
        $query = InvoiceItem::query();
        if (!Auth::user()->hasRole('super_admin')) {
            $query->where('school_id', Auth::user()->school_id);
        }
        if (!empty($filters['invoice_id'])) {
            $query->where('invoice_id', $filters['invoice_id']);
        }
        if (!empty($filters['fee_type_id'])) {
            $query->where('fee_type_id', $filters['fee_type_id']);
        }
        return $query->latest('id')->get();
    }

    public function get(int|string $id): InvoiceItem
    {
        $query = InvoiceItem::where('id', $id);
        if (!Auth::user()->hasRole('super_admin')) {
            $query->where('school_id', Auth::user()->school_id);
        }
        return $query->firstOrFail();
    }

    public function create(array $data): InvoiceItem
    {
        $data['school_id'] = Auth::user()->school_id ?? null;
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): InvoiceItem
    {
        $model = $this->get($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int|string $id): bool
    {
        $model = $this->get($id);
        return (bool) $model->delete();
    }
}
