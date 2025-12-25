<?php

namespace App\Services\Students;

use App\Repositories\Students\StudentRepository;
use App\Repositories\Users\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StudentService
{
    public function __construct(
        protected StudentRepository $studentRepository,
        protected UserRepository $userRepo
    ) {}

    /**
     * Get all students for the current school.
     */
    public function getAllStudents()
    {
        return $this->studentRepository->list([]);
    }

    /**
     * Get a single student by ID.
     */
    public function getStudentById($id)
    {
        return $this->studentRepository->findById($id, ['user', 'classRoom', 'section', 'guardians.user', 'enrollments']);
    }

    /**
     * Create a new student with associated User account and Enrollment.
     */
    public function createStudent(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Create User
            $userData = [
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'password'),
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'school_id' => $data['school_id'] ?? (Auth::user()->school_id ?? null),
            ];

            $user = $this->userRepo->create($userData);
            $user->assignRole('student');

            // 2. Create Student Profile
            $studentData = array_merge($data, ['user_id' => $user->id]);
            $student = $this->studentRepository->create($studentData);

            // 3. Create Enrollment (Consider using EnrollmentRepository)
            if (isset($data['class_id'])) {
                \App\Models\Enrollment::create([
                    'student_id' => $student->id,
                    'class_id' => $data['class_id'],
                    'session_id' => $data['session_id'] ?? $data['school_session_id'] ?? null,
                    'term_id' => $data['term_id'] ?? null,
                    'school_id' => $user->school_id,
                    'status' => 'active',
                ]);
            }

            Log::info("Student created successfully: {$student->admission_number}");

            return $student;
        });
    }

    /**
     * Update an existing student.
     */
    public function updateStudent($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $student = $this->studentRepository->findById($id);
            if (!$student) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Student record not found");
            }

            // Update User model
            if ($student->user) {
                $userUpdate = [];
                if (isset($data['first_name']) && isset($data['last_name'])) {
                    $userUpdate['name'] = $data['first_name'] . ' ' . $data['last_name'];
                }
                if (isset($data['email'])) $userUpdate['email'] = $data['email'];
                if (isset($data['gender'])) $userUpdate['gender'] = $data['gender'];
                if (isset($data['date_of_birth'])) $userUpdate['date_of_birth'] = $data['date_of_birth'];
                if (!empty($data['password'])) {
                    $userUpdate['password'] = Hash::make($data['password']);
                }

                if (!empty($userUpdate)) {
                    $this->userRepo->update($student->user->id, $userUpdate);
                }
            }

            // Update Student model
            $this->studentRepository->update($id, $data);

            return $student->fresh(['user', 'classRoom']);
        });
    }


    /**
     * Delete a student.
     */
    public function deleteStudent($id)
    {
        return $this->studentRepository->delete($id);
    }
}
