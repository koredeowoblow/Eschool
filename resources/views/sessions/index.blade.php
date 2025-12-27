@extends('layouts.app')

@section('title', 'Manage Sessions')
@section('header_title', 'Academic Sessions')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="sessionSearch" class="form-control border-start-0 ps-0" placeholder="Search sessions..."
                oninput="reloadSessions()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createSessionForm']);"
                data-bs-toggle="modal" data-bs-target="#createSessionModal">
                <i class="bi bi-plus-lg me-1"></i> Add Session
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Session Name</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sessionsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createSessionForm" action="/api/v1/school-sessions" method="POST"
                    onsubmit="App.submitForm(event, reloadSessions, 'session', 'createSessionModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Session</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Session Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. 2024/2025">
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
                            <small class="text-muted">Set the current status of this academic session</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Session</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSessionForm" method="POST"
                    onsubmit="App.submitForm(event, reloadSessions, 'session', 'editSessionModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Session</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Session Name <span class="text-danger">*</span></label>
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
                        <button type="submit" class="btn btn-primary-premium">Update Session</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadSessions();
        });

        function reloadSessions() {
            const query = document.getElementById('sessionSearch').value;
            App.renderTable('/api/v1/school-sessions?search=' + encodeURIComponent(query), 'sessionsTableBody', 'session');
        }

        function editSession(data) {
            const form = document.getElementById('editSessionForm');
            form.action = `/api/v1/school-sessions/${data.id}`;
            App.populateForm(form, data);
            const modal = new bootstrap.Modal(document.getElementById('editSessionModal'));
            modal.show();
        }
    </script>
@endsection
