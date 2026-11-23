<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('session', 100)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_name');
            $table->date('exam_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
            $table->unique(['exam_id', 'subject_name']);
        });

        Schema::create('roll_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('arrangement_id')->nullable();
            $table->string('student_name');
            $table->unsignedBigInteger('roll_no');
            $table->string('class_name', 50);
            $table->string('section_name', 100)->nullable();
            $table->string('room_no', 50);
            $table->unsignedInteger('chair_no');
            $table->json('datesheet');
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->timestamps();
            $table->unique(['exam_id', 'student_id']);
            $table->index(['exam_id', 'room_no', 'chair_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roll_slips');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exams');
    }
};