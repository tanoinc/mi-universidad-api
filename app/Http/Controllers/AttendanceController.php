<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\AbstractInformation;
use App\User;
use Illuminate\Http\Request;
use App\Exceptions\AttendanceInactiveException;
use Illuminate\Support\Facades\Auth;
use App\Library\AttendanceControls\AttendanceControlValidator;
use App\AttendanceControl;

/**
 * The AttendanceController controller class
 *
 * @author tanoinc
 */
class AttendanceController extends AbstractInformationController
{

    use Traits\InformationDateableControllerTrait;
    
    public function getFuture()
    {
        return $this->getResponseByFutureDate(
                'start_time', 
                AttendanceControl::fnWithAttendanceControl());
    }
    
    public function getNow()
    {
        return $this->getResponseByNowDate('start_time', 'end_time', 
                AttendanceControl::fnWithAttendanceControl()
        );
    }

    public function changeStatusPresent($attendance_id)
    {
        $attendance = Attendance::findOrFail($attendance_id);
        
        $this->checkAttendanceControls($attendance);
        
        $attendance_user = $attendance->setAttendanceUserPresent(Auth::user());
        $attendance_user->save();
        
        return $attendance_user;
    }
    
    function checkAttendanceControls(Attendance $attendance)
    {
        // Check if attendance is visible to auth user
        if (!$this->isVisible($attendance)) {
            throw new AttendanceInactiveException();
        }

        // Check defined controls from attendance
        $controls = new AttendanceControlValidator($attendance, app('request'));
        if (!$controls->allValid()) 
        {
            throw new \App\Exceptions\AttendanceControlException($controls->getInvalidControl());
        }
        
    }    
    
    protected function isVisible(Attendance $attendance)
    {
        $user_current_attendances = 
                $this->getByNowDate('start_time', 'end_time')
                ->get();
        
        $this->hydrateInformation($user_current_attendances);
        
        foreach ($user_current_attendances as $an_attendance) {
            if ($an_attendance->sameAs($attendance)) {
                return true;
            }
        }
        
        return false;
    }
    

    
    protected function getModelClass()
    {
        return Attendance::class;
    }

    protected function getModelName()
    {
        return 'attendance';
    }

    protected function getValidationRules()
    {
        $rules = parent::getValidationRules();
        $rules['name'] = 'required|max:100';
        $rules['description'] = 'max:255';
        $rules['location'] = 'max:255';
        $rules['start_time'] = 'required|date';
        $rules['end_time'] = 'required|date';

        return $rules;
    }

    protected function setModelDataFromRequest(AbstractInformation $attendance, Request $request)
    {
        $attendance->name = $request->input('name');
        $attendance->description = $request->input('description');
        $attendance->location = $request->input('location');
        $attendance->start_time = $request->input('start_time');
        $attendance->end_time = $request->input('end_time');

        return $attendance;
    }

    protected function getFromUser(User $user, $order_by = 'start_time', $order = 'asc')
    {
        return parent::getFromUser($user, $order_by, $order);
    }

}
