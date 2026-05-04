<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonorAchievement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'donor_id',
        'achievement_id',
        'earned_at',
        'meta',
        'awarded_by',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'meta'      => 'array',
    ];

    public function donor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function achievement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    // Admin user who manually awarded this (null = system auto-award)
    public function awardedByUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }
}
