<?php

namespace App\Repositories\Students;

use App\Models\Student;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class StudentRepository extends BaseRepository
{
    /**
     * StudentRepository constructor.
     *
     * @param Student $model
     */
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }

    /**
     * List all students with filters.
     */
    public function list(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->query();

        if (!empty($filters['admission_number'])) {
            $query->where('admission_number', 'like', '%' . $filters['admission_number'] . '%');
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        return $query->with(['user', 'classRoom', 'section'])
            ->latest()
            ->get();
    }

    public function findByAdmissionNumber(string $admissionNumber): ?Student
    {
        return $this->query()
            ->with('user')
            ->where('admission_number', $admissionNumber)
            ->first();
    }

    /**
     * Get all records (scoped).
     *
     * @param array $relations
     * @return Collection
     */
    public function all(array $relations = []): Collection
    {
        $rels = !empty($relations) ? $relations : ['user', 'classRoom', 'section'];
        return $this->query()
            ->with($rels)
            ->latest()
            ->get();
    }

    /**
     * Find a record by ID (scoped).
     *
     * @param int|string $id
     * @param array $relations
     * @return Model|null
     */
    public function findById(int|string $id, array $relations = []): ?Model
    {
        $rels = !empty($relations) ? $relations : ['user', 'classRoom', 'section', 'guardians.user', 'enrollments'];
        return $this->query()
            ->with($rels)
            ->find($id);
    }
}
