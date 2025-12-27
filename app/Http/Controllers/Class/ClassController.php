<?php

namespace App\Http\Controllers\Class;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassRooms\ClassRoomService;

use App\Http\Requests\Class\ClassRequest;
use App\Helpers\ResponseHelper;

class ClassController extends Controller
{
    public function __construct(private ClassRoomService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
        $this->middleware('check.session')->only(['store', 'update', 'destroy']);
    }

    public function index(\App\Http\Requests\Class\ClassIndexRequest $request)
    {
        $data = $this->service->list($request->validated());
        return ResponseHelper::success(
            $data->items(),
            'Classes fetched successfully',
            200,
            [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'total' => $data->total(),
            ]
        );
    }

    public function store(ClassRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Class created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Class fetched successfully');
    }

    public function update(ClassRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Class updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Class deleted successfully');
    }
}
