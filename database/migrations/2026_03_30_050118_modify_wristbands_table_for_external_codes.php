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
        Schema::table('wristbands', function (Blueprint $table) {
            // Change uuid to support any QR code content (not just UUID format)
            $table->string('uuid')->change();
            // Make checksum nullable since it's no longer generated for external codes
            $table->string('checksum')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wristbands', function (Blueprint $table) {
            $table->uuid('uuid')->change();
            $table->string('checksum')->nullable(false)->change();
        });
    }
};
