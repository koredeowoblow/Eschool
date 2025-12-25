<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AppOwnerSeeder extends Seeder
{
    public function run()
    {
        $ownerEmail = 'owner@example.com';

        $owner = User::firstOrCreate(
            ['email' => $ownerEmail],
            [
                'name' => 'Application Owner',
                'password' => Hash::make('password123'), // default password
            ]
        );

        // Assign super_admin role
        $role = Role::findByName('super_admin', 'api');
        $owner->assignRole($role);
        echo "✅ Application owner created and assigned super_admin role.\n";
    }
}
