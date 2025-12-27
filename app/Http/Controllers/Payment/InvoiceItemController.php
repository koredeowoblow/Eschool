<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Fees\InvoiceItemService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\InvoiceItem\StoreInvoiceItemRequest;
use App\Http\Requests\InvoiceItem\UpdateInvoiceItemRequest;

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
        return ResponseHelper::success($data, 'Invoice items fetched successfully');
    }

    public function store(StoreInvoiceItemRequest $request)
    {
        $validated = $request->validated();

        $item = $this->service->create($validated);
        return ResponseHelper::success($item, 'Invoice item created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Invoice item fetched successfully');
    }

    public function update(UpdateInvoiceItemRequest $request, string $id)
    {
        $validated = $request->validated();

        $updated = $this->service->update($id, $validated);
        return ResponseHelper::success($updated, 'Invoice item updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Invoice item deleted successfully');
    }
}
