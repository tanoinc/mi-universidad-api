<?php

namespace App\Library;

use App\Library\Generic\PushNotificationsInterface;

/**
 * Description of PushNotificationsFirebase
 * 
 * https://firebase.google.com/docs/cloud-messaging/http-server-ref
 *
 * @author lucianoc
 */
class PushNotificationsFirebase extends Http implements PushNotificationsInterface
{
    
    private $server_key;

    public function __construct($endpoint_url, $server_key)
    {
        parent::__construct($endpoint_url);
        $this->server_key = $server_key;
    }

    protected function getHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'key='.$this->server_key,
        ];
    }

    protected static function usersToTokens($users) {
        $tokens = [];
        foreach ($users as $user) {
            foreach ($user->pushTokens()->get() as $token) {
                if ($token->token != '') {
                    $tokens[] = $token->token;
                }
            }
        }

        return $tokens;
    }
    
    protected function encode($data)
    {
        return json_encode($data);
    }
    
    protected static function setNotificationRecipients(&$body, $recipients) {
        if ($recipients == static::RECIPIENT_ALL) {
            $body['send_to_all'] = true;
        } else {
            $body['registration_ids'] = static::usersToTokens($recipients);

            return count($body['registration_ids']);
        }
        
        return -1;
    }
    
    protected static function createNotificationBody($title, $message, $payload = null)
    {
        $body = [
            'registration_ids' => [],
            'data'=> $payload,
            'icon' => 'icon',
            'notification' => [
                "title" => $title,
                "body" => $message,
            ],
        ];
        
        return $body;
    }


    public function sendPushNotification($recipients, $title, $message, $payload = null)
    {
        $body = static::createNotificationBody($title, $message, $payload);
        
        if (static::setNotificationRecipients($body, $recipients) == 0) {
            return static::NO_TOKENS;
        }

        if (!env('PUSH_NOTIFICATIONS_ENABLED', false))
        {
            return 'push-notifications-disabled';
        }
        
        $notification = json_decode($this->post($body, ''));
        
        if ($notification->multicast_id) {
            return 'fcm.multicast_id:'.$notification->multicast_id;
        }
        throw new \App\Exceptions\PushNotificationException();
    }
}
