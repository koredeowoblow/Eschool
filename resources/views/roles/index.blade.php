@extends('layouts.app')

@section('title', 'Role Management')
@section('header_title', 'Role Management')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">School Roles</h5>
                    <button class="btn btn-primary" onclick="openCreateRoleModal()">
                        <i class="bi bi-plus-lg me-1"></i> Create Custom Role
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="roles-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Role Name</th>
                                    <th>Permissions</th>
                                    <th>Users</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="roles-body">
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <span class="spinner-border spinner-border-sm"></span> Loading roles...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Role Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalTitle">Create Custom Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="roleForm">
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="roleName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div id="permissions-container" class="border rounded p-3"
                                style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center py-4">
                                    <span class="spinner-border spinner-border-sm"></span> Loading permissions...
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveRole()">Save Role</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentRoleId = null;
        let allPermissions = {};

        async function loadRoles() {
            try {
                const response = await axios.get('/api/v1/roles');
                const roles = response.data.data;

                const tbody = document.getElementById('roles-body');

                if (roles.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No roles found</td></tr>';
                    return;
                }

                tbody.innerHTML = roles.map(role => {
                    const isCoreRole = ['super_admin', 'School Admin', 'Teacher', 'Finance Officer',
                        'Exams Officer',
                        'Guardian', 'Student'
                    ].includes(role.name);

                    return `
                <tr>
                    <td>
                        <strong>${role.name}</strong>
                        ${isCoreRole ? '<span class="badge bg-secondary ms-2">Core</span>' : '<span class="badge bg-info ms-2">Custom</span>'}
                    </td>
                    <td><small>${role.permissions?.length || 0} permissions</small></td>
                    <td><small>${role.users_count || 0} users</small></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewRole(${role.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                        ${!isCoreRole ? `
                                    <button class="btn btn-sm btn-outline-warning" onclick="editRole(${role.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRole(${role.id}, '${role.name}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    ` : ''}
                    </td>
                </tr>
            `;
                }).join('');
            } catch (error) {
                console.error('Failed to load roles:', error);
            }
        }

        async function loadPermissions() {
            try {
                const response = await axios.get('/api/v1/roles/permissions');
                allPermissions = response.data.data;

                const container = document.getElementById('permissions-container');
                let html = '';

                for (const [category, perms] of Object.entries(allPermissions)) {
                    html += `
                <div class="mb-3">
                    <h6 class="text-capitalize">${category}</h6>
                    ${perms.map(p => `
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="${p.name}" id="perm-${p.id}">
                                        <label class="form-check-label" for="perm-${p.id}">
                                            ${p.name}
                                        </label>
                                    </div>
                                `).join('')}
                </div>
            `;
                }

                container.innerHTML = html;
            } catch (error) {
                console.error('Failed to load permissions:', error);
            }
        }

        function openCreateRoleModal() {
            currentRoleId = null;
            document.getElementById('roleModalTitle').textContent = 'Create Custom Role';
            document.getElementById('roleName').value = '';
            document.querySelectorAll('#permissions-container input[type="checkbox"]').forEach(cb => cb.checked = false);

            const modal = new bootstrap.Modal(document.getElementById('roleModal'));
            modal.show();

            if (Object.keys(allPermissions).length === 0) {
                loadPermissions();
            }
        }

        async function saveRole() {
            const name = document.getElementById('roleName').value;
            const selectedPermissions = Array.from(
                document.querySelectorAll('#permissions-container input[type="checkbox"]:checked')
            ).map(cb => cb.value);

            if (!name || selectedPermissions.length === 0) {
                alert('Please provide a role name and select at least one permission');
                return;
            }

            try {
                const url = currentRoleId ? `/api/v1/roles/${currentRoleId}` : '/api/v1/roles';
                const method = currentRoleId ? 'put' : 'post';

                await axios[method](url, {
                    name: name,
                    permissions: selectedPermissions
                });

                bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
                loadRoles();

                Swal.fire('Success', 'Role saved successfully', 'success');
            } catch (error) {
                console.error('Failed to save role:', error);
                Swal.fire('Error', error.response?.data?.message || 'Failed to save role', 'error');
            }
        }

        async function deleteRole(id, name) {
            const result = await Swal.fire({
                title: 'Delete Role?',
                text: `Are you sure you want to delete "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it'
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/v1/roles/${id}`);
                    loadRoles();
                    Swal.fire('Deleted!', 'Role has been deleted.', 'success');
                } catch (error) {
                    Swal.fire('Error', error.response?.data?.message || 'Failed to delete role', 'error');
                }
            }
        }

        async function viewRole(id) {
            try {
                const response = await axios.get(`/api/v1/roles`);
                const roles = response.data.data;
                const role = roles.find(r => r.id === id);

                if (role) {
                    const permissionsList = role.permissions?.map(p => p.name).join(', ') || 'No permissions';
                    Swal.fire({
                        title: role.name,
                        html: `<strong>Permissions:</strong><br><small>${permissionsList}</small>`,
                        icon: 'info'
                    });
                }
            } catch (error) {
                console.error('Failed to view role:', error);
            }
        }

        async function editRole(id) {
            try {
                const response = await axios.get(`/api/v1/roles`);
                const roles = response.data.data;
                const role = roles.find(r => r.id === id);

                if (role) {
                    currentRoleId = id;
                    document.getElementById('roleModalTitle').textContent = 'Edit Role: ' + role.name;
                    document.getElementById('roleName').value = role.name;

                    // Load permissions if not loaded
                    if (Object.keys(allPermissions).length === 0) {
                        await loadPermissions();
                    }

                    // Check the role's permissions
                    document.querySelectorAll('#permissions-container input[type="checkbox"]').forEach(cb => {
                        cb.checked = role.permissions?.some(p => p.name === cb.value) || false;
                    });

                    const modal = new bootstrap.Modal(document.getElementById('roleModal'));
                    modal.show();
                }
            } catch (error) {
                console.error('Failed to edit role:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadRoles();
        });
    </script>
@endsection
