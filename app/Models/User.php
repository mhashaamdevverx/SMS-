<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'phone',
        'image',
        'password',
        'role_id',
        'must_change_password',
        'password_prompt_dismissed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function classIncharge(): HasOne
    {
        return $this->hasOne(ClassIncharge::class);
    }

    public function studentRecord(): HasOne
    {
        return $this->hasOne(Student::class, 'login_user_id');
    }

    public function teacherRecord(): HasOne
    {
        return $this->hasOne(Teacher::class, 'login_user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (str_ends_with($permission, '.view')) {
            return true;
        }

        $role = $this->role;
        if (!$role) {
            return false;
        }

        if (!$role->relationLoaded('permissions')) {
            $role->load('permissions');
        }

        return $role->permissions->contains('name', $permission);
    }
}