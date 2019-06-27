<?php

namespace App\Library\AttendanceControls;

use Illuminate\Http\Request;
use LaravelFCM\Message\Exceptions\InvalidOptionsException;

/**
 * Description of AbstracrAttendanceControl
 *
 * @author lucianoc
 */
abstract class AbstractAttendanceControl
{
    protected $parameters = null;
    protected $request;


    public function __construct($parameters, Request $request = null)
    {
        if (!static::areValidParameters($parameters)) {
            throw new InvalidOptionsException('Invalid attendance controls parameters');
        }        
        $this->parameters = $parameters;
        $this->request = $request;
    }
   
    public abstract static function areValidParameters($parameters);
    public abstract function isValid();
    
}
