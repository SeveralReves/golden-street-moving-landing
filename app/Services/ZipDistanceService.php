<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ZipDistanceService
{
    private const EARTH_RADIUS_MILES = 3958.8;

    /**
     * Straight-line (great-circle) distance between two US ZIP codes, in
     * miles. Returns null when either ZIP isn't in the dataset, so callers
     * can skip travel-fee calculations instead of guessing a distance.
     *
     * This is centroid-to-centroid, not a driving distance, so it will
     * usually read a bit shorter than an actual route. Good enough for a
     * threshold-based surcharge; calibrate `travel_threshold_miles` with
     * that in mind.
     */
    public static function milesBetween(?string $originZip, ?string $destinationZip): ?float
    {
        if (! $originZip || ! $destinationZip) {
            return null;
        }

        $coordinates = self::coordinates();

        $origin = $coordinates[$originZip] ?? null;
        $destination = $coordinates[$destinationZip] ?? null;

        if (! $origin || ! $destination) {
            return null;
        }

        return round(self::haversineMiles(
            $origin['lat'], $origin['lng'],
            $destination['lat'], $destination['lng']
        ), 1);
    }

    /**
     * @return array<string, array{lat: float, lng: float}>
     */
    private static function coordinates(): array
    {
        return Cache::rememberForever('zip_coordinates_dataset', function () {
            $path = resource_path('data/us_zip_coordinates.csv');
            $coordinates = [];

            $handle = fopen($path, 'r');
            while (($row = fgetcsv($handle)) !== false) {
                [$zip, $lat, $lng] = $row;
                $coordinates[$zip] = ['lat' => (float) $lat, 'lng' => (float) $lng];
            }
            fclose($handle);

            return $coordinates;
        });
    }

    private static function haversineMiles(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_MILES * $c;
    }
}
