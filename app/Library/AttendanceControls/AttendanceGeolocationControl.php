<?php

namespace App\Library\AttendanceControls;

use Illuminate\Support\Facades\Auth;
use App\Geolocation;

/**
 * Controls if the user is near (accuracy+distance) a geographic point.
 *
 * @author lucianoc
 */
class AttendanceGeolocationControl extends AbstractAttendanceControl
{

    public function isValid()
    {
        $user = Auth::user();
        $user_location = Geolocation::findByUser($user)->mostRecent()->first();
              
        if (!$user_location) {
            return false;
        }

        if (!$user_location->isRecentlyUpdated()) {
            return false;
        }
        
        if (!$user_location->isAccurate()) {
            return false;
        }

        return $this->isNear($user_location);
    }

    public static function areValidParameters($parameters)
    {        
        if (!isset($parameters->latitude)) {
            return false;
        }

        if (!isset($parameters->longitude)) {
            return false;
        }

        if (!isset($parameters->max_distance) or !(is_int($parameters->max_distance) or is_float($parameters->max_distance))) {
            return false;
        }

        if (!static::validLatitude($parameters->latitude)) {
            return false;
        }

        if (!static::validLongitude($parameters->longitude)) {
            return false;
        }

        return true;
    }

    protected static function validLatitude($latitude)
    {
        return preg_match(
                '/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/',
                $latitude
        );
    }

    protected static function validLongitude($longitude)
    {
        return preg_match(
                '/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/',
                $longitude
        );
    }

    protected function isNear(Geolocation $user_location)
    {
        $distance = $this->getDistanceOpt(
                $this->parameters->latitude, $this->parameters->longitude, 
                $user_location->latitude, $user_location->longitude
        );
        
        return ($distance <= $this->parameters->max_distance );
    }
    
    /**
     * Calculates the distance between two geographic points (optimized).
     * Optimized algorithm from http://www.codexworld.com
     *
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param float $latitudeTo
     * @param float $longitudeTo
     *
     * @return float [km]
     */
    function getDistanceOpt($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad) * sin($latitudeTo * $rad) + cos($latitudeFrom * $rad) * cos($latitudeTo * $rad) * cos($theta * $rad);

        return acos($dist) / $rad * 60 * 1.853;
    }

}
