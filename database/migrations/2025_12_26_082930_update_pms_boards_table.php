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
        Schema::table('pms_boards', function (Blueprint $table) {
            $table->string('board_name');
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
        Schema::table('pms_boards', function (Blueprint $table) {
            $table->dropColumn('board_name');
            $table->dropForeign(['created_by']);
        });
    }
};
