<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSalary extends Model
{
    protected $fillable = [
        'teacher_id',
        'month',
        'basic_salary',
        'allowances',
        'deductions',
        'net_salary',
        'status',
        'paid_date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
