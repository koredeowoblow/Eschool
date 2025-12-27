<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateUSerRequest;
use Illuminate\Http\Request;
use App\Services\Users\UserService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\User\UserRequest;
use App\Helpers\ResponseHelper;

class UserController extends Controller
{
    public function __construct(private UserService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin')->only(['store', 'update']);
    }

    /**
     * Get authenticated user profile
     */
    public function index()
    {
        return ResponseHelper::success(Auth::user(), 'User profile fetched successfully');
    }

    /**
     * Create a new user
     */
    public function store(UserRequest $request)
    {
        try {
            $user = $this->service->create($request->validated());
            return ResponseHelper::success($user, 'User created successfully', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }

    /**
     * Fetch students related to the user
     */
    public function fetchStudents()
    {
        $students = Auth::user()->myStudents();
        return ResponseHelper::success($students, 'Students fetched successfully');
    }

    /**
     * Get a student by ID
     */
    public function getStudent($id)
    {
        $student = Auth::user()->myStudentById($id);
        if (!$student) {
            return ResponseHelper::notFound('Student not found');
        }
        return ResponseHelper::success($student, 'Student fetched successfully');
    }

    /**
     * Fetch teachers related to the user
     */
    public function fetchTeachers()
    {
        $teachers = Auth::user()->myTeachers();
        return ResponseHelper::success($teachers, 'Teachers fetched successfully');
    }

    /**
     * Get a teacher by ID
     */
    public function getTeacher($id)
    {
        $teacher = Auth::user()->myTeacherById($id);
        if (!$teacher) {
            return ResponseHelper::notFound('Teacher not found');
        }
        return ResponseHelper::success($teacher, 'Teacher fetched successfully');
    }

    /**
     * Fetch guardians related to the user
     */
    public function fetchGuardians()
    {
        $guardians = Auth::user()->myGuardians();
        return ResponseHelper::success($guardians, 'Guardians fetched successfully');
    }

    /**
     * Get a guardian by ID
     */
    public function getGuardian($id)
    {
        $guardian = Auth::user()->myGuardianById($id);
        if (!$guardian) {
            return ResponseHelper::notFound('Guardian not found');
        }
        return ResponseHelper::success($guardian, 'Guardian fetched successfully');
    }

    /**
     * Update authenticated user profile
     */
    public function UpdateProfile(UserRequest $request)
    {
        $user = Auth::user();
        $updatedUser = $this->service->updateProfile($user, $request->validated());
        return ResponseHelper::success($updatedUser, 'Profile updated successfully');
    }

    /**
     * Update a user (admin managed)
     */
    public function update(UserRequest $request, string $id)
    {
        try {
            $updatedUser = $this->service->update($id, $request->validated());
            return ResponseHelper::success($updatedUser, 'User updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }
}
