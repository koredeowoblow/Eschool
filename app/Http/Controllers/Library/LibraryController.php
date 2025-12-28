<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Library\LibraryBookService;

use App\Http\Requests\Library\LibraryBookRequest;
use App\Helpers\ResponseHelper;

class LibraryController extends Controller
{
    public function __construct(private LibraryBookService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher|Student')->only(['index', 'show']);
        $this->middleware('role:super_admin|School Admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Books fetched successfully');
    }

    public function store(LibraryBookRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Book created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Book fetched successfully');
    }

    public function update(LibraryBookRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Book updated successfully');
    }

    public function destroy(string $id)
    {
        $deleted = $this->service->delete($id);
        if (! $deleted) {
            return ResponseHelper::error('Cannot delete book with active borrowings', 422);
        }
        return ResponseHelper::success(null, 'Book deleted successfully');
    }
}
