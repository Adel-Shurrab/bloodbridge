<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelTrainingLog extends Model
{
    protected $table = 'model_training_logs';
    protected $primaryKey = 'model_version';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    
    protected $fillable = [
        'model_version',
        'training_date',
        'data_records_used',
        'algorithm',
        'hyperparameters',
        'metrics',
        'feature_importance',
    ];

    protected $casts = [
        'training_date' => 'datetime',
        'hyperparameters' => 'array',
        'metrics' => 'array',
        'feature_importance' => 'array',
    ];
}
