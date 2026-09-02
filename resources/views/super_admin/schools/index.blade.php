@extends('layouts.app')

@section('title', 'Manage Schools')
@section('header_title', 'Schools Management')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-4">
        <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
            <div class="input-group w-100 w-md-50">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="schoolSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all "
                    placeholder="Search schools by name or location..." oninput="loadSchools()">
            </div>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-2"></i>Add School
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle">
                <thead>
                    <tr>
                        <th class="sortable-header" data-sort="name">School / Email</th>
                        <th class="sortable-header" data-sort="city">Location</th>
                        <th class="sortable-header" data-sort="contact_person">Contact Person</th>
                        <th>Stats</th>
                        <th class="sortable-header" data-sort="plan_id">Plan</th>
                        <th class="sortable-header" data-sort="is_active">Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="schools-table-body">
                    <tr>
                        <td colspan="5" class=" text-center  py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-gray-500 small mt-4">Loading schools...</p>
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
                    <h5 class="modal-title font-bold text-xl text-gray-800">Create New School</h5>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-bs-dismiss="modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createSchoolForm" method="POST" action="/api/v1/create-school"
                        onsubmit="App.submitForm(event, loadSchools, 'create-school', 'createSchoolModal')">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">School Name *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_name" name="name" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email *</label>
                                <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_email" name="email" required>
                                <div class="form-text">School Contact Email</div>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Admin Email *</label>
                                <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_admin_email" name="admin_email"
                                    required>
                                <div class="form-text">Login Email for Admin User</div>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Admin Name *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_admin_name" name="admin_name"
                                    required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone *</label>
                                <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_phone" name="phone" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Website</label>
                                <input type="url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_website" name="website">
                            </div>
                            <div class=" col-span-1 md:col-span-12 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Address *</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_address" name="address" rows="2" required></textarea>
                            </div>
                            <div class=" md:col-span-4 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">City *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_city" name="city" required>
                            </div>
                            <div class=" md:col-span-4 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">State *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_state" name="state" required>
                            </div>
                            <div class=" md:col-span-4 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Area</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_area" name="area">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contact Person *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_contact_person"
                                    name="contact_person" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contact Phone *</label>
                                <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="create_contact_phone"
                                    name="contact_person_phone" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Plan *</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="create_plan" name="plan" required>
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status *</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="create_status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="active" selected>Active</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <input type="hidden" name="slug" id="create_slug">
                        </div>

                        <div class="modal-footer mt-4">
                            <button type="button" class=" px-4 py-2 bg-slate-500 hover:bg-slate-600 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium " data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Create School</button>
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
                    <h5 class="modal-title font-bold text-xl text-gray-800">Edit School</h5>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors" data-bs-dismiss="modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editSchoolForm" method="PUT"
                        onsubmit="App.submitForm(event, loadSchools, 'edit-school', 'editSchoolModal')">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">


                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">School Name *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_name" name="name" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email *</label>
                                <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_email" name="email" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone *</label>
                                <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_phone" name="phone" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Website</label>
                                <input type="url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_website" name="website">
                            </div>
                            <div class=" col-span-1 md:col-span-12 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Address *</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_address" name="address" rows="2" required></textarea>
                            </div>
                            <div class=" md:col-span-4 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">City *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_city" name="city" required>
                            </div>
                            <div class=" md:col-span-4 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">State *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_state" name="state" required>
                            </div>
                            <div class=" md:col-span-4 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Area</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_area" name="area">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contact Person *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_contact_person"
                                    name="contact_person" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contact Phone *</label>
                                <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="edit_contact_phone"
                                    name="contact_person_phone" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Plan *</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="edit_plan" name="plan" required>
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status *</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="edit_status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <input type="hidden" name="slug" id="edit_slug">
                            <input type="hidden" name="is_active" id="edit_is_active">
                        </div>

                        <div class="modal-footer mt-4">
                            <button type="button" class=" px-4 py-2 bg-slate-500 hover:bg-slate-600 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium " data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Changes</button>
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
            App.loadOptions('/api/v1/plans', 'create_plan', null, 'id', 'name', 'Select Plan');
            App.loadOptions('/api/v1/plans', 'edit_plan', null, 'id', 'name', 'Select Plan');
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

            // Plan is now an ID
            if (school.school_plan_id) {
                document.getElementById('edit_plan').value = school.school_plan_id;
            } else if (school.plan_id) {
                document.getElementById('edit_plan').value = school.plan_id;
            }

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
