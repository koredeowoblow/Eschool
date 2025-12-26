<?php

namespace App\Http\Controllers\Api\Fees;

use App\Http\Controllers\Controller;
use App\Services\Fees\FeePaymentService;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeePaymentController extends Controller
{
    protected $paymentService;

    public function __construct(FeePaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * List all fee payments with filters
     */
    public function index(Request $request)
    {
        $query = FeePayment::with(['student.user', 'fee', 'processedBy']);

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->fee_id) {
            $query->where('fee_id', $request->fee_id);
        }

        $payments = $query->latest()->paginate($request->per_page ?? 15);

        return \App\Helpers\ResponseHelper::success(
            \App\Http\Resources\FeePaymentResource::collection($payments->items()),
            'Payments retrieved successfully',
            200,
            [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'total' => $payments->total(),
            ]
        );
    }

    /**
     * Process a fee payment
     */
    public function store(\App\Http\Requests\Fees\FeePaymentRequest $request)
    {
        try {
            $payment = $this->paymentService->processPayment($request->validated());

            return \App\Helpers\ResponseHelper::success(
                new \App\Http\Resources\FeePaymentResource($payment),
                'Payment processed successfully',
                201
            );
        } catch (\Exception $e) {
            return \App\Helpers\ResponseHelper::error($e->getMessage(), 400);
        }
    }

    /**
     * Get outstanding fees for a student
     */
    public function outstandingFees($studentId)
    {
        $fees = $this->paymentService->getOutstandingFees($studentId);

        return \App\Helpers\ResponseHelper::success(
            \App\Http\Resources\StudentFeeResource::collection($fees),
            'Outstanding fees retrieved successfully'
        );
    }
}
