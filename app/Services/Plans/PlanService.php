<?php

namespace App\Services\Plans;

use App\Repositories\Plans\PlanRepository;
use App\Repositories\Plans\SchoolPlanRepository;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Exception;

class PlanService
{
    protected $planRepository;
    protected $schoolPlanRepository;

    public function __construct(PlanRepository $planRepository, SchoolPlanRepository $schoolPlanRepository)
    {
        $this->planRepository = $planRepository;
        $this->schoolPlanRepository = $schoolPlanRepository;
    }

    public function getAllPlans()
    {
        return $this->planRepository->all();
    }

    public function createPlan(array $data)
    {
        return $this->planRepository->create($data);
    }

    public function updatePlan($id, array $data)
    {
        return $this->planRepository->update($id, $data);
    }

    public function deletePlan($id)
    {
        return $this->planRepository->delete($id);
    }

    public function assignPlanToSchool($schoolId, $planId)
    {
        // When assigning a global plan to a school, we should create a SchoolPlan copy 
        // to isolate that school's limits and track their specific usage/customization if enabled.
        // OR we can just link to global plan if no customization needed.
        // Requirement: "create a custom plan for a particular school". 
        // And "if they pass the limit it will return a message".

        // Strategy: Always create a SchoolPlan based on the Plan.

        return DB::transaction(function () use ($schoolId, $planId) {
            $school = School::findOrFail($schoolId);
            $plan = $this->planRepository->findById($planId);

            if (!$plan) {
                throw new Exception("Plan not found");
            }

            // Create SchoolPlan instance
            $schoolPlanData = [
                'name' => $plan->name,
                'price' => $plan->price,
                'no_of_students' => $plan->no_of_students,
                'no_of_teachers' => $plan->no_of_teachers,
                'no_of_guardians' => $plan->no_of_guardians,
                'no_of_staff' => $plan->no_of_staff,
                'plan_id' => $plan->id,
                'school_id' => $school->id,
            ];

            $schoolPlan = $this->schoolPlanRepository->create($schoolPlanData);

            // Update School
            $school->school_plan_id = $schoolPlan->id;
            $school->save();

            return $schoolPlan;
        });
    }

    public function createCustomPlanForSchool($schoolId, array $data)
    {
        return DB::transaction(function () use ($schoolId, $data) {
            $school = School::findOrFail($schoolId);

            $data['school_id'] = $schoolId;
            // Ensure no plan_id link if it's fully custom, or keep it if it's based on one?
            // Let's assume custom plan just creating a SchoolPlan directly.

            $schoolPlan = $this->schoolPlanRepository->create($data);

            $school->school_plan_id = $schoolPlan->id;
            $school->save();

            return $schoolPlan;
        });
    }

    public function updateSchoolPlanLimit($schoolId, array $limits)
    {
        $school = School::findOrFail($schoolId);
        if (!$school->schoolPlan) {
            throw new Exception("School has no active plan");
        }

        $school->schoolPlan->update($limits);
        return $school->schoolPlan;
    }
}
