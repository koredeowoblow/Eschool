<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\SchoolService;
use App\Models\School;
use Illuminate\Http\Request;

use App\Helpers\ResponseHelper;
use App\Http\Requests\School\CreateRequest;
use App\Http\Requests\School\UpdateRequest;

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

        $school = $this->schoolService->findSchool($id);
        return ResponseHelper::success($school, 'School fetched successfully');
    }

    public function store(CreateRequest $request)
    {

        $school = $this->schoolService->createSchool($request->validated());
        return ResponseHelper::success($school, 'School created successfully.', 201);
    }

    public function update(UpdateRequest $request, $id)
    {

        $updatedSchool = $this->schoolService->updateSchool($id, $request->validated());
        return ResponseHelper::success($updatedSchool, 'School updated successfully.');
    }

    public function destroy($id)
    {

        $this->schoolService->deleteSchool($id);
        return ResponseHelper::success(null, 'School deleted successfully.');
    }
}
