<?php

namespace App\Helpers;

class GeoHelper
{
    /**
     * Calculate distance between two coordinates using Haversine formula
     * 
     * @param float $lat1 Latitude of point 1
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

        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344); // Kilometers
        } elseif ($unit == "N") {
            return ($miles * 0.8684); // Nautical miles
        } else {
            return $miles; // Miles
        }
    }
    /**
     * Find donors within specified radius of a location
     * 
     * @param float $centerLat Center point latitude
     * @param float $centerLng Center point longitude
     * @param int $radiusKm Search radius in kilometers
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public static function getDonorsWithinRadius(
        float $centerLat,
        float $centerLng,
        int $radiusKm
    ) {
        if (config('database.default') === 'sqlite') {
            return \App\Models\Donor::select('donors.*')
                ->selectRaw("
                (
                    6371 * acos(
                        cos(radians({$centerLat})) 
                        * cos(radians(lat)) 
                        * cos(radians(lng) - radians({$centerLng})) 
                        + sin(radians({$centerLat})) 
                        * sin(radians(lat))
                    )
                ) AS distance
            ")
                ->having('distance', '<=', $radiusKm);
        }

        return \App\Models\Donor::select('donors.*')
            ->selectRaw("
                (
                    6371 * acos(
                        cos(radians(?)) 
                        * cos(radians(lat)) 
                        * cos(radians(lng) - radians(?)) 
                        + sin(radians(?)) 
                        * sin(radians(lat))
                    )
                ) AS distance
            ", [$centerLat, $centerLng, $centerLat])
            ->having('distance', '<=', $radiusKm);
    }
}
