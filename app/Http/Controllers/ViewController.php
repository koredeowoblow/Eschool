<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
