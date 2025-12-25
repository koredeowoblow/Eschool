<?php

namespace App\Http\Controllers\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Chat\ContactMessageService;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Chat\Contact\CreateRequest;
use App\Http\Requests\Chat\Contact\UpdateRequest;

class ContactController extends Controller
{
    public function __construct(private ContactMessageService $service)
    {
        $this->middleware('auth:sanctum');
        // Allow broader read for admins; creation can be any authenticated user in school
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list([
            'status' => $request->query('status'),
            'email'  => $request->query('email'),
        ]);
        return ResponseHelper::success($data, 'Contact messages fetched successfully');
    }

    public function store(CreateRequest $request)
    {
        try {
            $model = $this->service->create($request->validated());
            return ResponseHelper::success($model, 'Contact message created successfully', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }

    public function show(string $id)
    {
        try {
            $model = $this->service->get($id);
            return ResponseHelper::success($model, 'Contact message fetched successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    public function update(UpdateRequest $request, string $id)
    {
        try {
            $updated = $this->service->update($id, $request->validated());
            return ResponseHelper::success($updated, 'Contact message updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return ResponseHelper::success(null, 'Contact message deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }
}
