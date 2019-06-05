<?php

namespace App\Http\Controllers;

use App\CalendarEvent;
use Illuminate\Support\Facades\Auth;

/**
 * The AttendanceController controller class
 *
 * @author tanoinc
 */
class AttendanceController extends AbstractInformationController
{

    protected function getModelClass()
    {
        return CalendarEvent::class;
    }

    protected function getModelName()
    {
        return 'calendar_event';
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

    protected function setModelDataFromRequest(\App\AbstractInformation $attendance, \Illuminate\Http\Request $request)
    {
        $attendance->name = $request->input('name');
        $attendance->description = $request->input('description');
        $attendance->location = $request->input('location');
        $attendance->start_time = $request->input('start_time');
        $attendance->end_time = $request->input('end_time');

        return $attendance;
    }

    protected function getFromUser(\App\User $user, $order_by = 'start_time', $order = 'asc')
    {
        return parent::getFromUser($user, $order_by, $order);
    }
    
}
