<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'note_id',
        'file_path',
        'file_type',
        'school_id',
    ];
}
