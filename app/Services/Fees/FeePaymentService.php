<?php

namespace App\Services\Fees;

use App\Models\Fee;
use App\Models\StudentFee;
use App\Models\FeePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FeePaymentService
{
    /**
     * Process a fee payment for a student
     */
    public function processPayment(array $data): FeePayment
    {
        return DB::transaction(function () use ($data) {
            $studentId = $data['student_id'];
            $feeId = $data['fee_id'];
            $amountPaid = $data['amount_paid'];

            // 1. Fetch the StudentFee record (The Matrix entry)
            $studentFee = StudentFee::where('student_id', $studentId)
                ->where('fee_id', $feeId)
                ->firstOrFail();

            // 2. Create the payment record
            $payment = FeePayment::create([
                'student_id' => $studentId,
                'fee_id' => $feeId,
                'amount_paid' => $amountPaid,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'reference_number' => $data['reference_number'] ?? $this->generateReference(),
                'payment_date' => $data['payment_date'] ?? now(),
                'processed_by' => $data['processed_by'] ?? Auth::id(),
            ]);

            // 3. Update the student_fee balance and status
            $this->updateStudentFeeStatus($studentFee);

            return $payment;
        });
    }

    /**
     * Update the balance and status of a StudentFee record based on payments
     */
    public function updateStudentFeeStatus(StudentFee $studentFee): void
    {
        $totalPaid = FeePayment::where('student_id', $studentFee->student_id)
            ->where('fee_id', $studentFee->fee_id)
            ->sum('amount_paid');

        $feeAmount = $studentFee->fee->amount;
        $balance = $feeAmount - $totalPaid;

        $status = 'pending';
        if ($balance <= 0) {
            $status = 'paid';
            $balance = 0;
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        }

        $studentFee->update([
            'balance' => $balance,
            'status' => $status,
        ]);
    }

    /**
     * Get all outstanding fees for a student
     */
    public function getOutstandingFees(int|string $studentId)
    {
        return StudentFee::where('student_id', $studentId)
            ->with(['fee.term', 'fee.session'])
            ->latest()
            ->get();
    }

    /**
     * Generate a unique reference number for the payment
     */
    protected function generateReference(): string
    {
        return 'FEE-' . strtoupper(Str::random(8));
    }
}
