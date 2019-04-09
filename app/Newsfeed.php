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
class Newsfeed extends AbstractInformation
{

    const TABLE_NAME = 'newsfeed';

    protected $casts = [
        'created_at' => 'datetime:c',
        'updated_at' => 'datetime:c',
        'deleted_at' => 'datetime:c',
    ];
    
    protected $fillable = [
        'title',
        'content',
        'send_notification',
        'global',
        'context_id',
    ];

    protected function getPrivilegeSendNotification()
    {
        return Privilege::NEWSFEED_SEND_NOTIFICATION;
    }

    public function getNotificationContent()
    {
        return $this->content;
    }

    public function getNotificationTitle()
    {
        return $this->title;
    }

}
