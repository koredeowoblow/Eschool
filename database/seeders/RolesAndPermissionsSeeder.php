<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\School;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Global Permissions (No team_id needed strictly for permissions def, but usually good to have them available globally or per team)
        // Spatie docs: Permissions can be global. Roles are Team specific.
        // We will create permissions globally (school_id = null) so they can be assigned to any school role. 
        // OR we create them per school. Spatie recommends global permissions, scoped roles.

        $permissions = [
            // Finance
            'finance.manage.fees',
            'finance.generate.invoices',
            'finance.record.payments',
            'finance.manage.discounts',
            'finance.export.reports',
            'finance.view.reports', // Read-only

            // Results
            'results.enter',
            'results.submit.review',
            'results.review',
            'results.approve',
            'results.publish',
            'results.lock',
            'results.view.history',

            // Audit
            'audit.view.logs',
            'audit.view.finance',
            'audit.view.results',

            // Guardian
            'guardian.view.results',
            'guardian.view.attendance',
            'guardian.view.fees',
            'guardian.download.receipts',
        ];

        foreach ($permissions as $perm) {
            // Create global permissions with api guard
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }

        // 2. Create Global Preset Roles (school_id = null)

        // Super Admin (System Wide)
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'api',
            'school_id' => null
        ]);
        $superAdmin->syncPermissions($permissions);

        // School Admin
        $admin = Role::firstOrCreate([
            'name' => 'School Admin',
            'guard_name' => 'api',
            'school_id' => null
        ]);
        $admin->syncPermissions([
            'finance.view.reports',
            'audit.view.logs',
            'audit.view.results',
            'audit.view.finance',
            'results.publish',
            'results.lock'
        ]);

        // Finance Officer
        $financeOfficer = Role::firstOrCreate([
            'name' => 'Finance Officer',
            'guard_name' => 'api',
            'school_id' => null
        ]);
        $financeOfficer->syncPermissions([
            'finance.manage.fees',
            'finance.generate.invoices',
            'finance.record.payments',
            'finance.manage.discounts',
            'finance.export.reports',
            'finance.view.reports',
            'audit.view.finance'
        ]);

        // Teacher
        $teacher = Role::firstOrCreate([
            'name' => 'Teacher',
            'guard_name' => 'api',
            'school_id' => null
        ]);
        $teacher->syncPermissions([
            'results.enter',
            'results.submit.review',
            'results.view.history'
        ]);

        // Exams Officer
        $examsOfficer = Role::firstOrCreate([
            'name' => 'Exams Officer',
            'guard_name' => 'api',
            'school_id' => null
        ]);
        $examsOfficer->syncPermissions([
            'results.review',
            'results.approve',
            'results.view.history'
        ]);

        // Guardian
        $guardian = Role::firstOrCreate([
            'name' => 'Guardian',
            'guard_name' => 'api',
            'school_id' => null
        ]);
        $guardian->syncPermissions([
            'guardian.view.results',
            'guardian.view.attendance',
            'guardian.view.fees',
            'guardian.download.receipts'
        ]);

        // Student
        $student = Role::firstOrCreate([
            'name' => 'Student',
            'guard_name' => 'api',
            'school_id' => null
        ]);
    }
}
