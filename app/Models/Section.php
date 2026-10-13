<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public $timestamps = false;

    protected $table = 'sections';

    protected $fillable = [
        'section_name',
        'user_id'
    ];
}