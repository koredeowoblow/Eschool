<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryBorrowing extends Model
{
    protected $fillable = [
        'book_id',
        'user_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
        'school_id',
    ];
}
