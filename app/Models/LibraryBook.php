<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryBook extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'category',
        'copies',
        'school_id',
    ];
}
