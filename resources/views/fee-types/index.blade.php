@extends('layouts.app')

@section('title', 'Manage Fee Types')
@section('header_title', 'Fee Types')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="feeTypeSearch" class="form-control border-start-0 ps-0" placeholder="Search fee types..."
                oninput="reloadFeeTypes()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="btn btn-primary-premium requires-session-lock"
                onclick="App.resetForm(document.forms['createFeeTypeForm']);" data-bs-toggle="modal"
                data-bs-target="#createFeeTypeModal">
                <i class="bi bi-plus-lg me-1"></i> Add Fee Type
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="feeTypesTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createFeeTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createFeeTypeForm" action="/api/v1/fee-types" method="POST"
                    onsubmit="App.submitForm(event, reloadFeeTypes, 'fee-type', 'createFeeTypeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Fee Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Fee Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="e.g. Tuition Fee, Library Fee">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Optional description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" required min="0" step="0.01"
                                placeholder="0.00">
                            <small class="text-muted">Enter the default amount for this fee type</small>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Assignment (Optional)</h6>
                        <p class="text-muted small">Assign this fee type to specific grade, session or term. Leave blank for
                            general fees.</p>

                        <div class="mb-3">
                            <label class="form-label">Grade</label>
                            <select name="grade_id" id="gradeSelectFee" class="form-select">
                                <option value="">All Grades</option>
                            </select>
                            <small class="text-muted">Optional: Assign to a specific grade level</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Session</label>
                            <select name="session_id" id="sessionSelectFee" class="form-select"
                                onchange="loadTermsBySession('sessionSelectFee', 'termSelectFee')">
                                <option value="">All Sessions</option>
                            </select>
                            <small class="text-muted">Optional: Assign to a specific academic session</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term</label>
                            <select name="term_id" id="termSelectFee" class="form-select">
                                <option value="">All Terms</option>
                            </select>
                            <small class="text-muted">Optional: Assign to a specific term (select session first)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Fee Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editFeeTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFeeTypeForm" method="POST"
                    onsubmit="App.submitForm(event, reloadFeeTypes, 'fee-type', 'editFeeTypeModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Fee Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Fee Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" required min="0"
                                step="0.01">

                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Assignment (Optional)</h6>

                            <div class="mb-3">
                                <label class="form-label">Grade</label>
                                <select name="grade_id" id="editGradeSelectFee" class="form-select">
                                    <option value="">All Grades</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Session</label>
                                <select name="session_id" id="editSessionSelectFee" class="form-select"
                                    onchange="loadTermsBySession('editSessionSelectFee', 'editTermSelectFee')">
                                    <option value="">All Sessions</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Term</label>
                                <select name="term_id" id="editTermSelectFee" class="form-select">
                                    <option value="">All Terms</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary-premium">Update Fee Type</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadFeeTypes();

            // Initialize Create Modal Dropdowns
            const createModal = document.getElementById('createFeeTypeModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/grades', 'gradeSelectFee');
                App.loadOptions('/api/v1/school-sessions', 'sessionSelectFee');
            });

            // Initialize Edit Modal Dropdowns
            const editModal = document.getElementById('editFeeTypeModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/grades', 'editGradeSelectFee');
                App.loadOptions('/api/v1/school-sessions', 'editSessionSelectFee');
            });
        });

        function loadTermsBySession(sessionSelectId, termSelectId) {
            const sessionId = document.getElementById(sessionSelectId).value;
            const termSelect = document.getElementById(termSelectId);

            // Clear current options
            termSelect.innerHTML = '<option value="">All Terms</option>';

            if (sessionId) {
                // Load terms for the selected session
                App.loadOptions(`/api/v1/terms?session_id=${sessionId}`, termSelectId);
            }
        }

        function reloadFeeTypes() {
            const query = document.getElementById('feeTypeSearch').value;
            const studentId = window.App?.currentStudentId || '';
            App.renderTable(`/api/v1/fee-types?search=${encodeURIComponent(query)}&student_id=${studentId}`,
                'feeTypesTableBody', 'fee-type');
        }

        function editFeeType(data) {
            const form = document.getElementById('editFeeTypeForm');
            form.action = `/api/v1/fee-types/${data.id}`;
            App.populateForm(form, data);

            // Load sessions first, then load terms if session is selected
            App.loadOptions('/api/v1/school-sessions', 'editSessionSelectFee').then(() => {
                if (data.session_id) {
                    loadTermsBySession('editSessionSelectFee', 'editTermSelectFee');
                }
            });

            const modal = new bootstrap.Modal(document.getElementById('editFeeTypeModal'));
            modal.show();
        }
    </script>
@endsection
