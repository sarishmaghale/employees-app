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
        Schema::create('employee_kanban_status_links', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreignId('category_id')
                ->constrained('task_categories')->cascadeOnDelete();
            $table->foreignId('status_id')
                ->constrained('kanban_statuses')->cascadeOnDelete();
            $table->unsignedBigInteger('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_kanban_status_links');
    }
};
