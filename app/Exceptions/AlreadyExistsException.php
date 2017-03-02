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
class AlreadyExistsException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 2;
    }
    public function getCustomMessage()
    {
        return trim("Entity already exists. ".$this->getMessage());
    }
}
