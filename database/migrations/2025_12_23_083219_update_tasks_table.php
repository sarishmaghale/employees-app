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
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('status_link_id')
                ->nullable()->constrained('employee_kanban_status_links')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('position')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['status_link_id']);
            $table->dropColumn('status_link_id');
            $table->dropColumn('position');
        });
    }
};
