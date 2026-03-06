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
        
        Schema::table('donors', function (Blueprint $table) {
            $table->index(['lat', 'lng'], 'donors_location_index');
        });

        Schema::table('blood_requests', function (Blueprint $table) {
            $table->index(['lat', 'lng'], 'blood_requests_location_index');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->index(['lat', 'lng'], 'organizations_location_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->dropIndex('donors_location_index');
        });

        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropIndex('blood_requests_location_index');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropIndex('organizations_location_index');
        });
    }
};
