<?php

namespace App\Library;

use App\Library\Generic\PushNotificationsInterface;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use App\UserPushToken;

/**
 * Description of PushNotificationsLaravelFCM
 *
 * https://github.com/brozot/Laravel-FCM
 *
 * @author lucianoc
 */
class PushNotificationsLaravelFCM implements PushNotificationsInterface
{

    protected static function usersToTokens($users)
    {
        $tokens = [];
        foreach ($users as $user) {
            foreach ($user->pushTokens()->get() as $token) {
                if ($token->token != '' and !in_array($token->token, $tokens)) {
                    $tokens[] = $token->token;
                }
            }
        }

        return $tokens;
    }

    protected static function setNotificationRecipients(&$body, $recipients)
    {
        if ($recipients == static::RECIPIENT_ALL) {
            $body['send_to_all'] = true;
        } else {
            $body['registration_ids'] = static::usersToTokens($recipients);

            return count($body['registration_ids']);
        }

        return -1;
    }
    
    protected function updateTokens(\LaravelFCM\Response\DownstreamResponseContract $downstreamResponse)
    {
        if (!empty($tokensDelete = $downstreamResponse->tokensToDelete())) {
            foreach ($tokensDelete as $tokenDelete) {
                if ($token = UserPushToken::findByToken($tokenDelete)->first()) {
                    $token->delete();
                }
            }
        }
        if (!empty($tokensUpdate = $downstreamResponse->tokensToModify())) {
            foreach ($tokensUpdate as $oldToken => $newToken) {
                if ($token = UserPushToken::findByToken($oldToken)->first()) {
                    $token->token = $newToken;
                    $token->save();
                }
            }
        }
    }

    public function sendPushNotification($recipients, $title, $message, $payload = null)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($message)->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($payload);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = static::usersToTokens($recipients);

        if (!env('PUSH_NOTIFICATIONS_ENABLED', false)) {
            return 'push-notifications-disabled';
        }

        if ($recipients == static::RECIPIENT_ALL) {
            throw new \App\Exceptions\PushNotificationException('Sending to all recipients currently unsupported');
        } elseif (!empty($tokens)) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $this->updateTokens($downstreamResponse);

            if ($downstreamResponse) {
                
                return null;
            }
            throw new \App\Exceptions\PushNotificationException();    
        }
        
        return null;
    }

}
