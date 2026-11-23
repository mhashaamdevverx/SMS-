<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('password_changed')->default(false)->after('password_prompt_dismissed');
        });
        DB::table('Students')->join('users', 'users.id', '=', 'Students.login_user_id')
            ->where('users.password_changed', false)
            ->select('Students.name', 'users.id')->orderBy('users.id')->each(function ($row) {
                $plain = preg_replace('/[^\pL\pN]/u', '', mb_strtolower(trim((string) $row->name))) ?: 'student';
                DB::table('users')->where('id', $row->id)->update(['password' => Hash::make($plain.'@std')]);
            });
    }
    public function down(): void { Schema::table('users', fn (Blueprint $table) => $table->dropColumn('password_changed')); }
};