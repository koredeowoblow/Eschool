<?php

namespace App\Http\Controllers\Api\Fees;

use App\Http\Controllers\Controller;
use App\Services\Fees\FeePaymentService;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use App\Http\Resources\FeePaymentResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Fees\FeePaymentRequest;
use App\Http\Resources\StudentFeeResource;
use App\Helpers\ResponseHelper;

class FeePaymentController extends Controller
{
    protected $paymentService;

    public function __construct(FeePaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Finance Officer')->only(['index', 'store']);
        $this->middleware('role:super_admin|School Admin|Finance Officer|Student|Guardian')->only(['outstandingFees']);
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

        return ResponseHelper::success(
            FeePaymentResource::collection($payments->items()),
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
    public function store(FeePaymentRequest $request)
    {
        $payment = $this->paymentService->processPayment($request->validated());

        return ResponseHelper::success(
            new FeePaymentResource($payment),
            'Payment processed successfully',
            201
        );
    }

    /**
     * Get outstanding fees for a student
     */
    public function outstandingFees($studentId)
    {
        $fees = $this->paymentService->getOutstandingFees($studentId);

        return ResponseHelper::success(
            StudentFeeResource::collection($fees),
            'Outstanding fees retrieved successfully'
        );
    }
}
