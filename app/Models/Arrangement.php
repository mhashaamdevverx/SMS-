<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arrangement extends Model
{
    protected $table = 'arrangements';

    protected $fillable = [
        'chairs',
        'room',
        'roll_no',
        'class_id',
        'user_id',
    ];

    public function classRecord()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}