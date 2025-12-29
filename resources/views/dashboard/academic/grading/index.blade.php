@extends('layouts.app')

@section('title', 'Grading System')
@section('header_title', 'Grading System')

@section('content')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">

        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text"
                   id="gradingSearch"
                   class="form-control border-start-0 ps-0"
                   placeholder="Search grading records..."
                   oninput="reloadGradingSystem()">
        </div>

        <div class="d-flex gap-2 align-items-center">

            <div id="schoolSelectorRow" style="display:none; min-width:250px;">
                <select class="form-select border-warning"
                        id="schoolSelect"
                        onchange="handleSchoolChange(this.value)">
                </select>
            </div>

            @hasrole('super_admin|School Admin')
                <button type="button"
                        class="btn btn-primary-premium requires-session-lock"
                        onclick="App.resetForm(document.forms['createGradeForm']);"
                        data-bs-toggle="modal"
                        data-bs-target="#createGradeModal">
                    <i class="bi bi-plus-lg me-1"></i>
                    New Grade Scale
                </button>
            @endhasrole

        </div>
    </div>

    <!-- Grading Table -->
    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Score Range</th>
                            <th>Remark</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="gradingTableBody">
                        <!-- Loaded by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Grade Modal -->
    <div class="modal fade" id="createGradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createGradeForm"
                      action="/api/v1/grading-system"
                      method="POST"
                      onsubmit="App.submitForm(event, reloadGradingSystem, 'grading', 'createGradeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Grade Name</label>
                            <input type="text" name="grade" class="form-control" required placeholder="A, B, C">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Min Score</label>
                                <input type="number" name="min_score" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Score</label>
                                <input type="number" name="max_score" class="form-control" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Remark</label>
                            <input type="text" name="remark" class="form-control">
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        App.renderTable('/api/v1/grading-scales', 'gradingTableBody', 'grading');
    });

    function reloadGradingSystem() {
        const q = document.getElementById('gradingSearch').value;
        App.renderTable('/api/v1/grading-scales?search=' + q, 'gradingTableBody', 'grading');
    }

    function editGrade(data) {
        const form = document.getElementById('editGradeForm');
        form.action = `/api/v1/grading-scales/${data.id}`;
        App.populateForm(form, data);
        new bootstrap.Modal(document.getElementById('editGradeModal')).show();
    }

    function handleSchoolChange(id) {
        // keep your existing logic hook here
    }
</script>
@endsection
