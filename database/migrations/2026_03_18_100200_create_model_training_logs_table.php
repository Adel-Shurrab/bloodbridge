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
        Schema::create('model_training_logs', function (Blueprint $table) {
            $table->string('model_version', 10)->primary()->comment('Model version identifier (e.g., v1.0, v20260318)');
            $table->timestamp('training_date')->comment('When model was trained');
            $table->integer('data_records_used')->comment('Number of historical records used for training');
            $table->string('algorithm', 50)->default('xgboost')->comment('Algorithm used for training');
            $table->json('hyperparameters')->nullable()->comment('XGBoost hyperparameters used');
            $table->json('metrics')->nullable()->comment('Training metrics: {accuracy, precision, recall, auc_roc, f1_score}');
            $table->json('feature_importance')->nullable()->comment('Feature importance scores from trained model');
            $table->timestamps();
            
            // Indexes
            $table->index('training_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_training_logs');
    }
};
