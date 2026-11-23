<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('admission_applications') && !Schema::hasColumn('admission_applications', 'gender')) {
            Schema::table('admission_applications', function (Blueprint $table) {
                $table->string('gender', 25)->nullable()->after('guardian_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('admission_applications') && Schema::hasColumn('admission_applications', 'gender')) {
            Schema::table('admission_applications', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
    }
};
