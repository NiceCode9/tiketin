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
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('venue_section_id')->nullable()->constrained('venue_sections')->nullOnDelete();
            $table->string('name'); // e.g., "VIP", "Festival", "Regular"
            $table->decimal('price', 10, 2);
            $table->integer('quota')->unsigned();
            $table->integer('sold_count')->unsigned()->default(0);
            $table->boolean('is_seated')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            // Index for event queries
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};
