<?php

namespace App\Http\Controllers\Assignment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Assignments\AssignmentService;
use App\Http\Requests\Academic\AssignmentRequest;
use App\Helpers\ResponseHelper;

class AssignmentController extends Controller
{
    public function __construct(private AssignmentService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher|Student')->only(['index', 'show']);
        $this->middleware('role:super_admin|School Admin|Teacher')->only(['store', 'update', 'destroy']);
        $this->middleware('check.session')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Assignments fetched successfully');
    }

    public function store(AssignmentRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Assignment created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Assignment fetched successfully');
    }

    public function update(AssignmentRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Assignment updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Assignment deleted successfully');
    }
}
