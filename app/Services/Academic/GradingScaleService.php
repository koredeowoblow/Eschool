<?php

namespace App\Services\Academic;

use App\Models\GradingScale;
use Illuminate\Validation\ValidationException;

class GradingScaleService
{
    public function getSchoolGradingScales($schoolId, $sessionId = null)
    {
        $query = GradingScale::where('school_id', $schoolId);

        if ($sessionId) {
            // Fetch defaults + session specific? Or just session specific?
            // Usually we want to show what applies to the current session.
            // If we have session-specific overrides, they take precedence which logic handled in ReportService.
            // For management UI, let's just show all for the school, or filter by session.
            $query->where(function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId)
                    ->orWhereNull('session_id') // Defaults often have null session or flagged is_default
                    ->orWhere('is_default', true);
            });
        }

        return $query->orderBy('min_score', 'desc')->get();
    }

    public function createGradingScale(array $data)
    {
        $this->validateOverlap($data['school_id'], $data['min_score'], $data['max_score'], $data['session_id'] ?? null);

        return GradingScale::create($data);
    }

    public function updateGradingScale($id, array $data)
    {
        $grade = GradingScale::findOrFail($id);

        $this->validateOverlap($grade->school_id, $data['min_score'], $data['max_score'], $grade->session_id, $id);

        $grade->update($data);
        return $grade;
    }

    public function deleteGradingScale($id)
    {
        $grade = GradingScale::findOrFail($id);
        $grade->delete();
        return true;
    }

    protected function validateOverlap($schoolId, $min, $max, $sessionId = null, $ignoreId = null)
    {
        $query = GradingScale::where('school_id', $schoolId);

        if ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            // checking against global defaults for this school
            $query->where(function ($q) {
                $q->whereNull('session_id')->orWhere('is_default', true);
            });
        }

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        // Overlap logic: (StartA <= EndB) and (EndA >= StartB)
        // Here: (min <= existing_max) and (max >= existing_min)
        $exists = $query->where(function ($q) use ($min, $max) {
            $q->where(function ($sub) use ($min, $max) {
                $sub->where('min_score', '<=', $max)
                    ->where('max_score', '>=', $min);
            });
        })->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'grade_range' => 'The score range overlaps with an existing grading scale.',
            ]);
        }
    }
}
