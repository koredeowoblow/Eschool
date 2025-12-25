<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class SuperAdminPaymentController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            $payments = Payment::with(['school', 'student'])
                ->latest()
                ->paginate(20);

            return response()->json([
                'status' => 'success',
                'data' => $payments
            ]);
        }
        return view('super_admin.payments.index');
    }
}
