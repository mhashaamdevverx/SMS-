<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $students = DB::table('Students')
            ->join('users', 'users.id', '=', 'Students.login_user_id')
            ->where('users.email', 'like', '%@akm.std')
            ->select('Students.id', 'Students.name', 'Students.std_id', 'Students.login_user_id', 'users.email as login_email')
            ->get();

        foreach ($students as $student) {
            $base = Str::slug(trim((string) $student->name), '.');
            $base = $base !== '' ? $base : 'student';
            $email = $base.'@akm.std';
            if ($email !== $student->login_email && DB::table('users')->where('email', $email)->exists()) {
                $suffix = (string) ($student->std_id ?: $student->id);
                $email = $base.'.'.$suffix.'@akm.std';
                $counter = 1;
                while ($email !== $student->login_email && DB::table('users')->where('email', $email)->exists()) {
                    $counter++;
                    $email = $base.'.'.$suffix.'.'.$counter.'@akm.std';
                }
            }

            if ($email !== $student->login_email) {
                DB::table('users')->where('id', $student->login_user_id)->update(['email' => $email]);
            }
        }
    }

    public function down(): void
    {
        // Portal email changes are not reversible without storing prior addresses.
    }
};