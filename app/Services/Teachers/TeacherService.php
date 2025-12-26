<?php

namespace App\Services\Teachers;

use App\Repositories\Teachers\TeacherRepository;
use App\Models\TeacherProfile;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\DB;
use App\Repositories\Users\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TeacherService
{
    public function __construct(
        public TeacherRepository $repo,
        public UserRepository $userRepo
    ) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): TeacherProfile
    {
        $teacher = $this->repo->findById($id, ['user']);
        if (!$teacher) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Teacher profile not found");
        }
        return $teacher;
    }

    public function create(array $data): TeacherProfile
    {
        return DB::transaction(function () use ($data) {
            $schoolId = $data['school_id'] ?? Auth::user()->school_id;

            // Check Plan Limit
            $school = \App\Models\School::find($schoolId);
            if ($school) {
                $limit = $school->getLimit('teachers');
                if ($limit > 0) {
                    $currentCount = \App\Models\TeacherProfile::where('school_id', $schoolId)->count();
                    if ($currentCount >= $limit) {
                        throw new \Exception("Teacher limit reached for this school plan ({$limit}). Upgrade your plan to add more teachers.");
                    }
                }
            }

            // 1. Create User
            $user = $this->userRepo->create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password'] ?? 'password'),
                'school_id' => $schoolId,
                'status'    => 1, // Active by default
            ]);
            $user->assignRole('Teacher');

            // 2. Create Teacher Profile
            $data['user_id'] = $user->id;
            $data['school_id'] = $schoolId;
            $teacher = $this->repo->create($data);

            Log::info("Teacher created successfully: {$user->email} (ID: {$teacher->id})");

            // Audit log
            AuditLogger::logCreate('teacher', $teacher, [
                'user_email' => $user->email,
                'employee_number' => $teacher->employee_number ?? null,
            ]);

            return $teacher;
        });
    }

    public function update(int|string $id, array $data): TeacherProfile
    {
        return DB::transaction(function () use ($id, $data) {
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

            // Audit log
            AuditLogger::logUpdate('teacher', $model, [
                'updated_fields' => array_keys($data),
            ]);

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
