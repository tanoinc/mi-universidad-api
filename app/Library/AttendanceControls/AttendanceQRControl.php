<?php

namespace App\Library\AttendanceControls;

/**
 * Controls if the user has scanned the correct QR code (challenge) 
 *
 * @author lucianoc
 */
class AttendanceQRControl extends AbstractAttendanceControl
{

    public function isValid()
    {
        $input_code = $this->request->input('code');
        
        return ($input_code == $this->parameters->code);
    }

    public static function areValidParameters($parameters)
    {
        if (!isset($parameters->code)) {
            return false;
        }

        return true;
    }

}
