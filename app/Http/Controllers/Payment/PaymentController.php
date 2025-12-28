<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest as UpdatePaymentFormRequest;
use App\Http\Requests\Payment\VerifyPaymentRequest;
use App\Services\Fees\PaymentService;
use Illuminate\Http\Request;

use App\Http\Requests\Fees\PaymentRequest;
use App\Helpers\ResponseHelper;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher|Student')->only(['index', 'show', 'getStudentPayments', 'getInvoicePayments']);
        $this->middleware('role:super_admin|School Admin')->only(['store', 'update', 'destroy', 'verifyPayment', 'generateReceipt']);
    }

    /**
     * List all payments with optional filtering
     */
    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Payments fetched successfully');
    }

    /**
     * Create a new payment
     */
    public function store(PaymentRequest $request)
    {
        $validated = $request->validated();
        $validated['processed_by'] = $request->user()->id;

        // Security: Force pending status for non-admins to prevent self-approval
        if (!$request->user()->hasRole(['super_admin', 'School Admin'])) {
            $validated['status'] = 'pending';
        }

        $payment = $this->service->create($validated);
        return ResponseHelper::success($payment, 'Payment created successfully', 201);
    }

    /**
     * Get a specific payment by ID
     */
    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Payment fetched successfully');
    }

    /**
     * Update an existing payment
     */
    public function update(PaymentRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Payment updated successfully');
    }

    /**
     * Delete a payment
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Payment deleted successfully');
    }

    /**
     * Get all payments for a specific student
     */
    public function getStudentPayments(Request $request, string $studentId)
    {
        $data = $this->service->getStudentPayments($studentId, $request->query());
        return ResponseHelper::success($data, 'Student payments fetched successfully');
    }

    /**
     * Get all payments for a specific invoice
     */
    public function getInvoicePayments(Request $request, string $invoiceId)
    {
        $data = $this->service->getInvoicePayments($invoiceId, $request->query());
        return ResponseHelper::success($data, 'Invoice payments fetched successfully');
    }

    /**
     * Verify a pending payment
     */
    public function verifyPayment(PaymentRequest $request, string $id)
    {
        $result = $this->service->verifyPayment($id, $request->validated(), $request->user()->id);
        return ResponseHelper::success($result, 'Payment verified successfully');
    }

    /**
     * Generate a receipt for a payment
     */
    public function generateReceipt(string $id)
    {
        $receipt = $this->service->generateReceipt($id);
        return ResponseHelper::success($receipt, 'Receipt generated successfully');
    }
}
