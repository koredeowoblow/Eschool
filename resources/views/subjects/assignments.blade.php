@extends('layouts.app')

@section('title', 'Subject Assignments')
@section('header_title', 'Subject Assignments')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="assignmentSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all "
                placeholder="Search assignments..." oninput="reloadAssignments()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium requires-session-lock"
                onclick="App.resetForm(document.forms['createAssignmentForm']);" data-bs-toggle="modal"
                data-bs-target="#createAssignmentModal">
                <i class="bi bi-plus-lg me-1"></i> Assign Subject
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assignmentsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createAssignmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createAssignmentForm" action="/api/v1/teacher-subjects" method="POST"
                    onsubmit="App.submitForm(event, reloadAssignments, 'teacher-subject', 'createAssignmentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Assign Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="assignmentClassSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Class</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="assignmentSubjectSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Subject</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" id="assignmentTeacherSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Teacher</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Assign Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editAssignmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editAssignmentForm" method="POST"
                    onsubmit="App.submitForm(event, reloadAssignments, 'teacher-subject', 'editAssignmentModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="editAssignmentClassSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Class</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="editAssignmentSubjectSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Subject</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" id="editAssignmentTeacherSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Teacher</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadAssignments();

            const createModal = document.getElementById('createAssignmentModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/classes', 'assignmentClassSelect', null, 'id', (item) =>
                    `${item.name} (${item.section?.name || 'Main'})`);
                App.loadOptions('/api/v1/subjects', 'assignmentSubjectSelect');
                App.loadOptions('/api/v1/teachers', 'assignmentTeacherSelect', null, 'id', (item) => item
                    .user?.name || 'N/A');
            });

            const editModal = document.getElementById('editAssignmentModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/classes', 'editAssignmentClassSelect', null, 'id', (item) =>
                    `${item.name} (${item.section?.name || 'Main'})`);
                App.loadOptions('/api/v1/subjects', 'editAssignmentSubjectSelect');
                App.loadOptions('/api/v1/teachers', 'editAssignmentTeacherSelect', null, 'id', (item) =>
                    item.user?.name || 'N/A');
            });
        });

        function reloadAssignments() {
            const query = document.getElementById('assignmentSearch').value;
            App.renderTable('/api/v1/teacher-subjects?search=' + encodeURIComponent(query), 'assignmentsTableBody', (
                item) => {
                const className = item.class_room ?
                    `${item.class_room.name} (${item.class_room.section?.name || 'Main'})` :
                    'N/A';

                return App.safeHTML`
                    <tr>
                        <td>
                            <div class=" flex   items-center ">
                                <div class="bg-primary-subtle text-primary rounded px-2 py-1 small font-bold me-2">
                                    <i class="bi bi-grid-fill"></i>
                                </div>
                                <span class="font-bold text-dark">${className}</span>
                            </div>
                        </td>
                        <td>
                            <span class=" px-2.5 py-1 text-xs font-semibold rounded-md bg-blue-100 text-blue-700 -subtle text-info border-0 shadow-sm px-3 rounded-pill italic">
                                ${item.subject?.name || 'N/A'}
                            </span>
                        </td>
                        <td>
                            <div class=" flex   items-center ">
                                <div class="avatar-xs me-2 bg-light rounded-circle  text-center " style="width:24px; height:24px; line-height:24px;">
                                    <i class="bi bi-person text-primary" style="font-size: 0.8rem;"></i>
                                </div>
                                <span>${item.teacher?.user?.name || 'N/A'}</span>
                            </div>
                        </td>
                        <td class="text-right">
                            <div class=" flex   justify-end  gap-2">
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm" onclick='editAssignment(${JSON.stringify(item)})' title="Edit">
                                    <i class="bi bi-pencil-fill text-primary"></i>
                                </button>
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm" onclick="deleteAssignment(${item.id})" title="Delete">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        function editAssignment(data) {
            const form = document.getElementById('editAssignmentForm');
            form.action = `/api/v1/teacher-subjects/${data.id}`;
            App.populateForm(form, data);

            // Trigger dropdown loads with selected values
            App.loadOptions('/api/v1/classes', 'editAssignmentClassSelect', data.class_id, 'id', (item) =>
                `${item.name} (${item.section?.name || 'Main'})`);
            App.loadOptions('/api/v1/subjects', 'editAssignmentSubjectSelect', data.subject_id);
            App.loadOptions('/api/v1/teachers', 'editAssignmentTeacherSelect', data.teacher_id, 'id', (item) => item.user
                ?.name || 'N/A');

            const modal = new bootstrap.Modal(document.getElementById('editAssignmentModal'));
            modal.show();
        }

        function deleteAssignment(id) {
            App.deleteItem('/api/v1/teacher-subjects/' + id, reloadAssignments);
        }
    </script>
@endsection
