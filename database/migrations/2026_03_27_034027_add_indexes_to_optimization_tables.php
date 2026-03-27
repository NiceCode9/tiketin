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
        Schema::table('orders', function (Blueprint $table) {
            $table->index('payment_status');
            $table->index('expires_at');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['expires_at']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
