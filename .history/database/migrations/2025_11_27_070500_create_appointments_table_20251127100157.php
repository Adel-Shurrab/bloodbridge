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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
            ->constrained()
            ->onDelete('cascade')
            ->name('fk_appointments_organization_id')
            ->index();
            $table->foreignId('donor_id')
            ->constrained()
            ->onDelete('cascade')
            ->name('fk_appointments_donor_id')
            ->index();
            $table->foreignId('blood_request_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->dateTime('appointment_date')->index();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled')->index();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
