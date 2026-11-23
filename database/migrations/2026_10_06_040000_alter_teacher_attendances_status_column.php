<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE teacher_attendances MODIFY status VARCHAR(50) NOT NULL DEFAULT 'present'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE teacher_attendances MODIFY status ENUM('present', 'absent', 'late', 'leave') NOT NULL DEFAULT 'present'");
    }
};
