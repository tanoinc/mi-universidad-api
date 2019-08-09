<?php

namespace App\Exceptions;

/**
 * Exeption thrown when a request can not be authorized.
 *
 * @author tanoinc
 */
class UnauthorizedAccessException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 3;
    }
    public function getCustomMessage()
    {
        return trim("Authorization invalid. ".$this->getMessage());
    }
    public function getStatusCode()
    {
        return 401;
    }
}
