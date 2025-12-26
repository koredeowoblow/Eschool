<?php

namespace App\Services\Fees;

use App\Models\Fee;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Support\Facades\DB;

class FeeAssignmentService
{
    /**
     * Assign a fee to all students in a specific class
     */
    public function assignFeeToClass(int|string $feeId, int|string $classId): void
    {
        $fee = Fee::findOrFail($feeId);
        $students = Student::where('class_id', $classId)->get();

        foreach ($students as $student) {
            $this->assignFeeToStudent($fee, $student->id);
        }
    }

    /**
     * Assign a specific fee to an individual student
     */
    public function assignFeeToStudent(Fee|int|string $fee, int|string $studentId): void
    {
        if (!$fee instanceof Fee) {
            $fee = Fee::findOrFail($fee);
        }

        // Prevent duplicate assignment
        $exists = StudentFee::where('fee_id', $fee->id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$exists) {
            StudentFee::create([
                'fee_id' => $fee->id,
                'student_id' => $studentId,
                'status' => 'pending',
                'balance' => $fee->amount,
            ]);
        }
    }

    /**
     * Bulk assign all applicable fees to a student (e.g., on enrollment)
     */
    public function syncStudentFees(int|string $studentId): void
    {
        $student = Student::findOrFail($studentId);

        if (!$student->class_id) {
            return;
        }

        // Find all mandatory fees for the student's class
        $fees = Fee::where('class_id', $student->class_id)
            ->where('is_mandatory', true)
            ->get();

        foreach ($fees as $fee) {
            $this->assignFeeToStudent($fee, $studentId);
        }

        // Also check for school-wide mandatory fees (class_id is null)
        $schoolFees = Fee::where('school_id', $student->school_id)
            ->whereNull('class_id')
            ->where('is_mandatory', true)
            ->get();

        foreach ($schoolFees as $fee) {
            $this->assignFeeToStudent($fee, $studentId);
        }
    }
}
