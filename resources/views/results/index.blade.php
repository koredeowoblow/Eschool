@extends('layouts.app')

@section('title', 'Results')
@section('header_title', 'Results')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <select class="form-select" id="filter_session_id" onchange="reloadResults()">
                <option value="">All Sessions</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="filter_term_id" onchange="reloadResults()">
                <option value="">All Terms</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="filter_class_id" onchange="reloadResults()">
                <option value="">All Classes</option>
            </select>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="resultSearch" class="form-control border-start-0 ps-0"
                    placeholder="Student name..." oninput="reloadResults()">
            </div>
        </div>
    </div>

    @hasrole('super_admin|school_admin|teacher')
        <div class="d-flex justify-content-end mb-4">
            <button type="button" class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createResultForm']);"
                data-bs-toggle="modal" data-bs-target="#createResultModal">
                <i class="bi bi-plus-lg me-1"></i> Enter Results
            </button>
        </div>
    @endhasrole

    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th class="sortable-header" data-sort="student_id">Student</th>
                            <th>Assessment/Subject</th>
                            <th>Class/Term</th>
                            <th class="sortable-header" data-sort="marks">Marks</th>
                            <th class="sortable-header" data-sort="grade">Grade</th>
                            <th class="text-end">Actions</th>
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
                        <h5 class="modal-title fw-bold">Enter Student Result</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <select name="student_id" id="result_student_id" class="form-select" required>
                                <option value="">Select Student</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assessment</label>
                            <select name="assessment_id" id="result_assessment_id" class="form-select" required>
                                <option value="">Select Assessment</option>
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Marks Obtained</label>
                                <input type="number" name="marks_obtained" class="form-control" required min="0"
                                    max="100">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Grade</label>
                                <select name="grade" id="gradeSelectResult" class="form-select" required>
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Remark</label>
                            <textarea name="remark" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save Result</button>
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
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(item.student?.full_name || 'Student')}&background=random&color=fff" class="avatar-sm rounded-circle me-3 shadow-sm">
                                <div>
                                    <div class="fw-bold text-dark">${item.student?.full_name}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">${item.student?.admission_number}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">${item.assessment?.title || 'N/A'}</div>
                            <small class="text-muted">${item.assessment?.type || ''}</small>
                        </td>
                        <td>
                            <div>${item.assessment?.class_room?.name || 'N/A'}</div>
                            <small class="text-muted">${item.assessment?.term?.name || ''}</small>
                        </td>
                        <td>
                            <span class="fw-bold">${item.marks_obtained}</span>
                            <small class="text-muted">/ ${item.assessment?.total_marks || 100}</small>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-${gradeInfo.color}-subtle text-${gradeInfo.color} px-3">${item.grade}</span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-light shadow-sm btn-sm" onclick="contactGuardian('${item.student?.guardians?.[0]?.user_id || ''}', '${item.student?.guardians?.[0]?.user?.name || ''}')" title="Contact Guardian">
                                    <i class="bi bi-chat-dots-fill text-primary"></i>
                                </button>
                                <button class="btn btn-light shadow-sm btn-sm" onclick="App.deleteItem('/api/v1/results/${item.id}', reloadResults)" title="Delete">
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
