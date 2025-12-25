@extends('layouts.app')

@section('title', 'Manage Assignments')
@section('header_title', 'Assignments')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="assignmentSearch" class="form-control border-start-0 ps-0"
                placeholder="Search assignments..." oninput="reloadAssignments()">
        </div>

        @hasrole('super_admin|school_admin|teacher')
            <button type="button" class="btn btn-primary-premium requires-session-lock"
                onclick="App.resetForm(document.forms['createAssignmentForm']);" data-bs-toggle="modal"
                data-bs-target="#createAssignmentModal">
                <i class="bi bi-plus-lg me-1"></i> New Assignment
            </button>
        @endhasrole
    </div>

    <!-- Assignments Table -->
    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assignmentsTableBody">
                        <!-- Loaded by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createAssignmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createAssignmentForm" action="/api/v1/assignments" method="POST"
                    onsubmit="App.submitForm(event, reloadAssignments, 'assignment', 'createAssignmentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Assignment Title</label>
                            <input type="text" name="title" class="form-control" required
                                placeholder="Checking understanding of Algebra">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Class</label>
                                <select id="class_id" class="form-control" name="class_id">
                                    <option value="">Select class</option>
                                </select>

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <select id="subject_id" class="form-control" name="subject_id" disabled>
                                    <option value="">Select subject</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="draft">Draft</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Create Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editAssignmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editAssignmentForm" method="POST"
                    onsubmit="App.submitForm(event, reloadAssignments, 'assignment', 'editAssignmentModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Assignment Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Class</label>
                                <select name="class_room_id" class="form-select" required>
                                    {{-- @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <select name="subject_id" class="form-select" required>
                                    {{-- @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="draft">Draft</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Submission Modal -->
    <div class="modal fade" id="submitAssignmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="submissionForm" action="/api/v1/assignment-submissions" method="POST"
                    onsubmit="App.submitForm(event, reloadAssignments, 'assignmentSubmission', 'submitAssignmentModal')"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Submit Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="assignment_id" id="submission_assignment_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold" id="submission_title_label"></label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Answer / Notes</label>
                            <textarea name="answer" class="form-control" rows="4" placeholder="Type your answer here..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attach File (Optional)</label>
                            <input type="file" name="file" class="form-control">
                            <div class="form-text small">Max size: 5MB. Formats: PDF, Docx, Images.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.renderTable('/api/v1/assignments', 'assignmentsTableBody', 'assignment');
        });

        function reloadAssignments() {
            const query = document.getElementById('assignmentSearch').value;
            App.renderTable('/api/v1/assignments?search=' + query, 'assignmentsTableBody', 'assignment');
        }

        function editAssignment(data) {
            const form = document.getElementById('editAssignmentForm');
            form.action = `/api/v1/assignments/${data.id}`;
            App.populateForm(form, data);
            const modal = new bootstrap.Modal(document.getElementById('editAssignmentModal'));
            modal.show();
        }

        // Student Submission
        function openSubmissionModal(assignment) {
            document.getElementById('submission_assignment_id').value = assignment.id;
            document.getElementById('submission_title_label').textContent = `Submitting: ${assignment.title}`;
            const modal = new bootstrap.Modal(document.getElementById('submitAssignmentModal'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('createAssignmentModal');
            if (modal) { // Only if teacher
                const classSelect = document.getElementById('class_id');
                const subjectSelect = document.getElementById('subject_id');

                modal.addEventListener('show.bs.modal', function() {
                    App.loadOptions('/api/v1/classes', 'class_id');
                    // Reset subject
                    subjectSelect.innerHTML = '<option value="">Select Class First</option>';
                    subjectSelect.disabled = true;
                });

                classSelect.addEventListener('change', function() {
                    const classId = this.value;
                    if (!classId) {
                        subjectSelect.innerHTML = '<option value="">Select Class First</option>';
                        subjectSelect.disabled = true;
                        return;
                    }
                    App.loadOptions(`/api/v1/subjects?class_id=${classId}`, 'subject_id', 'id', 'name',
                        'Select Subject');
                });
            }
        });
    </script>
@endsection
