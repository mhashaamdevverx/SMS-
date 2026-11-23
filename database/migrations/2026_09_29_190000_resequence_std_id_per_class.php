<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Resequence std_id per class so every class starts from 1.
     */
    public function up(): void
    {
        $classes = DB::table('Students')
            ->whereNotNull('class')
            ->where('class', '!=', '')
            ->distinct()
            ->pluck('class');

        foreach ($classes as $className) {
            $students = DB::table('Students')
                ->where('class', $className)
                ->orderBy('id')
                ->get();

            $roll = 1;
            foreach ($students as $student) {
                $cleanClass = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $className));
                $formattedRoll = str_pad($roll, 3, '0', STR_PAD_LEFT);
                $regNo = "REG-2026-" . ($cleanClass ?: 'STD') . "-" . $formattedRoll;

                DB::table('Students')
                    ->where('id', $student->id)
                    ->update([
                        'std_id' => $roll,
                        'registration_no' => $regNo,
                    ]);

                $roll++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
