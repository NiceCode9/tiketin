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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g., "TKT-20260208-0001"
            $table->uuid('order_token')->unique(); // For secure tracking
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('consumer_name');
            $table->string('consumer_city');
            $table->date('consumer_birth_date');
            $table->string('consumer_email');
            $table->string('consumer_whatsapp');
            $table->enum('consumer_identity_type', ['KTP', 'SIM', 'Student Card', 'Passport']);
            $table->string('consumer_identity_number');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_status', ['pending', 'success', 'failed', 'paid', 'expired', 'canceled'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for lookups
            $table->index('consumer_identity_number');
            $table->index('order_token');
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
