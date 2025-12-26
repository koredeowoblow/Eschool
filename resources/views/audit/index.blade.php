@extends('layouts.app')

@section('title', 'Audit Logs')
@section('header_title', 'Audit Logs')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">System Audit Trail</h5>
                    <small class="text-muted">All sensitive operations are logged here</small>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small">Entity Type</label>
                            <select class="form-select" id="filter-entity">
                                <option value="">All Entities</option>
                                <option value="student">Student</option>
                                <option value="result">Result</option>
                                <option value="invoice">Invoice</option>
                                <option value="payment">Payment</option>
                                <option value="user_role">User Role</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Action</label>
                            <select class="form-select" id="filter-action">
                                <option value="">All Actions</option>
                                <option value="create">Create</option>
                                <option value="update">Update</option>
                                <option value="delete">Delete</option>
                                <option value="state_change">State Change</option>
                                <option value="role_change">Role Change</option>
                                <option value="unauthorized">Unauthorized</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Start Date</label>
                            <input type="date" class="form-control" id="filter-start-date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">End Date</label>
                            <input type="date" class="form-control" id="filter-end-date">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="filter-search"
                                placeholder="Search by entity, action, or user email...">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" onclick="loadAuditLogs()">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                        </div>
                    </div>

                    <!-- Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="audit-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Action</th>
                                    <th>Entity</th>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody id="audit-logs-body">
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Loading audit logs...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination-container" class="d-flex justify-content-between align-items-center mt-3">
                        <div id="pagination-info"></div>
                        <nav id="pagination-nav"></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="auditDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Audit Log Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="audit-detail-content">
                    <!-- Populated via JavaScript -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentPage = 1;

        async function loadAuditLogs(page = 1) {
            try {
                const params = new URLSearchParams({
                    page: page,
                    per_page: 20,
                    entity: document.getElementById('filter-entity').value,
                    action: document.getElementById('filter-action').value,
                    start_date: document.getElementById('filter-start-date').value,
                    end_date: document.getElementById('filter-end-date').value,
                    search: document.getElementById('filter-search').value
                });

                const response = await axios.get(`/api/v1/audit?${params}`);
                const data = response.data.data;

                renderAuditLogs(data.data);
                renderPagination(data);
                currentPage = page;
            } catch (error) {
                console.error('Failed to load audit logs:', error);
                document.getElementById('audit-logs-body').innerHTML =
                    '<tr><td colspan="6" class="text-center text-danger">Failed to load audit logs</td></tr>';
            }
        }

        function renderAuditLogs(logs) {
            const tbody = document.getElementById('audit-logs-body');

            if (logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No audit logs found</td></tr>';
                return;
            }

            tbody.innerHTML = logs.map(log => `
        <tr>
            <td>${new Date(log.created_at).toLocaleString()}</td>
            <td><span class="badge bg-${getActionColor(log.action)}">${log.action}</span></td>
            <td>${log.entity}</td>
            <td>${log.user_email || 'System'}</td>
            <td><small class="text-muted">${log.ip_address || 'N/A'}</small></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails(${log.id})">
                    <i class="bi bi-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');
        }

        function getActionColor(action) {
            const colors = {
                'create': 'success',
                'update': 'info',
                'delete': 'danger',
                'state_change': 'warning',
                'role_change': 'primary',
                'unauthorized': 'danger'
            };
            return colors[action] || 'secondary';
        }

        function renderPagination(data) {
            document.getElementById('pagination-info').textContent =
                `Showing ${data.from || 0} to ${data.to || 0} of ${data.total} entries`;

            // Simple pagination (you can enhance this)
            const nav = document.getElementById('pagination-nav');
            let html = '<ul class="pagination mb-0">';

            if (data.prev_page_url) {
                html +=
                    `<li class="page-item"><a class="page-link" href="#" onclick="loadAuditLogs(${data.current_page - 1}); return false;">Previous</a></li>`;
            }

            html += `<li class="page-item active"><span class="page-link">${data.current_page}</span></li>`;

            if (data.next_page_url) {
                html +=
                    `<li class="page-item"><a class="page-link" href="#" onclick="loadAuditLogs(${data.current_page + 1}); return false;">Next</a></li>`;
            }

            html += '</ul>';
            nav.innerHTML = html;
        }

        async function viewDetails(id) {
            try {
                const response = await axios.get(`/api/v1/audit/${id}`);
                const log = response.data.data;

                const content = `
            <div class="row g-3">
                <div class="col-md-6">
                    <strong>Action:</strong> ${log.action}
                </div>
                <div class="col-md-6">
                    <strong>Entity:</strong> ${log.entity}
                </div>
                <div class="col-md-6">
                    <strong>User:</strong> ${log.user_email || 'System'}
                </div>
                <div class="col-md-6">
                    <strong>Role:</strong> ${log.user_role || 'N/A'}
                </div>
                <div class="col-md-6">
                    <strong>IP Address:</strong> ${log.ip_address || 'N/A'}
                </div>
                <div class="col-md-6">
                    <strong>Timestamp:</strong> ${new Date(log.created_at).toLocaleString()}
                </div>
                <div class="col-12">
                    <strong>User Agent:</strong><br>
                    <small class="text-muted">${log.user_agent || 'N/A'}</small>
                </div>
                ${log.metadata ? `
                    <div class="col-12">
                        <strong>Additional Data:</strong>
                        <pre class="bg-light p-3 rounded mt-2"><code>${JSON.stringify(log.metadata, null, 2)}</code></pre>
                    </div>
                    ` : ''}
            </div>
        `;

                document.getElementById('audit-detail-content').innerHTML = content;
                new bootstrap.Modal(document.getElementById('auditDetailModal')).show();
            } catch (error) {
                console.error('Failed to load audit details:', error);
            }
        }

        // Load logs on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadAuditLogs();
        });
    </script>
@endsection
