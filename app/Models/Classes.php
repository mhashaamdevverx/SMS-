<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'class_no',
        'class_name',
        'chairs',
        'section_name',
        'user_id',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class', 'class_name');
    }

    public function getClassLabelAttribute(): string
    {
        return $this->section_name 
            ? "{$this->class_name} ({$this->section_name})" 
            : $this->class_name;
    }
}