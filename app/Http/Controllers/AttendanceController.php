<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\AbstractInformation;
use App\User;
use Illuminate\Http\Request;
use App\Exceptions\AttendanceInactiveException;
use Illuminate\Support\Facades\Auth;
use App\Library\AttendanceControls\AttendanceControlFactory;
use App\Library\AttendanceControls\AbstractAttendanceControl;

/**
 * The AttendanceController controller class
 *
 * @author tanoinc
 */
class AttendanceController extends AbstractInformationController
{

    use Traits\InformationDateableControllerTrait;
    
    protected function getWithAttendanceFunction()
    {
        return function ($query) {
            return $query->leftJoin('attendance_control', 'attendance_control.attendance_id', '=', 'attendance.id');
        };
    }

    public function getFuture()
    {
        $fn_custom_filter = $this->getWithAttendanceFunction();
        
        return $this->getResponseByFutureDate('start_time', $fn_custom_filter);
    }
    
    public function getNow()
    {
        $fn_custom_filter = $this->getWithAttendanceFunction();
        
        return $this->getResponseByNowDate('start_time', 'end_time', $fn_custom_filter);
    }

    public function changeStatus($attendance_id)
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
        $controls = AttendanceControlFactory::makeFromAttendance($attendance, app('request') );
        if (!AbstractAttendanceControl::allValid($controls)) 
        {
            throw new \App\Exceptions\AttendanceControlException();
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
