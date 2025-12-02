<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eligibility_logs', function (Blueprint $table) {
            $table->id();

            // Link to the Donor
            $table->foreignId('donor_id')
                ->constrained('donors')
                ->cascadeOnDelete();

            // The 3 Contexts (Points 1, 2, 3 from your text)
            $table->enum('check_type', [
                'registration',       // Point 1: Initial Signup
                'request_acceptance', // Point 2/4: The "Quick Check" before accepting
                'profile_update'      // Point 3: The "6-Month Rule" update
            ]);

            // The Result
            $table->enum('status', ['eligible', 'ineligible']);

            // "Why did they fail?" (e.g., "Answered Yes to Recent Surgery")
            $table->text('rejection_reason')->nullable();

            // Store the exact answers they gave at that moment (JSON is perfect for this)
            // Example: {"surgery": true, "antibiotics": false}
            $table->json('answers_snapshot')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eligibility_logs');
    }
};
