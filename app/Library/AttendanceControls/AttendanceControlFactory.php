<?php

namespace App\Library\AttendanceControls;

use App\AttendanceControl;
use App\Attendance;
use Illuminate\Http\Request;

/**
 * Attendance Control factory class
 *
 * @author lucianoc
 */
class AttendanceControlFactory
{

    protected static function decodeParameters($string_parameters)
    {
        if (empty($string_parameters)) {
            return null;
        }
        
        return json_decode($string_parameters);
    }

    public static function make(AttendanceControl $attendance_control, Request $request)
    {
        $control_class = $attendance_control->type;
        
        if (!in_array($control_class, AttendanceControl::typeClasses())) {
            throw new ClassNotFoundException("Control class '{$control_class}' not found for attendance control");
        }
        
        return new $control_class(
                static::decodeParameters($attendance_control->parameters),
                $request
        );
    }
    
    public static function makeFromAttendance(Attendance $attendance, Request $request)
    {
        $attendance_controls = $attendance->controls()->get();
        $controls = [];
        
        foreach ($attendance_controls as $control) {
            $controls[] = static::make($control, $request);
        }
        
        return $controls;
    }
}
