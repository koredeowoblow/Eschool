@extends('layouts.app')

@section('title', 'Manage Sections')
@section('header_title', 'Sections')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="sectionSearch" class="form-control border-start-0 ps-0" placeholder="Search sections..."
                oninput="reloadSections()">
        </div>

        @hasrole('super_admin|school_admin')
            <button type="button" class="btn btn-primary-premium requires-session-lock"
                onclick="App.resetForm(document.forms['createSectionForm']);" data-bs-toggle="modal"
                data-bs-target="#createSectionModal">
                <i class="bi bi-plus-lg me-1"></i> Add Section
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Section Name</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sectionsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createSectionForm" action="/api/v1/sections" method="POST"
                    onsubmit="App.submitForm(event, reloadSections, 'section', 'createSectionModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Section Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="e.g. A, B, Morning, Evening">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSectionForm" method="POST"
                    onsubmit="App.submitForm(event, reloadSections, 'section', 'editSectionModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Section Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadSections();
        });

        function reloadSections() {
            const query = document.getElementById('sectionSearch').value;
            App.renderTable('/api/v1/sections?search=' + encodeURIComponent(query), 'sectionsTableBody', 'section');
        }

        function editSection(data) {
            const form = document.getElementById('editSectionForm');
            form.action = `/api/v1/sections/${data.id}`;
            App.populateForm(form, data);
            const modal = new bootstrap.Modal(document.getElementById('editSectionModal'));
            modal.show();
        }
    </script>
@endsection
