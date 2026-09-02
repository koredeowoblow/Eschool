@extends('layouts.app')

@section('title', 'Grading System')
@section('header_title', 'Grading System')

@section('content')

    <div class=" flex  flex-column flex-md-row  justify-between   items-center   mb-6  gap-3">

        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="gradingSearch" class=" w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all  "
                placeholder="Search grading records..." oninput="reloadGradingSystem()">
        </div>

        <div class=" flex  gap-2  items-center ">
            @hasrole('super_admin')
                <div id="schoolSelectorRow" style="display:none; min-width:250px;">
                    <select class=" w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all  border-warning" id="schoolSelect" onchange="handleSchoolChange(this.value)">
                    </select>
                </div>
            @endhasrole


            @hasrole('super_admin|School Admin')
                <button type="button" class=" px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium -premium requires-session-lock"
                    onclick="App.resetForm(document.forms['createGradeForm']);" data-bs-toggle="modal"
                    data-bs-target="#createGradeModal">
                    <i class="bi bi-plus-lg me-1"></i>
                    New Grade Scale
                </button>
            @endhasrole

        </div>
    </div>

    <!-- Grading Table -->
    <div class=" bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden -premium">
        <div class=" bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden -body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Score Range</th>
                            <th>Remark</th>
                            <th>Status</th>
                            <th class=" text-right ">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="gradingTableBody">
                        <!-- Loaded by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grading Modal -->
    <div class="modal fade" id="gradingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="gradingForm" onsubmit="handleGradingSubmit(event)">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="gradingModalLabel">Add Grading Scale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="gradeId">
                        <input type="hidden" id="hiddenSchoolId">

                        <div class="  mb-6  ">
                            <label class="form-label">Grade Name</label>
                            <input type="text" id="gradeLabel" class=" w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " required placeholder="A, B, C">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="form-label">Min Score</label>
                                <input type="number" id="minScore" class=" w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " required>
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="form-label">Max Score</label>
                                <input type="number" id="maxScore" class=" w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all " required>
                            </div>
                        </div>

                        <div class=" mt-4 ">
                            <label class="form-label">Remark</label>
                            <input type="text" id="remark" class=" w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all ">
                        </div>

                        <div class=" mt-4 ">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isPass" checked>
                                <label class="form-check-label" for="isPass">Is Pass Grade?</label>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class=" px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium -premium">Save</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    {{-- <script>
    document.addEventListener('DOMContentLoaded', () => {
        App.renderTable('/api/v1/grading-system', 'gradingTableBody', 'grading');
    });

    function reloadGradingSystem() {
        const q = document.getElementById('gradingSearch').value;
        App.renderTable('/api/v1/grading-system?search=' + q, 'gradingTableBody', 'grading');
    }

    function editGrade(data) {
        const form = document.getElementById('editGradeForm');
        form.action = `/api/v1/grading-system/${data.id}`;
        App.populateForm(form, data);
        new bootstrap.Modal(document.getElementById('editGradeModal')).show();
    }

    function handleSchoolChange(id) {
        // keep your existing logic hook here
    }
</script> --}}
    <script>
        let currentSchoolId = null;
        let isSuperAdmin = false;

        document.addEventListener('DOMContentLoaded', () => {
            const appConfig = window.AppConfig || {};
            const user = appConfig.user || {};

            // Roles in AppConfig are already strings (getRoleNames())
            const roles = (user.roles || []).map(r => String(r).toLowerCase().replace(/\s+/g, '_'));

            // Strict Check: Only Super Admin sees the selector
            if (roles.includes('super_admin')) {
                isSuperAdmin = true;
                document.getElementById('schoolSelectorRow').style.display = 'block';
                // Show explicit message for super admin
                document.getElementById('gradingTableBody').innerHTML =
                    '<tr><td colspan="6" class=" text-center  py-4  text-slate-500 ">Please select a school to view grading scales</td></tr>';
                fetchSchools();
            } else {
                fetchGradingScales();
            }
        });

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        function fetchSchools() {
            fetch('/api/v1/schools', {
                    headers: headers
                }) // Assuming this endpoint exists for super admins
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('schoolSelect');
                    if (data.success || Array.isArray(data)) {
                        const schools = data.data || data;
                        schools.forEach(school => {
                            const option = document.createElement('option');
                            option.value = school.id;
                            option.textContent = school.name;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(err => console.error(err));
        }

        function handleSchoolChange(val) {
            currentSchoolId = val;
            // Update hidden input for usage in modal
            const hiddenInput = document.getElementById('hiddenSchoolId');
            if (hiddenInput) hiddenInput.value = val || '';

            if (currentSchoolId) {
                fetchGradingScales();
            } else {
                document.getElementById('gradingTableBody').innerHTML =
                    '<tr><td colspan="6" class=" text-center  py-4  text-slate-500 ">Please select a school to view grading scales</td></tr>';
            }
        }

        function fetchGradingScales() {
            let url = '/api/v1/grading-scales';
            if (isSuperAdmin && currentSchoolId) {
                url += `?school_id=${currentSchoolId}`;
            } else if (isSuperAdmin && !currentSchoolId) {
                return;
            }

            // Loading State
            document.getElementById('gradingTableBody').innerHTML =
                '<tr><td colspan="6" class=" text-center  py-4">Loading...</td></tr>';

            fetch(url, {
                    headers: headers
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderTable(data.data);
                    } else {
                        console.error('Failed to fetch grading scales', data.message);
                        document.getElementById('gradingTableBody').innerHTML =
                            '<tr><td colspan="6" class=" text-center  text-danger">Failed to load data</td></tr>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('gradingTableBody').innerHTML =
                        '<tr><td colspan="6" class=" text-center  text-danger">Error loading data</td></tr>';
                });
        }

        function renderTable(scales) {
            const tbody = document.getElementById('gradingTableBody');
            tbody.innerHTML = '';

            if (!scales || scales.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class=" text-center ">No grading scales found</td></tr>';
                return;
            }

            scales.forEach(scale => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${scale.grade_label}</td>
                <td>${scale.min_score}</td>
                <td>${scale.max_score}</td>
                <td>${scale.remark || '-'}</td>
                <td><span class="badge bg-${scale.is_pass ? 'success' : 'danger'}">${scale.is_pass ? 'Pass' : 'Fail'}</span></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editGrade(${scale.id}, '${scale.grade_label}', ${scale.min_score}, ${scale.max_score}, '${scale.remark || ''}', ${scale.is_pass})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteGrade(${scale.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
                tbody.appendChild(tr);
            });
        }

        function resetForm() {
            document.getElementById('gradingForm').reset();
            document.getElementById('gradeId').value = '';
            // Ensure school ID is kept if set
            const hiddenInput = document.getElementById('hiddenSchoolId');
            if (hiddenInput) hiddenInput.value = currentSchoolId || '';
            document.getElementById('gradingModalLabel').innerText = 'Add Grading Scale';
        }

        function editGrade(id, label, min, max, remark, isPass) {
            document.getElementById('gradeId').value = id;
            document.getElementById('gradeLabel').value = label;
            document.getElementById('minScore').value = min;
            document.getElementById('maxScore').value = max;
            document.getElementById('remark').value = remark;
            document.getElementById('isPass').checked = isPass;

            const hiddenInput = document.getElementById('hiddenSchoolId');
            if (hiddenInput) hiddenInput.value = currentSchoolId || '';

            document.getElementById('gradingModalLabel').innerText = 'Edit Grading Scale';

            new bootstrap.Modal(document.getElementById('gradingModal')).show();
        }

        function handleGradingSubmit(e) {
            e.preventDefault();

            if (isSuperAdmin && !currentSchoolId) {
                alert('Please select a school first.');
                return;
            }

            const id = document.getElementById('gradeId').value;
            const data = {
                grade_label: document.getElementById('gradeLabel').value,
                min_score: document.getElementById('minScore').value,
                max_score: document.getElementById('maxScore').value,
                remark: document.getElementById('remark').value,
                is_pass: document.getElementById('isPass').checked ? 1 : 0
            };

            if (isSuperAdmin) {
                data.school_id = currentSchoolId;
            }

            const url = id ?
                `/api/v1/grading-scales/${id}` :
                '/api/v1/grading-scales';

            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                    method: method,
                    headers: headers,
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        // Close modal properly
                        const modalEl = document.getElementById('gradingModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        } else {
                            const btnClose = modalEl.querySelector('.btn-close');
                            if (btnClose) btnClose.click();
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Success', response.message, 'success');
                        } else {
                            alert(response.message);
                        }
                        fetchGradingScales();
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', response.message || 'Validation Failed', 'error');
                        } else {
                            alert(response.message || 'Validation Failed');
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred');
                });
        }

        function deleteGrade(id) {
            if (!confirm('Are you sure?')) return;

            fetch(`/api/v1/grading-scales/${id}`, {
                    method: 'DELETE',
                    headers: headers
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        fetchGradingScales();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(err => console.error(err));
        }
    </script>
@endsection
