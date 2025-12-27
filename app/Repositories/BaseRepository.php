<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User; // Replace with your actual User model if different
use Illuminate\Support\Facades\Auth;

abstract class BaseRepository implements IRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var bool
     */
    protected $isScopedBySchool = true;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get a scoped query for the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = $this->model->newQuery();

        /** @var User|null $user */
        $user = Auth::user();

        if ($this->isScopedBySchool && $user && !$user->hasRole('super_admin')) {
            $query->where('school_id', $user->school_id);
        }

        return $query;
    }

    /**
     * Helper to apply relations to a query.
     */
    protected function withRelations($query, array $relations = [])
    {
        return !empty($relations) ? $query->with($relations) : $query;
    }

    /**
     * Get all records.
     *
     * @param array $relations
     * @return Collection
     */
    public function all(array $relations = []): Collection
    {
        $query = $this->withRelations($this->query(), $relations);
        return $query->latest()->get();
    }

    /**
     * Find a record by ID.
     *
     * @param int|string $id
     * @param array $relations
     * @return Model|null
     */
    public function findById(int|string $id, array $relations = []): Model
    {
        $query = $this->withRelations($this->query(), $relations);
        return $query->findOrFail($id);
    }

    public function single(int|string $id): Model
    {
        return $this->findById($id);
    }

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($this->isScopedBySchool && $user && !isset($data['school_id'])) {
            $data['school_id'] = $user->school_id;
        }

        return $this->model->create($data);
    }

    /**
     * Update an existing record.
     *
     * @param int|string $id
     * @param array $data
     * @return Model|null
     */
    public function update(int|string $id, array $data): Model
    {
        $record = $this->findById($id);
        $record->update($data);
        return $record;
    }

    /**
     * Delete a record.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $record = $this->findById($id);
        return $record->delete();
    }
}
