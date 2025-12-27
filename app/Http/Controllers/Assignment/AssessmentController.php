<?php

namespace App\Http\Controllers\Assignment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Assignments\AssessmentService;
use App\Http\Requests\Academic\AssessmentRequest;
use App\Helpers\ResponseHelper;

class AssessmentController extends Controller
{
    public function __construct(private AssessmentService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show', 'store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Assessments fetched successfully');
    }

    public function store(AssessmentRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Assessment created successfully', 201);
    }

    public function show($id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Assessment fetched successfully');
    }

    public function update(AssessmentRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Assessment updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Assessment deleted successfully');
    }
}
