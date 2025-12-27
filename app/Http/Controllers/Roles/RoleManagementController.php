<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\AuditLogger;

class RoleManagementController extends Controller
{
    /**
     * List all roles for the school
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->hasRole('School Admin') && !$user->hasRole('super_admin')) {
            return $this->error('Unauthorized', 403);
        }

        $query = Role::query()->with('permissions');

        // Super admin sees all schools' roles. 
        // Others see their school's roles + global preset roles (school_id is null)
        if (!$user->hasRole('super_admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('school_id', $user->school_id)
                    ->orWhereNull('school_id');
            })->where('name', '!=', 'super_admin');
        }

        $roles = $query->get();

        return $this->success($roles);
    }

    /**
     * Get all available permissions
     */
    public function getPermissions()
    {
        $user = Auth::user();

        if (!$user->hasRole('School Admin') && !$user->hasRole('super_admin')) {
            return $this->error('Unauthorized', 403);
        }

        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return $this->success($permissions);
    }

    /**
     * Create a new custom role
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('School Admin') && !$user->hasRole('super_admin')) {
            return $this->error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'school_id' => $request->get('school_id', $user->school_id),
            'guard_name' => 'api'
        ]);

        $role->givePermissionTo($validated['permissions']);

        AuditLogger::logCreate('role', $role, [
            'permissions' => $validated['permissions']
        ]);

        return $this->success($role->load('permissions'), 'Role created successfully', 201);
    }

    /**
     * Update role permissions
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user->hasRole('School Admin') && !$user->hasRole('super_admin')) {
            return $this->error('Unauthorized', 403);
        }

        $query = Role::query();

        // Super admin can update any role.
        // Others can only update roles belonging to their school.
        if (!$user->hasRole('super_admin')) {
            $query->where('school_id', $user->school_id);
        }

        $role = $query->find($id);

        if (!$role) {
            return $this->error('Role not found', 404);
        }

        // Prevent editing core roles
        $coreRoles = ['super_admin', 'School Admin', 'Teacher', 'Finance Officer', 'Exams Officer', 'Guardian', 'Student'];
        if (in_array($role->name, $coreRoles)) {
            return $this->error('Cannot modify core system roles', 403);
        }

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $oldPermissions = $role->permissions->pluck('name')->toArray();

        $role->syncPermissions($validated['permissions']);

        AuditLogger::logStateChange(
            'role',
            $role,
            ['permissions' => $oldPermissions],
            ['permissions' => $validated['permissions']],
            'Role permissions updated'
        );

        return $this->success($role->load('permissions'), 'Role updated successfully');
    }

    /**
     * Delete a custom role
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user->hasRole('School Admin') && !$user->hasRole('super_admin')) {
            return $this->error('Unauthorized', 403);
        }

        $query = Role::query();

        // Super admin can delete any role.
        // Others can only delete roles belonging to their school.
        if (!$user->hasRole('super_admin')) {
            $query->where('school_id', $user->school_id);
        }

        $role = $query->find($id);

        if (!$role) {
            return $this->error('Role not found', 404);
        }

        // Prevent deleting core roles
        $coreRoles = ['super_admin', 'School Admin', 'Teacher', 'Finance Officer', 'Exams Officer', 'Guardian', 'Student'];
        if (in_array($role->name, $coreRoles)) {
            return $this->error('Cannot delete core system roles', 403);
        }

        // Check if role is assigned to any users
        if ($role->users()->count() > 0) {
            return $this->error('Cannot delete role that is assigned to users', 400);
        }

        AuditLogger::logDelete('role', $role->id, ['name' => $role->name]);

        $role->delete();

        return $this->success(null, 'Role deleted successfully');
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('School Admin') && !$user->hasRole('super_admin')) {
            return $this->error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|string'
        ]);

        // Security check: only super_admin can assign super_admin
        if ($validated['role_name'] === 'super_admin' && !$user->hasRole('super_admin')) {
            return $this->error('Unauthorized to assign super_admin role', 403);
        }

        $query = \App\Models\User::query();
        if (!$user->hasRole('super_admin')) {
            $query->where('school_id', $user->school_id);
        }
        $targetUser = $query->find($validated['user_id']);

        if (!$targetUser) {
            return $this->error('User not found', 404);
        }

        $targetUser->assignRole($validated['role_name']);

        AuditLogger::logRoleChange(
            $targetUser->id,
            'assigned',
            $validated['role_name'],
            $user->school_id
        );

        return $this->success(null, 'Role assigned successfully');
    }
}
