@extends('layouts.app')

@section('title', 'Lesson Notes')
@section('header_title', 'Lesson Notes')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="lessonNoteSearch" class="form-control border-start-0 ps-0"
                placeholder="Search lesson notes..." oninput="reloadLessonNotes()">
        </div>

        @hasrole('super_admin|School Admin|Teacher')
            <button type="button" class="btn btn-primary-premium requires-session-lock"
                onclick="App.resetForm(document.forms['createLessonNoteForm']);" data-bs-toggle="modal"
                data-bs-target="#createLessonNoteModal">
                <i class="bi bi-plus-lg me-1"></i> New Lesson Note
            </button>
        @endhasrole
    </div>

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="lessonNotesTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createLessonNoteModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form name="createLessonNoteForm" action="/api/v1/lesson-notes" method="POST"
                    onsubmit="App.submitForm(event, reloadLessonNotes, 'lessonNote', 'createLessonNoteModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create Lesson Note</h5>
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
                                <select name="class_room_id" id="create_lesson_note_class_id" class="form-select" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <select name="subject_id" id="create_lesson_note_subject_id" class="form-select" required>
                                    <option value="">Select Subject</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Lesson Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editLessonNoteModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editLessonNoteForm" method="POST"
                    onsubmit="App.submitForm(event, reloadLessonNotes, 'lessonNote', 'editLessonNoteModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Lesson Note</h5>
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
                                <select name="class_room_id" id="edit_lesson_note_class_id" class="form-select" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <select name="subject_id" id="edit_lesson_note_subject_id" class="form-select" required>
                                    <option value="">Select Subject</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Lesson Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            App.renderTable('/api/v1/lesson-notes', 'lessonNotesTableBody', 'lessonNote');

            const createModal = document.getElementById('createLessonNoteModal');
            createModal.addEventListener('show.bs.modal', () => {
                // Signature: url, elementId, selectedId, valueKey, labelKey
                App.loadOptions('/api/v1/classes', 'create_lesson_note_class_id', null, 'id', (c) =>
                    `${c.grade?.name} ${c.section?.name || ''}`);
                App.loadOptions('/api/v1/subjects', 'create_lesson_note_subject_id', null);
            });

            const editModal = document.getElementById('editLessonNoteModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/classes', 'edit_lesson_note_class_id', null, 'id', (c) =>
                    `${c.grade?.name} ${c.section?.name || ''}`);
                App.loadOptions('/api/v1/subjects', 'edit_lesson_note_subject_id', null);
            });
        });

        function reloadLessonNotes() {
            const query = document.getElementById('lessonNoteSearch').value;
            App.renderTable('/api/v1/lesson-notes?search=' + encodeURIComponent(query), 'lessonNotesTableBody',
                'lessonNote');
        }

        function editLessonNote(data) {
            const form = document.getElementById('editLessonNoteForm');
            form.action = `/api/v1/lesson-notes/${data.id}`;
            App.populateForm(form, data);
            const modal = new bootstrap.Modal(document.getElementById('editLessonNoteModal'));
            modal.show();
        }

        function deleteLessonNote(id) {
            App.deleteItem(`/api/v1/lesson-notes/${id}`, reloadLessonNotes);
        }
    </script>
@endsection
