<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Fees\FeeTypeService;
use App\Http\Requests\Fees\FeeTypeRequest;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;

class FeeTypeController extends Controller
{
    public function __construct(private FeeTypeService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show', 'getUsageStats', 'getFeeTypesSummary']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy', 'bulkCreate']);
        $this->middleware('check.session')->only(['store', 'update', 'destroy', 'bulkCreate']);
    }

    /**
     * List all fee types with optional filtering
     */
    public function index(Request $request)
    {
        $data = $this->service->list($request->query());
        return ResponseHelper::success($data, 'Fee types fetched successfully');
    }

    /**
     * Store a newly created fee type
     */
    public function store(FeeTypeRequest $request)
    {
        $data = $this->service->create($request->validated());
        return ResponseHelper::success($data, 'Fee type created successfully', 201);
    }

    /**
     * Get a specific fee type
     */
    public function show(string $id)
    {
        try {
            $model = $this->service->get($id);
            return ResponseHelper::success($model, 'Fee type fetched successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    /**
     * Update an existing fee type
     */
    public function update(FeeTypeRequest $request, string $id)
    {
        try {
            $updated = $this->service->update($id, $request->validated());
            return ResponseHelper::success($updated, 'Fee type updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    /**
     * Delete a fee type
     */
    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return ResponseHelper::success(null, 'Fee type deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        } catch (\RuntimeException $e) {
            return ResponseHelper::error($e->getMessage(), 422);
        }
    }

    /**
     * Bulk create fee types
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'fee_types' => 'required|array',
            'fee_types.*.name' => 'required|string|max:255',
            'fee_types.*.description' => 'nullable|string',
            'fee_types.*.amount' => 'required|numeric|min:0',
        ]);

        $results = $this->service->bulkCreate($validated['fee_types']);
        return ResponseHelper::success($results, 'Fee types created successfully', 201);
    }

    /**
     * Get usage statistics for a fee type
     */
    public function getUsageStats(string $id)
    {
        try {
            $stats = $this->service->getUsageStats($id);
            return ResponseHelper::success($stats, 'Fee type usage statistics fetched successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::notFound($e->getMessage());
        }
    }

    /**
     * Get fee types summary for a school
     */
    public function getFeeTypesSummary(Request $request)
    {
        $schoolId = $request->query('school_id');
        $summary = $this->service->getFeeTypesSummary($schoolId);
        return ResponseHelper::success($summary, 'Fee types summary fetched successfully');
    }
}
