@extends('layouts.app')

@section('title', 'Academic Reports')
@section('header_title', 'Academic Performance & Collation')

@section('content')
    <div class="row g-4">
        <!-- Selection Sidebar -->
        <div class="col-md-4">
            <div class="card-premium p-4">
                <h5 class="mb-3">Report Scope</h5>
                <form id="reportScopeForm">
                    <div class="mb-3">
                        <label class="form-label">Academic Session</label>
                        <select class="form-select" name="session_id" id="sessionSelect" required>
                            <option value="">Select Session</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Term</label>
                        <select class="form-select" name="term_id" id="termSelect" required>
                            <option value="">Select Term</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Class</label>
                        <select class="form-select" name="class_id" id="classSelect" required>
                            <option value="">Select Class</option>
                        </select>
                    </div>
                    <hr>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="btnViewBroadsheet">
                            <i class="bi bi-table me-2"></i>View Broadsheet
                        </button>
                        <button type="button" class="btn btn-soft-info" id="btnCollate">
                            <i class="bi bi-cpu me-2"></i>Collate Results
                        </button>
                    </div>
                </form>
            </div>

            <!-- Missing Marks Alert Panel -->
            <div id="missingMarksPanel" class="mt-4 d-none">
                <div class="alert alert-warning border-0 shadow-sm">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                        <h6 class="mb-0">Missing Subject Results</h6>
                    </div>
                    <p class="small mb-2">The following students are missing approved marks for certain subjects:</p>
                    <div id="missingMarksList" class="small" style="max-height: 200px; overflow-y: auto;">
                        <!-- List populated via JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Display Area -->
        <div class="col-md-8">
            <div class="card-premium p-4 h-100 min-vh-50 d-flex align-items-center justify-content-center" id="emptyState">
                <div class="text-center text-muted">
                    <i class="bi bi-file-earmark-spreadsheet display-1 opacity-25"></i>
                    <p class="mt-3">Select a class, session, and term to view academic results.</p>
                </div>
            </div>

            <div class="card-premium p-4 d-none" id="broadsheetPanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0" id="broadsheetTitle">Academic Broadsheet</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-soft-primary" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle custom-table" id="broadsheetTable">
                        <thead class="bg-light">
                            <!-- Populated via JS -->
                        </thead>
                        <tbody>
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Load Dropdown Options
            App.loadOptions('/api/v1/school-sessions', 'sessionSelect', null, 'id', 'name', 'Select Session');
            App.loadOptions('/api/v1/classes', 'classSelect', null, 'id', 'name', 'Select Class');

            $('#sessionSelect').on('change', function() {
                const sessionId = $(this).val();
                if (sessionId) {
                    App.loadOptions(`/api/v1/terms?session_id=${sessionId}`, 'termSelect', null, 'id',
                        'name', 'Select Term');
                } else {
                    $('#termSelect').html('<option value="">Select Term</option>');
                }
            });

            // Handle Broadsheet View
            $('#reportScopeForm').on('submit', function(e) {
                e.preventDefault();
                loadBroadsheet();
            });

            // Handle Collation
            $('#btnCollate').on('click', function() {
                const data = {
                    session_id: $('#sessionSelect').val(),
                    term_id: $('#termSelect').val(),
                    class_id: $('#classSelect').val()
                };

                if (!data.session_id || !data.term_id || !data.class_id) {
                    Swal.fire('Error', 'Please select all report parameters.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Collate Results?',
                    text: "This will aggregate all approved assessment marks into subject totals.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Collate',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch('/api/v1/reports/collate', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content'),
                                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                                },
                                body: JSON.stringify(data)
                            })
                            .then(response => {
                                if (!response.ok) throw new Error(response.statusText);
                                return response.json();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`);
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        const res = result.value;
                        if (res.status === 'success') {
                            Swal.fire('Success', res.message, 'success');
                            loadBroadsheet();
                            checkMissingMarks();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    }
                });
            });

            function loadBroadsheet() {
                const session = $('#sessionSelect').val();
                const term = $('#termSelect').val();
                const classId = $('#classSelect').val();

                $('#emptyState').addClass('d-none');
                $('#broadsheetPanel').removeClass('d-none');

                App.get(`/reports/broadsheet?session_id=${session}&term_id=${term}&class_id=${classId}`, function(
                    res) {
                    if (res.status === 'success') {
                        renderBroadsheet(res.data);
                    }
                });
            }

            function renderBroadsheet(results) {
                const $thead = $('#broadsheetTable thead').empty();
                const $tbody = $('#broadsheetTable tbody').empty();

                if (results.length === 0) {
                    $tbody.append(
                        '<tr><td colspan="100%" class="text-center p-5">No collated results found for this selection. Click "Collate" to generate.</td></tr>'
                    );
                    return;
                }

                // Group by student
                const studentGroups = {};
                const subjects = new Set();

                results.forEach(item => {
                    const studentName = item.student.user.name;
                    if (!studentGroups[studentName]) studentGroups[studentName] = {};
                    studentGroups[studentName][item.subject.name] = item;
                    subjects.add(item.subject.name);
                });

                const sortedSubjects = Array.from(subjects).sort();

                // Header
                let headerHtml = '<tr><th>Student Name</th>';
                sortedSubjects.forEach(sub => {
                    headerHtml += `<th class="text-center">${sub}</th>`;
                });
                headerHtml +=
                    '<th class="text-center font-bold">Total</th><th class="text-center">Avg</th><th class="text-center">Status</th></tr>';
                $thead.append(headerHtml);

                // Rows
                Object.keys(studentGroups).forEach(student => {
                    let rowHtml = `<tr><td class="font-bold">${student}</td>`;
                    let total = 0;
                    let count = 0;
                    let hasFailed = false;

                    sortedSubjects.forEach(sub => {
                        const res = studentGroups[student][sub];
                        if (res) {
                            const score = parseFloat(res.total_score);
                            rowHtml += `<td class="text-center">
                                <span class="d-block font-bold">${score}</span>
                                <small class="text-muted">${res.grade}</small>
                            </td>`;
                            total += score;
                            count++;
                            if (res.status === 'Fail') hasFailed = true;
                        } else {
                            rowHtml += '<td class="text-center text-muted">-</td>';
                        }
                    });

                    const avg = count > 0 ? (total / count).toFixed(1) : 0;
                    const statusClass = hasFailed ? 'bg-soft-danger text-danger' :
                        'bg-soft-success text-success';

                    rowHtml += `<td class="text-center font-bold">${total}</td>`;
                    rowHtml += `<td class="text-center">${avg}%</td>`;
                    rowHtml +=
                        `<td class="text-center"><span class="badge ${statusClass}">${hasFailed ? 'Fail' : 'Pass'}</span></td></tr>`;
                    $tbody.append(rowHtml);
                });
            }

            function checkMissingMarks() {
                const session = $('#sessionSelect').val();
                const term = $('#termSelect').val();
                const classId = $('#classSelect').val();

                App.get(`/reports/missing?session_id=${session}&term_id=${term}&class_id=${classId}`, function(
                    res) {
                    if (res.status === 'success' && res.data.length > 0) {
                        $('#missingMarksPanel').removeClass('d-none');
                        const $list = $('#missingMarksList').empty();
                        res.data.forEach(item => {
                            $list.append(
                                `<div class="mb-1">• ${item.student} (${item.subject})</div>`);
                        });
                    } else {
                        $('#missingMarksPanel').addClass('d-none');
                    }
                });
            }
        });
    </script>
@endsection
