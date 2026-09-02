@extends('layouts.app')

@section('title', 'Manage Students')
@section('header_title', 'Students')

@section('content')
    <!-- Actions Toolbar -->
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="studentSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search students..."
                oninput="reloadStudents()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.resetForm(document.forms['createStudentForm']);"
                data-bs-toggle="modal" data-bs-target="#createStudentModal">
                <i class="bi bi-plus-lg me-1"></i> Add Student
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            {{-- H-7: role, scope, aria-sort, tabindex for accessibility --}}
                            <th class="sortable-header" data-sort="full_name"
                                role="columnheader" scope="col" aria-sort="none" tabindex="0">Student</th>
                            <th class="sortable-header" data-sort="admission_number"
                                role="columnheader" scope="col" aria-sort="none" tabindex="0">Admission #</th>
                            <th class="sortable-header" data-sort="class_id"
                                role="columnheader" scope="col" aria-sort="none" tabindex="0">Class</th>
                            <th class="sortable-header" data-sort="gender"
                                role="columnheader" scope="col" aria-sort="none" tabindex="0">Gender</th>
                            <th class="sortable-header" data-sort="status"
                                role="columnheader" scope="col" aria-sort="none" tabindex="0">Status</th>
                            <th class="text-right" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <!-- Content loaded via JS -->
                    </tbody>
                </table>
            </div>
            <!-- Pagination handled by JS if needed, or simple Load More -->
            <div class="p-3 border-top  text-center ">
                <small class="text-gray-500">Displaying recent records.</small>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createStudentForm" action="/api/v1/students" method="POST" novalidate
                    onsubmit="App.submitForm(event, reloadStudents, 'student', 'createStudentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Add New Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden role field -->
                        <input type="hidden" name="role" value="student">

                        <!-- Step 1: Student Information -->
                        <div id="student-wizard-step-1">
                            <h6 class="font-bold  mb-6 ">Step 1: Student Information</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                {{-- C-2: for/id pairs on all labels. M-5: Title Case labels --}}
                                <label for="create-student-name" class="block text-sm font-medium text-gray-700  mb-6 ">Full Name *</label>
                                <input id="create-student-name" type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="John Doe" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="create-student-email" class="block text-sm font-medium text-gray-700  mb-6 ">Email Address *</label>
                                <input id="create-student-email" type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="student@example.com"
                                    required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="genderSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Gender *</label>
                                <select name="gender" id="genderSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="create-student-dob" class="block text-sm font-medium text-gray-700  mb-6 ">Date of Birth</label>
                                <input id="create-student-dob" type="date" name="date_of_birth" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="create-student-admission" class="block text-sm font-medium text-gray-700  mb-6 ">Admission Number *</label>
                                <input id="create-student-admission" type="text" name="admission_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="STU2025001"
                                    required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="create-student-admission-date" class="block text-sm font-medium text-gray-700  mb-6 ">Admission Date *</label>
                                <input id="create-student-admission-date" type="date" name="admission_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="classSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Class *</label>
                                <select name="class_id" id="classSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="sectionSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Section (Optional)</label>
                                <select name="section_id" id="sectionSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">No Section</option>
                                </select>
                                <small class="text-gray-500">Optional: Assign student to a section</small>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="sessionSelect" class="block text-sm font-medium text-gray-700  mb-6 ">School Session *</label>
                                <select name="school_session_id" id="sessionSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="termSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Term *</label>
                                <select name="term_id" id="termSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                            <div class=" md:col-span-12 col-span-1 ">
                                <label for="create-student-password" class="block text-sm font-medium text-gray-700  mb-6 ">Password (Optional)</label>
                                <input id="create-student-password" type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Leave blank to auto-generate">
                                <small class="text-gray-500">If left blank, a password will be auto-generated and sent via
                                    email</small>
                            </div>
                        </div>

                        </div>

                        <!-- Step 2: Guardian Information -->
                        <div id="student-wizard-step-2" class=" hidden ">
                            <h6 class="font-bold  mb-6 ">Step 2: Guardian Information</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="create-guardian-name" class="block text-sm font-medium text-gray-700  mb-6 ">Guardian Name *</label>
                                <input id="create-guardian-name" type="text" name="guardian[name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="Jane Doe"
                                    required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="guardianEmailInput" class="block text-sm font-medium text-gray-700  mb-6 ">Guardian Email *</label>
                                <div class="input-group">
                                    <input type="email" id="guardianEmailInput" name="guardian[email]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="guardian@example.com" required>
                                    <button class=" px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 rounded-lg transition-all inline-flex items-center gap-2 font-medium " type="button"
                                        id="btnCheckGuardian">Check</button>
                                    <span class="input-group-text  hidden " id="guardianStatusIcon">
                                        <i class="bi bi-person-check-fill text-success" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input type="hidden" name="guardian_id" id="guardian_id_hidden">
                                <small id="guardianHelp" class="form-text text-gray-500">Enter email and click Check to
                                    lookup existing siblings' guardian.</small>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="relationSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Relation *</label>
                                <select name="guardian[relation]" id="relationSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Relation</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="create-guardian-occupation" class="block text-sm font-medium text-gray-700  mb-6 ">Occupation *</label>
                                <input id="create-guardian-occupation" type="text" name="guardian[occupation]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="e.g. Engineer" required>
                            </div>
                            <div class=" md:col-span-12 col-span-1 ">
                                <label for="create-guardian-password" class="block text-sm font-medium text-gray-700  mb-6 ">Guardian Password (Optional)</label>
                                <input id="create-guardian-password" type="password" name="guardian[password]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Leave blank to auto-generate">
                                <small class="text-gray-500">If left blank, a password will be auto-generated and sent via
                                    email</small>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div id="student-wizard-footer-1" class="w-100  flex   justify-between ">
                            <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.switchWizardStep(2, 'student')">Next Step <i class="bi bi-arrow-right ms-1"></i></button>
                        </div>
                        <div id="student-wizard-footer-2" class="w-100  flex   justify-between   hidden ">
                            <button type="button" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.switchWizardStep(1, 'student')"><i class="bi bi-arrow-left me-1"></i> Back</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Student</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editStudentForm" method="POST"
                    onsubmit="App.submitForm(event, reloadStudents, 'student', 'editStudentModal')">
                    @csrf @method('PUT')
                    <!-- Hidden ID field if needed, but action is set in JS -->
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Student Information -->
                        <h6 class="font-bold  mb-6 ">Student Information</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                {{-- C-2: for/id pairs. M-5: Title Case --}}
                                <label for="edit-student-name" class="block text-sm font-medium text-gray-700  mb-6 ">Full Name *</label>
                                <input id="edit-student-name" type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="John Doe" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="edit-student-email" class="block text-sm font-medium text-gray-700  mb-6 ">Email Address *</label>
                                <input id="edit-student-email" type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="student@example.com" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="editGenderSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Gender *</label>
                                <select name="gender" id="editGenderSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="edit-student-dob" class="block text-sm font-medium text-gray-700  mb-6 ">Date of Birth</label>
                                <input id="edit-student-dob" type="date" name="date_of_birth" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="edit-student-admission" class="block text-sm font-medium text-gray-700  mb-6 ">Admission Number *</label>
                                <input id="edit-student-admission" type="text" name="admission_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="STU2025001" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="edit-student-admission-date" class="block text-sm font-medium text-gray-700  mb-6 ">Admission Date *</label>
                                <input id="edit-student-admission-date" type="date" name="admission_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="editClassSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Class *</label>
                                <select name="class_id" id="editClassSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="editSectionSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Section (Optional)</label>
                                <select name="section_id" id="editSectionSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">No Section</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="editSessionSelect" class="block text-sm font-medium text-gray-700  mb-6 ">School Session *</label>
                                <select name="school_session_id" id="editSessionSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="editTermSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Term *</label>
                                <select name="term_id" id="editTermSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="edit-student-status" class="block text-sm font-medium text-gray-700  mb-6 ">Status</label>
                                <select id="edit-student-status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class=" md:col-span-12 col-span-1 ">
                                <label for="edit-student-password" class="block text-sm font-medium text-gray-700  mb-6 ">Password (Optional)</label>
                                <input id="edit-student-password" type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Leave blank to keep current password">
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <h6 class="font-bold  mb-6 ">Guardian Information</h6>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="edit-guardian-name" class="block text-sm font-medium text-gray-700  mb-6 ">Guardian Name *</label>
                                <input id="edit-guardian-name" type="text" name="guardian[name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="Jane Doe"
                                    required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="edit-guardian-email" class="block text-sm font-medium text-gray-700  mb-6 ">Guardian Email *</label>
                                <input id="edit-guardian-email" type="email" name="guardian[email]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="guardian@example.com" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="editRelationSelect" class="block text-sm font-medium text-gray-700  mb-6 ">Relation *</label>
                                <select name="guardian[relation]" id="editRelationSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Relation</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label for="edit-guardian-occupation" class="block text-sm font-medium text-gray-700  mb-6 ">Occupation *</label>
                                <input id="edit-guardian-occupation" type="text" name="guardian[occupation]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="e.g. Engineer" required>
                            </div>
                            <div class=" md:col-span-12 col-span-1 ">
                                <label for="edit-guardian-password" class="block text-sm font-medium text-gray-700  mb-6 ">Password (Optional)</label>
                                <input id="edit-guardian-password" type="password" name="guardian[password]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="Leave blank to keep current password">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadStudents();

            // Initialize Create Modal Dropdowns
            const createModal = document.getElementById('createStudentModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/classes', 'classSelect');
                App.loadOptions('/api/v1/sections', 'sectionSelect');
                App.loadOptions('/api/v1/school-sessions', 'sessionSelect');
                App.loadOptions('/api/v1/terms', 'termSelect');
                App.loadOptions('/api/v1/settings/enums?type=gender', 'genderSelect');
                App.loadOptions('/api/v1/settings/enums?type=relation', 'relationSelect');

                // Reset guardian lookup status
                document.getElementById('guardianStatusIcon')?.classList.add('d-none');
                document.getElementById('guardianEmailInput')?.classList.remove('is-valid');
            });

            // Guardian lookup logic (Manual Check)
            const btnCheck = document.getElementById('btnCheckGuardian');
            const guardianEmailInput = document.getElementById('guardianEmailInput');

            if (btnCheck && guardianEmailInput) {
                btnCheck.addEventListener('click', () => {
                    const email = guardianEmailInput.value.trim();
                    if (email.length > 5 && email.includes('@')) {
                        lookupGuardian(email);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Email',
                            text: 'Please enter a valid email address first.'
                        });
                    }
                });
            }

            // Initialize Edit Modal Dropdowns
            const editModal = document.getElementById('editStudentModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/classes', 'editClassSelect');
                App.loadOptions('/api/v1/sections', 'editSectionSelect');
                App.loadOptions('/api/v1/school-sessions', 'editSessionSelect');
                App.loadOptions('/api/v1/terms', 'editTermSelect');
            });
        });

        // loadClasses function removed in favor of App.loadOptions logic attached to modal events


        let lookupController = null;

        async function lookupGuardian(email) {
            const icon = document.getElementById('guardianStatusIcon');
            const input = document.getElementById('guardianEmailInput');
            const idHidden = document.getElementById('guardian_id_hidden');

            if (lookupController) lookupController.abort();
            lookupController = new AbortController();
            const {
                signal
            } = lookupController;

            try {
                const response = await axios.get(`/api/v1/guardians?email=${encodeURIComponent(email)}`, {
                    signal
                });
                const result = response.data;

                if (result.success && result.data && result.data.length > 0) {
                    const guardian = result.data[0];
                    fillGuardianInfo(guardian);
                    if (idHidden) idHidden.value = guardian.id;
                    icon?.classList.remove('d-none');
                    input?.classList.add('is-valid');

                    Swal.fire({
                        icon: 'success',
                        title: 'Guardian Found',
                        text: `Found existing guardian: ${guardian.user?.name}. We'll link the new student to this account.`,
                        toast: true,
                        position: 'top-end',
                        timer: 4000,
                        showConfirmButton: false
                    });
                } else {
                    if (idHidden) idHidden.value = '';
                    icon?.classList.add('d-none');
                    input?.classList.remove('is-valid');
                    Swal.fire({
                        icon: 'info',
                        title: 'Not Found',
                        text: 'No existing guardian found with this email. A new account will be created.',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Guardian lookup error:', error);
            }
        }

        function fillGuardianInfo(data) {
            const form = document.forms['createStudentForm'];
            if (!form) return;

            const nameInput = form.querySelector('input[name="guardian[name]"]');
            const relationSelect = form.querySelector('select[name="guardian[relation]"]');
            const occupationInput = form.querySelector('input[name="guardian[occupation]"]');

            if (nameInput) nameInput.value = data.user?.name || '';
            if (relationSelect) relationSelect.value = data.relation || '';
            if (occupationInput) occupationInput.value = data.occupation || '';
        }

        function reloadStudents() {
            const query = document.getElementById('studentSearch').value;
            App.renderTable('/api/v1/students?search=' + encodeURIComponent(query), 'studentsTableBody', (item) => {
                const isActive = (item.status === true || item.status === 1 || item.status === 'active');
                const statusClass = isActive ? 'success' : 'secondary';
                const statusText = isActive ? 'Active' : 'Inactive';

                return App.safeHTML`
                    <tr>
                        <td data-label="Student">{{-- C-4: data-label for mobile bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden view --}}
                            <div class=" flex   items-center ">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(item.full_name)}&background=2563eb&color=fff"
                                     class="avatar-sm rounded-circle me-3 shadow-sm" alt="${item.full_name}">
                                <div>
                                    <div class="font-bold text-dark">${item.full_name}</div>
                                    <small class="text-gray-500 text-uppercase notif-sender-role">${item.user?.email}</small>
                                </div>
                            </div>
                        </td>
                        <td data-label="Admission #"><code class="text-primary font-bold">${item.admission_number}</code></td>
                        <td data-label="Class">${item.current_class}</td>
                        <td data-label="Gender" class="text-capitalize">${item.user?.gender || 'N/A'}</td>
                        <td data-label="Status"><span class="badge rounded-pill bg-${statusClass}-subtle text-${statusClass} px-3">${statusText}</span></td>
                        <td data-label="Actions" class="text-right">
                            <div class=" flex   justify-end  gap-2">
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm"
                                    data-action="edit" data-entity="student" data-id="${item.id}"
                                    aria-label="Edit student ${item.full_name}" title="Edit">
                                    <i class="bi bi-pencil-fill text-primary" aria-hidden="true"></i>
                                </button>
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm"
                                    data-action="delete" data-entity="student" data-id="${item.id}"
                                    aria-label="Delete student ${item.full_name}" title="Delete">
                                    <i class="bi bi-trash text-danger" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        function editStudent(data) {
            const form = document.getElementById('editStudentForm');
            form.action = `/api/v1/students/${data.id}`;

            // Initialize formData with root data
            const formData = {
                ...data
            };

            // 1. Student Name (split full_name)
            const fullName = data.full_name || data.user?.name || '';
            const nameParts = fullName.trim().split(/\s+/);
            formData.first_name = nameParts[0] || '';
            formData.last_name = nameParts.slice(1).join(' ') || '';

            // 2. Student Info
            formData.email = data.user?.email || '';
            formData.gender = data.user?.gender || '';
            formData.date_of_birth = data.user?.date_of_birth || '';
            if (formData.date_of_birth && formData.date_of_birth.includes('T')) {
                formData.date_of_birth = formData.date_of_birth.split('T')[0];
            }

            // Admission and enrollment
            formData.admission_date = data.admission_date ?
                (data.admission_date.includes('T') ? data.admission_date.split('T')[0] : data.admission_date) :
                '';
            formData.admission_number = data.admission_number || '';
            formData.class_id = data.class_id || '';
            formData.school_session_id = data.school_session_id || '';
            formData.term_id = data.term_id || (data.enrollments?.[0]?.term_id || '');
            formData.status = (data.status === true || data.status === 1 || data.status === 'active') ? 'active' :
                'inactive';

            // 3. Guardian info (first guardian only)
            if (data.guardians && data.guardians.length > 0) {
                const g = data.guardians[0];
                formData.guardian = {
                    name: g.user?.name || g.name || '',
                    email: g.user?.email || g.email || '',
                    relation: g.relation || '',
                    occupation: g.occupation || ''
                };
            } else {
                formData.guardian = {
                    name: '',
                    email: '',
                    relation: '',
                    occupation: ''
                };
            }

            // 4. Load select options
            App.loadOptions('/api/v1/classes', 'editClassSelect', formData.class_id);
            App.loadOptions('/api/v1/school-sessions', 'editSessionSelect', formData.school_session_id);
            App.loadOptions('/api/v1/terms', 'editTermSelect', formData.term_id);
            App.loadOptions('/api/v1/sections', 'editSectionSelect', formData.section_id);
            App.loadOptions('/api/v1/settings/enums?type=gender', 'editGenderSelect', formData.gender);
            App.loadOptions('/api/v1/settings/enums?type=relation', 'editRelationSelect', formData.guardian.relation);

            // 5. Populate the form with all mapped fields
            App.populateForm(form, formData);

            // 6. Show modal
            const modal = new bootstrap.Modal(document.getElementById('editStudentModal'));
            modal.show();
        }
    </script>
@endsection
