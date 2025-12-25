@extends('layouts.app')

@section('title', 'Manage Students')
@section('header_title', 'Students')

@section('content')
    <!-- Actions Toolbar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="studentSearch" class="form-control border-start-0 ps-0" placeholder="Search students..."
                oninput="reloadStudents()">
        </div>

        @hasrole('super_admin|school_admin')
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createStudentForm']);"
                data-bs-toggle="modal" data-bs-target="#createStudentModal">
                <i class="bi bi-plus-lg me-1"></i> Add Student
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th class="sortable-header" data-sort="full_name">Student</th>
                            <th class="sortable-header" data-sort="admission_number">Admission #</th>
                            <th class="sortable-header" data-sort="class_id">Class</th>
                            <th class="sortable-header" data-sort="gender">Gender</th>
                            <th class="sortable-header" data-sort="status">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <!-- Content loaded via JS -->
                    </tbody>
                </table>
            </div>
            <!-- Pagination handled by JS if needed, or simple Load More -->
            <div class="p-3 border-top text-center">
                <small class="text-muted">Displaying recent records.</small>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createStudentForm" action="/api/v1/students" method="POST"
                    onsubmit="App.submitForm(event, reloadStudents, 'student', 'createStudentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add New Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden role field -->
                        <input type="hidden" name="role" value="student">

                        <!-- Student Information -->
                        <h6 class="fw-bold mb-3">Student Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control" placeholder="John" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control" placeholder="Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" placeholder="student@example.com"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender *</label>
                                <select name="gender" id="genderSelect" class="form-select" required>
                                    <option value="">Select Gender</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Number *</label>
                                <input type="text" name="admission_number" class="form-control" placeholder="STU2025001"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Date *</label>
                                <input type="date" name="admission_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Class *</label>
                                <select name="class_id" id="classSelect" class="form-select" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Section (Optional)</label>
                                <select name="section_id" id="sectionSelect" class="form-select">
                                    <option value="">No Section</option>
                                </select>
                                <small class="text-muted">Optional: Assign student to a section</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">School Session *</label>
                                <select name="school_session_id" id="sessionSelect" class="form-select" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Term *</label>
                                <select name="term_id" id="termSelect" class="form-select" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Password (Optional)</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Leave blank to auto-generate">
                                <small class="text-muted">If left blank, a password will be auto-generated and sent via
                                    email</small>
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <h6 class="fw-bold mb-3">Guardian Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Guardian Name *</label>
                                <input type="text" name="guardian[name]" class="form-control" placeholder="Jane Doe"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Guardian Email *</label>
                                <div class="input-group">
                                    <input type="email" id="guardianEmailInput" name="guardian[email]"
                                        class="form-control" placeholder="guardian@example.com" required>
                                    <button class="btn btn-outline-primary" type="button"
                                        id="btnCheckGuardian">Check</button>
                                    <span class="input-group-text d-none" id="guardianStatusIcon">
                                        <i class="bi bi-person-check-fill text-success"></i>
                                    </span>
                                </div>
                                <input type="hidden" name="guardian_id" id="guardian_id_hidden">
                                <small id="guardianHelp" class="form-text text-muted">Enter email and click Check to
                                    lookup existing
                                    siblings' guardian.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Relation *</label>
                                <select name="guardian[relation]" id="relationSelect" class="form-select" required>
                                    <option value="">Select Relation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Occupation *</label>
                                <input type="text" name="guardian[occupation]" class="form-control"
                                    placeholder="e.g. Engineer" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Guardian Password (Optional)</label>
                                <input type="password" name="guardian[password]" class="form-control"
                                    placeholder="Leave blank to auto-generate">
                                <small class="text-muted">If left blank, a password will be auto-generated and sent via
                                    email</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Student</button>
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
                        <h5 class="modal-title fw-bold">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Student Information -->
                        <h6 class="fw-bold mb-3">Student Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control" placeholder="John"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control" placeholder="Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control"
                                    placeholder="student@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender *</label>
                                <select name="gender" id="editGenderSelect" class="form-select" required>
                                    <option value="">Select Gender</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Number *</label>
                                <input type="text" name="admission_number" class="form-control"
                                    placeholder="STU2025001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Date *</label>
                                <input type="date" name="admission_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Class *</label>
                                <select name="class_id" id="editClassSelect" class="form-select" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Section (Optional)</label>
                                <select name="section_id" id="editSectionSelect" class="form-select">
                                    <option value="">No Section</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">School Session *</label>
                                <select name="school_session_id" id="editSessionSelect" class="form-select" required>
                                    <option value="">Select Session</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Term *</label>
                                <select name="term_id" id="editTermSelect" class="form-select" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Password (Optional)</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Leave blank to keep current password">
                            </div>
                        </div>

                        <!-- Guardian Information -->
                        <h6 class="fw-bold mb-3">Guardian Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Guardian Name *</label>
                                <input type="text" name="guardian[name]" class="form-control" placeholder="Jane Doe"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Guardian Email *</label>
                                <input type="email" name="guardian[email]" class="form-control"
                                    placeholder="guardian@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Relation *</label>
                                <select name="guardian[relation]" id="editRelationSelect" class="form-select" required>
                                    <option value="">Select Relation</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Occupation *</label>
                                <input type="text" name="guardian[occupation]" class="form-control"
                                    placeholder="e.g. Engineer" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Password (Optional)</label>
                                <input type="password" name="guardian[password]" class="form-control"
                                    placeholder="Leave blank to keep current password">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Student</button>
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
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(item.full_name)}&background=2563eb&color=fff" 
                                     class="avatar-sm rounded-circle me-3 shadow-sm" alt="${item.full_name}">
                                <div>
                                    <div class="fw-bold text-dark">${item.full_name}</div>
                                    <small class="text-muted text-uppercase" style="font-size: 0.7rem;">${item.user?.email}</small>
                                </div>
                            </div>
                        </td>
                        <td><code class="text-primary fw-bold">${item.admission_number}</code></td>
                        <td>${item.current_class}</td>
                        <td class="text-capitalize">${item.user?.gender || 'N/A'}</td>
                        <td><span class="badge rounded-pill bg-${statusClass}-subtle text-${statusClass} px-3">${statusText}</span></td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-light shadow-sm btn-sm" 
                                    data-action="edit" data-entity="student" data-id="${item.id}" title="Edit">
                                    <i class="bi bi-pencil-fill text-primary"></i>
                                </button>
                                <button class="btn btn-light shadow-sm btn-sm" 
                                    data-action="delete" data-entity="student" data-id="${item.id}" title="Delete">
                                    <i class="bi bi-trash text-danger"></i>
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
