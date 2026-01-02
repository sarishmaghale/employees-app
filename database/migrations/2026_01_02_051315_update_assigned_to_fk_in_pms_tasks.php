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
            $table->dropForeign(['assigned_to']);
            $table->unsignedBigInteger('assigned_to')->nullable()->change();
            $table->foreign('assigned_to')
                ->references('id')
                ->on('pms_task_assignments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pms_tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
        });
    }
};
