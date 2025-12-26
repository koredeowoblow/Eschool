<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenancyTrait;

class Result extends Model
{
    use HasTenancyTrait;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_PUBLISHED = 'published';
    const STATUS_LOCKED = 'locked';

    protected $fillable = [
        'assessment_id',
        'student_id',
        'marks_obtained',
        'grade',
        'remark',
        'school_id',
        'status',
        'reviewer_id',
        'submitted_at',
        'reviewed_at',
        'published_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function versions()
    {
        return $this->hasMany(ResultVersion::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check if result can be edited
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SUBMITTED]);
    }

    /**
     * Check if result is locked
     */
    public function isLocked(): bool
    {
        return $this->status === self::STATUS_LOCKED;
    }
}
