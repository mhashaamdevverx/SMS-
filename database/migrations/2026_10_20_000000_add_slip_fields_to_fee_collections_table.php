<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_collections', 'father_name')) {
                $table->string('father_name')->nullable()->after('student_name');
            }
            if (!Schema::hasColumn('fee_collections', 'mobile_no')) {
                $table->string('mobile_no')->nullable()->after('student_section');
            }
            if (!Schema::hasColumn('fee_collections', 'issue_date')) {
                $table->date('issue_date')->nullable()->after('month');
            }
            if (!Schema::hasColumn('fee_collections', 'arrears')) {
                $table->decimal('arrears', 10, 2)->default(0)->after('admission_fee');
            }
            if (!Schema::hasColumn('fee_collections', 'late_fee')) {
                $table->decimal('late_fee', 10, 2)->default(0)->after('net_amount');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'father_name')) {
                $table->string('father_name')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'mobile_no', 'issue_date', 'arrears', 'late_fee']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('father_name');
        });
    }
};
