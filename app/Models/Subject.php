<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    public $timestamps = false;
    protected $table = 'subjects';

    protected $fillable = [
        'subject_name',
        'total_marks',
        'obtained_marks',
        'user_id',
        'class_id',
        'class_name',
        'section_name',
    ];

    public function classRecord()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function getClassLabelAttribute()
    {
        if ($this->class_name && $this->section_name) {
            return "{$this->class_name} ({$this->section_name})";
        }

        if ($this->relationLoaded('classRecord') && $this->classRecord) {
            return "{$this->classRecord->class_name} ({$this->classRecord->section_name})";
        }

        if ($this->class_name) {
            return $this->class_name;
        }

        return null;
    }
}