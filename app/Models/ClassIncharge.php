<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassIncharge extends Model
{
    protected $fillable = ['user_id', 'teacher_id', 'class_id', 'assigned_by'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classRecord(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}