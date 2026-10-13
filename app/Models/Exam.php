<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['name', 'session', 'user_id'];

    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function rollSlips()
    {
        return $this->hasMany(RollSlip::class);
    }
}