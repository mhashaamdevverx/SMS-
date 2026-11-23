<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedInteger('class_no')->nullable()->after('id');
        });

        $classOrder = "CASE UPPER(TRIM(class_name))
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

        DB::table('classes')
            ->orderByRaw($classOrder)
            ->orderBy('class_name')
            ->orderBy('section_name')
            ->orderBy('id')
            ->pluck('id')
            ->values()
            ->each(fn ($id, $index) => DB::table('classes')->where('id', $id)->update([
                'class_no' => $index + 1,
            ]));

        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedInteger('class_no')->nullable(false)->change();
            $table->unique('class_no');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropUnique(['class_no']);
            $table->dropColumn('class_no');
        });
    }
};