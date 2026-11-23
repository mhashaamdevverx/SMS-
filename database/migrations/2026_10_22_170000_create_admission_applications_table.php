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
        if (!Schema::hasTable('admission_applications')) {
            Schema::create('admission_applications', function (Blueprint $table) {
                $table->id();
                $table->string('application_no', 30)->unique()->index();
                $table->string('student_name', 120);
                $table->string('guardian_name', 120);
                $table->string('student_bform', 25)->index(); // 13 digits
                $table->string('guardian_cnic', 25)->index(); // 13 digits
                $table->string('guardian_phone', 30);
                $table->string('guardian_email', 100)->nullable();
                $table->string('grade_applied', 80);
                $table->string('previous_school', 150)->nullable();
                $table->string('previous_class', 60)->nullable();
                $table->string('previous_marks', 60)->nullable();
                $table->text('message')->nullable();
                $table->string('status', 30)->default('pending')->index(); // pending, contacted, approved, rejected
                $table->text('admin_notes')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};
