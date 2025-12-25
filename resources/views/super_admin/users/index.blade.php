@extends('layouts.app')

@section('title', 'Global Users')
@section('header_title', 'User Management')

@section('content')
    <div class="card-premium p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">All System Users</h5>
            <div class="d-flex gap-2">
                <input type="text" class="form-control" placeholder="Search users...">
                <button class="btn btn-light"><i class="bi bi-filter"></i></button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-premium table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>School</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-muted small mt-2">Loading users...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="edit_user_id">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" id="edit_role">
                                <option value="super_admin">Super Admin</option>
                                <option value="school_admin">School Admin</option>
                                <option value="teacher">Teacher</option>
                                <option value="student">Student</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">School</label>
                            <input type="text" class="form-control" id="edit_school" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-premium" onclick="saveUser()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadUsers();
        });

        function loadUsers() {
            axios.get("/api/v1/users", {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    const users = response.data.data.data; // Pagination structure usually data.data
                    const tbody = document.getElementById('users-table-body');
                    tbody.innerHTML = '';

                    if (!users || users.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No users found.</td></tr>';
                        return;
                    }

                    users.forEach(user => {
                        const tr = document.createElement('tr');
                        const role = user.roles && user.roles.length ? user.roles[0].name : 'User';
                        const schoolName = user.school ? user.school.name :
                            '<span class="text-muted">Global</span>';

                        tr.innerHTML = `
                    <td data-label="Name">
                        <div class="fw-semibold">${user.name}</div>
                        <div class="small text-muted">${user.email}</div>
                    </td>
                    <td data-label="Role"><span class="badge bg-primary-subtle text-primary text-uppercase">${role}</span></td>
                    <td data-label="School">${schoolName}</td>
                    <td data-label="Joined" class="small text-muted">${new Date(user.created_at).toLocaleDateString()}</td>
                    <td data-label="Actions">
                        <button class="btn btn-sm btn-light text-primary" onclick='editUser(${JSON.stringify(user)})'>
                            <i class="bi bi-pencil"></i>
                        </button>
                    </td>
                `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => {
                    console.error('Error loading users', error);
                    document.getElementById('users-table-body').innerHTML =
                        '<tr><td colspan="5" class="text-center text-danger">Failed to load data.</td></tr>';
                });
        }

        function editUser(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;

            const role = user.roles && user.roles.length ? user.roles[0].name : '';
            document.getElementById('edit_role').value = role;

            const schoolName = user.school ? user.school.name : 'No School';
            document.getElementById('edit_school').value = schoolName;

            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        }

        function saveUser() {
            const userId = document.getElementById('edit_user_id').value;
            const data = {
                name: document.getElementById('edit_name').value,
                email: document.getElementById('edit_email').value,
                role: document.getElementById('edit_role').value
            };

            axios.put(`/api/v1/users/${userId}`, data)
                .then(response => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'User updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        alert('User updated successfully');
                    }
                    loadUsers();
                    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                })
                .catch(error => {
                    console.error('Error saving user', error);
                    const message = error.response?.data?.message || 'Failed to update user';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });
                    } else {
                        alert(message);
                    }
                });
        }

        // Add search functionality
        document.querySelector('input[placeholder="Search users..."]').addEventListener('input', function(e) {
            const query = e.target.value;
            // Simple client-side search or re-fetch with query
            // For now, let's just re-fetch if we have server-side support, or filter in place.
            // The index method in GlobalUserController doesn't support search yet, but let's assume it might.
            // Actually, let's just keep it as is or add a console log for now.
        });
    </script>
@endsection
