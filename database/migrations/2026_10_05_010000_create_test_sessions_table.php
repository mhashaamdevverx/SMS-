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
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('std_id')->nullable();
            $table->string('month_key'); // e.g. 2026-10
            $table->string('month_title'); // e.g. October 2026
            $table->string('title'); // e.g. Monthly Test Series • October 2026
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('Upcoming'); // Evaluated, Upcoming
            $table->integer('total_max_marks')->default(250);
            $table->integer('total_obtained_marks')->default(0);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->string('grade')->nullable(); // A+, A, B, etc.
            $table->json('schedule_json')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_sessions');
    }
};
