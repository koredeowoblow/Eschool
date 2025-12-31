<?php

namespace App\Http\Controllers\Assignment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Assignments\AssignmentSubmissionService;
use App\Http\Requests\Academic\AssignmentSubmissionRequest;
use App\Helpers\ResponseHelper;

class AssignmentSubmissionController extends Controller
{
    public function __construct(private AssignmentSubmissionService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher|Student')->only(['index', 'show', 'store', 'update', 'destroy']);
        $this->middleware('check.session')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Submissions fetched successfully');
    }

    public function store(AssignmentSubmissionRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Submission created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Submission fetched successfully');
    }

    public function update(AssignmentSubmissionRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Submission updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Submission deleted successfully');
    }
}
