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
        Schema::table('pms_card_comments', function (Blueprint $table) {
            //0: activity like task add/move, 1: commented
            $table->integer('comment_type')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pms_card_comments', function (Blueprint $table) {
            $table->dropColumn('comment_type');
        });
    }
};
