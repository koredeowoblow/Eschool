<?php

namespace App\Services\Students;

use App\Repositories\Students\StudentPromotionRepository;
use App\Models\Student;
use App\Models\StudentPromotion;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentPromotionService
{
    public function __construct(
        protected StudentPromotionRepository $repo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function promote(array $data)
    {
        return DB::transaction(function () use ($data) {
            $studentIds = (array) $data['student_ids'];
            $promotions = [];

            foreach ($studentIds as $studentId) {
                $student = Student::findOrFail($studentId);

                // Record the promotion
                $promotion = $this->repo->create([
                    'school_id' => $student->school_id,
                    'student_id' => $studentId,
                    'from_class_id' => $student->class_id,
                    'to_class_id' => $data['to_class_id'],
                    'from_session_id' => $student->school_session_id,
                    'to_session_id' => $data['to_session_id'],
                    'type' => $data['type'] ?? 'promote',
                    'promoted_by' => Auth::id(),
                ]);

                // Update student current class
                $student->update([
                    'class_id' => $data['to_class_id'],
                    'section_id' => $data['to_section_id'] ?? $student->section_id,
                    'school_session_id' => $data['to_session_id'],
                ]);

                // Create new enrollment record
                Enrollment::create([
                    'school_id' => $student->school_id,
                    'student_id' => $studentId,
                    'class_id' => $data['to_class_id'],
                    'session_id' => $data['to_session_id'],
                    'term_id' => $data['to_term_id'] ?? null, // Often starts with first term
                    'enrollment_date' => now(),
                    'status' => 'active',
                ]);

                $promotions[] = $promotion;
            }

            return $promotions;
        });
    }

    public function get(int|string $id)
    {
        return $this->repo->findById($id);
    }
}
