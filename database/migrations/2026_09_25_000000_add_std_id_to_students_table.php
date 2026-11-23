<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a permanent student ID without invalidating existing rows.
     */
    public function up(): void
    {
        Schema::table('Students', function (Blueprint $table) {
            $table->unsignedBigInteger('std_id')->nullable()->after('id');
        });

        // The primary key is already unique, so it is a safe deterministic ID
        // for every record that predates this column.
        DB::table('Students')->whereNull('std_id')->update([
            'std_id' => DB::raw('id'),
        ]);

        Schema::table('Students', function (Blueprint $table) {
            $table->unsignedBigInteger('std_id')->nullable(false)->change();
            $table->unique('std_id');
        });
    }

    public function down(): void
    {
        Schema::table('Students', function (Blueprint $table) {
            $table->dropUnique(['std_id']);
            $table->dropColumn('std_id');
        });
    }
};