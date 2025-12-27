<?php

namespace App\Http\Controllers\Class;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassRooms\TimetableService;

use App\Http\Requests\Class\TimetableRequest;
use App\Helpers\ResponseHelper;

class TimetableController extends Controller
{
    public function __construct(private TimetableService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher|student')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Timetables fetched successfully');
    }

    public function store(TimetableRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Timetable created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Timetable fetched successfully');
    }

    public function update(TimetableRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Timetable updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Timetable deleted successfully');
    }
}
