<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{  
    public $timestamps = true;
    protected $table = 'teachers';

    protected $fillable = [
        'teacher_name',
        'teacher_image',
        'teacher_email',
        'teacher_phone',
        'course',
        'user_id',
        'login_user_id',
    ];

    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'teacher_id');
    }

    public function loginAccount()
    {
        return $this->belongsTo(User::class, 'login_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'login_user_id');
    }

    public function salaries()
    {
        return $this->hasMany(TeacherSalary::class, 'teacher_id');
    }
}
