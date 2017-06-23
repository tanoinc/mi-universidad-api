<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\CalendarEvent;
use Illuminate\Support\Facades\Auth;

/**
 * The CalendarEventController controller class
 *
 * @author tanoinc
 */
class CalendarEventController extends AbstractInformationController
{

    protected function getModelClass()
    {
        return CalendarEvent::class;
    }

    protected function getModelName()
    {
        return 'calendar_event';
    }

    protected function setModelDataFromRequest(\App\AbstractInformation $calendar_event, \Illuminate\Http\Request $request)
    {
        $calendar_event->event_name = $request->input('event_name');
        $calendar_event->event_date = $request->input('event_date');
        $calendar_event->event_duration = $request->input('event_duration');

        return $calendar_event;
    }

    protected function getFromUser(\App\User $user, $order_by = 'event_date', $order = 'asc')
    {
        return parent::getFromUser($user, $order_by, $order);
    }
    
    public function getFuture()
    {
        $information = $this->getQueryFromUser(Auth::user())
            ->whereDate('event_date', '>=', \Carbon\Carbon::today()->toDateString())
            ->orderBy('event_date', 'asc')->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT', 20));

        return response()->json($information);
    }
    
    public function getPast()
    {
        $information = $this->getQueryFromUser(Auth::user())
            ->whereDate('event_date', '<', \Carbon\Carbon::today()->toDateString())
            ->orderBy('event_date', 'desc')->simplePaginate(env('ITEMS_PER_PAGE_DEFAULT', 20));

        return response()->json($information);
    }

}
