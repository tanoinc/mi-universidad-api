<?php

namespace App\Exceptions;

use Exception;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Exeption thrown when an object model already exists.
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
        return trim("Authorization header missing or invalid. ".$this->getMessage());
    }
    public function getStatusCode()
    {
        return 401;
    }
}
