<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'student_name',
        'student_section',
        'student_class',
        'subject',
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade',
        'term',
        'user_id',
    ];
}
