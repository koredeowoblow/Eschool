<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Fees\FeeTypeService;
use App\Http\Requests\Fees\FeeTypeRequest;
use App\Http\Requests\Fees\BulkFeeTypeRequest;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;

class FeeTypeController extends Controller
{
    public function __construct(private FeeTypeService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher')->only(['index', 'show', 'getUsageStats', 'getFeeTypesSummary']);
        $this->middleware('role:super_admin|School Admin')->only(['store', 'update', 'destroy', 'bulkCreate']);
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
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Fee type fetched successfully');
    }

    /**
     * Update an existing fee type
     */
    public function update(FeeTypeRequest $request, string $id)
    {
        $updated = $this->service->update($id, $request->validated());
        return ResponseHelper::success($updated, 'Fee type updated successfully');
    }

    /**
     * Delete a fee type
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Fee type deleted successfully');
    }

    /**
     * Bulk create fee types
     */
    public function bulkCreate(BulkFeeTypeRequest $request)
    {
        $validated = $request->validated();

        $results = $this->service->bulkCreate($validated['fee_types']);
        return ResponseHelper::success($results, 'Fee types created successfully', 201);
    }

    /**
     * Get usage statistics for a fee type
     */
    public function getUsageStats(string $id)
    {
        $stats = $this->service->getUsageStats($id);
        return ResponseHelper::success($stats, 'Fee type usage statistics fetched successfully');
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
