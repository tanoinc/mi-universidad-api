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
class IonicApiV2
{

    const RECIPIENT_ALL = "RECIPIENT_ALL";

    private $ionic_push_profile;
    private $ionic_token;
    private $http_client;

    public function __construct($ionic_endpoint_url, $ionic_push_profile, $ionic_token)
    {
        $this->ionic_push_profile = $ionic_push_profile;
        $this->ionic_token = $ionic_token;
        $this->http_client = new Client([ 'base_uri' => $ionic_endpoint_url]);
    }

    protected function getHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->ionic_token,
        ];
    }

    public function sendPushNotification($recipients, $title, $message)
    {
        $body = [
            //"tokens" => ["your", "device", "tokens"],
            "profile" => $this->ionic_push_profile,
            "notification" => [
                "title" => $title,
                "message" => $message,
            ]
        ];
        if ($recipients == static::RECIPIENT_ALL) {
            $body['send_to_all'] = true;
        } else {
            $body['tokens'] = $recipients;
        }
        $res = $this->http_client->request('POST', 'push/notifications', ['headers' => $this->getHeaders(), 'body' => json_encode($body)]);
        
        return static::decodeBody( $res->getHeaderLine('Content-Type'), $res->getBody() );
    }
    
    protected static function decodeBody($content_type, $body) 
    {
        if ($content_type == 'application/json; charset=utf8') {
            return json_decode($body);
        } else {
            throw new \App\Exceptions\ContentTypeDecodingException();
        }
    }

}
