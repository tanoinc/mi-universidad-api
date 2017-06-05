<?php

namespace App\Exceptions;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Exeption thrown when a request can not be authorized.
 *
 * @author tanoinc
 */
class ContentTypeDecodingException extends GenericErrorException
{
    public function getErrorCode() 
    {
        return 6;
    }
    public function getCustomMessage()
    {
        return trim("Http Content type unknown. ".$this->getMessage());
    }
    public function getStatusCode()
    {
        return 500;
    }
}
