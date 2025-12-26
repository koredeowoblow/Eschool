<?php

use App\Models\School;
use App\Models\Session;
use App\Models\Term;
use App\Models\Section;
use App\Models\ClassRoom;
use App\Models\User;
use App\Models\Student;
use App\Models\TeacherProfile;
use App\Services\Fees\FeeService;
use App\Services\Fees\FeeAssignmentService;
use App\Services\Fees\FeePaymentService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// 1. Setup Data
DB::beginTransaction();

try {
    echo "--- Fee System Final Verification ---\n";

    // Create School
    $school = School::create([
        'name' => 'Test School ' . Str::random(4),
        'slug' => 'test-school-' . Str::random(4),
        'email' => 'test' . Str::random(4) . '@school.com',
        'is_active' => true,
    ]);
    echo "1. School Created: {$school->name}\n";

    // Create Session
    $session = Session::create([
        'school_id' => $school->id,
        'name' => '2025/2026',
        'start_date' => now()->startOfYear(),
        'end_date' => now()->endOfYear(),
        'status' => 'active',
    ]);
    echo "2. Session Created: {$session->name}\n";

    // Create Term
    $term = Term::create([
        'school_id' => $school->id,
        'session_id' => $session->id,
        'name' => '1st Term',
        'start_date' => now()->startOfYear(),
        'end_date' => now()->addMonths(4)->endOfMonth(),
        'status' => 'active',
    ]);
    echo "3. Term Created: {$term->name}\n";

    // Create Section
    $section = Section::create([
        'school_id' => $school->id,
        'name' => 'Primary',
    ]);
    echo "4. Section Created: {$section->name}\n";

    // Create Teacher User & Profile
    $teacherUser = User::create([
        'id' => Str::uuid(),
        'school_id' => $school->id,
        'name' => 'Jane Teacher',
        'email' => 'jane' . Str::random(4) . '@test.com',
        'password' => Hash::make('password'),
        'gender' => 'female',
    ]);
    $teacherProfile = TeacherProfile::create([
        'school_id' => $school->id,
        'user_id' => $teacherUser->id,
        'employee_number' => 'TCH' . Str::random(4),
    ]);
    echo "5. Teacher Profile Created: {$teacherUser->name}\n";

    // Create Class
    $class = ClassRoom::create([
        'school_id' => $school->id,
        'name' => 'Grade 1A',
        'section_id' => $section->id,
        'session_id' => $session->id,
        'term_id' => $term->id,
        'class_teacher_id' => $teacherProfile->id,
    ]);
    echo "6. Class Created: {$class->name}\n";

    // Create Student User & Student
    $studentUser = User::create([
        'id' => Str::uuid(),
        'school_id' => $school->id,
        'name' => 'John Student',
        'email' => 'john' . Str::random(4) . '@test.com',
        'password' => Hash::make('password'),
        'gender' => 'male',
    ]);
    $student = Student::create([
        'school_id' => $school->id,
        'user_id' => $studentUser->id,
        'class_id' => $class->id,
        'admission_number' => 'ADM' . Str::random(4),
        'admission_date' => now(),
        'school_session_id' => $session->id,
        'status' => true,
    ]);
    echo "7. Student Created: {$studentUser->name} in {$class->name}\n";

    // 2. Test FeeService
    $feeService = new FeeService();
    $fee = $feeService->create([
        'school_id' => $school->id,
        'class_id' => $class->id,
        'term_id' => $term->id,
        'session_id' => $session->id,
        'title' => 'Tuition Fee',
        'amount' => 1000.00,
        'fee_type' => 'tuition',
        'due_date' => now()->addDays(30),
        'is_mandatory' => true,
        'created_by' => $teacherUser->id,
    ]);
    echo "8. Fee Created: {$fee->title} (Amount: {$fee->amount})\n";

    // 3. Test FeeAssignmentService
    $assignmentService = new FeeAssignmentService();
    $assignmentService->assignFeeToClass($fee->id, $class->id);

    $studentFee = \App\Models\StudentFee::where('student_id', $student->id)->where('fee_id', $fee->id)->first();
    if ($studentFee && (float)$studentFee->balance >= 999.99) {
        echo "9. Fee Successfully Assigned to Student. Balance: {$studentFee->balance}\n";
    } else {
        throw new \Exception("Failed to assign fee to student or incorrect balance: " . ($studentFee ? $studentFee->balance : 'null'));
    }

    // 4. Test FeePaymentService (Partial Payment)
    $paymentService = new FeePaymentService();
    $payment1 = $paymentService->processPayment([
        'student_id' => $student->id,
        'fee_id' => $fee->id,
        'amount_paid' => 400.00,
        'payment_method' => 'cash',
        'processed_by' => $teacherUser->id,
    ]);

    $studentFee->refresh();
    echo "10. Partial Payment Processed: 400.00. New Balance: {$studentFee->balance}, Status: {$studentFee->status}\n";
    if (abs((float)$studentFee->balance - 600.00) > 0.01 || $studentFee->status !== 'partial') {
        throw new \Exception("Incorrect balance or status after partial payment. Balance: {$studentFee->balance}, Status: {$studentFee->status}");
    }

    // 5. Test FeePaymentService (Full Payment)
    $payment2 = $paymentService->processPayment([
        'student_id' => $student->id,
        'fee_id' => $fee->id,
        'amount_paid' => 600.00,
        'payment_method' => 'bank_transfer',
        'processed_by' => $teacherUser->id,
    ]);

    $studentFee->refresh();
    echo "11. Full Payment Processed: 600.00. New Balance: {$studentFee->balance}, Status: {$studentFee->status}\n";
    if (abs((float)$studentFee->balance) > 0.01 || $studentFee->status !== 'paid') {
        throw new \Exception("Incorrect balance or status after full payment. Balance: {$studentFee->balance}, Status: {$studentFee->status}");
    }

    echo "\n✅ ALL TESTS PASSED!\n";
    DB::rollBack();
} catch (\Exception $e) {
    DB::rollBack();
    $errorMsg = "\n❌ TEST FAILED: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    file_put_contents('verify_error.log', $errorMsg);
    echo $errorMsg;
}
