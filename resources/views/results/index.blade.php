@extends('layouts.app')

@section('title', 'Results')
@section('header_title', 'Results')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
        <div class=" md:col-span-3 col-span-1 ">
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="filter_session_id" onchange="reloadResults()">
                <option value="">All Sessions</option>
            </select>
        </div>
        <div class=" md:col-span-3 col-span-1 ">
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="filter_term_id" onchange="reloadResults()">
                <option value="">All Terms</option>
            </select>
        </div>
        <div class=" md:col-span-3 col-span-1 ">
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="filter_class_id" onchange="reloadResults()">
                <option value="">All Classes</option>
            </select>
        </div>
        <div class=" md:col-span-3 col-span-1 ">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="resultSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all "
                    placeholder="Student name..." oninput="reloadResults()">
            </div>
        </div>
    </div>

    @hasrole('super_admin|School Admin|Teacher')
        <div class=" flex   justify-end   mb-6 ">
            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium" onclick="App.resetForm(document.forms['createResultForm']);"
                data-bs-toggle="modal" data-bs-target="#createResultModal">
                <i class="bi bi-plus-lg me-1"></i> Enter Results
            </button>
        </div>
    @endhasrole

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="sortable-header" data-sort="student_id">Student</th>
                            <th>Assessment/Subject</th>
                            <th>Class/Term</th>
                            <th class="sortable-header" data-sort="marks">Marks</th>
                            <th class="sortable-header" data-sort="grade">Grade</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Create Result Modal -->
    <div class="modal fade" id="createResultModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createResultForm" action="/api/v1/results" method="POST"
                    onsubmit="App.submitForm(event, reloadResults, 'result', 'createResultModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Enter Student Result</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Student</label>
                            <select name="student_id" id="result_student_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Student</option>
                            </select>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Assessment</label>
                            <select name="assessment_id" id="result_assessment_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                <option value="">Select Assessment</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class="col-6">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Marks Obtained</label>
                                <input type="number" name="marks_obtained" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required min="0"
                                    max="100">
                            </div>
                            <div class="col-6">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Grade</label>
                                <select name="grade" id="gradeSelectResult" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" required>
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                        </div>
                        <div class=" mb-6  mt-4">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Remark</label>
                            <textarea name="remark" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Save Result</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Load Filter Options
            App.loadOptions('/api/v1/school-sessions', 'filter_session_id', null, 'id', 'name', 'All Sessions');
            App.loadOptions('/api/v1/classes', 'filter_class_id', null, 'id', 'name', 'All Classes');

            $('#filter_session_id').on('change', function() {
                const sessionId = $(this).val();
                if (sessionId) {
                    App.loadOptions(`/api/v1/terms?session_id=${sessionId}`, 'filter_term_id', null, 'id',
                        'name',
                        'All Terms');
                } else {
                    $('#filter_term_id').html('<option value="">All Terms</option>');
                }
            });

            reloadResults();

            const modal = document.getElementById('createResultModal');
            if (modal) {
                modal.addEventListener('show.bs.modal', function() {
                    App.loadOptions('/api/v1/students', 'result_student_id', null, 'id', 'full_name',
                        'Select Student');
                    App.loadOptions('/api/v1/assessments', 'result_assessment_id', null, 'id', 'title',
                        'Select Assessment');
                    App.loadGradingOptions('gradeSelectResult');
                });
            }
        });

        function reloadResults() {
            const filters = {
                search: document.getElementById('resultSearch')?.value || '',
                session_id: document.getElementById('filter_session_id')?.value || '',
                term_id: document.getElementById('filter_term_id')?.value || '',
                class_id: document.getElementById('filter_class_id')?.value || ''
            };

            const params = new URLSearchParams(filters).toString();
            App.renderTable('/api/v1/results?' + params, 'resultsTableBody', (item) => {
                const gradeInfo = {
                    'A': {
                        color: 'success'
                    },
                    'B': {
                        color: 'info'
                    },
                    'C': {
                        color: 'warning'
                    },
                    'D': {
                        color: 'orange'
                    },
                    'E': {
                        color: 'secondary'
                    },
                    'F': {
                        color: 'danger'
                    }
                } [item.grade] || {
                    color: 'primary'
                };

                return App.safeHTML`
                    <tr>
                        <td>
                            <div class=" flex   items-center ">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(item.student?.full_name || 'Student')}&background=random&color=fff" class="avatar-sm rounded-circle me-3 shadow-sm">
                                <div>
                                    <div class="font-bold text-dark">${item.student?.full_name}</div>
                                    <small class="text-gray-500" style="font-size: 0.7rem;">${item.student?.admission_number}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="font-bold">${item.assessment?.title || 'N/A'}</div>
                            <small class="text-gray-500">${item.assessment?.type || ''}</small>
                        </td>
                        <td>
                            <div>${item.assessment?.class_room?.name || 'N/A'}</div>
                            <small class="text-gray-500">${item.assessment?.term?.name || ''}</small>
                        </td>
                        <td>
                            <span class="font-bold">${item.marks_obtained}</span>
                            <small class="text-gray-500">/ ${item.assessment?.total_marks || 100}</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-${gradeInfo.color}-subtle text-${gradeInfo.color} px-3">${item.grade}</span>
                        </td>
                        <td class="text-right">
                            <div class=" flex   justify-end  gap-2">
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm" onclick="contactGuardian('${item.student?.guardians?.[0]?.user_id || ''}', '${item.student?.guardians?.[0]?.user?.name || ''}')" title="Contact Guardian">
                                    <i class="bi bi-chat-dots-fill text-primary"></i>
                                </button>
                                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium shadow-sm px-3 py-1.5 text-sm" onclick="App.deleteItem('/api/v1/results/${item.id}', reloadResults)" title="Delete">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        function contactGuardian(userId, name) {
            if (!userId) {
                Swal.fire('Notice', 'No guardian linked to this student.', 'info');
                return;
            }
            window.location.href = `/chats?partner_id=${userId}`;
        }

        function viewResult(data) {
            // Placeholder if you later want a detailed modal/view.
        }
    </script>
@endsection
