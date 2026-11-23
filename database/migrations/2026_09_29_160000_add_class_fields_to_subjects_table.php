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
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->after('user_id');
            $table->string('class_name', 50)->nullable()->after('class_id');
            $table->string('section_name', 50)->nullable()->after('class_name');

            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['class_id', 'class_name', 'section_name']);
        });
    }
};
