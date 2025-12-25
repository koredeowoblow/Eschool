<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Fees\InvoiceItemService;

class InvoiceItemController extends Controller
{
    public function __construct(private InvoiceItemService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list([
            'invoice_id' => $request->query('invoice_id'),
            'fee_type_id' => $request->query('fee_type_id'),
        ]);
        return get_success_response($data, 'Invoice items fetched successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|integer|exists:invoices,id',
            'fee_type_id' => 'required|integer|exists:fee_types,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $item = $this->service->create($validated);
        return get_success_response($item, 'Invoice item created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return get_success_response($model, 'Invoice item fetched successfully');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'fee_type_id' => 'sometimes|integer|exists:fee_types,id',
            'amount' => 'sometimes|numeric|min:0',
        ]);

        $updated = $this->service->update($id, $validated);
        return get_success_response($updated, 'Invoice item updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return get_success_response(null, 'Invoice item deleted successfully');
    }
}
