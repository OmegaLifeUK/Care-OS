<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medication_logs', function (Blueprint $table) {
            $table->tinyInteger('is_deleted')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('medication_logs', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });
    }
};
