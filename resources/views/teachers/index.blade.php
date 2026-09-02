@extends('layouts.app')

@section('title', 'Manage Teachers')
@section('header_title', 'Teachers')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="teacherSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search teachers..."
                oninput="reloadTeachers()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.resetForm(document.forms['createTeacherForm']);"
                data-bs-toggle="modal" data-bs-target="#createTeacherModal">
                <i class="bi bi-plus-lg me-1"></i> Add Teacher
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Teacher</th>
                            <th>Employee #</th>
                            <th>Status</th>
                            <th>Subjects</th>
                            <th>Phone</th>
                            <th>Hire Date</th>
                            <th class="text-right">Actions</th>
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
                        <h5 class="modal-title font-bold">Add Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden role field -->
                        <input type="hidden" name="role" value="teacher">

                        <h6 class="font-bold  mb-6 ">Basic Information</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-12 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="John Doe" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Phone</label>
                                <input type="tel" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="font-bold  mb-6 ">Employment Details</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Employee Number <span class="text-danger">*</span></label>
                                <input type="text" name="employee_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required
                                    placeholder="e.g. EMP001">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Hire Date</label>
                                <input type="date" name="hire_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Qualification</label>
                                <input type="text" name="qualification" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="e.g. B.Ed, M.Sc">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Department</label>
                                <input type="text" name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="e.g. Mathematics, Science">
                            </div>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Bio</label>
                            <textarea name="bio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="3" placeholder="Brief biography or description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Teacher</button>
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
                        <h5 class="modal-title font-bold">Edit Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id">
                        <h6 class="font-bold  mb-6 ">Basic Information</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-12 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
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

                        <hr class="my-4">
                        <h6 class="font-bold  mb-6 ">Employment Details</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Employee Number <span class="text-danger">*</span></label>
                                <input type="text" name="employee_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Hire Date</label>
                                <input type="date" name="hire_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Qualification</label>
                                <input type="text" name="qualification" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Department</label>
                                <input type="text" name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Bio</label>
                            <textarea name="bio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Teacher</button>
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
