<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_outlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('class_name');
            $table->string('section_name')->nullable();
            $table->string('subject_name');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('term')->default('First Term');
            $table->timestamps();
        });

        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'leave'])->default('present');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('month'); // e.g. 2026-09
            $table->decimal('basic_salary', 10, 2)->default(45000);
            $table->decimal('allowances', 10, 2)->default(5000);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(50000);
            $table->enum('status', ['Paid', 'Pending', 'Processing'])->default('Paid');
            $table->date('paid_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_salaries');
        Schema::dropIfExists('teacher_attendances');
        Schema::dropIfExists('course_outlines');
    }
};
