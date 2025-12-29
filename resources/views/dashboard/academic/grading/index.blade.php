@extends('layouts.app')

@section('title', 'Grading System')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Grading System</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#gradingModal"
                onclick="resetForm()">
                <i class="fas fa-plus"></i> Add Grading Scale
            </button>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Grading Scales</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="gradingTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Grade</th>
                                <th>Min Score</th>
                                <th>Max Score</th>
                                <th>Remark</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="gradingTableBody">
                            <!-- Data will be populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4" id="schoolSelectorRow" style="display: none;">
        <div class="col-md-4">
            <label for="schoolSelect" class="form-label">Select School (Super Admin)</label>
            <select class="form-select" id="schoolSelect" onchange="handleSchoolChange()">
                <option value="">-- Select School --</option>
            </select>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="gradingModal" tabindex="-1" aria-labelledby="gradingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradingModalLabel">Add Grading Scale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="gradingForm" onsubmit="handleGradingSubmit(event)">
                    <div class="modal-body">
                        <input type="hidden" id="gradeId">
                        <div class="mb-3">
                            <label for="gradeLabel" class="form-label">Grade Label <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="gradeLabel" required maxlength="5"
                                placeholder="A, B, C...">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="minScore" class="form-label">Min Score <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="minScore" required min="0"
                                    max="100" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="maxScore" class="form-label">Max Score <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="maxScore" required min="0"
                                    max="100" step="0.01">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="remark" class="form-label">Remark</label>
                            <input type="text" class="form-control" id="remark" placeholder="Excellent, Very Good...">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="isPass" checked>
                            <label class="form-check-label" for="isPass">
                                Is Pass Grade?
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@section('scripts')
    <script>
        let currentSchoolId = null;
        let isSuperAdmin = false;

        document.addEventListener('DOMContentLoaded', function() {
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
                    <button class="btn btn-sm btn-info" onclick="editGrade(${scale.id}, '${scale.grade_label}', ${scale.min_score}, ${scale.max_score}, '${scale.remark || ''}', ${scale.is_pass})">
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
