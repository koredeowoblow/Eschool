<?php

namespace App\Http\Controllers\Api\Fees;

use App\Http\Controllers\Controller;
use App\Services\Fees\FeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeeController extends Controller
{
    protected $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Finance Officer');
    }

    /**
     * List all fees with filters
     */
    public function index(Request $request)
    {
        $filters = $request->only(['class_id', 'term_id', 'session_id', 'per_page']);
        $fees = $this->feeService->list($filters);

        return \App\Helpers\ResponseHelper::success(
            \App\Http\Resources\FeeResource::collection($fees->items()),
            'Fees retrieved successfully',
            200,
            [
                'current_page' => $fees->currentPage(),
                'last_page' => $fees->lastPage(),
                'total' => $fees->total(),
            ]
        );
    }

    /**
     * Store a new fee
     */
    public function store(\App\Http\Requests\Fees\FeeRequest $request)
    {
        $fee = $this->feeService->create($request->validated());

        return \App\Helpers\ResponseHelper::success(
            new \App\Http\Resources\FeeResource($fee),
            'Fee created successfully',
            201
        );
    }

    /**
     * Show a specific fee
     */
    public function show($id)
    {
        $fee = $this->feeService->get($id);

        return \App\Helpers\ResponseHelper::success(
            new \App\Http\Resources\FeeResource($fee),
            'Fee retrieved successfully'
        );
    }

    /**
     * Update an existing fee
     */
    public function update(\App\Http\Requests\Fees\FeeRequest $request, $id)
    {
        $fee = $this->feeService->update($id, $request->validated());

        return \App\Helpers\ResponseHelper::success(
            new \App\Http\Resources\FeeResource($fee),
            'Fee updated successfully'
        );
    }

    /**
     * Delete a fee
     */
    public function destroy($id)
    {
        $this->feeService->delete($id);

        return \App\Helpers\ResponseHelper::success(
            null,
            'Fee deleted successfully'
        );
    }
}
