<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherLeave extends Model
{
    protected $fillable = [
        'teacher_id',
        'teacher_name',
        'leave_type',
        'leave_date',
        'reason',
        'status',
        'approved_by',
        'user_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
