@extends('layouts.app')

@section('title', 'Manage Classes')
@section('header_title', 'Classes')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <h5 class="text-gray-500 fw-normal  mb-6 ">Academic Structures</h5>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium requires-session-lock" data-bs-toggle="modal"
                data-bs-target="#createClassModal">
                <i class="bi bi-plus-lg me-1"></i> Create Class
            </button>
        @endhasrole
    </div>

    <!-- We switched to Table for JS Generic Rendering Consistency -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle  mb-6 ">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Class Teacher</th>
                            <th>Students</th>
                            <th>Subjects</th>
                            <th>Assignments</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="classesTableBody">
                        <!-- Loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createClassModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/api/v1/classes" method="POST"
                    onsubmit="App.submitForm(event, reloadClasses, 'class', 'createClassModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Create New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="e.g. Primary 1, Grade 1"
                                required>
                            <small class="text-gray-500">Enter the academic level name.</small>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Section</label>
                            <select name="section_id" id="sectionSelectClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                <option value="">No Section</option>
                            </select>
                            <small class="text-gray-500">Optional: Select a section for this class</small>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Session <span class="text-danger">*</span></label>
                            <select name="session_id" id="sessionSelectClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required
                                onchange="loadTermsBySessionClass('sessionSelectClass', 'termSelectClass')">
                                <option value="">Select Session</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Term <span class="text-danger">*</span></label>
                            <select name="term_id" id="termSelectClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Term</option>
                            </select>
                            <small class="text-gray-500">Select session first</small>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Class Teacher <span class="text-danger">*</span></label>
                            <select name="class_teacher_id" id="teacherSelectClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Teacher</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Create Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editClassModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editClassForm" method="POST"
                    onsubmit="App.submitForm(event, reloadClasses, 'class', 'editClassModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Class Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="e.g. Primary 1" required>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Section</label>
                            <select name="section_id" id="editSectionSelectClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                <option value="">No Section</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Session <span class="text-danger">*</span></label>
                            <select name="session_id" id="editSessionSelectClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required
                                onchange="loadTermsBySessionClass('editSessionSelectClass', 'editTermSelectClass')">
                                <option value="">Select Session</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Term <span class="text-danger">*</span></label>
                            <select name="term_id" id="editTermSelectClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Term</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Class Teacher <span class="text-danger">*</span></label>
                            <select name="class_teacher_id" id="editTeacherSelectClass" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Teacher</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Manage Subjects Modal -->
    <div class="modal fade" id="manageSubjectsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-bold">Manage Class Subjects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small">
                        Assign subjects and their respective teachers to <strong id="manageSubjectsClassName"></strong>.
                    </div>

                    <!-- Assign Form -->
                    <form id="assignSubjectForm" class="row g-2  mb-6 ">
                        @csrf
                        <input type="hidden" name="class_id" id="assignSubjectClassId">
                        <div class="col-md-5">
                            <label class="block text-sm font-medium text-gray-700  mb-6  small font-bold">Subject</label>
                            <select name="subject_id" id="assign_subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white-sm" required>
                                <option value="">Select Subject</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="block text-sm font-medium text-gray-700  mb-6  small font-bold">Teacher</label>
                            <select name="teacher_id" id="assign_teacher_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white-sm" required>
                                <option value="">Select Teacher</option>
                            </select>
                        </div>
                        <div class="col-md-2  flex  align-items-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium px-3 py-1.5 text-sm w-100">Assign</button>
                        </div>
                    </form>

                    <h6 class="font-bold  mb-6  small text-uppercase text-gray-500">Current Assignments</h6>
                    <div class="overflow-x-auto">
                        <table class="table table-sm align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Subject</th>
                                    <th>Teacher</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="classSubjectsTableBody">
                                <!-- Loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadClasses();

            // Initialize Create Modal Dropdowns
            const createModal = document.getElementById('createClassModal');
            createModal.addEventListener('show.bs.modal', async () => {
                App.loadOptions('/api/v1/sections', 'sectionSelectClass');
                App.loadOptions('/api/v1/school-sessions', 'sessionSelectClass');
                App.loadOptions('/api/v1/terms', 'termSelectClass');

                App.loadOptions('/api/v1/teachers', 'teacherSelectClass', null, 'id', (item) => item
                    .user?.name || 'N/A');
            });


            // Initialize Edit Modal Dropdowns
            const editModal = document.getElementById('editClassModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/sections', 'editSectionSelectClass');
                App.loadOptions('/api/v1/school-sessions', 'editSessionSelectClass');
                App.loadOptions('/api/v1/teachers', 'editTeacherSelectClass');
            });
        });

        function loadTermsBySessionClass(sessionSelectId, termSelectId, selectedTermId = null) {
            const sessionId = document.getElementById(sessionSelectId).value;
            const termSelect = document.getElementById(termSelectId);

            // Clear current options
            termSelect.innerHTML = '<option value="">Select Term</option>';

            if (sessionId) {
                // Load terms for the selected session
                App.loadOptions(`/api/v1/terms?session_id=${sessionId}`, termSelectId, selectedTermId);
            }
        }

        function reloadClasses() {
            App.renderTable('/api/v1/classes', 'classesTableBody', 'class');
        }

        function editClass(data) {
            const form = document.getElementById('editClassForm');
            form.action = `/api/v1/classes/${data.id}`;
            App.populateForm(form, data);

            // Load dropdowns first, then load terms if session is selected
            // Load dropdowns first with selected values
            Promise.all([
                App.loadOptions('/api/v1/sections', 'editSectionSelectClass', data.section_id),
                App.loadOptions('/api/v1/school-sessions', 'editSessionSelectClass', data.session_id),
                App.loadOptions('/api/v1/teachers', 'editTeacherSelectClass', data.class_teacher_id, 'id', (item) =>
                    item.user?.name || 'N/A')
            ]).then(() => {
                if (data.session_id) {
                    loadTermsBySessionClass('editSessionSelectClass', 'editTermSelectClass', data.term_id);
                }
            });

            const modal = new bootstrap.Modal(document.getElementById('editClassModal'));
            modal.show();
        }

        function manageSubjects(classId, className) {
            document.getElementById('manageSubjectsClassName').textContent = className;
            document.getElementById('assignSubjectClassId').value = classId;

            // Load Options for assignment dropdowns
            App.loadOptions('/api/v1/subjects', 'assign_subject_id');
            App.loadOptions('/api/v1/teachers', 'assign_teacher_id', null, 'id', (item) => item.user?.name || 'N/A');

            reloadClassSubjects(classId);

            const modal = new bootstrap.Modal(document.getElementById('manageSubjectsModal'));
            modal.show();
        }

        async function reloadClassSubjects(classId) {
            const tbody = document.getElementById('classSubjectsTableBody');
            tbody.innerHTML =
                '<tr><td colspan="3" class=" text-center  py-3"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>';

            try {
                const res = await axios.get(`/api/v1/teacher-subjects?class_id=${classId}`);
                const data = res.data.data;

                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="3" class=" text-center  py-3 text-gray-500">No subjects assigned yet</td></tr>';
                    return;
                }

                data.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><strong>${item.subject?.name || 'N/A'}</strong></td>
                        <td>${item.teacher?.user?.name || 'N/A'}</td>
                        <td class="text-right">
                            <button class="btn px-3 py-1.5 text-sm btn-outline-danger border-0" onclick="removeSubjectAssignment(${item.id}, ${classId})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (err) {
                console.error(err);
                tbody.innerHTML =
                    '<tr><td colspan="3" class=" text-center  py-3 text-danger">Error loading assignments</td></tr>';
            }
        }

        document.getElementById('assignSubjectForm').onsubmit = async (e) => {
            e.preventDefault();
            const form = e.target;
            const classId = document.getElementById('assignSubjectClassId').value;
            const submitBtn = form.querySelector('button[type="submit"]');

            submitBtn.disabled = true;
            try {
                await axios.post('/api/v1/teacher-subjects', new FormData(form));
                Swal.fire({
                    icon: 'success',
                    title: 'Assigned',
                    timer: 1000,
                    showConfirmButton: false
                });
                reloadClassSubjects(classId);
                reloadClasses(); // Update count in main table
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.response?.data?.message || 'Assignment failed'
                });
            } finally {
                submitBtn.disabled = false;
            }
        };

        async function removeSubjectAssignment(id, classId) {
            const result = await Swal.fire({
                title: 'Unassign Subject?',
                text: "This teacher will no longer be assigned to this subject in this class.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove!'
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/v1/teacher-subjects/${id}`);
                    reloadClassSubjects(classId);
                    reloadClasses(); // Update count in main table
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to remove assignment'
                    });
                }
            }
        }
    </script>
@endsection
