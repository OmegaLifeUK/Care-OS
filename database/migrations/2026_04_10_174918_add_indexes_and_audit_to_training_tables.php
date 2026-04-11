<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // #7: Indexes for performance at scale
        Schema::table('training', function (Blueprint $table) {
            $table->index(['home_id', 'is_deleted'], 'idx_training_home_deleted');
            $table->index(['home_id', 'training_year', 'is_deleted'], 'idx_training_home_year');
            $table->index('training_date', 'idx_training_date');
            // #4: Audit trail columns
            $table->unsignedBigInteger('created_by')->nullable()->after('max_employees');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
        });

        Schema::table('staff_training', function (Blueprint $table) {
            $table->index(['training_id', 'status'], 'idx_staff_training_status');
            $table->index(['user_id', 'training_id'], 'idx_staff_training_user');
            $table->index('expiry_date', 'idx_staff_training_expiry');
            // #4: Audit trail columns
            $table->unsignedBigInteger('assigned_by')->nullable()->after('completion_notes');
            $table->unsignedBigInteger('status_changed_by')->nullable()->after('assigned_by');
            $table->timestamp('status_changed_at')->nullable()->after('status_changed_by');
        });
    }

    public function down(): void
    {
        Schema::table('training', function (Blueprint $table) {
            $table->dropIndex('idx_training_home_deleted');
            $table->dropIndex('idx_training_home_year');
            $table->dropIndex('idx_training_date');
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('staff_training', function (Blueprint $table) {
            $table->dropIndex('idx_staff_training_status');
            $table->dropIndex('idx_staff_training_user');
            $table->dropIndex('idx_staff_training_expiry');
            $table->dropColumn(['assigned_by', 'status_changed_by', 'status_changed_at']);
        });
    }
};
