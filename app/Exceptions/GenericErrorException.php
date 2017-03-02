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
class GenericErrorException extends Exception
{
    protected $extra_data = null;
    public function getErrorCode() 
    {
        return 1;
    }
    public function getStatusCode()
    {
        return 500;
    }
    public function getCustomMessage()
    {
        return trim("Error. ".$this->getMessage());
    }
    public function setExtraData($data)
    {
        $this->extra_data = $data;
        
        return $this;
    }
    public function getExtraData()
    {
        return $this->extra_data;
    }
}
