@extends('layouts.app')

@section('title', 'Role Management')
@section('header_title', 'Role Management')

@section('content')
<div x-data="rolesManager()" class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h5 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-shield-lock text-blue-600"></i> School Roles
                </h5>
                <p class="text-slate-500 text-sm mt-1">Manage user roles and permissions</p>
            </div>
            <button @click="openModal()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm shadow-blue-500/30 transition-all flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Create Custom Role
            </button>
        </div>
        
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm font-semibold">
                    <tr>
                        <th class="px-6 py-4">Role Name</th>
                        <th class="px-6 py-4">Permissions</th>
                        <th class="px-6 py-4">Users</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <tr x-show="loadingRoles">
                        <td colspan="4" class="px-6 py-8  text-center  text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="animate-spin h-8 w-8 text-blue-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Loading roles...</span>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="!loadingRoles && roles.length === 0" style="display: none;">
                        <td colspan="4" class="px-6 py-8  text-center  text-slate-500">
                            <i class="bi bi-shield-x text-3xl mb-2 block text-slate-300"></i>
                            No roles found.
                        </td>
                    </tr>
                    <template x-for="role in roles" :key="role.id">
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-slate-800" x-text="role.name"></span>
                                    <span x-show="isCoreRole(role.name)" class="px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-600 border border-slate-200">Core</span>
                                    <span x-show="!isCoreRole(role.name)" class="px-2.5 py-1 text-xs font-semibold rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">Custom</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-slate-500 bg-slate-100 px-3 py-1 rounded-lg w-max text-sm font-medium">
                                    <i class="bi bi-key"></i> <span x-text="(role.permissions ? role.permissions.length : 0) + ' permissions'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-slate-500">
                                    <i class="bi bi-people"></i> <span x-text="(role.users_count || 0) + ' users'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="viewRole(role)" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Permissions">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <template x-if="!isCoreRole(role.name)">
                                        <button @click="editRole(role)" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Role">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </template>
                                    <template x-if="!isCoreRole(role.name)">
                                        <button @click="deleteRole(role)" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete Role">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit Role Modal Backdrop -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20  text-center  sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeModal()" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-slate-200">
                
                <div class="bg-white px-6 pt-6 pb-6 border-b border-slate-100 flex justify-between items-center sticky top-0 z-10">
                    <h3 class="text-xl font-bold text-slate-800" id="modal-title" x-text="currentRoleId ? 'Edit Role: ' + roleName : 'Create Custom Role'"></h3>
                    <button @click="closeModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                
                <div class="px-6 py-6 overflow-y-auto max-h-[60vh]">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Role Name <span class="text-red-500">*</span></label>
                            <input type="text" x-model="roleName" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all" placeholder="e.g. Assistant Teacher" required>
                        </div>
                        
                        <div>
                            <div class="flex justify-between items-end   mb-6  ">
                                <label class="block text-sm font-semibold text-slate-700">Permissions <span class="text-red-500">*</span></label>
                                <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded-md" x-text="selectedPermissions.length + ' selected'"></span>
                            </div>
                            
                            <div class="border border-slate-200 rounded-xl bg-slate-50/50 p-4 min-h-[200px]">
                                <div x-show="loadingPermissions" class="flex flex-col items-center justify-center py-8">
                                    <svg class="animate-spin h-6 w-6 text-blue-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sm text-slate-500">Loading permissions...</span>
                                </div>
                                
                                <div x-show="!loadingPermissions" class="space-y-6">
                                    <template x-for="(perms, category) in allPermissions" :key="category">
                                        <div>
                                            <h6 class="text-sm font-bold text-slate-800 capitalize   mb-6   pb-2 border-b border-slate-200" x-text="category"></h6>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <template x-for="p in perms" :key="p.id">
                                                    <label class="flex items-start gap-3 cursor-pointer group">
                                                        <div class="relative flex items-center pt-0.5">
                                                            <input type="checkbox" :value="p.name" x-model="selectedPermissions" class="peer sr-only">
                                                            <div class="h-5 w-5 border-2 border-slate-300 rounded peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-colors flex items-center justify-center group-hover:border-blue-500">
                                                                <i class="bi bi-check text-white opacity-0 peer-checked:opacity-100 font-bold"></i>
                                                            </div>
                                                        </div>
                                                        <span class="text-sm text-slate-600 group-hover:text-slate-800 transition-colors select-none" x-text="p.name"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" @click="closeModal()" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl transition-all font-medium">
                        Cancel
                    </button>
                    <button type="button" @click="saveRole()" :disabled="saving" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm shadow-blue-500/30 transition-all font-medium flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                        <svg x-show="saving" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <i x-show="!saving" class="bi bi-save"></i>
                        <span x-text="saving ? 'Saving...' : 'Save Role'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rolesManager', () => ({
            roles: [],
            allPermissions: {},
            loadingRoles: true,
            loadingPermissions: false,
            showModal: false,
            saving: false,
            
            // Form state
            currentRoleId: null,
            roleName: '',
            selectedPermissions: [],
            
            coreRoles: ['super_admin', 'School Admin', 'Teacher', 'Finance Officer', 'Exams Officer', 'Guardian', 'Student'],
            
            init() {
                this.loadRoles();
            },
            
            isCoreRole(name) {
                return this.coreRoles.includes(name);
            },
            
            async loadRoles() {
                this.loadingRoles = true;
                try {
                    const response = await axios.get('/api/v1/roles');
                    this.roles = response.data.data || response.data;
                } catch (error) {
                    console.error('Failed to load roles:', error);
                    Swal.fire('Error', 'Failed to load roles.', 'error');
                } finally {
                    this.loadingRoles = false;
                }
            },
            
            async loadPermissions() {
                if (Object.keys(this.allPermissions).length > 0) return;
                
                this.loadingPermissions = true;
                try {
                    const response = await axios.get('/api/v1/roles/permissions');
                    this.allPermissions = response.data.data || response.data;
                } catch (error) {
                    console.error('Failed to load permissions:', error);
                    Swal.fire('Error', 'Failed to load permissions.', 'error');
                } finally {
                    this.loadingPermissions = false;
                }
            },
            
            async openModal() {
                this.currentRoleId = null;
                this.roleName = '';
                this.selectedPermissions = [];
                this.showModal = true;
                await this.loadPermissions();
            },
            
            closeModal() {
                this.showModal = false;
            },
            
            async saveRole() {
                if (!this.roleName) {
                    Swal.fire('Validation Error', 'Please provide a role name.', 'warning');
                    return;
                }
                
                if (this.selectedPermissions.length === 0) {
                    Swal.fire('Validation Error', 'Please select at least one permission.', 'warning');
                    return;
                }
                
                this.saving = true;
                try {
                    const url = this.currentRoleId ? `/api/v1/roles/${this.currentRoleId}` : '/api/v1/roles';
                    const method = this.currentRoleId ? 'put' : 'post';
                    
                    await axios[method](url, {
                        name: this.roleName,
                        permissions: this.selectedPermissions
                    });
                    
                    this.closeModal();
                    this.loadRoles();
                    Swal.fire('Success', 'Role saved successfully', 'success');
                } catch (error) {
                    console.error('Failed to save role:', error);
                    Swal.fire('Error', error.response?.data?.message || 'Failed to save role', 'error');
                } finally {
                    this.saving = false;
                }
            },
            
            async viewRole(role) {
                const permissionsList = role.permissions?.length > 0 
                    ? role.permissions.map(p => p.name).join(', ') 
                    : 'No permissions';
                    
                Swal.fire({
                    title: role.name,
                    html: `
                        <div class="text-left mt-4">
                            <h6 class="font-bold text-slate-800 mb-2 border-b pb-2">Assigned Permissions:</h6>
                            <div class="text-sm text-slate-600 bg-slate-50 p-4 rounded-xl leading-relaxed">
                                ${permissionsList}
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonColor: '#3b82f6'
                });
            },
            
            async editRole(role) {
                this.currentRoleId = role.id;
                this.roleName = role.name;
                this.selectedPermissions = role.permissions?.map(p => p.name) || [];
                this.showModal = true;
                await this.loadPermissions();
            },
            
            async deleteRole(role) {
                const result = await Swal.fire({
                    title: 'Delete Role?',
                    html: `Are you sure you want to delete <span class="font-bold text-red-500">${role.name}</span>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Yes, delete it'
                });
                
                if (result.isConfirmed) {
                    try {
                        await axios.delete(`/api/v1/roles/${role.id}`);
                        this.roles = this.roles.filter(r => r.id !== role.id);
                        Swal.fire('Deleted!', 'Role has been deleted.', 'success');
                    } catch (error) {
                        Swal.fire('Error', error.response?.data?.message || 'Failed to delete role', 'error');
                    }
                }
            }
        }));
    });
</script>
@endsection
