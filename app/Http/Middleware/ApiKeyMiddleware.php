<?php

namespace App\Http\Middleware;
use App\Exceptions\UnauthorizedAccessException;

use Closure;

class ApiKeyMiddleware
{
    const AUTH_METHOD = 'APIKEY';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_key_auth = $request->header('Authorization');
        if (empty($api_key_auth)) {
             throw new UnauthorizedAccessException();
        }
        $auth_data = $this->decodeAuthData($api_key_auth);
        $this->validateMethod($auth_data['method']);
        $auth_content = $this->decodeContent($auth_data['content']);
        //print_r($auth_content);
        return $next($request);
    }
    
    protected function decodeAuthData($api_key_auth)
    {
        $auth_data = explode(' ', $api_key_auth);
        if (count($auth_data) != 2) {
            throw new UnauthorizedAccessException('Invalid auth format.');
        }
        return [ 'method' => $auth_data[0], 'content' => $auth_data[1] ];
    }
    
    protected function decodeContent($content_data)
    {
        $content = explode(':', $content_data);
        if (count($content) != 2) {
            throw new UnauthorizedAccessException('Invalid auth content format.');
        }        
        return [ 'key' => $content[0], 'signature' => $content[1] ];
    }
    
    protected function validateMethod($method)
    {
        if ($method != static::AUTH_METHOD)
        {
            throw new UnauthorizedAccessException('Unkonwn auth method.');
        }
    }
}
