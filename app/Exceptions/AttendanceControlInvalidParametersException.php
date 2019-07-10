<?php

namespace App\Exceptions;

/**
 * Exeption thrown when the control class does not exist.
 *
 * @author tanoinc
 */
class AttendanceControlInvalidParametersException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 13;
    }
    public function getCustomMessage()
    {
        return trim("The attendance control parameters for the selected control are invalid. ".$this->getMessage());
    }
    public function getStatusCode()
    {
        return 422;
    }
}
