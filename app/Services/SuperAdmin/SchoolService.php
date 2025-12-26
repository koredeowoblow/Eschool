<?php

namespace App\Services\SuperAdmin;

use App\Repositories\SchoolRepository;
use App\Repositories\Users\UserRepository;
use App\Models\School;
use App\Models\User;
use App\Mail\SchoolAdminCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class SchoolService
{
    public function __construct(
        protected SchoolRepository $schoolRepo,
        protected UserRepository $userRepo,
        protected \App\Services\Plans\PlanService $planService
    ) {}

    /**
     * Get all schools with basic stats.
     */
    public function getAllSchools()
    {
        return $this->schoolRepo->list();
    }

    /**
     * Find a single school by ID.
     */
    public function findSchool($id)
    {
        $school = $this->schoolRepo->findById($id);
        if (!$school) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("School not found");
        }
        return $school->loadCount(['users', 'students']);
    }

    /**
     * Create a new school with an initial admin user.
     */
    public function createSchool(array $data): School
    {
        return DB::transaction(function () use ($data) {
            // Security: Force status=pending for public/non-superadmin registrations
            $status = $data['status'] ?? 'pending';
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user || !$user->hasRole('super_admin')) {
                $status = 'pending';
            }

            // 1. Create School
            $school = $this->schoolRepo->create([
                'name'      => $data['name'],
                'slug'      => $data['slug'],
                'address'   => $data['address'],
                'email'     => $data['email'],
                'phone'     => $data['phone'],
                'state'     => $data['state'],
                'area'      => $data['area'],
                'city'      => $data['city'],
                'website'   => $data['website'] ?? null,
                // 'plan' field removed/ignored as we use relation
                'status'    => $status,
                'contact_person' => $data['contact_person'] ?? null,
                'contact_person_phone' => $data['contact_person_phone'] ?? null,
                'is_active' => ($status === 'active'),
            ]);

            // 1b. Assign Plan
            if (isset($data['plan'])) {
                $this->planService->assignPlanToSchool($school->id, $data['plan']);
            }

            // 2. Generate Password
            $rawPassword = Str::random(10);

            // 3. Create Admin User for this School
            $adminUser = $this->userRepo->create([
                'name'      => $data['admin_name'],
                'email'     => $data['admin_email'],
                'password'  => Hash::make($rawPassword),
                'school_id' => $school->id,
                'status'    => $status, // Sync user status with school status
            ]);

            // 4. Assign Role
            $adminUser->assignRole('School Admin');

            // 5. Send Email
            try {
                Mail::to($adminUser->email)->send(new SchoolAdminCreated($rawPassword, $school->name));
            } catch (Exception $e) {
                Log::error('Failed to send school admin email: ' . $e->getMessage());
            }

            return $school;
        });
    }

    /**
     * Update an existing school.
     */
    public function updateSchool($id, array $data): School
    {
        return DB::transaction(function () use ($id, $data) {
            // Automatically set is_active based on status field
            if (isset($data['status'])) {
                $data['is_active'] = ($data['status'] === 'active');
            }

            // If is_active is set directly, ensure status is also set
            if (isset($data['is_active']) && !isset($data['status'])) {
                $data['status'] = $data['is_active'] ? 'active' : 'inactive';
            }

            $school = $this->schoolRepo->update($id, $data);
            if (!$school) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("School not found");
            }

            // If school is being activated, also activate all its users
            if (isset($data['is_active']) && $data['is_active']) {
                User::where('school_id', $school->id)
                    ->where('status', '!=', 'active')
                    ->update(['status' => 'active']);
            }

            return $school;
        });
    }

    /**
     * Delete a school.
     */
    public function deleteSchool($id): bool
    {
        return $this->schoolRepo->delete($id);
    }
}
