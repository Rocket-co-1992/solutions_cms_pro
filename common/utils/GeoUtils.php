<?php

namespace Pandao\Common\Utils;

/**
 * Class GeoUtils
 * - getDistance
 * - getCoords
 */

class GeoUtils
{
    /**
     * Calculates the distance between two geographic coordinates.
     *
     * @param float $lat1 The latitude of the first point.
     * @param float $lng1 The longitude of the first point.
     * @param float $lat2 The latitude of the second point.
     * @param float $lng2 The longitude of the second point.
     * @return float The distance in meters.
     */
    public static function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earth_radius = 6378137; // Earth radius in meters
        $rlo1 = deg2rad($lng1);
        $rla1 = deg2rad($lat1);
        $rlo2 = deg2rad($lng2);
        $rla2 = deg2rad($lat2);
        $dlo = ($rlo2 - $rlo1) / 2;
        $dla = ($rla2 - $rla1) / 2;
        $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return ($earth_radius * $d);
    }

    /**
     * Retrieves the geographic coordinates of an address using Google Maps API.
     *
     * @param string $address The address to geocode.
     * @return array|bool An array containing the latitude and longitude, or false if not found.
     */
    public static function getCoords($address)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=' . urlencode($address));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        $phpresult = json_decode($result);
        if (is_array($phpresult->results) && sizeof($phpresult->results) > 0) {
            $lat = $phpresult->results[0]->geometry->location->lat;
            $lng = $phpresult->results[0]->geometry->location->lng;
        }
        if (isset($lat) && isset($lng)) return array($lat, $lng); else return false;
    }
}
