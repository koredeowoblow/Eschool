<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $roles = [
            'super_admin' => 'Owner of the application',
            'School Admin'       => 'School administrator',
            'teacher'     => 'Teacher user',
            'student'     => 'Student user',
            'guardian'    => 'Parent or guardian',
        ];

        foreach ($roles as $name => $desc) {
            Role::firstOrCreate(['name' => $name], ['guard_name' => 'api']);
        }

        echo "✅ Roles seeded successfully.\n";
    }
}
