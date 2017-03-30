<?php

namespace App\Exceptions;

use Exception;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Exeption thrown when request cannot access a resource.
 *
 * @author tanoinc
 */
class ForbiddenAccessException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 4;
    }
    public function getCustomMessage()
    {
        return trim("Forbidden: Not enough privileges. ".$this->getMessage());
    }
    public function getStatusCode()
    {
        return 403;
    }
}
