<?php

use App\Models\Donor;
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
            $table->foreignIdFor(Donor::class)
                ->constrained()
                ->cascadeOnDelete();

            // The 3 Contexts (Points 1, 2, 3 from your text)
            $table->unsignedTinyInteger('check_type')->index();

            // The Result
            $table->boolean('is_eligible')->default(true)->index();

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
