<?php

namespace App\Http\Controllers\School\Academic;

use App\Http\Controllers\Controller;
use App\Services\Academic\GradingScaleService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Requests\School\Academic\StoreGradingScaleRequest;
use App\Http\Requests\School\Academic\UpdateGradingScaleRequest;

class GradingScaleController extends Controller
{
    protected $gradingService;

    public function __construct(GradingScaleService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->school_id && !$user->hasRole('super_admin')) {
            return ResponseHelper::error('Unauthorized', 403);
        }

        $schoolId = $user->school_id;

        // If super admin and passing a school_id in query, use that.
        if ($user->hasRole('super_admin') && $request->has('school_id')) {
            $schoolId = $request->get('school_id');
        }

        if (!$schoolId) {
            // For super admin, if no school selected, just return empty list or handle gracefully
            if ($user->hasRole('super_admin')) {
                return ResponseHelper::success([], 'Select a school to view grading scales.');
            }
            return ResponseHelper::error('School context required', 400);
        }

        $sessionId = $request->get('session_id'); // Optional filtering

        $scales = $this->gradingService->getSchoolGradingScales($schoolId, $sessionId);

        return ResponseHelper::success($scales, 'Grading scales fetched successfully');
    }

    public function store(StoreGradingScaleRequest $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        if ($user->hasRole('super_admin') && $request->has('school_id')) {
            $schoolId = $request->get('school_id');
        }

        if (!$schoolId) {
            return ResponseHelper::error('School context required', 400);
        }

        $validated = $request->validated();
        $validated['school_id'] = $schoolId;
        $validated['is_default'] = empty($validated['session_id']);

        try {
            $scale = $this->gradingService->createGradingScale($validated);
            return ResponseHelper::success($scale, 'Grading scale created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), 422, $e->errors());
        }
    }

    public function update(UpdateGradingScaleRequest $request, $id)
    {
        $validated = $request->validated();

        try {
            $scale = $this->gradingService->updateGradingScale($id, $validated);
            return ResponseHelper::success($scale, 'Grading scale updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error($e->getMessage(), 422, $e->errors());
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update grading scale', 500);
        }
    }

    public function destroy($id)
    {
        $this->gradingService->deleteGradingScale($id);
        return ResponseHelper::success(null, 'Grading scale deleted successfully');
    }
}
