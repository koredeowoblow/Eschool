@extends('layouts.app')

@section('title', 'Manage Schools')
@section('header_title', 'Schools Management')

@section('content')
    <div class="card-premium p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <div class="input-group w-100 w-md-50">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="schoolSearch" class="form-control border-start-0 ps-0"
                    placeholder="Search schools by name or location..." oninput="loadSchools()">
            </div>
            <button class="btn btn-primary-premium" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-2"></i>Add School
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-premium table-hover align-middle">
                <thead>
                    <tr>
                        <th class="sortable-header" data-sort="name">School / Email</th>
                        <th class="sortable-header" data-sort="city">Location</th>
                        <th class="sortable-header" data-sort="contact_person">Contact Person</th>
                        <th>Stats</th>
                        <th class="sortable-header" data-sort="plan_id">Plan</th>
                        <th class="sortable-header" data-sort="is_active">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="schools-table-body">
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-muted small mt-2">Loading schools...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create School Modal -->
    <div class="modal fade" id="createSchoolModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New School</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createSchoolForm" method="POST" action="/api/v1/create-school"
                        onsubmit="App.submitForm(event, loadSchools, 'create-school', 'createSchoolModal')">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">School Name *</label>
                                <input type="text" class="form-control" id="create_name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" id="create_email" name="email" required>
                                <div class="form-text">School Contact Email</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admin Email *</label>
                                <input type="email" class="form-control" id="create_admin_email" name="admin_email"
                                    required>
                                <div class="form-text">Login Email for Admin User</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admin Name *</label>
                                <input type="text" class="form-control" id="create_admin_name" name="admin_name"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="tel" class="form-control" id="create_phone" name="phone" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-control" id="create_website" name="website">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address *</label>
                                <textarea class="form-control" id="create_address" name="address" rows="2" required></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City *</label>
                                <input type="text" class="form-control" id="create_city" name="city" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State *</label>
                                <input type="text" class="form-control" id="create_state" name="state" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" id="create_area" name="area">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Person *</label>
                                <input type="text" class="form-control" id="create_contact_person"
                                    name="contact_person" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Phone *</label>
                                <input type="tel" class="form-control" id="create_contact_phone"
                                    name="contact_person_phone" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Plan *</label>
                                <select class="form-select" id="create_plan" name="plan" required>
                                    <option value="basic">Basic</option>
                                    <option value="standard">Standard</option>
                                    <option value="premium">Premium</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status *</label>
                                <select class="form-select" id="create_status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="active" selected>Active</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <input type="hidden" name="slug" id="create_slug">
                        </div>

                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary-premium">Create School</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit School Modal -->
    <div class="modal fade" id="editSchoolModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit School</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editSchoolForm" method="PUT"
                        onsubmit="App.submitForm(event, loadSchools, 'edit-school', 'editSchoolModal')">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">


                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">School Name *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-control" id="edit_website" name="website">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address *</label>
                                <textarea class="form-control" id="edit_address" name="address" rows="2" required></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City *</label>
                                <input type="text" class="form-control" id="edit_city" name="city" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State *</label>
                                <input type="text" class="form-control" id="edit_state" name="state" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" id="edit_area" name="area">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Person *</label>
                                <input type="text" class="form-control" id="edit_contact_person"
                                    name="contact_person" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Phone *</label>
                                <input type="tel" class="form-control" id="edit_contact_phone"
                                    name="contact_person_phone" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Plan *</label>
                                <select class="form-select" id="edit_plan" name="plan" required>
                                    <option value="basic">Basic</option>
                                    <option value="standard">Standard</option>
                                    <option value="premium">Premium</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status *</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <input type="hidden" name="slug" id="edit_slug">
                            <input type="hidden" name="is_active" id="edit_is_active">
                        </div>

                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary-premium">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadSchools();
        });

        // Expose to window for global access
        window.openCreateModal = function() {
            const form = document.getElementById('createSchoolForm');
            if (form) form.reset();
            const modalEl = document.getElementById('createSchoolModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        };

        // Expose to window for global access from generated buttons
        window.approveSchool = function(id) {
            if (!confirm('Are you sure you want to approve this school?')) return;

            axios.put(`/api/v1/schools/${id}`, {
                    is_active: 1
                }, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved',
                        text: 'School has been approved.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    App.renderTable('/api/v1/schools', 'schools-table-body', 'school');
                })
                .catch(error => {
                    console.error('Error approving school', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to approve school.'
                    });
                });
        };

        // Note: App.js handles the generic 'edit' button click using verify/edit modes. 
        // We need to define window.editSchool for the generic handler to call.

        window.editSchool = function(school) {
            document.getElementById('edit_name').value = school.name;
            document.getElementById('edit_email').value = school.email;
            document.getElementById('edit_phone').value = school.phone;
            document.getElementById('edit_website').value = school.website || '';
            document.getElementById('edit_address').value = school.address;
            document.getElementById('edit_city').value = school.city || '';
            document.getElementById('edit_state').value = school.state || '';
            document.getElementById('edit_area').value = school.area || '';
            document.getElementById('edit_contact_person').value = school.contact_person;
            document.getElementById('edit_contact_phone').value = school.contact_person_phone;

            let planValue = 'basic';
            if (school.plan_id == 2) planValue = 'standard';
            if (school.plan_id == 3) planValue = 'premium';
            if (school.plan) planValue = school.plan;
            document.getElementById('edit_plan').value = planValue;

            const statusValue = school.is_active == 1 ? 'active' : (school.status || 'pending');
            document.getElementById('edit_status').value = statusValue;
            document.getElementById('edit_slug').value = school.slug || '';
            document.getElementById('edit_is_active').value = school.is_active == 1 ? '1' : '0';

            // **Set form action dynamically**
            document.getElementById('editSchoolForm').action = `/api/v1/schools/${school.id}`;

            const modal = new bootstrap.Modal(document.getElementById('editSchoolModal'));
            modal.show();
        };

        // Sync is_active with status dropdown
        document.getElementById('edit_status').addEventListener('change', e => {
            const isActive = e.target.value === 'active' ? '1' : '0';
            document.getElementById('edit_is_active').value = isActive;
        });

        // Slug auto-generation
        document.getElementById('create_name').addEventListener('input', e => {
            const slug = e.target.value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            document.getElementById('create_slug').value = slug;
        });
        document.getElementById('edit_name').addEventListener('input', e => {
            const slug = e.target.value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            document.getElementById('edit_slug').value = slug;
        });

        // Define callback for create form
        window.loadSchools = () => {
            const query = document.getElementById('schoolSearch').value;
            App.renderTable('/api/v1/schools?search=' + encodeURIComponent(query), 'schools-table-body', 'school');
        };
    </script>
@endsection
