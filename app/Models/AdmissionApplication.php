<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionApplication extends Model
{
    protected $table = 'admission_applications';

    protected $fillable = [
        'application_no',
        'student_name',
        'guardian_name',
        'gender',
        'student_bform',
        'guardian_cnic',
        'guardian_phone',
        'guardian_email',
        'grade_applied',
        'previous_school',
        'previous_class',
        'previous_marks',
        'message',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Generate sequential unique application number: ADM-YYYY-XXXX
     */
    public static function generateApplicationNumber(): string
    {
        $year = date('Y');
        $latest = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNum = $latest ? ($latest->id + 1) : 1;
        return sprintf('ADM-%s-%04d', $year, $nextNum);
    }
}
