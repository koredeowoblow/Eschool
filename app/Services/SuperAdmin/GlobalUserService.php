<?php

namespace App\Services\SuperAdmin;

use App\Repositories\Users\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GlobalUserService
{
    public function __construct(protected UserRepository $userRepo) {}

    /**
     * Get all users globally.
     */
    public function getAllUsers()
    {
        return $this->userRepo->query()->with(['roles', 'school'])->latest()->paginate(20);
    }

    /**
     * Create a new user (global context)
     */
    public function createUser(array $data): \App\Models\User
    {
        if (!empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }
        return $this->userRepo->create($data);
    }

    /**
     * Update an existing user (global context)
     */
    public function updateUser($id, array $data): \App\Models\User
    {
        $user = $this->userRepo->query()->findOrFail($id);

        if (!empty($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }

        $user = $this->userRepo->update($id, $data);

        if (!empty($data['role'])) {
            $user->syncRoles($data['role']);
        }

        return $user;
    }
}
