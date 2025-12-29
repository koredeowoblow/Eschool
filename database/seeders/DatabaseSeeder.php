<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Run Roles and Permissions Seeder (Global & Preset Roles)
        $this->call(RolesAndPermissionsSeeder::class);
        echo "✅ Roles and permissions seeded (Global).\n";

        // 2. Run App Owner Seeder (Super Admin)
        $this->call(AppOwnerSeeder::class);
        echo "✅ Super Admin user seeded.\n";

        // 3. Run Plan Seeder
        $this->call(PlanSeeder::class);
        echo "✅ Plans seeded.\n";
    }
}
