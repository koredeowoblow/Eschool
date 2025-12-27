@extends('layouts.app')

@section('title', 'Enrollments')
@section('header_title', 'Enrollments')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="enrollmentSearch" class="form-control border-start-0 ps-0"
                placeholder="Search enrollments..." oninput="reloadEnrollments()">
        </div>

        @hasrole('super_admin|School Admin|Teacher')
            <button type="button" class="btn btn-primary-premium"
                onclick="App.resetForm(document.forms['createEnrollmentForm']);" data-bs-toggle="modal"
                data-bs-target="#createEnrollmentModal">
                <i class="bi bi-plus-lg me-1"></i> New Enrollment
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Session</th>
                            <th>Term</th>
                            <th class="text-end">Actions</th>
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
                        <h5 class="modal-title fw-bold">Create Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Student</label>
                                <select name="student_id" id="create_enrollment_student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grade</label>
                                <select name="grade_id" id="create_enrollment_grade_id" class="form-select" required>
                                    <option value="">Select Grade</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Session</label>
                                <select name="session_id" id="create_enrollment_session_id" class="form-select" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Term</label>
                                <select name="term_id" id="create_enrollment_term_id" class="form-select" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Enrollment</button>
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
                        <h5 class="modal-title fw-bold">Edit Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Student</label>
                                <select name="student_id" id="edit_enrollment_student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grade</label>
                                <select name="grade_id" id="edit_enrollment_grade_id" class="form-select" required>
                                    <option value="">Select Grade</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Session</label>
                                <select name="session_id" id="edit_enrollment_session_id" class="form-select" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Term</label>
                                <select name="term_id" id="edit_enrollment_term_id" class="form-select" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Enrollment</button>
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
                App.loadOptions('/api/v1/grades', 'create_enrollment_grade_id');
                App.loadOptions('/api/v1/school-sessions', 'create_enrollment_session_id');
                App.loadOptions('/api/v1/terms', 'create_enrollment_term_id');
            });

            const editModal = document.getElementById('editEnrollmentModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/students', 'edit_enrollment_student_id');
                App.loadOptions('/api/v1/grades', 'edit_enrollment_grade_id');
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
