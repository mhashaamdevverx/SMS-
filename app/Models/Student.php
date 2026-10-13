<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    public $timestamps = false;
    protected $table = 'Students';

    protected $fillable = [
        'std_id',
        'registration_no',
        'first_name',
        'last_name',
        'name',
        'email',
        'phone_no',
        'emergency_phone',
        'class',
        'section',
        'image',
        'user_id',
        'subject',
        'total_marks',
        'obtained_marks',
        'monthly_fee',
        'admission_fee',
        'test_session_fee',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function loginAccount(): BelongsTo
    {
        return $this->belongsTo(User::class, 'login_user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'login_user_id');
    }
}