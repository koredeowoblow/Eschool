<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\LessonNote;

class Attachment extends Model
{
    protected $fillable = [
        'note_id',
        'title',
        'class_id',
        'subject_id',
        'file_path',
        'file_type',
        'school_id',
    ];

    public function note()
    {
        return $this->belongsTo(LessonNote::class, 'note_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
