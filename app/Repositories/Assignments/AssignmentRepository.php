<?php

namespace App\Repositories\Assignments;

use App\Models\Assignment;
use App\Repositories\BaseRepository;

class AssignmentRepository extends BaseRepository
{
     public function __construct(Assignment $model)
     {
          parent::__construct($model);
     }

     /**
      * Scoped query: Enforce class-ownership for student/guardian users.
      */
     public function query()
     {
          $query = parent::query();
          /** @var \App\Models\User $user */
          $user = \Illuminate\Support\Facades\Auth::user();

          if ($user && $user->hasRole('Student')) {
               $student = $user->student()->first();
               if ($student) {
                    $classIds = $this->getStudentClassIds($student);
                    $query->whereIn('class_id', $classIds);
               } else {
                    $query->where('id', 0); // Safe failure for orphaned users
               }
          } elseif ($user && $user->hasRole('Guardian')) {
               // Security: Enforce child's class-scoping for parents
               $students = $user->guardianStudents();
               if ($students->isNotEmpty()) {
                    $allClassIds = [];
                    foreach ($students as $student) {
                         $allClassIds = array_merge($allClassIds, $this->getStudentClassIds($student));
                    }
                    $query->whereIn('class_id', array_unique($allClassIds));
               } else {
                    $query->where('id', 0); // Safe failure for orphaned guardians
               }
          } elseif ($user && $user->hasRole('Teacher')) {
               // Security: Enforce class-scoping for teachers
               $query->whereIn('class_id', function ($q) use ($user) {
                    $q->select('class_id')
                         ->from('teacher_subjects')
                         ->where('teacher_id', $user->teacherProfile?->id ?? 0);
               });
          }

          return $query;
     }

     /**
      * Helper to resolve class IDs for a student.
      */
     private function getStudentClassIds($student): array
     {
          return $student->class_id ? [$student->class_id] : [];
     }


     /**
      * List assignments with filters.
      */
     public function list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
     {
          $query = $this->query(); // Automatically scoped

          if (!empty($filters['class_id'])) {
               $query->where('class_id', $filters['class_id']);
          }

          if (!empty($filters['subject_id'])) {
               $query->where('subject_id', $filters['subject_id']);
          }

          if (!empty($filters['teacher_id'])) {
               $query->where('teacher_id', $filters['teacher_id']);
          }

          if (!empty($filters['due_date'])) {
               $query->whereDate('due_date', $filters['due_date']);
          }

          return $query
               ->with(['class', 'subject', 'teacher.user'])
               ->latest()
               ->paginate(pageCount());
     }
}
