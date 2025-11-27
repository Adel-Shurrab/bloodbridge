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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->constrained()
                ->onDelete('cascade')
                ->
                ->index();
            $table->enum('blood_type', ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'])->index();
            $table->unsignedInteger('units_needed');
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('low')->index();
            $table->text('additional_notes')->nullable();
            $table->unsignedInteger('search_radius_km')->default(10);
            $table->enum('status', ['pending', 'broadcasted', 'matched', 'fulfilled', 'cancelled', 'expired'])->default('pending')->index();
            $table->unsignedInteger('donors_accepted')->default(0);
            $table->unsignedInteger('donors_completed')->default(0);
            $table->timestamp('broadcasted_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
