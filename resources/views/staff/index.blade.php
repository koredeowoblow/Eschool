@extends('layouts.app')

@section('title', 'Manage Staff')
@section('header_title', 'Staff Management')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="staffSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search staff..."
                oninput="reloadStaff()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.resetForm(document.forms['createStaffForm']);"
                data-bs-toggle="modal" data-bs-target="#createStaffModal">
                <i class="bi bi-plus-lg me-1"></i> Add Staff
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
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
                        <h5 class="modal-title font-bold">Add Staff Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="John Doe" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Role <span class="text-danger">*</span></label>
                                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Role</option>
                                    <option value="Finance Officer">Finance Officer</option>
                                    <option value="Exams Officer">Exams Officer</option>
                                    <option value="School Admin">School Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required
                                    placeholder="staff@example.com">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Phone</label>
                                <input type="tel" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="+234...">
                            </div>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Password <span class="text-gray-500 small">(Leave blank to
                                    auto-generate)</span></label>
                            <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="******">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Staff</button>
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
                        <h5 class="modal-title font-bold">Edit Staff Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Role <span class="text-danger">*</span></label>
                                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="Finance Officer">Finance Officer</option>
                                    <option value="Exams Officer">Exams Officer</option>
                                    <option value="School Admin">School Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Staff</button>
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
