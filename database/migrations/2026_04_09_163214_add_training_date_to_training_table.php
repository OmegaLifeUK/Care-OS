<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('training', function (Blueprint $table) {
            $table->date('training_date')->nullable()->after('training_year');
        });

        // Backfill existing rows: set training_date from month/year (use 1st of month)
        DB::table('training')
            ->whereNotNull('training_month')
            ->whereNotNull('training_year')
            ->whereNull('training_date')
            ->get()
            ->each(function ($row) {
                DB::table('training')->where('id', $row->id)->update([
                    'training_date' => sprintf('%04d-%02d-01', $row->training_year, $row->training_month),
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training', function (Blueprint $table) {
            $table->dropColumn('training_date');
        });
    }
};
