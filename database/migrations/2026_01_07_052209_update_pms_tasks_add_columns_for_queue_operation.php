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
        Schema::table('pms_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('pms_tasks', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            }
            $table->timestamp('reminder_sent_at')->nullable();
            $table->date('reminder_for_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pms_tasks', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at', 'reminder_for_date']);
        });
    }
};
