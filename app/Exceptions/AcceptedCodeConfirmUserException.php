<?php

namespace App\Exceptions;

/**
 * Exeption thrown when the user confirms his account correctly.
 *
 * @author tanoinc
 */
class AcceptedCodeConfirmUserException extends GenericErrorException
{
    public function getStatusCode()
    {
        return 200;
    }    
    public function getErrorCode() 
    {
        return 1;
    }
    public function getCustomMessage()
    {
        return trim("Confirm user.");
    }
}
