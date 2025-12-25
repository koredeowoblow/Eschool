<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenancyTrait;

class GradingScale extends Model
{
    use HasTenancyTrait;

    protected $fillable = [
        'session_id',
        'min_score',
        'max_score',
        'grade_label',
        'remark',
        'is_pass',
        'is_default',
        'school_id',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
