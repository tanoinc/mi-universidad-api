<?php

namespace App\Library;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\Client;

/**
 * Description of IonicApiServiceProvider
 *
 * @author lucianoc
 */
class IonicApiV2 extends Http
{

    const RECIPIENT_ALL = "RECIPIENT_ALL";

    private $ionic_push_profile;
    private $ionic_token;

    public function __construct($ionic_endpoint_url, $ionic_push_profile, $ionic_token)
    {
        parent::__construct($ionic_endpoint_url);
        $this->ionic_push_profile = $ionic_push_profile;
        $this->ionic_token = $ionic_token;
    }

    protected function getHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->ionic_token,
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
    
    protected static function encode($data)
    {
        return json_encode($data);
    }
    
    public function sendPushNotification($recipients, $title, $message, $payload = null)
    {
        $body = [
            //"tokens" => ["your", "device", "tokens"],
            "profile" => $this->ionic_push_profile,
            "notification" => [
                "title" => $title,
                "message" => $message,
                "payload" => $payload,
            ]
        ];
        if ($recipients == static::RECIPIENT_ALL) {
            $body['send_to_all'] = true;
        } else {
            $body['tokens'] = static::usersToTokens($recipients);
            if (empty($body['tokens'])) {
                return 'push-notifications-without-tokens';
            }
        }
        if (!env('PUSH_NOTIFICATIONS_ENABLED', false))
        {
            return 'push-notifications-disabled';
        }
        
        $notification = $this->post($body, 'push/notifications');

        if ($notification->meta->status >= 200 and $notification->meta->status <= 299) {
            return $notification->data->uuid;
        }
        throw new \App\Exceptions\PushNotificationException();
    }
}
