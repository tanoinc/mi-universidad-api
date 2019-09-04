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
use App\AttendanceUser;
use Illuminate\Validation\Rule;
use App\Exceptions\AttendanceControlClassNotFoundException;
use App\Library\AttendanceControls\AbstractAttendanceControl;
use App\Exceptions\UnauthorizedAccessException;

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
        $user = Auth::user();

        return $this->getResponseByFutureDate(
                'start_time',
                $this->fnWithAttendanceUserAndControl($user)
        );
    }

    public function getNow()
    {
        $user = Auth::user();

        return $this->getResponseByNowDate(
                'start_time', 'end_time',
                $this->fnWithAttendanceUserAndControl($user)
        );
    }
    
    public function getPresent($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        if (!$this->canRetreieve($attendance)) {
            throw new UnauthorizedAccessException();
        }
        
        return response()->json( 
                AttendanceUser::statusWithExternalIds($attendance)
                ->presents()
                ->get()
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

    protected function checkAttendanceControls(Attendance $attendance)
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

    protected function fnWithAttendanceUserAndControl(User $user)
    {
        return function ($query) use ($user) {
            $fn_control = AttendanceControl::fnWithAttendanceControl();
            $fn_user = AttendanceUser::fnWithAttendanceUser($user);
            return $fn_control($fn_user($query));
        };
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
        $rules['controls.*.type'] = [
            'required',
            Rule::in(AttendanceControl::types()),
        ];
        $rules['controls.*.parameters'] = 'required'; //@TODO: Validar parametros dependiendo del control.

        return $rules;
    }

    protected function setModelDataFromRequest(AbstractInformation $attendance, Request $request)
    {
        $attendance->name = $request->input('name');
        $attendance->description = $request->input('description');
        $attendance->location = $request->input('location');
        $attendance->start_time = $request->input('start_time');
        $attendance->end_time = $request->input('end_time');
        
        foreach ($request->input('controls', []) as $control_request) {
            $attendance_control = $this->newAttendanceControlFromRequest(
                    $attendance, 
                    $control_request
            );
            
            if (!$current_contol = $attendance->hasControl($attendance_control)) {
                $attendance->controls->add($attendance_control);
            } elseif (!$current_contol->sameAs($attendance_control)) {
                $current_contol->copyFrom($attendance_control);
                $current_contol->save();
            }
        }

        return $attendance;
    }
    
    protected function customSave(AbstractInformation $information)
    {
        $information->controls()->saveMany($information->controls);
    }

    protected function newAttendanceControlFromRequest(Attendance $attendance, $control_request)
    {
        list($control_class, $parameters) = $this->getClassAndParametersFromRequest($control_request);
        
        $attendance_control = new AttendanceControl();
        $attendance_control->parameters = $parameters;
        $attendance_control->type = $control_class;
                
        return $attendance_control;
    }
    
    protected function getClassAndParametersFromRequest($control_request)
    {
        $parameters = $control_request['parameters'];
        $decoded_parameters = json_decode($parameters);
        $control_class = AttendanceControl::toClass($control_request['type']);
        
        AbstractAttendanceControl::checkValidParameters($decoded_parameters, $control_class);
        
        return [$control_class, $parameters];
    }

    protected function getFromUser(User $user, $order_by = 'start_time', $order = 'asc')
    {
        return parent::getFromUser($user, $order_by, $order);
    }

}
