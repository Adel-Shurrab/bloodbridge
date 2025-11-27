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
        Schema::create('donor_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade')->index();
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade')->index();
            $table->timestamp('earned_at')->nullable()->index();
            $table->json('meta')->nullable()->comment('Additional context (e.g. proof, notes)');
            $table->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete()->index();
            $table->timestamps();
            $table->softDeletes();

            // Prevent assigning the same achievement to the same donor twice
            $table->unique(['donor_id', 'achievement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_achievements');
    }
};
