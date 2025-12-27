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
            $table->foreignId('governorate_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->dropColumn('city');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('governorate_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->dropColumn(['city', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropColumn('governorate_id');
            $table->string('city')->nullable()->index();
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropColumn('governorate_id');
            $table->string('city')->nullable()->index();
            $table->string('state')->nullable();
        });
    }
};
