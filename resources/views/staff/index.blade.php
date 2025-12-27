@extends('layouts.app')

@section('title', 'Manage Staff')
@section('header_title', 'Staff Management')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="staffSearch" class="form-control border-start-0 ps-0" placeholder="Search staff..."
                oninput="reloadStaff()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createStaffForm']);"
                data-bs-toggle="modal" data-bs-target="#createStaffModal">
                <i class="bi bi-plus-lg me-1"></i> Add Staff
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="staffTableBody">
                        <!-- Loaded by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createStaffModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createStaffForm" action="/api/v1/staff" method="POST"
                    onsubmit="App.submitForm(event, reloadStaff, 'staff', 'createStaffModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Staff Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <option value="Finance Officer">Finance Officer</option>
                                    <option value="Exams Officer">Exams Officer</option>
                                    <option value="School Admin">School Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required
                                    placeholder="staff@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+234...">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-muted small">(Leave blank to
                                    auto-generate)</span></label>
                            <input type="password" name="password" class="form-control" placeholder="******">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editStaffForm" method="POST"
                    onsubmit="App.submitForm(event, reloadStaff, 'staff', 'editStaffModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Staff Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="Finance Officer">Finance Officer</option>
                                    <option value="Exams Officer">Exams Officer</option>
                                    <option value="School Admin">School Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.renderTable('/api/v1/staff', 'staffTableBody', 'staff');
        });

        function reloadStaff() {
            const query = document.getElementById('staffSearch').value;
            App.renderTable('/api/v1/staff?search=' + encodeURIComponent(query), 'staffTableBody', 'staff');
        }

        function editStaff(data) {
            const form = document.getElementById('editStaffForm');
            form.action = `/api/v1/staff/${data.id}`;

            const formData = {
                ...data,
                role: data.roles && data.roles.length ? data.roles[0].name : '',
                status: (data.status === 'active' || data.status === 1 || data.status === true) ? 1 : 0
            };

            App.populateForm(form, formData);
            const modal = new bootstrap.Modal(document.getElementById('editStaffModal'));
            modal.show();
        }
    </script>
@endsection
