<?php

namespace App\Exceptions;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Exeption thrown when the first step in the "forgot password" process succeded.
 *
 * @author tanoinc
 */
class RejectedCodeConfirmUserException extends GenericErrorException
{
    public function getStatusCode()
    {
        return 401;
    }    
    public function getErrorCode() 
    {
        return 4;
    }
    public function getCustomMessage()
    {
        return trim("Invalid confirmation code.");
    }
}
