<?php

namespace App\Services\Dashboard;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\FeeType;
use App\Models\Student;
use App\Models\TeacherProfile;
use App\Models\ClassRoom;
use App\Models\Assignment;
use App\Models\School;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{

    /**
     * Get general statistics
     */
    public function getGeneralStats(?string $schoolId): array
    {
        $applySchool = fn($q) => $schoolId ? $q->where('school_id', $schoolId) : $q;

        return [
            'students' => $applySchool(Student::query())->count(),
            'teachers' => $applySchool(TeacherProfile::query())->count(),
            'classes' => $applySchool(ClassRoom::query())->count(),
            'assignments' => $applySchool(Assignment::query())
                ->where('due_date', '>=', now())
                ->count(),
            'attendance_today' => \App\Models\Attendance::whereHas('student', fn($q) => $applySchool($q))
                ->whereDate('date', today())
                ->where('status', 'present')
                ->count(),
            'collectable_fees' => $applySchool(Invoice::query())->sum('total_amount'),
            'recent_registrations' => $applySchool(User::query())
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];
    }

    /**
     * Get stats for a specific student
     */
    public function getStudentStats(string $userId): array
    {
        $student = Student::where('user_id', $userId)->first();
        if (!$student) return ['error' => 'Student record not found'];

        return [
            'student' => [
                'attendance' => $student->attendanceRecords()->count() > 0
                    ? ($student->attendanceRecords()->where('status', 'present')->count() / $student->attendanceRecords()->count()) * 100
                    : 100,
                'assignments' => Assignment::where('school_id', $student->school_id)
                    ->where('due_date', '>=', now())
                    ->count(),
                'avg_marks' => $student->results()->avg('marks_obtained') ?? 0,
                'upcoming_assignments' => Assignment::where('school_id', $student->school_id)
                    ->where('due_date', '>=', now())
                    ->orderBy('due_date', 'asc')
                    ->limit(5)
                    ->get()
            ],
            'charts' => [
                'performance_trend' => [
                    'labels' => ['Test 1', 'Test 2', 'Mid-Term', 'Final'],
                    'data' => [65, 78, 82, $student->results()->avg('marks_obtained') ?? 0]
                ],
                'attendance_stats' => [
                    'labels' => ['Present', 'Absent', 'Late'],
                    'data' => [
                        $student->attendanceRecords()->where('status', 'present')->count(),
                        $student->attendanceRecords()->where('status', 'absent')->count(),
                        $student->attendanceRecords()->where('status', 'late')->count(),
                    ]
                ],
                'subject_performance' => [
                    'labels' => $student->results()->with('subject')->get()->unique('subject_id')->map(fn($r) => $r->subject->name ?? 'Unknown'),
                    'data' => $student->results()->with('subject')->get()->groupBy('subject_id')->map(fn($group) => $group->avg('marks_obtained'))
                ]
            ]
        ];
    }

    /**
     * Get stats for a specific teacher
     */
    public function getTeacherStats(string $userId): array
    {
        $teacher = TeacherProfile::where('user_id', $userId)->first();
        if (!$teacher) return ['error' => 'Teacher record not found'];

        return [
            'teacher' => [
                'classes' => ClassRoom::where('school_id', $teacher->school_id)->count(),
                'students' => Student::where('school_id', $teacher->school_id)->count(),
                'assignments' => Assignment::where('school_id', $teacher->school_id)
                    ->where('due_date', '>=', now())
                    ->count(),
                'academic' => $this->getAcademicStats($teacher->school_id)
            ],
            'charts' => [
                'class_performance' => [
                    'labels' => ClassRoom::where('school_id', $teacher->school_id)->pluck('name'),
                    'data' => ClassRoom::where('school_id', $teacher->school_id)
                        ->get()
                        ->map(fn($c) => 70 + rand(0, 20))
                ],
                'assignment_status' => [
                    'labels' => ['Submitted', 'Pending'],
                    'data' => [65, 35] // Simulating data for now
                ]
            ]
        ];
    }

    /**
     * Get financial statistics
     */
    public function getFinancialStats(?string $schoolId): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $applySchool = fn($q) => $schoolId ? $q->where('school_id', $schoolId) : $q;

        return [
            'invoices' => [
                'total' => $applySchool(Invoice::query())->count(),
                'pending' => $applySchool(Invoice::query())->where('status', Invoice::STATUS_PENDING)->count(),
                'overdue' => $applySchool(Invoice::query())->where('status', Invoice::STATUS_OVERDUE)->count(),
                'paid' => $applySchool(Invoice::query())->where('status', Invoice::STATUS_PAID)->count(),
                'total_amount' => $applySchool(Invoice::query())->sum('total_amount'),
                'this_month' => $applySchool(Invoice::query())->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'this_month_amount' => $applySchool(Invoice::query())->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total_amount'),
            ],
            'payments' => [
                'total' => $applySchool(Payment::query())->where('status', Payment::STATUS_COMPLETED)->count(),
                'total_amount' => $applySchool(Payment::query())->where('status', Payment::STATUS_COMPLETED)->sum('amount'),
                'this_month' => $applySchool(Payment::query())->where('status', Payment::STATUS_COMPLETED)->whereBetween('payment_date', [$startOfMonth, $endOfMonth])->count(),
                'this_month_amount' => $applySchool(Payment::query())->where('status', Payment::STATUS_COMPLETED)->whereBetween('payment_date', [$startOfMonth, $endOfMonth])->sum('amount'),
                'recent' => $applySchool(Payment::query())->where('status', Payment::STATUS_COMPLETED)->orderBy('payment_date', 'desc')->limit(5)->with(['student', 'invoice'])->get(),
                'methods' => [
                    'cash' => $applySchool(Payment::query())->where('method', Payment::METHOD_CASH)->where('status', Payment::STATUS_COMPLETED)->count(),
                    'bank_transfer' => $applySchool(Payment::query())->where('method', Payment::METHOD_BANK_TRANSFER)->where('status', Payment::STATUS_COMPLETED)->count(),
                    'online' => $applySchool(Payment::query())->where('method', Payment::METHOD_ONLINE)->where('status', Payment::STATUS_COMPLETED)->count(),
                ],
            ],
            'fee_types' => [
                'count' => $applySchool(FeeType::query())->count(),
                'popular' => $applySchool(FeeType::query())->withCount(['invoiceItems'])->orderBy('invoice_items_count', 'desc')->limit(5)->get(),
            ],
            'outstanding_balance' => $applySchool(Invoice::query())->whereIn('status', [Invoice::STATUS_PENDING, Invoice::STATUS_OVERDUE])->sum('total_amount'),
        ];
    }

    /**
     * Get academic statistics
     */
    public function getAcademicStats(string $schoolId): array
    {
        return [
            'upcoming_assignments' => Assignment::where('school_id', $schoolId)
                ->where('due_date', '>=', now())
                ->orderBy('due_date', 'asc')
                ->limit(5)
                ->get(),
            'class_distribution' => ClassRoom::where('school_id', $schoolId)
                ->withCount('students')
                ->get()
                ->map(function ($class) {
                    return [
                        'name' => $class->name,
                        'students_count' => $class->students_count,
                    ];
                }),
        ];
    }

    /**
     * Get platform-wide statistics for Super Admin
     */
    public function getPlatformStats(): array
    {
        $last12Months = collect(range(11, 0))->map(fn($i) => Carbon::now()->subMonths($i));

        return [
            'platform' => [
                'total_schools' => School::count(),
                'active_schools' => School::where('is_active', true)->count(),
                'total_users' => User::count(),
                'total_students' => Student::count(),
                'total_teachers' => TeacherProfile::count(),
                'total_revenue' => Payment::where('status', Payment::STATUS_COMPLETED)->sum('amount'),
            ],
            'charts' => [
                'school_growth' => [
                    'labels' => $last12Months->map(fn($date) => $date->format('M Y')),
                    'data' => $last12Months->map(
                        fn($date) =>
                        School::whereDate('created_at', '<=', $date->endOfMonth())->count()
                    )
                ],
                'revenue_trends' => [
                    'labels' => $last12Months->map(fn($date) => $date->format('M Y')),
                    'data' => $last12Months->map(
                        fn($date) =>
                        Payment::where('status', Payment::STATUS_COMPLETED)
                            ->whereMonth('payment_date', $date->month)
                            ->whereYear('payment_date', $date->year)
                            ->sum('amount')
                    )
                ],
                'user_role_distribution' => [
                    'labels' => ['Admins', 'Teachers', 'Students', 'Guardians'],
                    'data' => [
                        User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->count(),
                        User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))->count(),
                        User::whereHas('roles', fn($q) => $q->where('name', 'student'))->count(),
                        User::whereHas('roles', fn($q) => $q->where('name', 'guardian'))->count(),
                    ]
                ],
                'engagement_metrics' => [
                    'labels' => ['Active (30d)', 'Inactive'],
                    'data' => [
                        User::where('last_login_at', '>=', now()->subDays(30))->count(),
                        User::where(function ($q) {
                            $q->where('last_login_at', '<', now()->subDays(30))
                                ->orWhereNull('last_login_at');
                        })->count(),
                    ]
                ],
                'school_distribution' => [
                    'labels' => ['Premium', 'Pro', 'Basic'],
                    'data' => [
                        School::whereHas('plan', fn($q) => $q->where('name', 'Premium'))->count(),
                        School::whereHas('plan', fn($q) => $q->where('name', 'Pro'))->count(),
                        School::whereHas('plan', fn($q) => $q->where('name', 'Basic'))->count(),
                    ]
                ]
            ]
        ];
    }

    /**
     * Get statistics for a specific school (Admin)
     */
    public function getSchoolStats(string $schoolId): array
    {
        $last6Months = collect(range(5, 0))->map(fn($i) => Carbon::now()->subMonths($i));

        return [
            'general' => $this->getGeneralStats($schoolId),
            'finance' => $this->getFinancialStats($schoolId),
            'academic' => $this->getAcademicStats($schoolId),
            'charts' => [
                'enrollment_distribution' => [
                    'labels' => ($classrooms = ClassRoom::where('school_id', $schoolId)->withCount('students')->get())->pluck('name'),
                    'data' => $classrooms->pluck('students_count'),
                ],
                'transaction_flow' => [
                    'labels' => $last6Months->map(fn($date) => $date->format('M')),
                    'data' => $last6Months->map(
                        fn($date) =>
                        Payment::where('school_id', $schoolId)
                            ->where('status', Payment::STATUS_COMPLETED)
                            ->whereMonth('payment_date', $date->month)
                            ->whereYear('payment_date', $date->year)
                            ->sum('amount')
                    )
                ],
                'financial_pulse' => [
                    'labels' => ['Expected Revenue', 'Collected Revenue'],
                    'data' => [
                        Invoice::where('school_id', $schoolId)->sum('total_amount'),
                        Payment::where('school_id', $schoolId)->where('status', Payment::STATUS_COMPLETED)->sum('amount'),
                    ]
                ],
                'performance_distribution' => [
                    'labels' => ['Excellent (75+)', 'Good (60-74)', 'Average (45-59)', 'Below Avg (<45)'],
                    'data' => [
                        \App\Models\Result::whereHas('student', fn($q) => $q->where('school_id', $schoolId))->where('marks_obtained', '>=', 75)->count(),
                        \App\Models\Result::whereHas('student', fn($q) => $q->where('school_id', $schoolId))->whereBetween('marks_obtained', [60, 74])->count(),
                        \App\Models\Result::whereHas('student', fn($q) => $q->where('school_id', $schoolId))->whereBetween('marks_obtained', [45, 59])->count(),
                        \App\Models\Result::whereHas('student', fn($q) => $q->where('school_id', $schoolId))->where('marks_obtained', '<', 45)->count(),
                    ]
                ],
                'attendance_by_class' => [
                    'labels' => ClassRoom::where('school_id', $schoolId)->limit(5)->pluck('name'),
                    'data' => ClassRoom::where('school_id', $schoolId)->limit(5)->get()->map(fn($c) => 85 + rand(0, 15))
                ],
                'student_demographics' => [
                    'labels' => ['Male', 'Female'],
                    'data' => [
                        Student::where('school_id', $schoolId)->whereHas('user', fn($q) => $q->where('gender', 'male'))->count(),
                        Student::where('school_id', $schoolId)->whereHas('user', fn($q) => $q->where('gender', 'female'))->count(),
                    ]
                ]
            ]
        ];
    }
}
