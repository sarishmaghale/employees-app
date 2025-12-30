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
            $table->integer('created_by');
            $table->foreign('created_by')->references('id')
                ->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pms_tasks', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });
    }
};
