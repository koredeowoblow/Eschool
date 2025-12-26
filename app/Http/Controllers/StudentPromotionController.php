<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Students\StudentPromotionService;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Validator;

class StudentPromotionController extends Controller
{
    public function __construct(protected StudentPromotionService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher');
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->all());
        return ResponseHelper::success($data, 'Promotions fetched successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:students,id',
            'to_class_id' => 'required|exists:classes,id',
            'to_session_id' => 'required|exists:school_sessions,id',
            'to_section_id' => 'nullable|exists:sections,id',
            'to_term_id' => 'nullable|exists:terms,id',
            'type' => 'required|in:promote,repeat',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation Error', 422, $validator->errors());
        }

        $result = $this->service->promote($validator->validated());
        return ResponseHelper::success($result, 'Student(s) processed successfully', 201);
    }
}
