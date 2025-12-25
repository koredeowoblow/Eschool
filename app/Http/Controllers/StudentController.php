<?php

namespace App\Http\Controllers;

use App\Services\Students\StudentService;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;

use App\Helpers\ResponseHelper;
use App\Http\Requests\User\UserRequest;

class StudentController extends Controller
{
    public function __construct(private StudentService $studentService)
    {
        // Apply role-based authorization middleware
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);

        // Rate limiting: 60 requests per minute for create operations
        $this->middleware('throttle:60,1')->only(['store']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = $this->studentService->getAllStudents();
        return ResponseHelper::success($students, 'Students retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            $student = $this->studentService->createStudent($request->validated());
            return ResponseHelper::success($student, 'Student created successfully.', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $student = $this->studentService->getStudentById($id);
            if (!$student) {
                return ResponseHelper::notFound('Student not found.');
            }
            return ResponseHelper::success($student, 'Student retrieved successfully.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, $id)
    {
        try {
            $student = $this->studentService->updateStudent($id, $request->validated());
            return ResponseHelper::success($student, 'Student updated successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->studentService->deleteStudent($id);
            if (!$deleted) {
                return ResponseHelper::notFound('Student not found.');
            }
            return ResponseHelper::success(null, 'Student deleted successfully.');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
