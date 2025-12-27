<?php

namespace App\Repositories\Fees;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository extends BaseRepository
{
    public function __construct(Invoice $model)
    {
        parent::__construct($model);
    }

    public function all(array $relations = []): Collection
    {
        $rels = !empty($relations) ? $relations : ['payments', 'student', 'student.user', 'session', 'term'];
        return $this->query()->with($rels)->latest()->get();
    }

    public function findById(int|string $id, array $relations = []): Model
    {
        $rels = !empty($relations) ? $relations : ['payments', 'student', 'student.user', 'session', 'term', 'items'];
        return $this->query()->with($rels)->findOrFail($id);
    }

    /**
     * Scoped query: Enforce ownership for student/guardian users.
     */
    public function query()
    {
        $query = parent::query();
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->hasRole('Student')) {
            $studentId = $user->student()->value('id');
            if ($studentId) {
                $query->where('student_id', $studentId);
            } else {
                $query->where('id', 0); // Safe failure for orphaned users
            }
        } elseif ($user && $user->hasRole('Guardian')) {
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
}
