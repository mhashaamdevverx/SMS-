<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arrangements', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('chairs');
            $table->string('room', 50);
            $table->string('roll_no', 20);
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // ek room ki ek chair sirf ek dafa
            $table->unique(['room', 'chairs']);
            // ek class ka ek roll no sirf ek dafa
            $table->unique(['class_id', 'roll_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arrangements');
    }
};