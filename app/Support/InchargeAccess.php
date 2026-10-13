<?php

namespace App\Support;

use App\Models\ClassIncharge;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class InchargeAccess
{
    public static function isIncharge(): bool
    {
        return User::with('role')->find(session('user_id'))?->role?->slug === 'class-incharge';
    }

    public static function assignment(): ?ClassIncharge
    {
        $user = User::with(['role', 'classIncharge.classRecord'])->find(session('user_id'));
        if ($user?->role?->slug !== 'class-incharge') {
            return null;
        }

        abort_unless($user->classIncharge?->classRecord, 403, 'No class has been assigned to this incharge account.');

        return $user->classIncharge;
    }

    public static function studentRecord(): ?Student
    {
        $user = User::with(['role', 'studentRecord'])->find(session('user_id'));
        if ($user?->role?->slug !== 'student') {
            return null;
        }

        abort_unless($user->studentRecord, 403, 'No student profile is linked to this account.');

        return $user->studentRecord;
    }

    public static function constrainStudents(Builder $query): Builder
    {
        $assignment = self::assignment();
        if ($assignment) {
            return $query
                ->whereRaw('LOWER(TRIM(class)) = ?', [mb_strtolower(trim($assignment->classRecord->class_name))])
                ->whereRaw('LOWER(TRIM(section)) = ?', [mb_strtolower(trim($assignment->classRecord->section_name))]);
        }

        $student = self::studentRecord();

        return $student ? $query->whereKey($student->id) : $query;
    }

    public static function assertClassSection(?string $className, ?string $sectionName): void
    {
        $assignment = self::assignment();
        if (!$assignment) {
            return;
        }

        abort_unless(
            mb_strtolower(trim((string) $className)) === mb_strtolower(trim($assignment->classRecord->class_name))
            && mb_strtolower(trim((string) $sectionName)) === mb_strtolower(trim($assignment->classRecord->section_name)),
            403,
            'You can only access your assigned class and section.'
        );
    }

    public static function assertStudent(Student $student): void
    {
        $ownStudent = self::studentRecord();
        if ($ownStudent) {
            abort_unless($ownStudent->id === $student->id, 403, 'You can only access your own student profile.');

            return;
        }

        self::assertClassSection($student->class, $student->section);
    }
}