<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\Plans\PlanService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SuperAdmin\StorePlanRequest;
use App\Http\Requests\SuperAdmin\UpdatePlanRequest;
use App\Http\Requests\SuperAdmin\AssignPlanToSchoolRequest;

class PlanController extends Controller
{
    protected $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    public function index()
    {
        $plans = $this->planService->getAllPlans();
        return ResponseHelper::success($plans, 'Plans retrieved successfully.');
    }

    public function store(StorePlanRequest $request)
    {
        $data = $request->validated();
        $plan = $this->planService->createPlan($data);
        return ResponseHelper::success($plan, 'Plan created successfully.');
    }

    public function update(UpdatePlanRequest $request, $id)
    {
        $data = $request->validated();
        $plan = $this->planService->updatePlan($id, $data);
        return ResponseHelper::success($plan, 'Plan updated successfully.');
    }

    public function destroy($id)
    {
        $this->planService->deletePlan($id);
        return ResponseHelper::success(null, 'Plan deleted successfully.');
    }

    public function assignToSchool(AssignPlanToSchoolRequest $request, $schoolId)
    {
        $validated = $request->validated();

        $schoolPlan = $this->planService->assignPlanToSchool($schoolId, $validated['plan_id']);
        return ResponseHelper::success($schoolPlan, 'Plan assigned to school successfully.');
    }

    public function createCustom(StorePlanRequest $request, $schoolId)
    {
        $data = $request->validated();

        $schoolPlan = $this->planService->createCustomPlanForSchool($schoolId, $data);
        return ResponseHelper::success($schoolPlan, 'Custom plan created for school successfully.');
    }

    public function updateSchoolLimits(UpdatePlanRequest $request, $schoolId)
    {
        $data = $request->validated();

        $schoolPlan = $this->planService->updateSchoolPlanLimit($schoolId, $data);
        return ResponseHelper::success($schoolPlan, 'School plan limits updated.');
    }
}
