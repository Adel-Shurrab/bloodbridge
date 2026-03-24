<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorPredictiveScore extends Model
{
    protected $table = 'donor_predictive_scores';
    protected $primaryKey = 'donor_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;
    
    protected $fillable = [
        'donor_id',
        'acceptance_probability',
        'data_points_count',
        'computed_at',
        'model_version',
    ];

    protected $casts = [
        'computed_at' => 'datetime',
        'acceptance_probability' => 'float',
    ];

    /**
     * Get the donor associated with this score.
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }
}
