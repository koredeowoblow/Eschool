<?php

namespace App\Http\Controllers\Class;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassRooms\SubjectService;

use App\Http\Requests\Class\SubjectRequest;
use App\Helpers\ResponseHelper;

class SubjectController extends Controller
{
    public function __construct(private SubjectService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Subjects fetched successfully');
    }

    public function store(SubjectRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Subject created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Subject fetched successfully');
    }

    public function update(SubjectRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Subject updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Subject deleted successfully');
    }
}
