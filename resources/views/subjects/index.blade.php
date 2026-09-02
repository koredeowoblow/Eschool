@extends('layouts.app')

@section('title', 'Manage Subjects')
@section('header_title', 'Subjects')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="subjectSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search subjects..."
                oninput="reloadSubjects()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium requires-session-lock"
                onclick="App.resetForm(document.forms['createSubjectForm']);" data-bs-toggle="modal"
                data-bs-target="#createSubjectModal">
                <i class="bi bi-plus-lg me-1"></i> Add Subject
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle  mb-6 ">
                    <thead>
                        <tr>
                            <th>Subject Name</th>
                            {{-- <th>Code</th> --}}
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="subjectsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createSubjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createSubjectForm" action="/api/v1/subjects" method="POST"
                    onsubmit="App.submitForm(event, reloadSubjects, 'subject', 'createSubjectModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Add Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" col-span-1 md:col-span-12 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="e.g. MTH101"
                                    required>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editSubjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSubjectForm" method="POST"
                    onsubmit="App.submitForm(event, reloadSubjects, 'subject', 'editSubjectModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" col-span-1 md:col-span-12 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadSubjects();
        });

        function reloadSubjects() {
            const query = document.getElementById('subjectSearch').value;
            App.renderTable('/api/v1/subjects?search=' + encodeURIComponent(query), 'subjectsTableBody', 'subject');
        }

        function editSubject(data) {
            const form = document.getElementById('editSubjectForm');
            form.action = `/api/v1/subjects/${data.id}`;
            App.populateForm(form, data);
            const modal = new bootstrap.Modal(document.getElementById('editSubjectModal'));
            modal.show();
        }
    </script>
@endsection
