@extends('layouts.app')

@section('title', 'Manage Fee Types')
@section('header_title', 'Fee Types')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="feeTypeSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " placeholder="Search fee types..."
                oninput="reloadFeeTypes()">
        </div>

        @hasrole('super_admin|School Admin')
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium requires-session-lock"
                onclick="App.resetForm(document.forms['createFeeTypeForm']);" data-bs-toggle="modal"
                data-bs-target="#createFeeTypeModal">
                <i class="bi bi-plus-lg me-1"></i> Add Fee Type
            </button>
        @endhasrole
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left divide-y divide-gray-200 align-middle  mb-6 ">
                    <thead>
                        <tr>
                            <th>Fee Type</th>
                            <th>Amount</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="feeTypesTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createFeeTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createFeeTypeForm" action="/api/v1/fee-types" method="POST"
                    onsubmit="App.submitForm(event, reloadFeeTypes, 'fee-type', 'createFeeTypeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Add Fee Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Fee Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required
                                placeholder="e.g. Tuition Fee, Library Fee">
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                            <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="3" placeholder="Optional description"></textarea>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required min="0" step="0.01"
                                placeholder="0.00">
                            <small class="text-gray-500">Enter the default amount for this fee type</small>
                        </div>

                        <hr class="my-4">
                        <h6 class="font-bold  mb-6 ">Assignment (Optional)</h6>
                        <p class="text-gray-500 small">Assign this fee type to specific grade, session or term. Leave blank for
                            general fees.</p>

                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Grade</label>
                            <select name="grade_id" id="gradeSelectFee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                <option value="">All Grades</option>
                            </select>
                            <small class="text-gray-500">Optional: Assign to a specific grade level</small>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Session</label>
                            <select name="session_id" id="sessionSelectFee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white"
                                onchange="loadTermsBySession('sessionSelectFee', 'termSelectFee')">
                                <option value="">All Sessions</option>
                            </select>
                            <small class="text-gray-500">Optional: Assign to a specific academic session</small>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Term</label>
                            <select name="term_id" id="termSelectFee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                <option value="">All Terms</option>
                            </select>
                            <small class="text-gray-500">Optional: Assign to a specific term (select session first)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Fee Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editFeeTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFeeTypeForm" method="POST"
                    onsubmit="App.submitForm(event, reloadFeeTypes, 'fee-type', 'editFeeTypeModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Fee Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Fee Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Description</label>
                            <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="3"></textarea>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required min="0"
                                step="0.01">

                            <hr class="my-4">
                            <h6 class="font-bold  mb-6 ">Assignment (Optional)</h6>

                            <div class=" mb-6 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Grade</label>
                                <select name="grade_id" id="editGradeSelectFee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">All Grades</option>
                                </select>
                            </div>
                            <div class=" mb-6 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Session</label>
                                <select name="session_id" id="editSessionSelectFee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white"
                                    onchange="loadTermsBySession('editSessionSelectFee', 'editTermSelectFee')">
                                    <option value="">All Sessions</option>
                                </select>
                            </div>
                            <div class=" mb-6 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Term</label>
                                <select name="term_id" id="editTermSelectFee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white">
                                    <option value="">All Terms</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Fee Type</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            reloadFeeTypes();

            // Initialize Create Modal Dropdowns
            const createModal = document.getElementById('createFeeTypeModal');
            createModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/grades', 'gradeSelectFee');
                App.loadOptions('/api/v1/school-sessions', 'sessionSelectFee');
            });

            // Initialize Edit Modal Dropdowns
            const editModal = document.getElementById('editFeeTypeModal');
            editModal.addEventListener('show.bs.modal', () => {
                App.loadOptions('/api/v1/grades', 'editGradeSelectFee');
                App.loadOptions('/api/v1/school-sessions', 'editSessionSelectFee');
            });
        });

        function loadTermsBySession(sessionSelectId, termSelectId) {
            const sessionId = document.getElementById(sessionSelectId).value;
            const termSelect = document.getElementById(termSelectId);

            // Clear current options
            termSelect.innerHTML = '<option value="">All Terms</option>';

            if (sessionId) {
                // Load terms for the selected session
                App.loadOptions(`/api/v1/terms?session_id=${sessionId}`, termSelectId);
            }
        }

        function reloadFeeTypes() {
            const query = document.getElementById('feeTypeSearch').value;
            const studentId = window.App?.currentStudentId || '';
            App.renderTable(`/api/v1/fee-types?search=${encodeURIComponent(query)}&student_id=${studentId}`,
                'feeTypesTableBody', 'fee-type');
        }

        function editFeeType(data) {
            const form = document.getElementById('editFeeTypeForm');
            form.action = `/api/v1/fee-types/${data.id}`;
            App.populateForm(form, data);

            // Load sessions first, then load terms if session is selected
            App.loadOptions('/api/v1/school-sessions', 'editSessionSelectFee').then(() => {
                if (data.session_id) {
                    loadTermsBySession('editSessionSelectFee', 'editTermSelectFee');
                }
            });

            const modal = new bootstrap.Modal(document.getElementById('editFeeTypeModal'));
            modal.show();
        }
    </script>
@endsection
