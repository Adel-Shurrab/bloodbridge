<?php

namespace App\Constants;

/**
 * Centroids and zoom levels for maps within Palestine.
 * Following best practices to avoid magic numbers in forms and logic.
 */
class PalestineCoordinates
{
    /**
     * General center for Palestine (West Bank & Gaza)
     */
    public const PALESTINE_CENTER = [
        'lat' => 31.9,
        'lng' => 35.2,
    ];

    /**
     * Gaza area coordinates
     */
    public const GAZA = [
        'lat' => 31.5,
        'lng' => 34.4667,
    ];

    /**
     * West Bank area coordinates
     */
    public const WEST_BANK = [
        'lat' => 31.9,
        'lng' => 35.2,
    ];

    /**
     * Standardized zoom levels for consistent UX
     */
    public const ZOOM_COUNTRY = 7;
    public const ZOOM_REGION = 10;
    public const ZOOM_CITY = 13;
    public const ZOOM_STREET = 16;
}
