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
        Schema::create('pms_task_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('pms_tasks')->onDelete('cascade');
            $table->foreignId('label_id')->constrained('pms_labels')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pms_task_labels');
    }
};
