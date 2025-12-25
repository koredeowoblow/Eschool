@extends('layouts.app')

@section('title', 'Manage Grades')
@section('header_title', 'Grades')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="gradeSearch" class="form-control border-start-0 ps-0" placeholder="Search grades..."
                oninput="reloadGrades()">
        </div>

        @hasrole('super_admin|school_admin')
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createGradeForm']);"
                data-bs-toggle="modal" data-bs-target="#createGradeModal">
                <i class="bi bi-plus-lg me-1"></i> Add Grade
            </button>
        @endhasrole
    </div>

    <!-- Grades Table -->
    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Grade Name</th>
                            <th class="text-end">Actions</th>
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
                        <h5 class="modal-title fw-bold">Add Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Grade Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="e.g. Grade 1, Senior High 1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Grade</button>
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
                        <h5 class="modal-title fw-bold">Edit Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Grade Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Grade</button>
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
