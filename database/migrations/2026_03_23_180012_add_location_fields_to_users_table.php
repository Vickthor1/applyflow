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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('experience');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('city')->nullable()->after('longitude');
            $table->string('country')->nullable()->after('city');
            $table->string('preferred_language', 2)->default('pt')->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'city', 'country', 'preferred_language']);
        });
    }
};
