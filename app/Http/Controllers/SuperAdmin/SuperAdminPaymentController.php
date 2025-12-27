<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class SuperAdminPaymentController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            $payments = Payment::with(['school', 'student'])
                ->latest()
                ->paginate(20);

            return ResponseHelper::success($payments, 'Payments fetched successfully');
        }
        return view('super_admin.payments.index');
    }
}
