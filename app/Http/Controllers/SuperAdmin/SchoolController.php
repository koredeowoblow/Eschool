<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\SchoolService;
use App\Models\School;
use Illuminate\Http\Request;

use App\Helpers\ResponseHelper;
use App\Http\Requests\School\CreateRequest;

class SchoolController extends Controller
{
    public function __construct(private SchoolService $schoolService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin');
    }

    public function index()
    {
        if (request()->wantsJson()) {
            $schools = $this->schoolService->getAllSchools();
            return ResponseHelper::success($schools, 'Schools fetched successfully');
        }

        return view('super_admin.schools.index');
    }

    public function show($id)
    {
        try {
            $school = $this->schoolService->findSchool($id);
            return ResponseHelper::success($school, 'School fetched successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $school = $this->schoolService->createSchool($request->validated());
            return ResponseHelper::success($school, 'School created successfully.', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $updatedSchool = $this->schoolService->updateSchool($id, $request->all());
            return ResponseHelper::success($updatedSchool, 'School updated successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }

    public function destroy($id)
    {
        try {
            $this->schoolService->deleteSchool($id);
            return ResponseHelper::success(null, 'School deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }
}
