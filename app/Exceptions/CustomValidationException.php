<?php

namespace App\Exceptions;

use Exception;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Exeption thrown when a validation fails
 *
 * @author tanoinc
 */
class CustomValidationException extends GenericErrorException
{
    protected $exception = null;
    /**
     * Create a new mi-universidad validation exception instance.
     *
     * @param  \Illuminate\Validation\ValidationException $exception
     * @return void
     */
    public function __construct(\Illuminate\Validation\ValidationException $exception)
    {
        $this->exception = $exception;
        $this->setExtraData($exception->getResponse()->original);
    }
    public function getStatusCode()
    {
        return 422;
    }

    public function getErrorCode() 
    {
        return 4;
    }
    public function getCustomMessage()
    {
        return $this->exception->getMessage();
    }
}
