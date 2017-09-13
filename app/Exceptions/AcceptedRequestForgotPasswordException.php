<?php

namespace App\Exceptions;

use Exception;

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
class AcceptedRequestForgotPasswordException extends GenericErrorException
{
    public function getStatusCode()
    {
        return 202;
    }    
    public function getErrorCode() 
    {
        return 1;
    }
    public function getCustomMessage()
    {
        return trim("Recovery password code sent.");
    }
}
