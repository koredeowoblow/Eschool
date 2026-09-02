@extends('layouts.app')

@section('title', 'Manage Assignments')
@section('header_title', 'Assignments')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="assignmentSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all "
                placeholder="Search assignments..." oninput="reloadAssignments()">
        </div>

        @hasrole('super_admin|School Admin|Teacher')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium requires-session-lock"
                onclick="App.resetForm(document.forms['createAssignmentForm']);" data-bs-toggle="modal"
                data-bs-target="#createAssignmentModal">
                <i class="bi bi-plus-lg me-1"></i> New Assignment
            </button>
        @endhasrole
    </div>

    <!-- Assignments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assignmentsTableBody">
                        <!-- Loaded by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createAssignmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createAssignmentForm" action="/api/v1/assignments" method="POST"
                    onsubmit="App.submitForm(event, reloadAssignments, 'assignment', 'createAssignmentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Create Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Assignment Title</label>
                            <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required
                                placeholder="Checking understanding of Algebra">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Class</label>
                                <select id="class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" name="class_id">
                                    <option value="">Select class</option>
                                </select>

                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject</label>
                                <select id="subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" name="subject_id" disabled>
                                    <option value="">Select subject</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Due Date</label>
                                <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="active">Active</option>
                                    <option value="draft">Draft</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                            <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Create Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editAssignmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editAssignmentForm" method="POST"
                    onsubmit="App.submitForm(event, reloadAssignments, 'assignment', 'editAssignmentModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Assignment Title</label>
                            <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Class</label>
                                <select name="class_room_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    {{-- @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject</label>
                                <select name="subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    {{-- @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Due Date</label>
                                <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="active">Active</option>
                                    <option value="draft">Draft</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                            <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="3"></textarea>
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
    <!-- Submission Modal -->
    <div class="modal fade" id="submitAssignmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="submissionForm" action="/api/v1/assignment-submissions" method="POST"
                    onsubmit="App.submitForm(event, reloadAssignments, 'assignmentSubmission', 'submitAssignmentModal')"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Submit Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="assignment_id" id="submission_assignment_id">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6  font-bold" id="submission_title_label"></label>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Your Answer / Notes</label>
                            <textarea name="answer" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="4" placeholder="Type your answer here..."></textarea>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Attach File (Optional)</label>
                            <input type="file" name="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <div class="form-text small">Max size: 5MB. Formats: PDF, Docx, Images.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.renderTable('/api/v1/assignments', 'assignmentsTableBody', 'assignment');
        });

        function reloadAssignments() {
            const query = document.getElementById('assignmentSearch').value;
            App.renderTable('/api/v1/assignments?search=' + query, 'assignmentsTableBody', 'assignment');
        }

        function editAssignment(data) {
            const form = document.getElementById('editAssignmentForm');
            form.action = `/api/v1/assignments/${data.id}`;
            App.populateForm(form, data);
            const modal = new bootstrap.Modal(document.getElementById('editAssignmentModal'));
            modal.show();
        }

        // Student Submission
        function openSubmissionModal(assignment) {
            document.getElementById('submission_assignment_id').value = assignment.id;
            document.getElementById('submission_title_label').textContent = `Submitting: ${assignment.title}`;
            const modal = new bootstrap.Modal(document.getElementById('submitAssignmentModal'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('createAssignmentModal');
            if (modal) { // Only if teacher
                const classSelect = document.getElementById('class_id');
                const subjectSelect = document.getElementById('subject_id');

                modal.addEventListener('show.bs.modal', function() {
                    App.loadOptions('/api/v1/classes', 'class_id');
                    // Reset subject
                    subjectSelect.innerHTML = '<option value="">Select Class First</option>';
                    subjectSelect.disabled = true;
                });

                classSelect.addEventListener('change', function() {
                    const classId = this.value;
                    if (!classId) {
                        subjectSelect.innerHTML = '<option value="">Select Class First</option>';
                        subjectSelect.disabled = true;
                        return;
                    }
                    App.loadOptions(`/api/v1/subjects?class_id=${classId}`, 'subject_id', 'id', 'name',
                        'Select Subject');
                });
            }
        });
    </script>
@endsection
