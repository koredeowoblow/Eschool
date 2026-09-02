@extends('layouts.app')

@section('title', 'Attachments')
@section('header_title', 'Attachments')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="attachmentSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all "
                placeholder="Search attachments..." oninput="reloadAttachments()">
        </div>

        @hasrole('super_admin|School Admin|Teacher')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium"
                onclick="App.resetForm(document.forms['createAttachmentForm']);" data-bs-toggle="modal"
                data-bs-target="#createAttachmentModal">
                <i class="bi bi-plus-lg me-1"></i> New Attachment
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Related To</th>
                            <th class="text-right">Actions</th>
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
                <form name="createAttachmentForm" action="/api/v1/attachments" method="POST" enctype="multipart/form-data"
                    onsubmit="App.submitForm(event, reloadAttachments, 'attachment', 'createAttachmentModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Upload Attachment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">File</label>
                            <input type="file" name="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-12 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Title / Description</label>
                                <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                    placeholder="e.g. Weekly Lesson Plan" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Class (Optional)</label>
                                <select name="class_id" id="create_attachment_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject (Optional)</label>
                                <select name="subject_id" id="create_attachment_subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">Select Subject</option>
                                </select>
                            </div>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Category</label>
                            <input type="text" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                placeholder="e.g. Lesson Note, Assignment">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal (metadata only) -->
    <div class="modal fade" id="editAttachmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editAttachmentForm" method="POST"
                    onsubmit="App.submitForm(event, reloadAttachments, 'attachment', 'editAttachmentModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Attachment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Type</label>
                                <input type="text" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Related ID</label>
                                <input type="number" name="related_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Attachment</button>
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

        const createModal = document.getElementById('createAttachmentModal');
        createModal.addEventListener('show.bs.modal', () => {
            App.loadOptions('/api/v1/classes', 'create_attachment_class_id');
            App.loadOptions('/api/v1/subjects', 'create_attachment_subject_id');
        });

        function reloadAttachments() {
            const query = document.getElementById('attachmentSearch').value;
            App.renderTable('/api/v1/attachments?search=' + encodeURIComponent(query), 'attachmentsTableBody',
                'attachment');
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
