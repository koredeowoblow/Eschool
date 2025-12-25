<?php

namespace App\Services\Academic;

use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Models\GradingScale;
use App\Models\SubjectResult;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Collate results for a class in a specific term/session.
     * Raw assessments -> Aggregated Subject Results.
     */
    public function collateResults(int $classId, int $termId, int $sessionId)
    {
        $class = ClassRoom::findOrFail($classId);

        // 1. Validate Session Lock (To be implemented with Session model extension if needed)
        // For now, we assume if we are here, we can collate.

        $students = $class->students;
        $subjects = $class->subjects; // Via teacher_subjects relationship

        $collatedCount = 0;
        $errors = [];

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                $result = $this->aggregateStudentSubjectScore($student, $subject, $class, $termId, $sessionId);

                if ($result['status'] === 'success') {
                    $this->upsertSubjectResult($student, $subject, $class, $termId, $sessionId, $result['data']);
                    $collatedCount++;
                } else {
                    $errors[] = [
                        'student' => $student->user->name,
                        'subject' => $subject->name,
                        'error' => $result['message']
                    ];
                }
            }
        }

        return [
            'collated_count' => $collatedCount,
            'errors' => $errors
        ];
    }

    /**
     * Aggregate individual marks for a student-subject pair.
     */
    private function aggregateStudentSubjectScore($student, $subject, $class, $termId, $sessionId)
    {
        // Fetch all approved assessments for this context
        $assessments = Assessment::where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->where('is_approved', 1)
            ->get();

        if ($assessments->isEmpty()) {
            return ['status' => 'error', 'message' => 'No approved assessments found.'];
        }

        $caScore = 0;
        $examScore = 0;
        $hasExam = false;
        $hasCA = false;

        foreach ($assessments as $assessment) {
            $mark = Result::where('assessment_id', $assessment->id)
                ->where('student_id', $student->id)
                ->first();

            if (!$mark) continue;

            if (strtoupper($assessment->type) === 'EXAM') {
                if ($hasExam) {
                    return ['status' => 'error', 'message' => 'Multiple exam records detected. Correlation rejected.'];
                }
                $examScore = $mark->marks_obtained;
                $hasExam = true;
            } else {
                $caScore += $mark->marks_obtained;
                $hasCA = true;
            }
        }

        if (!$hasExam && !$hasCA) {
            return ['status' => 'error', 'message' => 'No marks entered for any assessment.'];
        }

        return [
            'status' => 'success',
            'data' => [
                'ca_score' => $caScore,
                'exam_score' => $examScore,
                'total_score' => $caScore + $examScore,
                'is_incomplete' => (!$hasExam || !$hasCA)
            ]
        ];
    }

    /**
     * Safe upsert into subject_results table.
     */
    private function upsertSubjectResult($student, $subject, $class, $termId, $sessionId, $data)
    {
        $grading = $this->getGradeForScore($data['total_score'], $sessionId, $student->school_id);

        SubjectResult::updateOrCreate(
            [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'term_id' => $termId,
                'session_id' => $sessionId,
            ],
            [
                'ca_score' => $data['ca_score'],
                'exam_score' => $data['exam_score'],
                'total_score' => $data['total_score'],
                'grade' => $grading ? $grading->grade_label : null,
                'remark' => $grading ? $grading->remark : null,
                'status' => $data['is_incomplete'] ? 'incomplete' : ($grading && $grading->is_pass ? 'Pass' : 'Fail'),
                'is_collated' => !$data['is_incomplete'],
                'school_id' => $student->school_id,
            ]
        );
    }

    /**
     * Map total score to a grade from the grading_scales table.
     */
    private function getGradeForScore($score, $sessionId, $schoolId)
    {
        return GradingScale::where('school_id', $schoolId)
            ->where(function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId)
                    ->orWhere('is_default', true);
            })
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->orderBy('is_default', 'asc') // Prefer session-specific over default
            ->first();
    }

    /**
     * Detect missing results for a class.
     */
    public function getMissingResults(int $classId, int $termId, int $sessionId)
    {
        $class = ClassRoom::findOrFail($classId);
        $students = $class->students;
        $subjects = $class->subjects;

        $gaps = [];

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                $exists = SubjectResult::where('student_id', $student->id)
                    ->where('subject_id', $subject->id)
                    ->where('class_id', $class->id)
                    ->where('term_id', $termId)
                    ->where('session_id', $sessionId)
                    ->where('is_collated', true)
                    ->exists();

                if (!$exists) {
                    $gaps[] = [
                        'student' => $student->user->name,
                        'subject' => $subject->name,
                    ];
                }
            }
        }

        return $gaps;
    }
}
