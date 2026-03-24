<?php

use App\Models\Donor;
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
        Schema::create('donor_predictive_scores', function (Blueprint $table) {
            $table->foreignIdFor(Donor::class)
                ->primary()
                ->constrained()
                ->cascadeOnDelete();
            $table->float('acceptance_probability')->default(0.5)->comment('Score from 0.0 (never accepts) to 1.0 (always accepts)');
            $table->integer('data_points_count')->default(0)->comment('Number of historical records used in model');
            $table->timestamp('computed_at')->nullable()->comment('Timestamp of last computation');
            $table->string('model_version', 10)->default('v1.0')->comment('Model version for A/B testing and rollback');
            $table->timestamps();
            
            // Indexes
            $table->index('computed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_predictive_scores');
    }
};
