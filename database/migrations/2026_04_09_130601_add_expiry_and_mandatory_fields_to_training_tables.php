<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training', function (Blueprint $table) {
            $table->boolean('is_mandatory')->default(0)->after('status');
            $table->string('category', 50)->nullable()->after('is_mandatory'); // mandatory, recommended, optional
            $table->unsignedSmallInteger('expiry_months')->nullable()->after('category');
        });

        Schema::table('staff_training', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('status');
            $table->date('started_date')->nullable()->after('due_date');
            $table->date('completed_date')->nullable()->after('started_date');
            $table->date('expiry_date')->nullable()->after('completed_date');
            $table->text('completion_notes')->nullable()->after('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('training', function (Blueprint $table) {
            $table->dropColumn(['is_mandatory', 'category', 'expiry_months']);
        });

        Schema::table('staff_training', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'started_date', 'completed_date', 'expiry_date', 'completion_notes']);
        });
    }
};
