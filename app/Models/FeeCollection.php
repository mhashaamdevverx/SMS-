<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCollection extends Model
{
    protected $fillable = [
        'user_id',
        'voucher_no',
        'student_id',
        'student_name',
        'father_name',
        'std_id',
        'student_class',
        'student_section',
        'mobile_no',
        'month',
        'issue_date',
        'tuition_fee',
        'admission_fee',
        'arrears',
        'exam_fee',
        'lab_sports_fee',
        'other_charges',
        'gross_amount',
        'discount',
        'net_amount',
        'late_fee',
        'status',
        'due_date',
        'payment_date',
        'payment_method',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
