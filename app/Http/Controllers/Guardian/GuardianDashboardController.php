<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Result;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuardianDashboardController extends Controller
{
    /**
     * Get guardian's children (students)
     */
    public function getChildren()
    {
        $user = Auth::user();

        if (!$user->hasRole('Guardian')) {
            return $this->error('Unauthorized', 403);
        }

        $students = $user->guardianStudents();

        return $this->success($students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->user->name ?? 'N/A',
                'admission_number' => $student->admission_number,
                'class' => $student->currentClass->name ?? 'N/A',
            ];
        }));
    }

    /**
     * Get child's results (published only)
     */
    public function getChildResults(Request $request, $studentId)
    {
        $user = Auth::user();

        if (!$user->can('guardian.view.results')) {
            return $this->error('Unauthorized', 403);
        }

        // Verify this student belongs to this guardian
        $student = $user->guardianStudents()->where('id', $studentId)->first();

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $results = Result::where('student_id', $studentId)
            ->where('school_id', $user->school_id)
            ->whereIn('status', ['published', 'locked'])
            ->with(['assessment.subject', 'assessment.term', 'assessment.session'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($results);
    }

    /**
     * Get child's attendance
     */
    public function getChildAttendance(Request $request, $studentId)
    {
        $user = Auth::user();

        if (!$user->can('guardian.view.attendance')) {
            return $this->error('Unauthorized', 403);
        }

        $student = $user->guardianStudents()->where('id', $studentId)->first();

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $attendance = Attendance::where('student_id', $studentId)
            ->where('school_id', $user->school_id)
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get();

        return $this->success($attendance);
    }

    /**
     * Get child's fees and invoices
     */
    public function getChildFees(Request $request, $studentId)
    {
        $user = Auth::user();

        if (!$user->can('guardian.view.fees')) {
            return $this->error('Unauthorized', 403);
        }

        $student = $user->guardianStudents()->where('id', $studentId)->first();

        if (!$student) {
            return $this->error('Student not found', 404);
        }

        $invoices = Invoice::where('student_id', $studentId)
            ->where('school_id', $user->school_id)
            ->with(['payments', 'feeType'])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_invoiced' => $invoices->sum('amount'),
            'total_paid' => Payment::whereIn('invoice_id', $invoices->pluck('id'))->sum('amount'),
            'pending' => $invoices->where('status', 'pending')->sum('amount'),
        ];

        return $this->success([
            'invoices' => $invoices,
            'summary' => $summary
        ]);
    }

    /**
     * Download receipt
     */
    public function downloadReceipt($paymentId)
    {
        $user = Auth::user();

        if (!$user->can('guardian.download.receipts')) {
            return $this->error('Unauthorized', 403);
        }

        $payment = Payment::with(['invoice.student'])
            ->where('id', $paymentId)
            ->where('school_id', $user->school_id)
            ->first();

        if (!$payment) {
            return $this->error('Payment not found', 404);
        }

        // Verify this payment belongs to guardian's child
        $studentIds = $user->guardianStudents()->pluck('id');

        if (!$studentIds->contains($payment->invoice->student_id)) {
            return $this->error('Unauthorized access to this receipt', 403);
        }

        return $this->success([
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'date' => $payment->payment_date,
            'receipt_number' => $payment->receipt_number ?? 'RCP-' . $payment->id,
            'download_url' => route('api.guardian.receipt', $payment->id)
        ]);
    }
}
