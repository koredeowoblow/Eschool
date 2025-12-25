@extends('layouts.app')

@section('title', 'Assessments')
@section('header_title', 'Assessments')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="assessmentSearch" class="form-control border-start-0 ps-0"
                placeholder="Search assessments..." oninput="reloadAssessments()">
        </div>

        @hasrole('super_admin|school_admin|teacher')
            <button type="button" class="btn btn-primary-premium requires-session-lock"
                onclick="App.resetForm(document.forms['createAssessmentForm']);" data-bs-toggle="modal"
                data-bs-target="#createAssessmentModal">
                <i class="bi bi-plus-lg me-1"></i> New Assessment
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th class="sortable-header" data-sort="title">Title</th>
                            <th class="sortable-header" data-sort="grade_id">Class</th>
                            <th class="sortable-header" data-sort="term_id">Term</th>
                            <th class="sortable-header" data-sort="max_score">Max Score</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assessmentsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createAssessmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createAssessmentForm" action="/api/v1/assessments" method="POST"
                    onsubmit="App.submitForm(event, reloadAssessments, 'assessment', 'createAssessmentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create Assessment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Class</label>
                                <select name="class_room_id" id="create_assessment_class_id" class="form-select" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Term</label>
                                <select name="term_id" id="create_assessment_term_id" class="form-select" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maximum Score</label>
                            <input type="number" name="max_score" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Assessment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editAssessmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editAssessmentForm" method="POST"
                    onsubmit="App.submitForm(event, reloadAssessments, 'assessment', 'editAssessmentModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Assessment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Class</label>
                                <select name="class_room_id" id="edit_assessment_class_id" class="form-select" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Term</label>
                                <select name="term_id" id="edit_assessment_term_id" class="form-select" required>
                                    <option value="">Select Term</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maximum Score</label>
                            <input type="number" name="max_score" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Assessment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.renderTable('/api/v1/assessments', 'assessmentsTableBody', 'assessment');

            // Load options when Create Modal opens
            const createModal = document.getElementById('createAssessmentModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/classes', 'create_assessment_class_id', null, 'id', (c) =>
                    `${c.grade?.name} (${c.section?.name || 'Main'})`, 'Select Class');
                App.loadOptions('/api/v1/terms', 'create_assessment_term_id', null, 'id', 'name',
                    'Select Term');
            });

            // Load options when Edit Modal opens
            const editModal = document.getElementById('editAssessmentModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/classes', 'edit_assessment_class_id', null, 'id', (c) =>
                    `${c.grade?.name} (${c.section?.name || 'Main'})`, 'Select Class');
                App.loadOptions('/api/v1/terms', 'edit_assessment_term_id', null, 'id', 'name',
                    'Select Term');
            });
        });

        function reloadAssessments() {
            const query = document.getElementById('assessmentSearch').value;
            App.renderTable('/api/v1/assessments?search=' + encodeURIComponent(query), 'assessmentsTableBody', (item) => {
                return App.safeHTML`
                    <tr>
                        <td class="fw-bold text-dark">${item.title}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border-0 shadow-sm rounded-pill px-3">
                                ${item.class_room?.grade?.name} ${item.class_room?.section?.name || ''}
                            </span>
                        </td>
                        <td><span class="text-muted">${item.term?.name}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-star-fill text-warning" style="font-size: 0.8rem;"></i>
                                <span class="fw-bold text-dark">${item.max_score}</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-light shadow-sm btn-sm" onclick='editAssessment(${JSON.stringify(item)})' title="Edit">
                                    <i class="bi bi-pencil-fill text-primary"></i>
                                </button>
                                <button class="btn btn-light shadow-sm btn-sm" onclick="deleteAssessment(${item.id})" title="Delete">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        function editAssessment(data) {
            const form = document.getElementById('editAssessmentForm');
            form.action = `/api/v1/assessments/${data.id}`;
            App.populateForm(form, data);
            const modal = new bootstrap.Modal(document.getElementById('editAssessmentModal'));
            modal.show();
        }

        function deleteAssessment(id) {
            App.deleteItem(`/api/v1/assessments/${id}`, reloadAssessments);
        }
    </script>
@endsection
