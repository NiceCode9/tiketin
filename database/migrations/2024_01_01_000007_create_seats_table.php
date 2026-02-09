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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_section_id')->constrained('venue_sections')->cascadeOnDelete();
            $table->string('row_label'); // e.g., "A", "B", "C"
            $table->integer('seat_number')->unsigned(); // e.g., 1, 2, 3
            $table->enum('status', ['available', 'reserved', 'blocked'])->default('available');
            $table->timestamps();
            
            // Ensure unique seat per section
            $table->unique(['venue_section_id', 'row_label', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
