<?php

namespace App\Library\AttendanceControls;


use App\Attendance;
use Illuminate\Http\Request;
use App\AttendanceControl;

/**
 * Description of AttendanceControlValidator
 *
 * @author lucianoc
 */
class AttendanceControlValidator
{
    protected $controls = null;
    protected $invalid_control = null;


    public function __construct(Attendance $attendance, Request $request)
    {
        $this->controls = AttendanceControlFactory::makeFromAttendance($attendance, $request);
        $this->invalid_control = null;
    }


    public function allValid()
    {
        $this->invalid_control = null;
        foreach ($this->controls as $control) {
            if (!$control->isValid()) {
                $this->invalid_control = AttendanceControl::toType(get_class($control));
                return false;
            }
        }
        
        return true;
    }
    
    public function getInvalidControl()
    {
        return $this->invalid_control;
    }
}
