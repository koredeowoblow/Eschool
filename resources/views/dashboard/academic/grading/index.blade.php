@extends('layouts.app')

@section('title', 'Grading System')
@section('header_title', 'Grading System')

@section('content')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">

        <div class="input-group w-100 w-md-50">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="gradingSearch" class="form-control border-start-0 ps-0"
                placeholder="Search grading records..." oninput="reloadGradingSystem()">
        </div>

        <div class="d-flex gap-2 align-items-center">
            @hasrole('super_admin')
                <div id="schoolSelectorRow" style="display:none; min-width:250px;">
                    <select class="form-select border-warning" id="schoolSelect" onchange="handleSchoolChange(this.value)">
                    </select>
                </div>
            @endhasrole


            @hasrole('super_admin|School Admin')
                <button type="button" class="btn btn-primary-premium requires-session-lock"
                    onclick="App.resetForm(document.forms['createGradeForm']);" data-bs-toggle="modal"
                    data-bs-target="#createGradeModal">
                    <i class="bi bi-plus-lg me-1"></i>
                    New Grade Scale
                </button>
            @endhasrole

        </div>
    </div>

    <!-- Grading Table -->
    <div class="card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Score Range</th>
                            <th>Remark</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="gradingTableBody">
                        <!-- Loaded by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Grade Modal -->
    <div class="modal fade" id="createGradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createGradeForm" action="/api/v1/grading-system" method="POST"
                    onsubmit="App.submitForm(event, reloadGradingSystem, 'grading', 'createGradeModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Create Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Grade Name</label>
                            <input type="text" name="grade" class="form-control" required placeholder="A, B, C">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Min Score</label>
                                <input type="number" name="min_score" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Max Score</label>
                                <input type="number" name="max_score" class="form-control" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Remark</label>
                            <input type="text" name="remark" class="form-control">
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Save</button>
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

        function handleSchoolChange() {
            currentSchoolId = document.getElementById('schoolSelect').value;
            if (currentSchoolId) {
                fetchGradingScales();
            } else {
                document.getElementById('gradingTableBody').innerHTML = '';
            }
        }

        function fetchGradingScales() {
            let url = '/api/v1/grading-scales';
            if (isSuperAdmin && currentSchoolId) {
                url += `?school_id=${currentSchoolId}`;
            } else if (isSuperAdmin && !currentSchoolId) {
                // If super admin hasn't selected a school, don't fetch or fetch empty
                document.getElementById('gradingTableBody').innerHTML =
                    '<tr><td colspan="6" class="text-center">Select a school to view data</td></tr>';
                return;
            }

            fetch(url, {
                    headers: headers
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        renderTable(data.data);
                    } else {
                        console.error('Failed to fetch grading scales', data.message);
                        if (isSuperAdmin && !currentSchoolId) {
                            document.getElementById('gradingTableBody').innerHTML =
                                '<tr><td colspan="6" class="text-center">Select a school to view data</td></tr>';
                        }
                    }
                })
                .catch(err => console.error(err));
        }

        function renderTable(scales) {
            const tbody = document.getElementById('gradingTableBody');
            tbody.innerHTML = '';

            if (!scales || scales.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No grading scales found</td></tr>';
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
            document.getElementById('gradingModalLabel').innerText = 'Add Grading Scale';
        }

        function editGrade(id, label, min, max, remark, isPass) {
            document.getElementById('gradeId').value = id;
            document.getElementById('gradeLabel').value = label;
            document.getElementById('minScore').value = min;
            document.getElementById('maxScore').value = max;
            document.getElementById('remark').value = remark;
            document.getElementById('isPass').checked = isPass;
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
