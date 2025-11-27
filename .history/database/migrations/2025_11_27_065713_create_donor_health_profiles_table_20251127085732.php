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
        Schema::create('donor_health_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->integer('weight')->nullable();
            $table->integer('height')->nullable();
            $table->boolean('chronic_disease')->default(false);
            $table->boolean('recent_donation')->default(false);
            $table->boolean('infection')->default(false);
            $table->boolean('is_eligible')->default(true);
            $table->boolean('is_smoker')->default(false);
            $table->boolean('has_recent_surgery')->default(false);
            $table->date('surgery_date')->nullable();
            $table->date('next_eligible_date')->nullable();
            $table->date('last_donation_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_health_profiles');
    }
};
