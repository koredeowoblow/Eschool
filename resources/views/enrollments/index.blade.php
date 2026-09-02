@extends('layouts.app')

@section('title', 'Enrollments')
@section('header_title', 'Enrollments')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="enrollmentSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all "
                placeholder="Search enrollments..." oninput="reloadEnrollments()">
        </div>

        @hasrole('super_admin|School Admin|Teacher')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium"
                onclick="App.resetForm(document.forms['createEnrollmentForm']);" data-bs-toggle="modal"
                data-bs-target="#createEnrollmentModal">
                <i class="bi bi-plus-lg me-1"></i> New Enrollment
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Session</th>
                            <th>Term</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="enrollmentsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createEnrollmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createEnrollmentForm" action="/api/v1/enrollments" method="POST"
                    onsubmit="App.submitForm(event, reloadEnrollments, 'enrollment', 'createEnrollmentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Create Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Student</label>
                                <select name="student_id" id="create_enrollment_student_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Student</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Class Placement</label>
                                <select name="class_id" id="create_enrollment_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Session</label>
                                <select name="session_id" id="create_enrollment_session_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Term</label>
                                <select name="term_id" id="create_enrollment_term_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Enrollment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editEnrollmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editEnrollmentForm" method="POST"
                    onsubmit="App.submitForm(event, reloadEnrollments, 'enrollment', 'editEnrollmentModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Student</label>
                                <select name="student_id" id="edit_enrollment_student_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Student</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Class Placement</label>
                                <select name="class_id" id="edit_enrollment_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Session</label>
                                <select name="session_id" id="edit_enrollment_session_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Term</label>
                                <select name="term_id" id="edit_enrollment_term_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Enrollment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.renderTable('/api/v1/enrollments', 'enrollmentsTableBody', 'enrollment');

            const createModal = document.getElementById('createEnrollmentModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/students', 'create_enrollment_student_id');
                App.loadOptions('/api/v1/classes', 'create_enrollment_class_id');
                App.loadOptions('/api/v1/school-sessions', 'create_enrollment_session_id');
                App.loadOptions('/api/v1/terms', 'create_enrollment_term_id');
            });

            const editModal = document.getElementById('editEnrollmentModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/students', 'edit_enrollment_student_id');
                App.loadOptions('/api/v1/classes', 'edit_enrollment_class_id');
                App.loadOptions('/api/v1/school-sessions', 'edit_enrollment_session_id');
                App.loadOptions('/api/v1/terms', 'edit_enrollment_term_id');
            });
        });

        function reloadEnrollments() {
            const query = document.getElementById('enrollmentSearch').value;
            App.renderTable('/api/v1/enrollments?search=' + encodeURIComponent(query), 'enrollmentsTableBody',
                'enrollment');
        }

        function editEnrollment(data) {
            const form = document.getElementById('editEnrollmentForm');
            form.action = `/api/v1/enrollments/${data.id}`;

            // We need to wait for options to load if we want to ensure selected value is set correctly 
            // by App.populateForm. However, App.populateForm usually sets values directly.
            // If the options aren't there yet, the select might not show the correct one.
            // Let's rely on App.populateForm for now, and see if we need a promise-based approach.
            App.populateForm(form, data);

            const modal = new bootstrap.Modal(document.getElementById('editEnrollmentModal'));
            modal.show();
        }

        function deleteEnrollment(id) {
            App.deleteItem(`/api/v1/enrollments/${id}`, reloadEnrollments);
        }
    </script>
@endsection
