<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradingScaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolId = \App\Models\School::first()->id ?? 1;
        $scales = [
            ['grade_label' => 'A', 'min_score' => 80, 'max_score' => 100, 'remark' => 'Excellent', 'is_pass' => true],
            ['grade_label' => 'B', 'min_score' => 70, 'max_score' => 79, 'remark' => 'Very Good', 'is_pass' => true],
            ['grade_label' => 'C', 'min_score' => 60, 'max_score' => 69, 'remark' => 'Good', 'is_pass' => true],
            ['grade_label' => 'D', 'min_score' => 50, 'max_score' => 59, 'remark' => 'Credit', 'is_pass' => true],
            ['grade_label' => 'E', 'min_score' => 40, 'max_score' => 49, 'remark' => 'Pass', 'is_pass' => true],
            ['grade_label' => 'F', 'min_score' => 0, 'max_score' => 39, 'remark' => 'Fail', 'is_pass' => false],
        ];

        foreach ($scales as $scale) {
            \App\Models\GradingScale::create(array_merge($scale, [
                'school_id' => $schoolId,
                'is_default' => true,
            ]));
        }
    }
}
