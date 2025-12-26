<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolPlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'no_of_students',
        'no_of_teachers',
        'no_of_guardians',
        'no_of_staff',
        'plan_id',
        'school_id',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class); // Ideally hasOne, but belongsTo is inverse of hasOne/hasMany logic if school has the FK, wait. 
        // School table has school_plan_id. So School belongsTo SchoolPlan. 
        // And SchoolPlan hasMany School (if it was shared, but custom plan is usually 1-1). 
        // But the table requirement 'school_plans' has 'school_id' column too? 
        // Let's check my migration.
        // My migration: $table->foreignId('school_id')->nullable()->constrained('schools')->cascadeOnDelete();
        // And School table has school_plan_id.
        // This is a bit circular or bidirectional. 
        // If a School has a SchoolPlan, usually School -> belongsTo -> SchoolPlan.
        // But if SchoolPlan is custom for a School, SchoolPlan -> belongsTo -> School.
        // Let's support both directions for flexibility but primary link is School -> SchoolPlan (active plan).
        // The SchoolPlan table having 'school_id' implies it belongs to a school specifically.
    }

    public function schools()
    {
        return $this->hasMany(School::class);
    }
}
