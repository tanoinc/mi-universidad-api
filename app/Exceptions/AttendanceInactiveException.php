<?php

namespace App\Exceptions;

/**
 * Exeption thrown when the attendance event cannot be accesed or modified.
 *
 * @author tanoinc
 */
class AttendanceInactiveException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 10;
    }
    public function getCustomMessage()
    {
        return trim("Forbidden: Attendance event unavailable for access or modification. ".$this->getMessage());
    }
    public function getStatusCode()
    {
        return 403;
    }
}
