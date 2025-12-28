<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Requests\Invoice\BulkGenerateInvoiceRequest;
use App\Http\Requests\Invoice\MarkInvoiceAsPaidRequest;
use App\Services\Fees\InvoiceService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher')->only(['index', 'show', 'getStudentInvoices']);
        $this->middleware('role:super_admin|School Admin')->only(['store', 'update', 'destroy', 'bulkGenerate', 'markAsPaid']);
    }

    /**
     * List all invoices with optional filtering
     */
    public function index(Request $request)
    {
        $filters = [
            'student_id' => $request->query('student_id'),
            'session_id' => $request->query('session_id'),
            'term_id'    => $request->query('term_id'),
            'status'     => $request->query('status'),
            'due_before' => $request->query('due_before'),
            'due_after'  => $request->query('due_after'),
            'school_id'  => $request->user()->school_id,
            'per_page'   => $request->query('per_page', 15),
        ];

        $data = $this->service->list($filters);
        return ResponseHelper::success($data, 'Invoices fetched successfully');
    }

    /**
     * Create a new invoice
     */
    public function store(StoreInvoiceRequest $request)
    {
        $validated = $request->validated();
        $items = $validated['items'] ?? [];
        unset($validated['items']);

        // Add school_id from authenticated user
        $validated['school_id'] = $request->user()->school_id;

        $invoice = $this->service->create($validated, $items);
        return ResponseHelper::success($invoice, 'Invoice created successfully', 201);
    }

    /**
     * Get a specific invoice by ID
     */
    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Invoice fetched successfully');
    }

    /**
     * Update an existing invoice
     */
    public function update(UpdateInvoiceRequest $request, string $id)
    {
        $validated = $request->validated();
        $updated = $this->service->update($id, $validated);

        return ResponseHelper::success($updated, 'Invoice updated successfully');
    }

    /**
     * Delete an invoice
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Invoice deleted successfully');
    }

    /**
     * Get all invoices for a specific student
     */
    public function getStudentInvoices(Request $request, string $studentId)
    {
        $filters = [
            'student_id' => $studentId,
            'session_id' => $request->query('session_id'),
            'term_id'    => $request->query('term_id'),
            'status'     => $request->query('status'),
            'school_id'  => $request->user()->school_id,
            'per_page'   => $request->query('per_page', 15),
        ];

        $data = $this->service->list($filters);
        return ResponseHelper::success($data, 'Student invoices fetched successfully');
    }

    /**
     * Generate invoices in bulk for multiple students
     */
    public function bulkGenerate(BulkGenerateInvoiceRequest $request)
    {
        $validated = $request->validated();
        // Add school_id from authenticated user
        $validated['school_id'] = $request->user()->school_id;

        $result = $this->service->bulkGenerate($validated);
        return ResponseHelper::success($result, 'Invoices generated successfully');
    }

    /**
     * Mark an invoice as paid manually
     */
    public function markAsPaid(MarkInvoiceAsPaidRequest $request, string $id)
    {
        $validated = $request->validated();
        $result = $this->service->markAsPaid($id, $validated, $request->user()->id);

        return ResponseHelper::success($result, 'Invoice marked as paid successfully');
    }
}
