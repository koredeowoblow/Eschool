<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Library\LibraryBorrowingService;

use App\Http\Requests\Library\LibraryBorrowingRequest;
use App\Helpers\ResponseHelper;

class LibraryBorrowingController extends Controller
{
    public function __construct(private LibraryBorrowingService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher|Student')->only(['index', 'show']);
        $this->middleware('role:super_admin|School Admin|Student')->only(['store']);
        $this->middleware('role:super_admin|School Admin')->only(['update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Borrowings fetched successfully');
    }

    public function store(LibraryBorrowingRequest $request)
    {
        $model = $this->service->create($request->validated());
        return ResponseHelper::success($model, 'Borrowing created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Borrowing fetched successfully');
    }

    public function update(LibraryBorrowingRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Borrowing updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Borrowing deleted successfully');
    }
}
