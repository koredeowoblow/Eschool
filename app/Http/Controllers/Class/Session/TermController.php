<?php

namespace App\Http\Controllers\Class\Session;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassRooms\TermService;

use App\Http\Requests\Class\TermRequest;
use App\Helpers\ResponseHelper;

class TermController extends Controller
{
    public function __construct(private TermService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Terms fetched successfully');
    }

    public function store(TermRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Term created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Term fetched successfully');
    }

    public function update(TermRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Term updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Term deleted successfully');
    }
}
