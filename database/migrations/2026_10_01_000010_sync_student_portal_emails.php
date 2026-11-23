<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::table('Students')->whereNotNull('login_user_id')->orderBy('id')->each(function ($student) {
            $email = strtolower(trim((string) $student->email));
            if ($email === '') return;
            $used = DB::table('users')->where('email', $email)->where('id', '!=', $student->login_user_id)->exists();
            if (! $used) DB::table('users')->where('id', $student->login_user_id)->update(['email' => $email, 'updated_at' => now()]);
        });
    }
    public function down(): void {}
};