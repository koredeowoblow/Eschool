@extends('layouts.app')

@section('title', 'Manage Grades')
@section('header_title', 'Grades')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="gradeSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search grades..."
                oninput="reloadGrades()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.resetForm(document.forms['createGradeForm']);"
                data-bs-toggle="modal" data-bs-target="#createGradeModal">
                <i class="bi bi-plus-lg me-1"></i> Add Grade
            </button>
        @endhasrole
    </div>

    <!-- Grades Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle  mb-6 ">
                    <thead>
                        <tr>
                            <th>Grade Name</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="gradesTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createGradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createGradeForm" action="/api/v1/grades" method="POST"
                    onsubmit="App.submitForm(event, reloadGrades, 'grade', 'createGradeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Add Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Grade Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required
                                placeholder="e.g. Grade 1, Senior High 1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Grade</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editGradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editGradeForm" method="POST"
                    onsubmit="App.submitForm(event, reloadGrades, 'grade', 'editGradeModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Grade Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Grade</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadGrades();
        });

        function reloadGrades() {
            const query = document.getElementById('gradeSearch').value;
            App.renderTable('/api/v1/grades?search=' + encodeURIComponent(query), 'gradesTableBody', 'grade');
        }

        function editGrade(data) {
            const form = document.getElementById('editGradeForm');
            form.action = `/api/v1/grades/${data.id}`;
            App.populateForm(form, data);
            const modal = new bootstrap.Modal(document.getElementById('editGradeModal'));
            modal.show();
        }
    </script>
@endsection
