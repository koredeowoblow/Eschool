@extends('layouts.app')

@section('title', 'Manage Subjects')
@section('header_title', 'Subjects')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="subjectSearch" class="form-control border-start-0 ps-0" placeholder="Search subjects..."
                oninput="reloadSubjects()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="btn btn-primary-premium requires-session-lock"
                onclick="App.resetForm(document.forms['createSubjectForm']);" data-bs-toggle="modal"
                data-bs-target="#createSubjectModal">
                <i class="bi bi-plus-lg me-1"></i> Add Subject
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Subject Name</th>
                            {{-- <th>Code</th> --}}
                            <th class="text-end">Actions</th>
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
                        <h5 class="modal-title fw-bold">Add Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" placeholder="e.g. MTH101"
                                    required>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Subject</button>
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
                        <h5 class="modal-title fw-bold">Edit Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" required>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Subject</button>
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
