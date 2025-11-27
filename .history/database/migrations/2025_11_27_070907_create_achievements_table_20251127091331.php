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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->text('description')->nullable();
            $table->unsignedInteger('points_rewards')->default(0);
            $table->string('badge_icon')->nullable();
            $table->string('badge_type')->nullable()->index();
            $table->enum('criteria_type', ['donations', 'points', 'referrals', 'custom'])->default('donations')->index();
            $table->unsignedInteger('criteria_value')->default(0);
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->json('meta')->nullable()->comment('Additional metadata for the achievement');
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
