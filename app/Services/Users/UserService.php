<?php

namespace App\Services\Users;

use App\Repositories\Students\StudentRepository;
use App\Repositories\Teachers\TeacherRepository;
use App\Repositories\Users\GuardianRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Students\EnrollmentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\TeacherWelcome;
use App\Mail\GuardianWelcome;
use App\Models\Student;
use Exception;
use Illuminate\Support\Facades\Log;


class UserService
{
    public function __construct(
        protected UserRepository $userRepo,
        protected StudentRepository $studentRepo,
        protected TeacherRepository $teacherRepo,
        protected GuardianRepository $guardianRepo,
        protected EnrollmentRepository $enrollmentRepo
    ) {}

    /**
     * Create a user (student, teacher, admin) with optional guardian and enrollment.
     */
    public function create(array $data): array|User
    {
        return DB::transaction(function () use ($data) {
            $mainPassword = $data['password'] ?? $this->generatePassword();
            $data['password'] = bcrypt($mainPassword);

            // Security: Force session-based school_id for non-super_admins
            if (!Auth::user()->hasRole('super_admin')) {
                $data['school_id'] = Auth::user()->school_id;
            } else {
                $data['school_id'] = $data['school_id'] ?? Auth::user()->school_id;
            }

            $guardianData = $data['guardian'] ?? null;

            // 1. Create main user
            $user = $this->userRepo->create($data);

            // Security: Prevent lower-level admins from assigning super_admin role
            $requestedRole = $data['role'];
            if ($requestedRole === 'super_admin' && !Auth::user()->hasRole('super_admin')) {
                $requestedRole = 'school_admin'; // Downgrade if unauthorized
                Log::warning("Unauthorized attempt to create super_admin by user " . Auth::id());
            }
            $user->assignRole($requestedRole);

            // 2. Handle student-specific logic
            if ($data['role'] === 'student') {
                if (!$guardianData || !is_array($guardianData)) {
                    throw new Exception("Guardian information is required for student.");
                }

                $student = $this->studentRepo->create([
                    'school_id'         => $data['school_id'],
                    'user_id'           => $user->id,
                    'admission_number'  => $data['admission_number'],
                    'admission_date'    => $data['admission_date'],
                    'class_id'          => $data['class_id'],
                    'school_session_id' => $data['school_session_id'],
                ]);

                $this->enrollmentRepo->create([
                    'school_id'       => $data['school_id'],
                    'student_id'      => $student->id,
                    'class_id'        => $data['class_id'],
                    'session_id'      => $data['school_session_id'] ?? $data['session_id'],
                    'term_id'         => $data['term_id'],
                    'enrollment_date' => $data['admission_date'],
                    'status'          => 'active',
                ]);

                $guardianUser = $this->createGuardian(
                    $guardianData,
                    [$student->id],
                    $user->name,
                    $mainPassword,
                    $user->email,
                    $data['guardian_id'] ?? null
                );

                return [
                    'student_user'  => $user,
                    'guardian_user' => $guardianUser['guardian_user'],
                ];
            }

            // 3. Handle teacher-specific logic
            if ($data['role'] === 'teacher') {
                $this->teacherRepo->create([
                    'school_id'       => $user->school_id,
                    'user_id'         => $user->id,
                    'employee_number' => $data['employee_number'],
                    'hire_date'       => $data['hire_date'],
                    'qualification'   => $data['qualification'] ?? null,
                    'department'      => $data['department'] ?? null,
                    'bio'             => $data['bio'] ?? null,
                ]);

                // Send welcome email to teacher
                try {
                    Mail::to($user->email)->send(new TeacherWelcome(
                        $user->name,
                        $user->email,
                        $mainPassword,
                        $user->school->name ?? 'School'
                    ));
                } catch (\Exception $e) {
                    Log::error("Failed to send welcome email to teacher {$user->email}: " . $e->getMessage());
                }
            }

            return $user;
        });
    }

    /**
     * Update user profile (self-update)
     */
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            // Security: Limit what can be self-updated
            $safeFields = ['name', 'phone', 'address', 'city', 'state', 'zip', 'country'];
            // If email is allowed to be updated, it should trigger re-verification. 
            // For now, removing it from self-update to prevent unverified identity shifts.

            // Update main user
            $user->update(array_intersect_key($data, array_flip($safeFields)));

            // Update related profiles (Teacher self-update)
            if ($user->hasRole('Teacher') && $user->teacher()->exists()) {
                $user->teacher()->update(array_intersect_key($data, array_flip([
                    'qualification',
                    'department',
                    'bio'
                ])));
            }

            // Note: Students and Guardians should NOT be able to self-update 
            // class_id, status, or admission_number via this flow.


