<?php

namespace App\Http\Controllers\Api\Fees;

use App\Http\Controllers\Controller;
use App\Services\Fees\FeeAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Fees\FeeAssignmentRequest;

class FeeAssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(FeeAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Assign a fee to a class or individual student
     */
    public function assign(FeeAssignmentRequest $request)
    {
        $validated = $request->validated();

        if (isset($validated['class_id'])) {
            $this->assignmentService->assignFeeToClass($validated['fee_id'], $validated['class_id']);
            $message = 'Fee assigned to all students in the class.';
        } elseif (isset($validated['student_id'])) {
            $this->assignmentService->assignFeeToStudent($validated['fee_id'], $validated['student_id']);
            $message = 'Fee assigned to the student.';
        } else {
            return ResponseHelper::error('Either class_id or student_id is required.', 400);
        }

        return ResponseHelper::success(
            null,
            $message
        );
    }

    /**
     * Sync mandatory fees for a student
     */
    public function sync($studentId)
    {
        $this->assignmentService->syncStudentFees($studentId);

        return ResponseHelper::success(
            null,
            'Mandatory fees synced for the student.'
        );
    }
}
