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
        $this->middleware('role:super_admin|school_admin|teacher|student')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin|teacher')->only(['store', 'update', 'destroy']);
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
        try {
            $model = $this->service->get($id);
            return ResponseHelper::success($model, 'Assignment fetched successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    public function update(AssignmentRequest $request, string $id)
    {
        try {
            $updated = $this->service->update($id, $request->validated());
            return ResponseHelper::success($updated, 'Assignment updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return ResponseHelper::success(null, 'Assignment deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }
}
