@extends('layouts.app')

@section('title', 'Audit Logs')
@section('header_title', 'Audit Logs')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4  mb-6 ">
        <div class=" col-span-1 md:col-span-12 ">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden border-0 shadow-sm">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-header bg-white py-3">
                    <h5 class=" mb-6  font-bold">System Audit Trail</h5>
                    <small class="text-gray-500">All sensitive operations are logged here</small>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 ">
                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                        <div class=" md:col-span-3 col-span-1 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6  small">Entity Type</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="filter-entity">
                                <option value="">All Entities</option>
                                <option value="student">Student</option>
                                <option value="result">Result</option>
                                <option value="invoice">Invoice</option>
                                <option value="payment">Payment</option>
                                <option value="user_role">User Role</option>
                            </select>
                        </div>
                        <div class=" md:col-span-3 col-span-1 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6  small">Action</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-white" id="filter-action">
                                <option value="">All Actions</option>
                                <option value="create">Create</option>
                                <option value="update">Update</option>
                                <option value="delete">Delete</option>
                                <option value="state_change">State Change</option>
                                <option value="role_change">Role Change</option>
                                <option value="unauthorized">Unauthorized</option>
                            </select>
                        </div>
                        <div class=" md:col-span-3 col-span-1 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6  small">Start Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="filter-start-date">
                        </div>
                        <div class=" md:col-span-3 col-span-1 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6  small">End Date</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="filter-end-date">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4   mb-6 ">
                        <div class="col-md-9">
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" id="filter-search"
                                placeholder="Search by entity, action, or user email...">
                        </div>
                        <div class=" md:col-span-3 col-span-1 ">
                            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium w-100" onclick="loadAuditLogs()">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                        </div>
                    </div>

                    <!-- Logs Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left divide-y divide-gray-200" id="audit-table">
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
                                    <td colspan="6" class=" text-center  py-4">
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Loading audit logs...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination-container" class="flex justify-between items-center mt-4">
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
                    '<tr><td colspan="6" class=" text-center  text-danger">Failed to load audit logs</td></tr>';
            }
        }

        function renderAuditLogs(logs) {
            const tbody = document.getElementById('audit-logs-body');

            if (logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class=" text-center  text-gray-500">No audit logs found</td></tr>';
                return;
            }

            tbody.innerHTML = logs.map(log => `
        <tr>
            <td>${new Date(log.created_at).toLocaleString()}</td>
            <td><span class="badge bg-${getActionColor(log.action)}">${log.action}</span></td>
            <td>${log.entity}</td>
            <td>${log.user_email || 'System'}</td>
            <td><small class="text-gray-500">${log.ip_address || 'N/A'}</small></td>
            <td>
                <button class="btn px-3 py-1.5 text-sm btn-outline-primary" onclick="viewDetails(${log.id})">
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
            let html = '<ul class="pagination  mb-6 ">';

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
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                <div class=" md:col-span-6 col-span-1 ">
                    <strong>Action:</strong> ${log.action}
                </div>
                <div class=" md:col-span-6 col-span-1 ">
                    <strong>Entity:</strong> ${log.entity}
                </div>
                <div class=" md:col-span-6 col-span-1 ">
                    <strong>User:</strong> ${log.user_email || 'System'}
                </div>
                <div class=" md:col-span-6 col-span-1 ">
                    <strong>Role:</strong> ${log.user_role || 'N/A'}
                </div>
                <div class=" md:col-span-6 col-span-1 ">
                    <strong>IP Address:</strong> ${log.ip_address || 'N/A'}
                </div>
                <div class=" md:col-span-6 col-span-1 ">
                    <strong>Timestamp:</strong> ${new Date(log.created_at).toLocaleString()}
                </div>
                <div class=" col-span-1 md:col-span-12 ">
                    <strong>User Agent:</strong><br>
                    <small class="text-gray-500">${log.user_agent || 'N/A'}</small>
                </div>
                ${log.metadata ? `
                    <div class=" col-span-1 md:col-span-12 ">
                        <strong>Additional Data:</strong>
                        <pre class="bg-light p-3 rounded mt-4"><code>${JSON.stringify(log.metadata, null, 2)}</code></pre>
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
