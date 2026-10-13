<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RollSlip extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'arrangement_id', 'student_name', 'roll_no',
        'class_name', 'section_name', 'room_no', 'chair_no', 'datesheet', 'issued_by',
    ];

    protected $casts = ['datesheet' => 'array'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function arrangement()
    {
        return $this->belongsTo(Arrangement::class);
    }
}