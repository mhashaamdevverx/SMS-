<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'std_id',
        'month_key',
        'month_title',
        'title',
        'start_date',
        'end_date',
        'status',
        'total_max_marks',
        'total_obtained_marks',
        'percentage',
        'grade',
        'schedule_json',
        'user_id',
    ];

    protected $casts = [
        'schedule_json' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
