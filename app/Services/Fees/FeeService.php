<?php

namespace App\Services\Fees;

use App\Models\Fee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FeeService
{
    /**
     * List fees with optional filters
     */
    public function list(array $filters = [])
    {
        $query = Fee::query();

        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['term_id'])) {
            $query->where('term_id', $filters['term_id']);
        }

        if (!empty($filters['session_id'])) {
            $query->where('session_id', $filters['session_id']);
        }

        return $query->with(['classRoom', 'term', 'session', 'creator'])->paginate(pageCount());
    }

    /**
     * Create a new fee definition
     */
    public function create(array $data): Fee
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['created_by'])) {
                $data['created_by'] = Auth::id();
            }

            if (empty($data['school_id']) && Auth::check()) {
                $data['school_id'] = Auth::user()->school_id;
            }

            return Fee::create($data);
        });
    }

    /**
     * Get a specific fee by ID
     */
    public function get(int|string $id): Fee
    {
        return Fee::with(['classRoom', 'term', 'session', 'creator'])->findOrFail($id);
    }

    /**
     * Update an existing fee definition
     */
    public function update(int|string $id, array $data): Fee
    {
        return DB::transaction(function () use ($id, $data) {
            $fee = $this->get($id);
            $fee->update($data);
            return $fee;
        });
    }

    /**
     * Delete a fee definition (Soft Delete)
     */
    public function delete(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $fee = $this->get($id);
            return $fee->delete();
        });
    }
}
