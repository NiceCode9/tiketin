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
        Schema::create('wristbands', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // Separate QR code from ticket
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->enum('status', ['active', 'validated', 'revoked'])->default('active');
            $table->timestamp('exchanged_at');
            $table->foreignId('exchanged_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('checksum'); // For QR validation
            $table->timestamps();
            $table->softDeletes();
            
            // Critical index for QR scanning
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wristbands');
    }
};
