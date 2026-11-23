<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('fee_structures', 'test_session_fee')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                $table->decimal('test_session_fee', 10, 2)->default(0)->after('exam_fee');
            });
        }

        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'monthly_fee')) {
                $table->decimal('monthly_fee', 10, 2)->nullable()->default(0);
            }
            if (!Schema::hasColumn('students', 'admission_fee')) {
                $table->decimal('admission_fee', 10, 2)->nullable()->default(0);
            }
            if (!Schema::hasColumn('students', 'test_session_fee')) {
                $table->decimal('test_session_fee', 10, 2)->nullable()->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropColumn('test_session_fee');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['monthly_fee', 'admission_fee', 'test_session_fee']);
        });
    }
};
