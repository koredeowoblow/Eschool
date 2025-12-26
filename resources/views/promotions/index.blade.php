@extends('layouts.app')

@section('title', 'Student Promotions')
@section('header_title', 'Student Promotions')

@section('content')
    <div class="promotion-workflow">
        <!-- Step 1 & 3: Configuration Panel -->
        <div class="row g-4">
            <div class="col-xl-4 col-lg-5">
                <!-- Source Config Card -->
                <div class="card card-premium mb-4 border-0 shadow-sm overflow-hidden animate-in" style="--delay: 0.1s">
                    <div class="card-header bg-primary-subtle border-0 py-3">
                        <h6 class="card-title mb-0 fw-bold text-primary">
                            <i class="bi bi-funnel-fill me-2"></i> 1. Select Source
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted text-uppercase">Current Class</label>
                            <select id="fromClassSelect" class="form-select border-2" onchange="loadSourceStudents()">
                                <option value="">Choose class...</option>
                            </select>
                            <div id="selectionStats" class="mt-3 d-none">
                                <div class="p-2 bg-light rounded text-center">
                                    <span class="d-block small text-muted">Total Students</span>
                                    <span id="totalClassStudents" class="h5 fw-bold text-primary mb-0">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destination Config Card -->
                <div id="destinationCard" class="card card-premium border-0 shadow-sm overflow-hidden animate-in"
                    style="--delay: 0.2s; opacity: 0.5; pointer-events: none;">
                    <div class="card-header bg-success-subtle border-0 py-3">
                        <h6 class="card-title mb-0 fw-bold text-success">
                            <i class="bi bi-box-arrow-right me-2"></i> 2. Target Destination
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="promotionForm" onsubmit="handlePromotion(event)">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Next Session *</label>
                                <select name="to_session_id" id="toSessionSelect" class="form-select border-2" required>
                                    <option value="">Choose session...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Next Class *</label>
                                <select name="to_class_id" id="toClassSelect" class="form-select border-2" required>
                                    <option value="">Choose class...</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Action Type *</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="type" id="typePromote" value="promote"
                                        checked>
                                    <label class="btn btn-outline-success py-2 fw-bold" for="typePromote">
                                        <i class="bi bi-graph-up-arrow me-1"></i> Promote
                                    </label>

                                    <input type="radio" class="btn-check" name="type" id="typeRepeat" value="repeat">
                                    <label class="btn btn-outline-warning py-2 fw-bold" for="typeRepeat">
                                        <i class="bi bi-arrow-repeat me-1"></i> Repeat
                                    </label>
                                </div>
                            </div>

                            <div class="summary-card p-3 mb-4 rounded-3 d-none" id="promotionSummary">
                                <h6 class="fw-bold mb-2 small text-uppercase">Execution Summary</h6>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-primary rounded-pill me-2" id="summaryCount">0</span>
                                    <span class="small text-muted">Students selected for processing</span>
                                </div>
                                <div class="small fw-bold text-success mt-2" id="summaryActionDesc"></div>
                            </div>

                            <button type="submit" id="btnSubmitPromotion"
                                class="btn btn-primary-premium w-100 py-3 fw-bold shadow-sm" disabled>
                                <i class="bi bi-send-fill me-2"></i> Finalize Operation
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Step 2: Student Selection Panel -->
            <div class="col-xl-8 col-lg-7">
                <div class="card card-premium h-100 border-0 shadow-sm overflow-hidden animate-in" style="--delay: 0.3s">
                    <div
                        class="card-header bg-white border-bottom py-3 d-flex flex-sm-row flex-column justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center">
                            <h5 class="fw-bold mb-0 me-3 text-dark">
                                <i class="bi bi-people-fill text-primary me-2"></i> Eligible Students
                            </h5>
                            <span id="selectedCounterBadge" class="badge rounded-pill bg-primary ms-1"
                                style="display:none">0 Selected</span>
                        </div>

                        <div class="d-flex align-items-center gap-2 w-100 w-sm-auto">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="innerStudentSearch" class="form-control border-start-0 bg-light"
                                    placeholder="Filter current list...">
                            </div>
                            <div class="form-check form-switch mb-0 text-nowrap">
                                <input class="form-check-input" type="checkbox" id="selectAllStudents"
                                    onchange="toggleSelectAll(this)">
                                <label class="form-check-label small fw-bold" for="selectAllStudents">Select All</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div id="studentContainer" class="table-responsive"
                            style="max-height: 650px; min-height: 400px;">
                            <table class="table table-premium table-hover align-middle mb-0">
                                <thead class="bg-gray-50 border-bottom sticky-top">
                                    <tr>
                                        <th style="width: 50px;" class="ps-4">
                                            <i class="bi bi-check-all text-muted"></i>
                                        </th>
                                        <th>Student Details</th>
                                        <th>Admission #</th>
                                        <th class="text-center">Academic Status</th>
                                        <th class="text-end pe-4">Current Grade</th>
                                    </tr>
                                </thead>
                                <tbody id="sourceStudentsTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state py-5">
                                                <div class="mb-3 text-muted opacity-25">
                                                    <i class="bi bi-journal-text" style="font-size: 5rem;"></i>
                                                </div>
                                                <h5 class="fw-bold text-muted">No students to display</h5>
                                                <p class="text-muted small">Select a source class from the left panel to
                                                    begin.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .promotion-workflow .card-premium {
                border-radius: var(--border-radius-lg);
            }

            .summary-card {
                background: linear-gradient(45deg, var(--color-primary-50), white);
                border: 1px dashed var(--color-primary-200);
            }

            .form-select.border-2 {
                border-width: 2px !important;
                transition: border-color 0.2s;
            }

            .form-select.border-2:focus {
                border-color: var(--color-primary-500);
                box-shadow: none;
            }

            .btn-check:checked+.btn-outline-success {
                background-color: var(--color-success);
                border-color: var(--color-success);
                box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
            }

            .btn-check:checked+.btn-outline-warning {
                background-color: var(--color-warning);
                border-color: var(--color-warning);
                box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
                color: white;
            }

            .student-row-selected {
                background-color: var(--color-primary-50) !important;
            }

            .avatar-initials {
                width: 40px;
                height: 40px;
                line-height: 40px;
                font-size: 14px;
            }
        </style>
    @endpush
