<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Give every existing student a final sequential Std. ID in list order.
     */
    public function up(): void
    {
        $classOrder = "CASE UPPER(TRIM(`class`))
            WHEN 'PRE-KG' THEN 1
            WHEN 'KG' THEN 2
            WHEN 'ONE' THEN 3
            WHEN 'TWO' THEN 4
            WHEN 'THREE' THEN 5
            WHEN 'FOUR' THEN 6
            WHEN 'FIVE' THEN 7
            WHEN 'SIX' THEN 8
            WHEN 'SEVEN' THEN 9
            WHEN 'EIGHT' THEN 10
            WHEN 'NINE' THEN 11
            WHEN 'TEN' THEN 12
            ELSE 999
        END";
        $sectionOrder = "CASE LOWER(TRIM(section))
            WHEN 'red' THEN 1
            WHEN 'blue' THEN 2
            WHEN 'pink' THEN 3
            WHEN 'green' THEN 4
            ELSE 999
        END";

        $studentIds = DB::table('Students')
            ->orderByRaw($classOrder)
            ->orderBy('class')
            ->orderByRaw($sectionOrder)
            ->orderBy('section')
            ->orderBy('std_id')
            ->orderBy('id')
            ->pluck('id')
            ->values();

        // Temporary IDs avoid unique-index collisions during the renumbering.
        $temporaryStart = ((int) DB::table('Students')->max('std_id')) + $studentIds->count() + 1;

        $studentIds->each(fn ($id, $index) => DB::table('Students')
            ->where('id', $id)
            ->update(['std_id' => $temporaryStart + $index]));

        $studentIds->each(fn ($id, $index) => DB::table('Students')
            ->where('id', $id)
            ->update(['std_id' => $index + 1]));
    }

    /** Previous manually assigned Std. IDs cannot be reconstructed safely. */
    public function down(): void
    {
        // Intentionally irreversible.
    }
};