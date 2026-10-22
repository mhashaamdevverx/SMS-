<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerApplication extends Model
{
    protected $table = 'career_applications';

    protected $fillable = [
        'application_no',
        'job_title',
        'wing',
        'full_name',
        'email',
        'phone',
        'cnic',
        'city',
        'gender',
        'introduction',
        'highest_degree',
        'degree_title',
        'institute',
        'passing_year',
        'marks_cgpa',
        'experience_years',
        'last_organization',
        'last_designation',
        'core_skills',
        'cover_note',
        'has_relative',
        'relative_name',
        'relative_designation',
        'relative_relation',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'has_relative' => 'boolean',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Generate sequential unique application number: JOB-YYYY-XXXX
     */
    public static function generateApplicationNumber(): string
    {
        $year = date('Y');
        $latest = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNum = $latest ? ($latest->id + 1) : 1;
        return sprintf('JOB-%s-%04d', $year, $nextNum);
    }
}