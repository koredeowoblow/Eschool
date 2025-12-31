<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\Finance\FinanceService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Finance\InvoiceRequest;
use App\Http\Requests\Finance\PaymentRecordRequest;

class FinanceController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Finance Officer');
    }

    public function index()
    {
        $overview = $this->financeService->getOverview();
        return ResponseHelper::success($overview, 'Finance overview fetched successfully');
    }

    public function createInvoice(InvoiceRequest $request)
    {
        $data = $request->validated();

        $invoice = $this->financeService->createInvoice($data);

        return ResponseHelper::success($invoice, 'Invoice created successfully', 201);
    }

    public function recordPayment(PaymentRecordRequest $request)
    {
        $data = $request->validated();

        $payment = $this->financeService->recordPayment($data);

        return ResponseHelper::success($payment, 'Payment recorded successfully', 201);
    }
}
