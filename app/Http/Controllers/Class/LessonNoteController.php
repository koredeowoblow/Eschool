<?php

namespace App\Http\Controllers\Class;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\ClassRooms\LessonNoteService;

use App\Http\Requests\Class\LessonNoteRequest;
use App\Helpers\ResponseHelper;

class LessonNoteController extends Controller
{
    public function __construct(private LessonNoteService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show', 'store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Lesson notes fetched successfully');
    }

    public function store(LessonNoteRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Lesson note created successfully', 201);
    }

    public function show(string $id)
    {
        try {
            $model = $this->service->get($id);
            return ResponseHelper::success($model, 'Lesson note fetched successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    public function update(LessonNoteRequest $request, string $id)
    {
        try {
            $updated = $this->service->update($id, $request->validated());
            return ResponseHelper::success($updated, 'Lesson note updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return ResponseHelper::success(null, 'Lesson note deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }
}
