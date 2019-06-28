<?php

namespace App\Library\AttendanceControls;

/**
 * Controls if the user is in a given network (by its mask).
 *
 * @author lucianoc
 */
class AttendanceIPControl extends AbstractAttendanceControl
{

    public function isValid()
    {
        return static::ipInRange($this->request->ip(), $this->parameters->mask);
    }

    public static function areValidParameters($parameters)
    {
        if (!isset($parameters->mask)) {
            return false;
        }

        $regex_mask = "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/([1-9]|1[0-9]|2[0-9]|3[0-2])$/";
        if (!preg_match($regex_mask, $parameters->mask)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a given ip is in a network
     * Credits: https://gist.github.com/tott/7684443
     * 
     * @param  string $ip    IP to check in IPV4 format eg. 127.0.0.1
     * @param  string $range IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     * @return boolean true if the ip is in this range / false if not.
     */
    static function ipInRange($ip, $range)
    {
        if (strpos($range, '/') == false) {
            $range .= '/32';
        }
        // $range is in IP/CIDR format eg 127.0.0.1/24
        list( $range, $netmask ) = explode('/', $range, 2);
        $range_decimal = ip2long($range);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = pow(2, ( 32 - $netmask)) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
    }

}
