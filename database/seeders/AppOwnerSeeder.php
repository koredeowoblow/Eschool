<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AppOwnerSeeder extends Seeder
{
    public function run()
    {
        $ownerEmail = 'owner@example.com';

        $owner = User::firstOrCreate(
            ['email' => $ownerEmail],
            [
                'name' => 'Application Owner',
                'password' => Hash::make('password123'),
                'school_id' => null  // Super admin has no school
            ]
        );

        // Get super_admin role
        $superAdminRole = Role::where('name', 'super_admin')
            ->where('guard_name', 'api')
            ->first();

        if (!$superAdminRole) {
            echo "❌ super_admin role not found. Please run RolesAndPermissionsSeeder first.\n";
            return;
        }

        // Check if already assigned
        $alreadyAssigned = DB::table('model_has_roles')
            ->where('model_id', $owner->id)
            ->where('model_type', User::class)
            ->where('role_id', $superAdminRole->id)
            ->exists();

        if (!$alreadyAssigned) {
            // Manually insert with null school_id for global super_admin
            DB::table('model_has_roles')->insert([
                'role_id' => $superAdminRole->id,
                'model_type' => User::class,
                'model_id' => $owner->id,
                'school_id' => null  // Global role - no school
            ]);

            // Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }

        echo "✅ Application owner created and assigned super_admin role (global, no school).\n";
    }
}
