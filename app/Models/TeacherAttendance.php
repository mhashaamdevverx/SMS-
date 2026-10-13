<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    protected $fillable = [
        'teacher_id',
        'date',
        'status',
        'time_in',
        'time_out',
        'remarks',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
