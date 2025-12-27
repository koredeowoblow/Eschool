<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Students\StudentPromotionService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Student\PromotionRequest;

class StudentPromotionController extends Controller
{
    public function __construct(protected StudentPromotionService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher');
    }

    public function index(PromotionRequest $request)
    {
        $data = $this->service->list($request->validated());
        return ResponseHelper::success($data, 'Promotions fetched successfully');
    }

    public function store(PromotionRequest $request)
    {
        $result = $this->service->promote($request->validated());
        return ResponseHelper::success($result, 'Student(s) processed successfully', 201);
    }
}
