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
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->decimal('biaya_layanan', 15, 2)->default(0)->after('price');
            $table->decimal('biaya_admin_payment', 15, 2)->default(0)->after('biaya_layanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->dropColumn(['biaya_layanan', 'biaya_admin_payment']);
        });
    }
};
