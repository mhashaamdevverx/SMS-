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
        if (!Schema::hasTable('career_applications')) {
            Schema::create('career_applications', function (Blueprint $table) {
                $table->id();
                $table->string('application_no', 30)->unique()->index();
                $table->string('job_title', 150)->index();
                $table->string('wing', 80)->nullable();

                // Step 1: Personal & Introduction
                $table->string('full_name', 120);
                $table->string('email', 100);
                $table->string('phone', 30);
                $table->string('cnic', 25)->index(); // 13 digits
                $table->string('city', 80)->nullable();
                $table->string('gender', 25)->nullable();
                $table->text('introduction')->nullable();

                // Step 2: Education Details
                $table->string('highest_degree', 80);
                $table->string('degree_title', 150);
                $table->string('institute', 180);
                $table->string('passing_year', 20);
                $table->string('marks_cgpa', 50)->nullable();

                // Step 3: Experience Details
                $table->string('experience_years', 50);
                $table->string('last_organization', 180)->nullable();
                $table->string('last_designation', 120)->nullable();
                $table->string('core_skills', 255)->nullable();
                $table->text('cover_note')->nullable();

                // Step 4: Relative / Reference Details
                $table->boolean('has_relative')->default(false);
                $table->string('relative_name', 120)->nullable();
                $table->string('relative_designation', 120)->nullable();
                $table->string('relative_relation', 80)->nullable();

                // Management & Status
                $table->string('status', 30)->default('pending')->index(); // pending, shortlisted, interviewed, rejected, hired
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
        Schema::dropIfExists('career_applications');
    }
};
