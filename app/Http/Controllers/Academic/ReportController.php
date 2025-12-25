<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Services\Academic\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct(protected ReportService $service) {}

    /**
     * Trigger collation for a class.
     */
    public function collate(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'session_id' => 'required|exists:school_sessions,id',
        ]);

        try {
            DB::beginTransaction();
            $result = $this->service->collateResults(
                $request->class_id,
                $request->term_id,
                $request->session_id
            );
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Successfully collated {$result['collated_count']} subject records.",
                'errors' => $result['errors']
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get missing results list.
     */
    public function missing(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'session_id' => 'required|exists:school_sessions,id',
        ]);

        $gaps = $this->service->getMissingResults(
            $request->class_id,
            $request->term_id,
            $request->session_id
        );

        return response()->json([
            'status' => 'success',
            'data' => $gaps
        ]);
    }

    /**
     * Fetch collated results for a class (Broadsheet data).
     */
    public function broadsheet(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'session_id' => 'required|exists:school_sessions,id',
        ]);

        // This would typically involve a repository call or a service method to get SubjectResult entries
        // For now, let's keep it simple.
        $results = \App\Models\SubjectResult::with(['student.user', 'subject'])
            ->where('class_id', $request->class_id)
            ->where('term_id', $request->term_id)
            ->where('session_id', $request->session_id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }
}