@endsection

@section('scripts')
    <script>
        let allCurrentStudents = [];

        document.addEventListener('DOMContentLoaded', () => {
            // Load initial options
            App.loadOptions('/api/v1/classes', 'fromClassSelect');
            App.loadOptions('/api/v1/classes', 'toClassSelect');
            App.loadOptions('/api/v1/school-sessions', 'toSessionSelect');
            App.loadOptions('/api/v1/sections', 'toSectionSelect');

            // Inner Search Logic
            document.getElementById('innerStudentSearch').addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                filterTable(term);
            });
        });

        function loadSourceStudents() {
            const classId = document.getElementById('fromClassSelect').value;
            const tbody = document.getElementById('sourceStudentsTableBody');
            const destPanel = document.getElementById('destinationCard');
            const stats = document.getElementById('selectionStats');
            const totalCountEl = document.getElementById('totalClassStudents');

            if (!classId) {
                tbody.innerHTML =
                    `<tr><td colspan="5" class="text-center py-5 text-muted">Select a class to see students</td></tr>`;
                destPanel.style.opacity = '0.5';
                destPanel.style.pointerEvents = 'none';
                stats.classList.add('d-none');
                return;
            }

            // Enable destination panel
            destPanel.style.opacity = '1';
            destPanel.style.pointerEvents = 'all';
            stats.classList.remove('d-none');

            tbody.innerHTML =
                `<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>`;

            axios.get(`/api/v1/students?class_id=${classId}`).then(res => {
                allCurrentStudents = res.data.data || res.data;
                totalCountEl.textContent = allCurrentStudents.length;
                renderStudentList(allCurrentStudents);
            }).catch(err => {
                tbody.innerHTML =
                    `<tr><td colspan="5" class="text-center py-5 text-danger">Failed to load students</td></tr>`;
            });
        }

        function renderStudentList(students) {
            const tbody = document.getElementById('sourceStudentsTableBody');
            if (!students || students.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="5" class="text-center py-5 text-muted">No students found in this class</td></tr>`;
                return;
            }

            tbody.innerHTML = '';
            const fragment = document.createDocumentFragment();

            students.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.classList.add('animate-in');
                tr.style.setProperty('--delay', (index * 0.05) + 's');
                tr.dataset.name = item.full_name?.toLowerCase() || '';
                tr.dataset.admission = item.admission_number?.toLowerCase() || '';

                tr.innerHTML = `
                    <td class="ps-4">
                        <input type="checkbox" name="student_ids[]" value="${item.id}" class="form-check-input student-checkbox border-2" onchange="handleRowToggle(this)">
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-initials rounded-circle bg-primary-subtle text-primary fw-bold text-center me-3 shadow-sm">
                                ${item.full_name?.charAt(0) || 'S'}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">${item.full_name}</div>
                                <div class="small text-muted">${item.user?.email || 'No Email'}</div>
                            </div>
                        </div>
                    </td>
                    <td><code class="text-primary fw-bold">${item.admission_number}</code></td>
                    <td class="text-center">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 rounded-pill">Active</span>
                    </td>
                    <td class="text-end pe-4">
                        <span class="fw-bold text-muted">${item.current_class || 'N/A'}</span>
                    </td>
                `;
                fragment.appendChild(tr);
            });
            tbody.appendChild(fragment);
            updatePromotionSummary();
        }

        function filterTable(term) {
            const rows = document.querySelectorAll('#sourceStudentsTableBody tr');
            rows.forEach(row => {
                if (!row.dataset.name) return;
                const matches = row.dataset.name.includes(term) || row.dataset.admission.includes(term);
                row.style.display = matches ? '' : 'none';
            });
        }

        function handleRowToggle(checkbox) {
            const tr = checkbox.closest('tr');
            if (checkbox.checked) {
                tr.classList.add('student-row-selected');
            } else {
                tr.classList.remove('student-row-selected');
            }
            updatePromotionSummary();
        }

        function toggleSelectAll(master) {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = master.checked;
                const tr = cb.closest('tr');
                if (master.checked) tr.classList.add('student-row-selected');
                else tr.classList.remove('student-row-selected');
            });
            updatePromotionSummary();
        }

        function updatePromotionSummary() {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            const summaryDiv = document.getElementById('promotionSummary');
            const summaryCount = document.getElementById('summaryCount');
            const summaryAction = document.getElementById('summaryActionDesc');
            const submitBtn = document.getElementById('btnSubmitPromotion');
            const badge = document.getElementById('selectedCounterBadge');

            if (checkedCount > 0) {
                summaryDiv.classList.remove('d-none');
                summaryCount.textContent = checkedCount;
                badge.style.display = 'inline-block';
                badge.textContent = `${checkedCount} Selected`;
                submitBtn.disabled = false;

                const type = document.querySelector('input[name="type"]:checked').value;
                const className = document.getElementById('fromClassSelect').options[document.getElementById(
                    'fromClassSelect').selectedIndex]?.text;
                summaryAction.textContent = `${type === 'promote' ? 'Promoting' : 'Repeating'} from ${className}`;
            } else {
                summaryDiv.classList.add('d-none');
                badge.style.display = 'none';
                submitBtn.disabled = true;
            }
        }

        // Listen for type changes
        document.querySelectorAll('input[name="type"]').forEach(input => {
            input.addEventListener('change', updatePromotionSummary);
        });

        async function handlePromotion(e) {
            e.preventDefault();
            const form = e.target;
            const checkedNodes = document.querySelectorAll('.student-checkbox:checked');
            const studentIds = Array.from(checkedNodes).map(node => node.value);
            const toClassText = document.getElementById('toClassSelect').options[document.getElementById(
                'toClassSelect').selectedIndex].text;
            const toSessionText = document.getElementById('toSessionSelect').options[document.getElementById(
                'toSessionSelect').selectedIndex].text;

            const formData = new FormData(form);
            const payload = {
                student_ids: studentIds,
                to_class_id: formData.get('to_class_id'),
                to_session_id: formData.get('to_session_id'),
                to_section_id: formData.get('to_section_id'),
                type: formData.get('type')
            };

            const typeText = payload.type === 'promote' ? 'Promote' : 'Repeat';

            const confirm = await Swal.fire({
                title: `${typeText} Confirmation`,
                html: `You are about to process <b>${studentIds.length} students</b> to:<br><br>
                       <div class="alert alert-primary py-2 small">
                         <b>Class:</b> ${toClassText}<br>
                         <b>Session:</b> ${toSessionText}
                       </div>
                       This action will update their primary records and generate academic history.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Proceed with ${typeText}`,
                confirmButtonColor: payload.type === 'promote' ? '#10b981' : '#f59e0b',
                cancelButtonText: 'Review Selection'
            });

            if (!confirm.isConfirmed) return;

            try {
                const res = await axios.post('/api/v1/promotions', payload);
                if (res.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Operation Complete!',
                        text: `Successfully processed ${studentIds.length} students.`,
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            } catch (err) {
                if (err.response?.data?.errors) {
                    App.showFormErrors(form, err.response.data.errors);
                } else {
                    Swal.fire('Error', err.response?.data?.message || 'Failed to process promotions', 'error');
                }
            }
        }
    </script>
@endsection
