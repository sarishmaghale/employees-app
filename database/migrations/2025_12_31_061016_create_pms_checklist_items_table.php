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
        Schema::create('pms_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_title');
            $table->foreignId('checklist_id')->constrained('pms_checklists')->onDelete('cascade');
            $table->boolean('isCompleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pms_checklist_items');
    }
};
