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
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('type')->nullable(); // payment, refund, etc.
            $table->json('payload');
            $table->string('status')->default('pending'); // pending, processed, failed
            $table->text('response')->nullable(); // response from our system
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
