<?php

namespace App\Library\AttendanceControls;


use App\Attendance;
use Illuminate\Http\Request;

/**
 * Description of AttendanceControlValidator
 *
 * @author lucianoc
 */
class AttendanceControlValidator
{
    protected $controls = null;
    
    public function __construct(Attendance $attendance, Request $request)
    {
        $this->controls = AttendanceControlFactory::makeFromAttendance($attendance, $request);
    }


    public function allValid()
    {
        foreach ($this->controls as $control) {
            if (!$control->isValid()) {
                return false;
            }
        }
        
        return true;
    }
}
