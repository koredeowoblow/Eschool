<?php

namespace App\Repositories\Assignments;

use App\Models\AssignmentSubmission;
use App\Repositories\BaseRepository;

class AssignmentSubmissionRepository extends BaseRepository
{
     public function __construct(AssignmentSubmission $model)
     {
          parent::__construct($model);
     }

     /**
      * Scoped query: Enforce student ownership for student users.
      */
     public function query()
     {
          $query = parent::query();
          /** @var \App\Models\User $user */
          $user = \Illuminate\Support\Facades\Auth::user();

          if ($user && $user->hasRole('student')) {
               $student = $user->student()->first();
               if ($student) {
                    $query->where('student_id', $student->id);
               } else {
                    $query->where('id', 0); // Safe failure for orphaned users
               }
          } elseif ($user && $user->hasRole('guardian')) {
               // Security: Enforce child-scoping for parents
               $studentIds = $user->guardianStudents()->pluck('id');
               if ($studentIds->isNotEmpty()) {
                    $query->whereIn('student_id', $studentIds);
               } else {
                    $query->where('id', 0); // Safe failure for orphaned guardians
               }
          }

          return $query;
     }

     /**
      * List submissions with filters.
      */
     public function list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
     {
          $query = $this->query(); // Automatically scoped

          if (!empty($filters['student_id'])) {
               $query->where('student_id', $filters['student_id']);
          }

          if (!empty($filters['assignment_id'])) {
               $query->where('assignment_id', $filters['assignment_id']);
          }

          if (!empty($filters['status'])) {
               $query->where('status', $filters['status']);
          }

          return $query->latest()->paginate(pageCount());
     }
}
