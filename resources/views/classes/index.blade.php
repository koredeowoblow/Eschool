@extends('layouts.app')

@section('title', 'Manage Classes')
@section('header_title', 'Classes')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <h5 class="text-muted fw-normal mb-0">Academic Structures</h5>

        @hasrole('super_admin|school_admin')
            <button type="button" class="btn btn-primary-premium requires-session-lock" data-bs-toggle="modal"
                data-bs-target="#createClassModal">
                <i class="bi bi-plus-lg me-1"></i> Create Class
            </button>
        @endhasrole
    </div>

    <!-- We switched to Table for JS Generic Rendering Consistency -->
    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Class Teacher</th>
                            <th>Students</th>
                            <th>Subjects</th>
                            <th>Assignments</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="classesTableBody">
                        <!-- Loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createClassModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/api/v1/classes" method="POST"
                    onsubmit="App.submitForm(event, reloadClasses, 'class', 'createClassModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Primary 1, Grade 1"
                                required>
                            <small class="text-muted">Enter the academic level name.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <select name="section_id" id="sectionSelectClass" class="form-select">
                                <option value="">No Section</option>
                            </select>
                            <small class="text-muted">Optional: Select a section for this class</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Session <span class="text-danger">*</span></label>
                            <select name="session_id" id="sessionSelectClass" class="form-select" required
                                onchange="loadTermsBySessionClass('sessionSelectClass', 'termSelectClass')">
                                <option value="">Select Session</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term <span class="text-danger">*</span></label>
                            <select name="term_id" id="termSelectClass" class="form-select" required>
                                <option value="">Select Term</option>
                            </select>
                            <small class="text-muted">Select session first</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Class Teacher <span class="text-danger">*</span></label>
                            <select name="class_teacher_id" id="teacherSelectClass" class="form-select" required>
                                <option value="">Select Teacher</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Create Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editClassModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editClassForm" method="POST"
                    onsubmit="App.submitForm(event, reloadClasses, 'class', 'editClassModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Primary 1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <select name="section_id" id="editSectionSelectClass" class="form-select">
                                <option value="">No Section</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Session <span class="text-danger">*</span></label>
                            <select name="session_id" id="editSessionSelectClass" class="form-select" required
                                onchange="loadTermsBySessionClass('editSessionSelectClass', 'editTermSelectClass')">
                                <option value="">Select Session</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term <span class="text-danger">*</span></label>
                            <select name="term_id" id="editTermSelectClass" class="form-select" required>
                                <option value="">Select Term</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Class Teacher <span class="text-danger">*</span></label>
                            <select name="class_teacher_id" id="editTeacherSelectClass" class="form-select" required>
                                <option value="">Select Teacher</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadClasses();

            // Initialize Create Modal Dropdowns
            const createModal = document.getElementById('createClassModal');
            createModal.addEventListener('show.bs.modal', async () => {
                App.loadOptions('/api/v1/sections', 'sectionSelectClass');
                App.loadOptions('/api/v1/school-sessions', 'sessionSelectClass');
                App.loadOptions('/api/v1/terms', 'termSelectClass');

                App.loadOptions('/api/v1/teachers', 'teacherSelectClass', null, 'id', (item) => item
                    .user?.name || 'N/A');
            });


            // Initialize Edit Modal Dropdowns
            const editModal = document.getElementById('editClassModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/sections', 'editSectionSelectClass');
                App.loadOptions('/api/v1/school-sessions', 'editSessionSelectClass');
                App.loadOptions('/api/v1/teachers', 'editTeacherSelectClass');
            });
        });

        function loadTermsBySessionClass(sessionSelectId, termSelectId, selectedTermId = null) {
            const sessionId = document.getElementById(sessionSelectId).value;
            const termSelect = document.getElementById(termSelectId);

            // Clear current options
            termSelect.innerHTML = '<option value="">Select Term</option>';

            if (sessionId) {
                // Load terms for the selected session
                App.loadOptions(`/api/v1/terms?session_id=${sessionId}`, termSelectId, selectedTermId);
            }
        }

        function reloadClasses() {
            App.renderTable('/api/v1/classes', 'classesTableBody', 'class');
        }

        function editClass(data) {
            const form = document.getElementById('editClassForm');
            form.action = `/api/v1/classes/${data.id}`;
            App.populateForm(form, data);

            // Load dropdowns first, then load terms if session is selected
            // Load dropdowns first with selected values
            Promise.all([
                App.loadOptions('/api/v1/sections', 'editSectionSelectClass', data.section_id),
                App.loadOptions('/api/v1/school-sessions', 'editSessionSelectClass', data.session_id),
                App.loadOptions('/api/v1/teachers', 'editTeacherSelectClass', data.class_teacher_id, 'id', (item) =>
                    item.user?.name || 'N/A')
            ]).then(() => {
                if (data.session_id) {
                    loadTermsBySessionClass('editSessionSelectClass', 'editTermSelectClass', data.term_id);
                }
            });

            const modal = new bootstrap.Modal(document.getElementById('editClassModal'));
            modal.show();
        }
    </script>
@endsection
