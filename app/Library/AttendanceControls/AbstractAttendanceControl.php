<?php

namespace App\Library\AttendanceControls;

use Illuminate\Http\Request;
use LaravelFCM\Message\Exceptions\InvalidOptionsException;
use App\AttendanceControl;
use App\Exceptions\AttendanceControlInvalidParametersException;

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
        static::checkValidParameters($parameters);
        $this->parameters = $parameters;
        $this->request = $request;
    }

    public static function isValidClass($type_class)
    {
        return \App\AttendanceControl::toClass($type_class);
    }

    public static function checkValidParameters($parameters, $control_class = null)
    {
        if (!$control_class) {
            $control_class = static::class;
        }
        
        static::checkValidClass($control_class);
        
        if (!$control_class::areValidParameters($parameters)) {
            throw new AttendanceControlInvalidParametersException(json_encode($parameters));
        }
    }

    public static function checkValidClass($control_class)
    {
        if (!in_array($control_class, AttendanceControl::typeClasses())) {
            throw new AttendanceControlClassNotFoundException("Control class '{$control_class}' not found for attendance control");
        }        
    }

    public abstract static function areValidParameters($parameters);
    
    public abstract function isValid();
}
