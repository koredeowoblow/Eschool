<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\Plans\PlanService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'no_of_students' => 'required|integer',
            'no_of_teachers' => 'required|integer',
            'no_of_guardians' => 'required|integer',
            'no_of_staff' => 'required|integer',
        ]);

        $plan = $this->planService->createPlan($data);
        return ResponseHelper::success($plan, 'Plan created successfully.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric',
            'no_of_students' => 'sometimes|integer',
            'no_of_teachers' => 'sometimes|integer',
            'no_of_guardians' => 'sometimes|integer',
            'no_of_staff' => 'sometimes|integer',
        ]);

        $plan = $this->planService->updatePlan($id, $data);
        return ResponseHelper::success($plan, 'Plan updated successfully.');
    }

    public function destroy($id)
    {
        $this->planService->deletePlan($id);
        return ResponseHelper::success(null, 'Plan deleted successfully.');
    }

    public function assignToSchool(Request $request, $schoolId)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $schoolPlan = $this->planService->assignPlanToSchool($schoolId, $request->plan_id);
        return ResponseHelper::success($schoolPlan, 'Plan assigned to school successfully.');
    }

    public function createCustom(Request $request, $schoolId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'no_of_students' => 'required|integer',
            'no_of_teachers' => 'required|integer',
            'no_of_guardians' => 'required|integer',
            'no_of_staff' => 'required|integer',
        ]);

        $schoolPlan = $this->planService->createCustomPlanForSchool($schoolId, $data);
        return ResponseHelper::success($schoolPlan, 'Custom plan created for school successfully.');
    }

    public function updateSchoolLimits(Request $request, $schoolId)
    {
        $data = $request->validate([
            'no_of_students' => 'sometimes|integer',
            'no_of_teachers' => 'sometimes|integer',
            'no_of_guardians' => 'sometimes|integer',
            'no_of_staff' => 'sometimes|integer',
        ]);

        $schoolPlan = $this->planService->updateSchoolPlanLimit($schoolId, $data);
        return ResponseHelper::success($schoolPlan, 'School plan limits updated.');
    }
}
