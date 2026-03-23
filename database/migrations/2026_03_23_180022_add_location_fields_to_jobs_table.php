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
        Schema::table('jobs', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('source');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('city')->nullable()->after('longitude');
            $table->string('country')->nullable()->after('city');
            $table->string('language', 2)->default('en')->after('country');
            $table->integer('distance_km')->nullable()->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'city', 'country', 'language', 'distance_km']);
        });
    }
};
