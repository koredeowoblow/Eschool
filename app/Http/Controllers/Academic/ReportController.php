<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Services\Academic\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Academic\ReportCollationRequest;

class ReportController extends Controller
{
    public function __construct(protected ReportService $service) {}

    /**
     * Trigger collation for a class.
     */
    public function collate(ReportCollationRequest $request)
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($validated) {
            return $this->service->collateResults(
                $validated['class_id'],
                $validated['term_id'],
                $validated['school_session_id']
            );
        });

        return ResponseHelper::success(
            ['errors' => $result['errors']],
            "Successfully collated {$result['collated_count']} subject records."
        );
    }

    /**
     * Get missing results list.
     */
    public function missing(ReportCollationRequest $request)
    {
        $validated = $request->validated();

        $gaps = $this->service->getMissingResults(
            $validated['class_id'],
            $validated['term_id'],
            $validated['school_session_id']
        );

        return ResponseHelper::success($gaps, 'Missing results fetched successfully');
    }

    /**
     * Fetch collated results for a class (Broadsheet data).
     */
    public function broadsheet(ReportCollationRequest $request)
    {
        $validated = $request->validated();

        // This would typically involve a repository call or a service method to get SubjectResult entries
        // For now, let's keep it simple.
        $results = \App\Models\SubjectResult::with(['student.user', 'subject'])
            ->where('class_id', $validated['class_id'])
            ->where('term_id', $validated['term_id'])
            ->where('session_id', $validated['school_session_id'])
            ->get();

        return ResponseHelper::success($results, 'Broadsheet data fetched successfully');
    }
}
