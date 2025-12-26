@extends('layouts.app')

@section('title', 'Manage Teachers')
@section('header_title', 'Teachers')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="teacherSearch" class="form-control border-start-0 ps-0" placeholder="Search teachers..."
                oninput="reloadTeachers()">
        </div>

        @hasrole('super_admin|school_admin')
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createTeacherForm']);"
                data-bs-toggle="modal" data-bs-target="#createTeacherModal">
                <i class="bi bi-plus-lg me-1"></i> Add Teacher
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Teacher</th>
                            <th>Employee #</th>
                            <th>Status</th>
                            <th>Subjects</th>
                            <th>Phone</th>
                            <th>Hire Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="teachersTableBody">
                        <!-- Loaded by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createTeacherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createTeacherForm" action="/api/v1/teachers" method="POST"
                    onsubmit="App.submitForm(event, reloadTeachers, 'teacher', 'createTeacherModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden role field -->
                        <input type="hidden" name="role" value="teacher">

                        <h6 class="fw-bold mb-3">Basic Information</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Employment Details</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee Number <span class="text-danger">*</span></label>
                                <input type="text" name="employee_number" class="form-control" required
                                    placeholder="e.g. EMP001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hire Date</label>
                                <input type="date" name="hire_date" class="form-control">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Qualification</label>
                                <input type="text" name="qualification" class="form-control"
                                    placeholder="e.g. B.Ed, M.Sc">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control"
                                    placeholder="e.g. Mathematics, Science">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="3" placeholder="Brief biography or description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editTeacherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editTeacherForm" method="POST"
                    onsubmit="App.submitForm(event, reloadTeachers, 'teacher', 'editTeacherModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id">
                        <h6 class="fw-bold mb-3">Basic Information</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
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

                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">Employment Details</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee Number <span class="text-danger">*</span></label>
                                <input type="text" name="employee_number" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hire Date</label>
                                <input type="date" name="hire_date" class="form-control">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Qualification</label>
                                <input type="text" name="qualification" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.renderTable('/api/v1/teachers', 'teachersTableBody', 'teacher');
        });

        function reloadTeachers() {
            const query = document.getElementById('teacherSearch').value;
            App.renderTable('/api/v1/teachers?search=' + query, 'teachersTableBody', 'teacher');
        }

        function editTeacher(data) {
            const form = document.getElementById('editTeacherForm');
            form.action = `/api/v1/teachers/${data.id}`;

            // Flatten nested user data for form population
            const formData = {
                ...data,
                user_id: data.user_id || data.user?.id || '',
                name: data.user?.name || data.name || '',
                email: data.user?.email || data.email || '',
                phone: data.user?.phone || data.phone || '',
                status: data.user?.status ?? data.status ?? 1,
                employee_number: data.employee_number || '',
                hire_date: data.hire_date ?
                    (data.hire_date.includes('T') ? data.hire_date.split('T')[0] : data.hire_date) : '',
                qualification: data.qualification || '',
                department: data.department || '',
                bio: data.bio || ''
            };

            App.populateForm(form, formData);
            const modal = new bootstrap.Modal(document.getElementById('editTeacherModal'));
            modal.show();
        }
    </script>
@endsection
