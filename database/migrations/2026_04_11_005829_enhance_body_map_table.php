<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_map', function (Blueprint $table) {
            $table->unsignedBigInteger('home_id')->nullable()->after('id');
            $table->string('injury_type', 50)->nullable()->after('sel_body_map_id');
            $table->text('injury_description')->nullable()->after('injury_type');
            $table->date('injury_date')->nullable()->after('injury_description');
            $table->string('injury_size', 100)->nullable()->after('injury_date');
            $table->string('injury_colour', 50)->nullable()->after('injury_size');
            $table->unsignedBigInteger('created_by')->nullable()->after('is_deleted');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            $table->index(['home_id', 'is_deleted'], 'bm_home_deleted_idx');
            $table->index(['service_user_id', 'is_deleted'], 'bm_su_deleted_idx');
        });

        // Backfill home_id from su_risk table for existing rows
        DB::statement("
            UPDATE body_map bm
            INNER JOIN su_risk sr ON bm.su_risk_id = sr.id
            SET bm.home_id = sr.home_id
            WHERE bm.home_id IS NULL
        ");

        // Backfill created_by from staff_id
        DB::statement("
            UPDATE body_map
            SET created_by = staff_id
            WHERE created_by IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('body_map', function (Blueprint $table) {
            $table->dropIndex('bm_home_deleted_idx');
            $table->dropIndex('bm_su_deleted_idx');
            $table->dropColumn([
                'home_id', 'injury_type', 'injury_description',
                'injury_date', 'injury_size', 'injury_colour',
                'created_by', 'updated_by',
            ]);
        });
    }
};
