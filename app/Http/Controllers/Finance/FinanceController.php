<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\Finance\FinanceService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
        // Middleware can also be applied here for broader role checks, 
        // but Service handles granular permissions.
    }

    public function index()
    {
        $overview = $this->financeService->getOverview();
        return response()->json([
            'success' => true,
            'data' => $overview
        ]);
    }

    public function createInvoice(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id', // Should verify student belongs to school? Service/Repo usually scopes this.
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string'
            // Add other fields as per schema
        ]);

        $invoice = $this->financeService->createInvoice($data);

        return response()->json([
            'message' => 'Invoice created successfully',
            'data' => $invoice
        ], 201);
    }

    public function recordPayment(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string', // cash, bank_transfer, etc.
            'reference' => 'nullable|string',
            'paid_at' => 'required|date'
        ]);

        $payment = $this->financeService->recordPayment($data);

        return response()->json([
            'message' => 'Payment recorded successfully',
            'data' => $payment
        ], 201);
    }
}
