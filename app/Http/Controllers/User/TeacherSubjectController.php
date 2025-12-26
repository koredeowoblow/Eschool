<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\TeacherSubjectRequest;
use App\Services\Teachers\TeacherSubjectService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;

class TeacherSubjectController extends Controller
{
    public function __construct(private TeacherSubjectService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $assignments = $this->service->list($request->all());
        return ResponseHelper::success($assignments, 'Assignments retrieved successfully');
    }

    public function store(TeacherSubjectRequest $request)
    {
        $assignment = $this->service->create($request->validated());
        return ResponseHelper::success($assignment, 'Subject assigned successfully', 201);
    }

    public function show($id)
    {
        $assignment = $this->service->get($id);
        return ResponseHelper::success($assignment, 'Assignment details retrieved');
    }

    public function update(TeacherSubjectRequest $request, $id)
    {
        $assignment = $this->service->update($id, $request->validated());
        return ResponseHelper::success($assignment, 'Assignment updated successfully');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Assignment removed successfully');
    }
}
