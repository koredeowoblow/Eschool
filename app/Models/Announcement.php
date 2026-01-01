<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTenancyTrait;

class Announcement extends Model
{
    use SoftDeletes, HasTenancyTrait;

    protected $fillable = [
        'school_id',
        'user_id',
        'title',
        'content',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
