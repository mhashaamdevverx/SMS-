<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLeave extends Model
{
    use HasFactory;

    protected $table = 'student_leaves';

    protected $fillable = [
        'student_id',
        'std_id',
        'student_name',
        'student_class',
        'student_section',
        'leave_type',
        'leave_date',
        'end_date',
        'reason',
        'status',
        'is_fine_applied',
        'fine_amount',
        'approved_by',
        'user_id',
    ];

    protected $casts = [
        'leave_date' => 'date',
        'end_date' => 'date',
        'is_fine_applied' => 'boolean',
        'fine_amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
