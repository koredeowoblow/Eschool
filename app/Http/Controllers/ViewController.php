<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

/**
 * ViewController - Handles VIEW RENDERING ONLY
 * All data operations are handled by API controllers
 * All CRUD operations happen via modals on index pages
 */
class ViewController extends Controller
{
    // Students
    public function studentsIndex()
    {
        return view('students.index');
    }

    public function promotionsIndex()
    {
        return view('promotions.index');
    }

    // Teachers
    public function teachersIndex()
    {
        return view('teachers.index');
    }

    // Guardians
    public function guardiansIndex()
    {
        return view('guardians.index');
    }

    // Classes
    public function classesIndex()
    {
        return view('classes.index');
    }

    public function subjectsIndex()
    {
        return view('subjects.index');
    }

    public function subjectAssignmentsIndex()
    {
        return view('subjects.assignments');
    }

    public function sessionsIndex()
    {
        return view('sessions.index');
    }

    public function termsIndex()
    {
        return view('terms.index');
    }

    public function sectionsIndex()
    {
        return view('sections.index');
    }

    public function enrollmentsIndex()
    {
        return view('enrollments.index');
    }

    public function attachmentsIndex()
    {
        return view('attachments.index');
    }

    // Assignments
    public function assignmentsIndex()
    {
        return view('assignments.index');
    }

    // Assignment Submissions
    public function assignmentSubmissionsIndex()
    {
        return view('assignment-submissions.index');
    }

    // Attendance
    public function attendanceIndex()
    {
        return view('attendance.index');
    }

    // Library
    public function libraryIndex()
    {
        return view('library.index');
    }

    // Payments
    public function paymentsIndex()
    {
        return view('payments.index');
    }

    // Invoices
    public function invoicesIndex()
    {
        return view('invoices.index');
    }

    // Fee Types
    public function feeTypesIndex()
    {
        return view('fee-types.index');
    }

    // New Fee Module
    public function feesIndex()
    {
        return view('fees.index');
    }

    public function feesAssignIndex()
    {
        return view('fees.assign');
    }

    public function studentFeesOverview($id)
    {
        return view('fees.student-overview', ['student_id' => $id]);
    }

    public function feePaymentsHistory()
    {
        return view('fees.payments');
    }

    public function myFeesIndex()
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $student = $user->student;
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student record not found.');
        }
        return view('fees.student-overview', ['student_id' => $student->id]);
    }

    // Chats
    public function chatsIndex()
    {
        return view('chats.index');
    }

    // Timetables
    public function timetablesIndex()
    {
        return view('timetables.index');
    }

    // Reports
    public function reportsIndex()
    {
        return view('reports.index');
    }

    public function academicReportsIndex()
    {
        return view('reports.academic');
    }

    // Assessments
    public function assessmentsIndex()
    {
        return view('assessments.index');
    }

    // Results
    public function resultsIndex()
    {
        return view('results.index');
    }

    // Lesson Notes
    public function lessonNotesIndex()
    {
        return view('lesson-notes.index');
    }

    // Settings
    public function settingsIndex()
    {
        return view('settings.index');
    }

    // Staff
    public function staffIndex()
    {
        return view('staff.index');
    }
}
