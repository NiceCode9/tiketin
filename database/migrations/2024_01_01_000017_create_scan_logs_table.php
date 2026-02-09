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
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scanned_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('scannable_type'); // Ticket or Wristband
            $table->unsignedBigInteger('scannable_id');
            $table->enum('scan_type', ['exchange', 'validation']);
            $table->enum('status', ['success', 'failed', 'duplicate'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamp('scanned_at');
            $table->timestamps();
            // NO soft deletes - permanent audit record
            
            // Indexes for reporting and lookups
            $table->index('scanned_by');
            $table->index('event_id');
            $table->index(['scannable_type', 'scannable_id']);
            $table->index('scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
    }
};
