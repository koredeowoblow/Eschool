@extends('layouts.app')

@section('title', 'Lesson Notes')
@section('header_title', 'Lesson Notes')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="lessonNoteSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all "
                placeholder="Search lesson notes..." oninput="reloadLessonNotes()">
        </div>

        @hasrole('super_admin|School Admin|Teacher')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium requires-session-lock"
                onclick="App.resetForm(document.forms['createLessonNoteForm']);" data-bs-toggle="modal"
                data-bs-target="#createLessonNoteModal">
                <i class="bi bi-plus-lg me-1"></i> New Lesson Note
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th class="text-right">Actions</th>
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
                        <h5 class="modal-title font-bold">Create Lesson Note</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Title</label>
                            <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Class</label>
                                <select name="class_room_id" id="create_lesson_note_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject</label>
                                <select name="subject_id" id="create_lesson_note_subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Subject</option>
                                </select>
                            </div>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Date</label>
                            <input type="date" name="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Content</label>
                            <textarea name="content" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Lesson Note</button>
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
                        <h5 class="modal-title font-bold">Edit Lesson Note</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Title</label>
                            <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Class</label>
                                <select name="class_room_id" id="edit_lesson_note_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Subject</label>
                                <select name="subject_id" id="edit_lesson_note_subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Select Subject</option>
                                </select>
                            </div>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Date</label>
                            <input type="date" name="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Content</label>
                            <textarea name="content" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Lesson Note</button>
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
