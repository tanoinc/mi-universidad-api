<?php

namespace App\Exceptions;

/**
 * Exeption thrown when the attendance control condition has not been met.
 *
 * @author tanoinc
 */
class AttendanceControlException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 11;
    }
    public function getCustomMessage()
    {
        return trim("Forbidden: Attendance control condition not met. ".$this->getMessage());
    }
    public function getStatusCode()
    {
        return 403;
    }
}
