<?php

namespace App\Http\Controllers\Class\Session;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassRooms\SectionService;

use App\Http\Requests\Class\SectionRequest;
use App\Helpers\ResponseHelper;

class SectionController extends Controller
{
    public function __construct(private SectionService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Sections fetched successfully');
    }

    public function store(SectionRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Section created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Section fetched successfully');
    }

    public function update(SectionRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Section updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Section deleted successfully');
    }
}
