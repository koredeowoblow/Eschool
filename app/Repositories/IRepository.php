<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface IRepository
 *
 * Standard contract for all Repositories.
 */
interface IRepository
{
    /**
     * Get all records.
     *
     * @param array $relations
     * @return Collection
     */
    public function all(array $relations = []): Collection;

    /**
     * Find a record by ID.
     *
     * @return Model
     */
    public function findById(int|string $id, array $relations = []): Model;

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update an existing record.
     *
     * @return Model Returns the updated model.
     */
    public function update(int|string $id, array $data): Model;

    /**
     * Delete a record.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool;
}
