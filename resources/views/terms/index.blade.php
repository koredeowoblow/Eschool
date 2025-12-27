@extends('layouts.app')

@section('title', 'Manage Terms')
@section('header_title', 'Terms')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="termSearch" class="form-control border-start-0 ps-0" placeholder="Search terms..."
                oninput="reloadTerms()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createTermForm']);"
                data-bs-toggle="modal" data-bs-target="#createTermModal">
                <i class="bi bi-plus-lg me-1"></i> Add Term
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Term Name</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="termsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createTermModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createTermForm" action="/api/v1/terms" method="POST"
                    onsubmit="App.submitForm(event, reloadTerms, 'term', 'createTermModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">School Session <span class="text-danger">*</span></label>
                            <select name="session_id" id="sessionSelect" class="form-select" required>
                                <option value="">Select Session</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="e.g. First Term">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="upcoming">Upcoming</option>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <small class="text-muted">Set the current status of this term</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Term</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editTermModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTermForm" method="POST"
                    onsubmit="App.submitForm(event, reloadTerms, 'term', 'editTermModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">School Session <span class="text-danger">*</span></label>
                            <select name="session_id" id="editSessionSelect" class="form-select" required>
                                <option value="">Select Session</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="upcoming">Upcoming</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Term</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadTerms();

            // Initialize Create Modal Dropdowns
            const createModal = document.getElementById('createTermModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/school-sessions', 'sessionSelect');
            });

            // Initialize Edit Modal Dropdowns
            const editModal = document.getElementById('editTermModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/school-sessions', 'editSessionSelect');
            });
        });

        function reloadTerms() {
            const query = document.getElementById('termSearch').value;
            App.renderTable('/api/v1/terms?search=' + encodeURIComponent(query), 'termsTableBody', 'term');
        }

        function editTerm(data) {
            const form = document.getElementById('editTermForm');
            form.action = `/api/v1/terms/${data.id}`;
            App.populateForm(form, data);
            const modal = new bootstrap.Modal(document.getElementById('editTermModal'));
            modal.show();
        }
    </script>
@endsection
