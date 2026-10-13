<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    protected $fillable = ['exam_id', 'subject_id', 'subject_name', 'exam_date', 'start_time', 'end_time'];

    protected $casts = ['exam_date' => 'date'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}