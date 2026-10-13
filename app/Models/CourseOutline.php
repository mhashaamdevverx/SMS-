<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseOutline extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_name',
        'section_name',
        'subject_name',
        'title',
        'description',
        'file_path',
        'term',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
