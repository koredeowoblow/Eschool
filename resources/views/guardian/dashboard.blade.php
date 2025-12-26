@extends('layouts.app')

@section('title', 'My Children')
@section('header_title', 'Guardian Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">My Children</h5>
                </div>
                <div class="card-body">
                    <div class="row" id="children-container">
                        <div class="col-12 text-center py-4">
                            <span class="spinner-border spinner-border-sm"></span> Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Child Detail Modal -->
    <div class="modal fade" id="childDetailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="childName">Child Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="childTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#results-tab">
                                Results
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#attendance-tab">
                                Attendance
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fees-tab">
                                Fees & Payments
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Results Tab -->
                        <div class="tab-pane fade show active" id="results-tab">
                            <div id="results-content">
                                <div class="text-center py-4">
                                    <span class="spinner-border spinner-border-sm"></span> Loading results...
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Tab -->
                        <div class="tab-pane fade" id="attendance-tab">
                            <div id="attendance-content">
                                <div class="text-center py-4">
                                    <span class="spinner-border spinner-border-sm"></span> Loading attendance...
                                </div>
                            </div>
                        </div>

                        <!-- Fees Tab -->
                        <div class="tab-pane fade" id="fees-tab">
                            <div id="fees-content">
                                <div class="text-center py-4">
                                    <span class="spinner-border spinner-border-sm"></span> Loading fees...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentStudentId = null;

        async function loadChildren() {
            try {
                const response = await axios.get('/api/v1/guardian/children');
                const children = response.data.data;

                const container = document.getElementById('children-container');

                if (children.length === 0) {
                    container.innerHTML =
                        '<div class="col-12 text-center text-muted">No children linked to your account</div>';
                    return;
                }

                container.innerHTML = children.map(child => `
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-primary">
                    <div class="card-body">
                        <h5 class="card-title">${child.name}</h5>
                        <p class="card-text">
                            <small class="text-muted">Admission: ${child.admission_number}</small><br>
                            <small class="text-muted">Class: ${child.class}</small>
                        </p>
                        <button class="btn btn-primary btn-sm" onclick="viewChild(${child.id}, '${child.name}')">
                            <i class="bi bi-eye me-1"></i> View Details
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
            } catch (error) {
                console.error('Failed to load children:', error);
                document.getElementById('children-container').innerHTML =
                    '<div class="col-12 text-center text-danger">Failed to load children</div>';
            }
        }

        async function viewChild(studentId, name) {
            currentStudentId = studentId;
            document.getElementById('childName').textContent = name;

            const modal = new bootstrap.Modal(document.getElementById('childDetailModal'));
            modal.show();

            // Load results by default
            loadResults(studentId);
        }

        async function loadResults(studentId) {
            try {
                const response = await axios.get(`/api/v1/guardian/children/${studentId}/results`);
                const results = response.data.data;

                const content = document.getElementById('results-content');

                if (results.length === 0) {
                    content.innerHTML = '<div class="text-center text-muted">No published results available</div>';
                    return;
                }

                content.innerHTML = `
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Assessment</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    ${results.map(r => `
                            <tr>
                                <td>${r.assessment?.subject?.name || 'N/A'}</td>
                                <td>${r.assessment?.name || 'N/A'}</td>
                                <td>${r.marks_obtained}</td>
                                <td><span class="badge bg-${getGradeColor(r.grade)}">${r.grade}</span></td>
                                <td>${r.remark || '-'}</td>
                            </tr>
                        `).join('')}
                </tbody>
            </table>
        `;
            } catch (error) {
                console.error('Failed to load results:', error);
                document.getElementById('results-content').innerHTML =
                    '<div class="text-center text-danger">Failed to load results</div>';
            }
        }

        async function loadAttendance(studentId) {
            try {
                const response = await axios.get(`/api/v1/guardian/children/${studentId}/attendance`);
                const attendance = response.data.data;

                const content = document.getElementById('attendance-content');

                if (attendance.length === 0) {
                    content.innerHTML = '<div class="text-center text-muted">No attendance records</div>';
                    return;
                }

                content.innerHTML = `
            <table class="table table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${attendance.map(a => `
                            <tr>
                                <td>${new Date(a.date).toLocaleDateString()}</td>
                                <td><span class="badge bg-${a.status === 'present' ? 'success' : 'danger'}">${a.status}</span></td>
                            </tr>
                        `).join('')}
                </tbody>
            </table>
        `;
            } catch (error) {
                console.error('Failed to load attendance:', error);
            }
        }

        async function loadFees(studentId) {
            try {
                const response = await axios.get(`/api/v1/guardian/children/${studentId}/fees`);
                const data = response.data.data;

                const content = document.getElementById('fees-content');

                content.innerHTML = `
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Invoiced</small>
                            <h5>$${parseFloat(data.summary.total_invoiced || 0).toFixed(2)}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Total Paid</small>
                            <h5 class="text-success">$${parseFloat(data.summary.total_paid || 0).toFixed(2)}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <small class="text-muted">Pending</small>
                            <h5 class="text-warning">$${parseFloat(data.summary.pending || 0).toFixed(2)}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.invoices.map(inv => `
                            <tr>
                                <td>${inv.id}</td>
                                <td>$${parseFloat(inv.amount).toFixed(2)}</td>
                                <td><span class="badge bg-${inv.status === 'paid' ? 'success' : 'warning'}">${inv.status}</span></td>
                                <td>${new Date(inv.created_at).toLocaleDateString()}</td>
                            </tr>
                        `).join('')}
                </tbody>
            </table>
        `;
            } catch (error) {
                console.error('Failed to load fees:', error);
            }
        }

        function getGradeColor(grade) {
            const colors = {
                'A': 'success',
                'B': 'primary',
                'C': 'info',
                'D': 'warning',
                'F': 'danger'
            };
            return colors[grade] || 'secondary';
        }

        // Tab click handlers
        document.addEventListener('shown.bs.tab', function(e) {
            if (!currentStudentId) return;

            if (e.target.getAttribute('data-bs-target') === '#attendance-tab') {
                loadAttendance(currentStudentId);
            } else if (e.target.getAttribute('data-bs-target') === '#fees-tab') {
                loadFees(currentStudentId);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            loadChildren();
        });
    </script>
@endsection
