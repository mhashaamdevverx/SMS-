<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Students', function (Blueprint $table) {
            $table->text('subject')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('Students', function (Blueprint $table) {
            $table->string('subject')->nullable()->change();
        });
    }
};