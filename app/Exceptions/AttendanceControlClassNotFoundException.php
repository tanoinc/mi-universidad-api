<?php

namespace App\Exceptions;

/**
 * Exeption thrown when the control class does not exist.
 *
 * @author tanoinc
 */
class AttendanceControlClassNotFoundException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 12;
    }
    public function getCustomMessage()
    {
        return trim("Attendance control class not found. ".$this->getMessage());
    }
    public function getStatusCode()
    {
        return 422;
    }
}
