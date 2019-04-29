<?php

namespace App\Exceptions;

/**
 * The user cannot unsubscribe from the main application (representing the mobile app)
 *
 * @author tanoinc
 */
class RejectedUnsubscribeApplicationException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 9;
    }
    public function getCustomMessage()
    {
        return trim("Cannot unsubscribe from main application.");
    }
    public function getStatusCode()
    {
        return 422;
    }
}
