<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * The newsfeed model class
 *
 * @author tanoinc
 */
class CalendarEvent extends AbstractInformation
{

    const TABLE_NAME = 'calendar_event';

    protected $fillable = [
        'event_name',
        'event_date',
        'event_duration',
        'send_notification',
        'global',
        'context_id',
    ];
    protected $casts = [
        'event_date' => 'datetime:c',
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
        'deleted_at' => 'datetime:c',
    ];    

    protected function getPrivilegeSendNotification()
    {
        return Privilege::CALENDAR_EVENT_SEND_NOTIFICATION;
    }

    public function getNotificationContent()
    {
        return $this->event_name;
    }

    public function getNotificationTitle()
    {
        return $this->event_date->format(env('DATE_FORMAT_READABLE','d/m/Y H:i'));
    }
    
    public static function fromUserBetweenDates(User $user, \DateTime $start, \DateTime $end)
    {
        $fn_filter = function ($query) use ($start, $end) {
            return $query->whereBetween('event_date', [$start, $end]);
        };
        
        return static::fromUser($user, $fn_filter);
    }
}
