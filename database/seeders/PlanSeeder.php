<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'name' => 'Basic',
                // 'slug' => 'basic', // Removed as it is not in the schema 
                // Wait, Plan model fillable doesn't have slug. 
                // Let's check Plan model again or migration for columns. 
                // Original blade used "value='basic'". API usually returns ID. Name is "Basic".
                // I'll stick to fillable: name, price, description, limits.
                'price' => 29,
                'description' => 'Basic plan for small schools',
                'no_of_students' => 100,
                'no_of_teachers' => 5,
                'no_of_guardians' => 100,
                'no_of_staff' => 5,
            ],
            [
                'name' => 'Standard',
                'price' => 59,
                'description' => 'Standard plan for growing schools',
                'no_of_students' => 500,
                'no_of_teachers' => 20,
                'no_of_guardians' => 500,
                'no_of_staff' => 20,
            ],
            [
                'name' => 'Premium',
                'price' => 99,
                'description' => 'Premium plan for established schools',
                'no_of_students' => 1000,
                'no_of_teachers' => 50,
                'no_of_guardians' => 1000,
                'no_of_staff' => 50,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(
                ['name' => $plan['name']], // Check by name
                $plan
            );
        }
    }
}
