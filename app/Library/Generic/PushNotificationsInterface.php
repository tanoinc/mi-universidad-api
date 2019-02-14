<?php

namespace App\Library\Generic;

/**
 *
 * @author lucianoc
 */
interface PushNotificationsInterface 
{
    const RECIPIENT_ALL = "RECIPIENT_ALL";
    const NO_TOKENS = 'push-notifications-without-tokens';
    
    public function sendPushNotification($recipients, $title, $message, $payload = null);
}
