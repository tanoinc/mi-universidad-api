<?php
namespace App\Library;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use GuzzleHttp\Client;
/**
 * mi-universidad Http requests class
 *
 * @author tanoinc
 */
class Http
{
    protected $http_client;
    protected $base_uri;

    public function __construct($base_uri = null)
    {
        $this->http_client = new Client([ 'base_uri' => $base_uri]);
        $this->base_uri = $base_uri;
    }
    
    protected function getHeaders()
    {
        return [];
    }
    
    protected static function encode($data) 
    {
        $headers = $this->getHeaders();
        if (isset($headers['Content-Type'])) {
            if ($headers['Content-Type'] == 'application/json') {
                
                return json_encode($data);
            }
            throw new \App\Exceptions\CustomValidationException();
        }
        
        return http_build_query($data);
    }

    protected static function decodeBody($content_type, $body) 
    {
        if ($content_type == 'application/json; charset=utf-8') {
            return json_decode($body);
        } else {
            return strval($body);
        }
    }    
    
    protected function fetch($url, $method, $data = null )
    {
        $content = array();
        $content['headers'] = $this->getHeaders();
        if ($method != 'GET') {
            $content['body'] = static::encode($data);
        }
        try {
            $res = $this->http_client->request($method, $url, $content);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            throw new \App\Exceptions\RemoteResourceUnaccessableException();
        }
        return static::decodeBody( $res->getHeaderLine('Content-Type'), $res->getBody() );
    }
    
    public function get($url = '')
    {
        return $this->fetch($url, 'GET');
    }
    
    public function post($data, $url = '')
    {
        return $this->fetch($url, 'POST', $data);
    }    
}
