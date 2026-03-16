<?php

namespace App\Helpers;

use App\Models\Donor;
use Illuminate\Database\Eloquent\Builder;

class GeoHelper
{
    /**
     * Calculate distance between two coordinates using PHP Math (Haversine formula)
     * Useful for single point-to-point calculations without database queries.
     * * @param float $lat1 Latitude of point 1
     * @param float $lng1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lng2 Longitude of point 2
     * @param string $unit Unit of measurement (K=kilometers, M=miles, N=nautical miles)
     * @return float Distance in specified unit
     */
    public static function calculateDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2,
        string $unit = 'K'
    ): float {
        if (($lat1 == $lat2) && ($lng1 == $lng2)) {
            return 0;
        }

        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2))
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * cos(deg2rad($theta));

        $dist = min(max($dist, -1.0), 1.0);

        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } elseif ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /**
     * Find donors within specified radius using MySQL Spatial Math
     * @deprecated Use Donor::withinRadius() instead
     * * @param float $centerLat Center point latitude
     * @param float $centerLng Center point longitude
     * @param int $radiusKm Search radius in kilometers
     * @return Builder
     */
    public static function getDonorsWithinRadius(
        float $centerLat,
        float $centerLng,
        int $radiusKm,
        ?int $governorateId = null
    ): Builder {
        return Donor::withinRadius($centerLat, $centerLng, $radiusKm, $governorateId);
    }
}
