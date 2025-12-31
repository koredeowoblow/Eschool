<?php

namespace App\Http\Controllers\Class\Session;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Students\EnrollmentRepository;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Class\EnrollmentRequest;
use App\Services\ClassRooms\EnrollmentService;
use App\Helpers\ResponseHelper;

class EnrollmentController extends Controller
{
    public function __construct(private EnrollmentService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|School Admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Enrollments fetched successfully');
    }

    public function store(EnrollmentRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Enrollment created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Enrollment fetched successfully');
    }

    public function update(EnrollmentRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Enrollment updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Enrollment deleted successfully');
    }
}
