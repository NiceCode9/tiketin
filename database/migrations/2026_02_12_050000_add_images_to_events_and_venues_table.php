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
        Schema::table('events', function (Blueprint $table) {
            $table->string('banner_image')->nullable()->after('description');
            $table->json('additional_images')->nullable()->after('banner_image');
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->string('image')->nullable()->after('capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['banner_image', 'additional_images']);
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
