<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'result_id',
        'school_id',
        'data',
        'changed_by',
        'action',
        'reason',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
