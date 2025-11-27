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
$table->foreignId('organization_id')->constrained()->onDelete('cascade');
$table->string('blood_type');
$table->integer('units_needed');
$table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('low');
$table->text('additional_notes')->nullable();
$table->integer('search_radius_km')->default(10);
$table->enum('status', ['pending', 'broadcasted', 'matched', 'fulfilled', 'cancelled', 'expired'])->default('pending');
$table->integer('donors_accepted')->default(0);
$table->integer('donors_completed')->default(0);
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
