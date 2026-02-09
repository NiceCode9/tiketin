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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->dateTime('event_end_date')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->boolean('has_assigned_seating')->default(false);
            $table->dateTime('wristband_exchange_start')->nullable();
            $table->dateTime('wristband_exchange_end')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index for client isolation queries
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