            return $user->fresh();
        });
    }

    /**
     * Update a user (general update by admin)
     */
    public function update($id, array $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->userRepo->findById($id);
            if (!$user) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("User not found");
            }

            // Security: Prevent lower-level admins from changing school_id
            if (!Auth::user()->hasRole('super_admin')) {
                unset($data['school_id']);
            }

            // Security: Role change should be explicit. 
            // If 'role' is passed, we handle it via assignRole
            if (isset($data['role'])) {
                if ($data['role'] === 'super_admin' && !Auth::user()->hasRole('super_admin')) {
                    unset($data['role']);
                } else {
                    $user->syncRoles([$data['role']]);
                }
            }

            $model = $this->userRepo->update($id, $data);
            return $model;
        });
    }

    /**
     * Create a guardian user and attach to students
     */
    public function createGuardian(array $guardianData, array $studentIds, ?string $studentName = null, ?string $studentPassword = null, ?string $studentEmail = null, ?int $guardianId = null): array
    {
        return DB::transaction(function () use ($guardianData, $studentIds, $studentName, $studentPassword, $studentEmail, $guardianId) {
            $isNewGuardian = true;
            $guardianUser = null;
            $guardianProfile = null;

            // 1. Try to find by explicit ID if provided
            if ($guardianId) {
                $guardianProfile = $this->guardianRepo->findById($guardianId);
                if ($guardianProfile) {
                    $guardianUser = $guardianProfile->user;
                    $isNewGuardian = false;
                }
            }

            // 2. Fallback to finding by email if no ID or ID lookup failed
            if (!$guardianUser) {
                $existingUser = $this->userRepo->findByEmail($guardianData['email']);
                if ($existingUser && $existingUser->hasRole('Guardian')) {
                    $guardianUser = $existingUser;
                    $guardianProfile = $existingUser->guardian;
                    $isNewGuardian = false;
                }
            }

            $guardianPassword = $guardianData['password'] ?? $this->generatePassword();

            if ($guardianUser && $guardianProfile) {
                // Update profile details if provided
                $guardianProfile->update([
                    'relation' => $guardianData['relation'] ?? $guardianProfile->relation,
                    'occupation' => $guardianData['occupation'] ?? $guardianProfile->occupation,
                ]);
            } else {
                $guardianData['password'] = bcrypt($guardianPassword);
                $guardianData['role'] = 'guardian';

                $guardianUser = $this->userRepo->create($guardianData);
                $guardianUser->assignRole('Guardian');

                $guardianProfile = $this->guardianRepo->create([
                    'school_id'  => $guardianUser->school_id,
                    'user_id'    => $guardianUser->id,
                    'relation'   => $guardianData['relation'] ?? 'guardian',
                    'occupation' => $guardianData['occupation'] ?? 'N/A',
                ]);
            }

            if (!empty($studentIds)) {
                $syncData = [];
                foreach ($studentIds as $id) {
                    $syncData[$id] = ['school_id' => $guardianUser->school_id];
                }
                // syncWithoutDetaching is critical to avoid unlinking siblings
                $guardianProfile->students()->syncWithoutDetaching($syncData);
            }

            // Only send welcome email if it's a new account to avoid credential spam
            if ($isNewGuardian && $studentName && $studentPassword && $studentEmail) {
                try {
                    Mail::to($guardianUser->email)->send(new GuardianWelcome(
                        $guardianUser->name,
                        $guardianUser->email,
                        $guardianPassword,
                        $studentName,
                        $studentPassword,
                        $studentEmail,
                        $guardianUser->school->name ?? 'School'
                    ));
                } catch (\Exception $e) {
                    Log::error("Failed to send welcome email to guardian {$guardianUser->email}: " . $e->getMessage());
                }
            }

            return [
                'guardian'      => $guardianProfile->load(['user', 'students']),
                'guardian_user' => $guardianUser,
            ];
        });
    }

    /**
     * Helper to update or create guardian.
     */
    protected function updateOrCreateGuardian(array $guardianData, $student, $user)
    {
        $guardian = $this->userRepo->single($guardianData['email'] ?? '');

        if ($guardian && $guardian->hasRole('Guardian')) {
            $guardianFields = array_intersect_key($guardianData, array_flip(['name', 'email', 'phone']));
            if (!empty($guardianData['password'])) {
                $guardianFields['password'] = bcrypt($guardianData['password']);
            }
            if (!empty($guardianFields)) {
                $guardian->update($guardianFields);
            }

            $guardianProfileFields = array_intersect_key($guardianData, array_flip(['relation', 'occupation']));
            if (!empty($guardianProfileFields)) {
                $guardian->guardian()->update($guardianProfileFields);
            }
        } else {
            $this->createGuardian($guardianData, [$student->id], $user->name, $user->password ?? null, $user->email);
        }
    }




    /**
     * Generate random password
     */
    private function generatePassword(): string
    {
        return Str::random(16);
    }
}
