<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorBehavioralMetric extends Model
{
    protected $table = 'donor_behavioral_metrics';
    protected $primaryKey = 'donor_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = true;
    
    protected $fillable = [
        'donor_id',
        'response_count_30d',
        'acceptance_rate',
        'ignore_rate',
        'decline_rate',
        'no_show_rate',
        'avg_response_time_minutes',
        'last_response_date',
        'total_donations_lifetime',
        'time_of_day_preference',
        'day_of_week_preference',
        'computed_at',
    ];

    protected $casts = [
        'time_of_day_preference' => 'array',
        'day_of_week_preference' => 'array',
        'computed_at' => 'datetime',
        'last_response_date' => 'date',
        'acceptance_rate' => 'float',
        'ignore_rate' => 'float',
        'decline_rate' => 'float',
        'no_show_rate' => 'float',
    ];

    /**
     * Get the donor associated with these behavioral metrics.
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }
}
