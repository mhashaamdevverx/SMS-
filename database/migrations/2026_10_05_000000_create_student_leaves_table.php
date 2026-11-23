<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('std_id', 50)->nullable();
            $table->string('student_name', 100)->nullable();
            $table->string('student_class', 50)->nullable();
            $table->string('student_section', 50)->nullable();
            $table->string('leave_type', 50); // 'Casual Leave', 'Sick Leave'
            $table->date('leave_date');
            $table->date('end_date')->nullable();
            $table->text('reason');
            $table->string('status', 20)->default('Pending'); // 'Pending', 'Approved', 'Rejected'
            $table->boolean('is_fine_applied')->default(false);
            $table->decimal('fine_amount', 8, 2)->default(0.00);
            $table->string('approved_by', 100)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'leave_date']);
            $table->index(['student_class', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_leaves');
    }
};
