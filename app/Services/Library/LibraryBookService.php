<?php

namespace App\Services\Library;

use App\Repositories\Library\LibraryBookRepository;
use App\Models\LibraryBook;
use App\Models\LibraryBorrowing;
use Illuminate\Support\Facades\Auth;

class LibraryBookService
{
    public function __construct(public LibraryBookRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): LibraryBook
    {
        return $this->repo->findById($id);
    }

    public function create(array $data): LibraryBook
    {
        $data['copies'] = isset($data['copies']) ? max(0, (int) $data['copies']) : 1;
        return $this->repo->create($data);
    }

    public function update(int|string $id, array $data): LibraryBook
    {
        if (isset($data['copies'])) {
            $data['copies'] = max(0, (int) $data['copies']);
        }
        return $this->repo->update($id, $data);
    }

    public function delete(int|string $id): bool
    {
        $model = $this->get($id);
        // prevent delete if there are active borrowings
        $active = LibraryBorrowing::where('book_id', $model->id)
            ->where('status', 'borrowed')
            ->exists();
        if ($active) {
            return false;
        }
        return $this->repo->delete($id);
    }
}
