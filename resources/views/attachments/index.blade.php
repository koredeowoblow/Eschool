@extends('layouts.app')

@section('title', 'Attachments')
@section('header_title', 'Attachments')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="attachmentSearch" class="form-control border-start-0 ps-0" placeholder="Search attachments..." oninput="reloadAttachments()">
        </div>

        @hasrole('super_admin|school_admin|teacher')
        <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createAttachmentForm']);" data-bs-toggle="modal" data-bs-target="#createAttachmentModal">
            <i class="bi bi-plus-lg me-1"></i> New Attachment
        </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Related To</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="attachmentsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createAttachmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createAttachmentForm" action="/api/v1/attachments" method="POST" enctype="multipart/form-data" onsubmit="App.submitForm(event, reloadAttachments, 'attachment', 'createAttachmentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Upload Attachment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <input type="text" name="type" class="form-control" placeholder="e.g. assignment, lesson_note">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Related ID</label>
                                <input type="number" name="related_id" class="form-control" placeholder="Related record ID">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal (metadata only) -->
    <div class="modal fade" id="editAttachmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editAttachmentForm" method="POST" onsubmit="App.submitForm(event, reloadAttachments, 'attachment', 'editAttachmentModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Attachment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <input type="text" name="type" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Related ID</label>
                                <input type="number" name="related_id" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Attachment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        App.renderTable('/api/v1/attachments', 'attachmentsTableBody', 'attachment');
    });

    function reloadAttachments() {
        const query = document.getElementById('attachmentSearch').value;
        App.renderTable('/api/v1/attachments?search=' + encodeURIComponent(query), 'attachmentsTableBody', 'attachment');
    }

    function editAttachment(data) {
        const form = document.getElementById('editAttachmentForm');
        form.action = `/api/v1/attachments/${data.id}`;
        App.populateForm(form, data);
        const modal = new bootstrap.Modal(document.getElementById('editAttachmentModal'));
        modal.show();
    }

    function deleteAttachment(id) {
        App.deleteItem(`/api/v1/attachments/${id}`, reloadAttachments);
    }
</script>
@endsection
