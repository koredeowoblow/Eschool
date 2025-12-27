<?php

namespace App\Http\Controllers\Class\Session;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassRooms\SessionService;

use App\Http\Requests\Class\SessionRequest;
use App\Helpers\ResponseHelper;

class SessionController extends Controller
{
    public function __construct(private SessionService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Sessions fetched successfully');
    }

    public function store(SessionRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Session created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Session fetched successfully');
    }

    public function update(SessionRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Session updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Session deleted successfully');
    }
}
