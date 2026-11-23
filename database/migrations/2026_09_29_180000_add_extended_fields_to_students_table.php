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
        Schema::table('Students', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('emergency_phone')->nullable()->after('phone_no');
            $table->string('registration_no')->nullable()->after('std_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Students', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'emergency_phone', 'registration_no']);
        });
    }
};
