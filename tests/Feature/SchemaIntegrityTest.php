<?php

use Illuminate\Support\Facades\Schema;

// ---------------------------------------------------------------------------
// Verifies the database schema matches expected state after all migrations.
// ---------------------------------------------------------------------------

test('donor_behavioral_metrics table was dropped', function () {
    expect(Schema::hasTable('donor_behavioral_metrics'))->toBeFalse();
});

test('donor_predictive_scores table exists', function () {
    expect(Schema::hasTable('donor_predictive_scores'))->toBeTrue();
});

test('model_training_logs table exists', function () {
    expect(Schema::hasTable('model_training_logs'))->toBeTrue();
});

test('model_training_logs has required columns', function () {
    expect(Schema::hasColumns('model_training_logs', [
        'model_version',
        'training_date',
        'data_records_used',
        'algorithm',
        'hyperparameters',
        'metrics',
        'feature_importance',
    ]))->toBeTrue();
});
