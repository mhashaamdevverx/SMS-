<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'user_id',
        'class_name',
        'tuition_fee',
        'admission_fee',
        'exam_fee',
        'test_session_fee',
        'lab_sports_fee',
        'other_charges',
        'total_fee',
    ];
}
