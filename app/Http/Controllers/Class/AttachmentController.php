<?php

namespace App\Http\Controllers\Class;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassRooms\AttachmentService;

use App\Http\Requests\Class\AttachmentRequest;
use App\Helpers\ResponseHelper;

class AttachmentController extends Controller
{
    public function __construct(private AttachmentService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Attachments fetched successfully');
    }

    public function store(AttachmentRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Attachment created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Attachment fetched successfully');
    }

    public function update(AttachmentRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Attachment updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Attachment deleted successfully');
    }
}
