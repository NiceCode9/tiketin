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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('biaya_layanan', 15, 2)->default(0)->after('unit_price');
            $table->decimal('biaya_admin_payment', 15, 2)->default(0)->after('biaya_layanan');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_biaya_layanan', 15, 2)->default(0)->after('subtotal');
            $table->decimal('total_biaya_admin_payment', 15, 2)->default(0)->after('total_biaya_layanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['biaya_layanan', 'biaya_admin_payment']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_biaya_layanan', 'total_biaya_admin_payment']);
        });
    }
};
