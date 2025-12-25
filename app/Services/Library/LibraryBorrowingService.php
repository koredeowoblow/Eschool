<?php

namespace App\Services\Library;

use App\Repositories\Library\LibraryBorrowingRepository;
use App\Models\LibraryBorrowing;
use App\Models\LibraryBook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LibraryBorrowingService
{
    public function __construct(public LibraryBorrowingRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): LibraryBorrowing
    {
        $model = $this->repo->findById($id);
        if (!$model) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Borrowing record not found");
        }
        return $model;
    }

    public function create(array $data): LibraryBorrowing
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $isStudent = $user->hasRole('student');
            $data['status'] = $isStudent ? 'pending' : ($data['status'] ?? 'borrowed');
            $data['borrowed_at'] = $data['borrowed_at'] ?? now();

            if ($isStudent) {
                $data['user_id'] = $user->id;
            }

            $book = LibraryBook::lockForUpdate()->findOrFail($data['book_id']);

            // Security: Calculate real availability including pending requests
            $pendingCount = LibraryBorrowing::where('book_id', $book->id)
                ->where('status', 'pending')
                ->count();

            $availableReal = $book->copies - $pendingCount;

            if ($availableReal <= 0) {
                throw new \RuntimeException('No copies currently available (including pending requests)');
            }

            if (!$isStudent && $data['status'] === 'borrowed') {
                $book->copies = $book->copies - 1;
                $book->save();
            }

            return $this->repo->create($data);
        });
    }

    public function update(int|string $id, array $data): LibraryBorrowing
    {
        return DB::transaction(function () use ($id, $data) {
            $model = $this->get($id);
            $originalStatus = $model->status;

            $model = $this->repo->update($id, $data);
            if (!$model) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Borrowing record not found");
            }

            if ($originalStatus !== 'returned' && $model->status === 'returned') {
                // returning a book, increment copies
                $book = LibraryBook::lockForUpdate()->findOrFail($model->book_id);
                $book->copies = $book->copies + 1;
                $book->save();
            }

            return $model;
        });
    }

    public function delete(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $model = $this->get($id);
            // if deleting a borrowed record (not yet returned), increment copies
            if ($model->status === 'borrowed') {
                $book = LibraryBook::lockForUpdate()->findOrFail($model->book_id);
                $book->copies = $book->copies + 1;
                $book->save();
            }
            return $this->repo->delete($id);
        });
    }
}
