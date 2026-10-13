<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAccessRequest extends Model
{
    protected $fillable = [
        'teacher_id',
        'name',
        'email',
        'phone',
        'password',
        'image',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $hidden = ['password'];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}