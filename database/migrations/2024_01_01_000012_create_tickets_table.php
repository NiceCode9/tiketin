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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // For QR code
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('ticket_category_id')->constrained('ticket_categories')->cascadeOnDelete();
            $table->foreignId('seat_id')->nullable()->constrained('seats')->nullOnDelete();
            $table->string('consumer_name');
            $table->enum('consumer_identity_type', ['KTP', 'SIM', 'Student Card', 'Passport']);
            $table->string('consumer_identity_number');
            $table->enum('status', ['pending_payment', 'paid', 'exchanged', 'cancelled'])->default('pending_payment');
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
        Schema::dropIfExists('tickets');
    }
};
