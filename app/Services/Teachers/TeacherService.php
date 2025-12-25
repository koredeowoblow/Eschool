<?php

namespace App\Services\Teachers;

use App\Repositories\Teachers\TeacherRepository;
use App\Models\TeacherProfile;
use Illuminate\Support\Facades\Auth;

class TeacherService
{
    public function __construct(
        public \App\Repositories\Teachers\TeacherRepository $repo,
        public \App\Repositories\Users\UserRepository $userRepo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): \App\Models\TeacherProfile
    {
        $teacher = $this->repo->findById($id, ['user']);
        if (!$teacher) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Teacher profile not found");
        }
        return $teacher;
    }

    public function create(array $data): \App\Models\TeacherProfile
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            // 1. Create User
            $user = $this->userRepo->create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => \Illuminate\Support\Facades\Hash::make($data['password'] ?? 'password'),
                'school_id' => \Illuminate\Support\Facades\Auth::user()->school_id, // Security: Force session-based school_id
                'status'    => 1, // Active by default
            ]);
            $user->assignRole('teacher');

            // 2. Create Teacher Profile
            $data['user_id'] = $user->id;
            $teacher = $this->repo->create($data);

            \Illuminate\Support\Facades\Log::info("Teacher created successfully: {$user->email} (ID: {$teacher->id})");

            return $teacher;
        });
    }

    public function update(int|string $id, array $data): \App\Models\TeacherProfile
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data) {
            $model = $this->get($id);

            // Sync user details if provided
            if (isset($model->user)) {
                $userData = [];
                if (isset($data['name'])) $userData['name'] = $data['name'];
                if (isset($data['email'])) $userData['email'] = $data['email'];
                if (isset($data['status'])) $userData['status'] = $data['status'];

                if (!empty($userData)) {
                    $this->userRepo->update($model->user->id, $userData);
                }
            }

            $this->repo->update($id, $data);
            return $model->fresh(['user']);
        });
    }

    public function delete(int|string $id): bool
    {
        // First check if it exists (scopes handled byrepo)
        $this->get($id);
        return $this->repo->delete($id);
    }
}
