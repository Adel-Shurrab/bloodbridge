<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\BloodType;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Translatable\HasTranslations;

class Donor extends Model
{
    use SoftDeletes, HasFactory;

    protected $casts = [
        'blood_type' => BloodType::class,
        'gender' => \App\Enums\Gender::class,
        'birth_date' => 'date',

    ];



    public const DEFAULT_POINTS = 0;
    public const DEFAULT_LEVEL = 1;
    public const NATIONAL_ID_LENGTH = 9;

    protected $fillable = [
        'user_id',
        'governorate_id',
        'national_id',
        'gender',
        'birth_date',
        'auto_location_address',
        'lat',
        'lng',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function healthProfile()
    {
        return $this->hasOne(DonorHealthProfile::class);
    }

    public function responses()
    {
        return $this->hasMany(RequestResponse::class);
    }

    public function eligibilityLogs()
    {
        return $this->hasMany(EligibilityLog::class);
    }

    /**
     * Scope a query to only include donors within a given radius.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float|null  $lat
     * @param  float|null  $lng
     * @param  int  $radiusKm
     * @param  int|null  $governorateId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithinRadius(Builder $query, ?float $lat, ?float $lng, int $radiusKm, ?int $governorateId = null): Builder
    {
        $hasLocation = !is_null($lat) && !is_null($lng) && $lat != 0 && $lng != 0;

        $haversine = null;
        $bbox = [];

        if ($hasLocation) {
            $haversine = "(
                6371 * acos(
                    cos(radians(?))
                    * cos(radians(lat))
                    * cos(radians(lng) - radians(?))
                    + sin(radians(?))
                    * sin(radians(lat))
                )
            )";

            $cosLat = cos(deg2rad($lat));
            $cosLat = abs($cosLat) > 0.0001 ? $cosLat : 0.0001;

            $latChange = $radiusKm / 111;
            $lngChange = abs($radiusKm / (111 * $cosLat));

            $bbox = [
                'minLat' => $lat - $latChange,
                'maxLat' => $lat + $latChange,
                'minLng' => $lng - $lngChange,
                'maxLng' => $lng + $lngChange,
            ];
        }

        $query->select('donors.*');

        if ($hasLocation) {
            $query->selectRaw("{$haversine} AS distance", [$lat, $lng, $lat]);
        }

        $query->where(function ($group) use ($hasLocation, $bbox, $radiusKm, $governorateId, $lat, $lng, $haversine) {

            if ($hasLocation) {
                $group->where(function ($q) use ($bbox, $radiusKm, $lat, $lng, $haversine) {
                    $q->whereNotNull('lat')
                        ->whereNotNull('lng')
                        
                        ->whereBetween('lat', [$bbox['minLat'], $bbox['maxLat']])
                        ->whereBetween('lng', [$bbox['minLng'], $bbox['maxLng']])
                        ->whereRaw("{$haversine} <= ?", [$lat, $lng, $lat, $radiusKm]);
                });
            }

            if ($governorateId) {
                $method = $hasLocation ? 'orWhere' : 'where';

                $group->$method(function ($q) use ($governorateId) {
                    $q->whereNull('lat')
                        ->whereNull('lng')
                        ->where('governorate_id', $governorateId);
                });
            }
        });

        if ($hasLocation) {
            $query->orderByRaw('ISNULL(distance), distance ASC');
        }

        return $query;
    }

    /**
     * Scope a query to only include donors compatible with specific blood types.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $bloodTypes
     * @return void
     */
    public function scopeCompatibleWith(Builder $query, array $bloodTypes): void
    {
        $query->whereHas('healthProfile', function ($q) use ($bloodTypes) {
            $q->where(function ($subQuery) use ($bloodTypes) {
                $subQuery->whereIn('verified_blood_type', $bloodTypes)
                    ->orWhere(function ($q2) use ($bloodTypes) {
                        $q2->whereNull('verified_blood_type')
                            ->whereIn('blood_type', $bloodTypes);
                    });
            });
        });
    }

    /**
     * Scope a query to only include eligible donors.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeEligible(Builder $query): void
    {
        $query->whereHas('healthProfile', function ($q) {
            $q->where('is_eligible', true)
                ->where(function ($sq) {
                    $sq->whereNull('next_eligible_date')
                        ->orWhereDate('next_eligible_date', '<=', now());
                });
        });
    }
}
