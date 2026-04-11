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
        Schema::table('training', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_employees')->nullable()->after('expiry_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training', function (Blueprint $table) {
            $table->dropColumn('max_employees');
        });
    }
};
