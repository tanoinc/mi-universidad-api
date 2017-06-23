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
class RemoteResourceUnaccessableException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 8;
    }
    public function getCustomMessage()
    {
        return trim("Remote resource couldn't be retrieved. ".$this->getMessage());
    }
    public function getStatusCode()
    {
        return 404;
    }
}
