<?php

use App\Models\Achievement;
use App\Models\Donor;
use App\Models\User;
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
            $table->foreignIdFor(Donor::class)
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(Achievement::class)
                ->constrained()
                ->onDelete('cascade');
            $table->timestamp('earned_at')->nullable()->index();
            $table->json('meta')->nullable()->comment('Additional context (e.g. proof, notes)');
            $table->foreignIdFor(User::class, 'awarded_by')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

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
