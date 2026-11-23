<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('Students', function (Blueprint $table) {
        $table->string('subject')->nullable();
        $table->integer('total_marks')->nullable();
        $table->integer('obtained_marks')->nullable();
    });
}

public function down()
{
    Schema::table('Students', function (Blueprint $table) {
        $table->dropColumn(['subject', 'total_marks', 'obtained_marks']);
    });
}
};
