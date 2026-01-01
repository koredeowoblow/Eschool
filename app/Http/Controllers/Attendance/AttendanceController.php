<?php

namespace App\Http\Controllers\Attendance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\AttendanceRequest;
use App\Services\Attendance\AttendanceService;
use App\Helpers\ResponseHelper;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher|Student')->only(['index']);
        $this->middleware('role:super_admin|School Admin|Teacher')->only(['store', 'update', 'destroy', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Attendance fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttendanceRequest $request)
    {
        $data = $request->validated();
        $classId = $data['class_id'];
        $date = $data['date'];
        $records = [];

        foreach ($data['student_id'] as $index => $studentId) {
            $recordData = [
                'student_id' => $studentId,
                'class_id' => $classId,
                'date' => $date,
                'status' => $data['status'][$index] ?? 'absent',
                'remarks' => $data['remarks'][$index] ?? null,
            ];
            $records[] = $this->service->create($recordData);
        }

        return ResponseHelper::success($records, 'Attendance records created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = $this->service->get($id);
        return ResponseHelper::success($record, 'Attendance fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttendanceRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Attendance updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Attendance deleted successfully');
    }
}
