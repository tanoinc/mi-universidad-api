<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedAccessException;
use Closure;
use Illuminate\Support\Facades\Log;

class ApiKeyMiddleware
{

    const AUTH_METHOD = 'APIKEY';

    protected $application = null;
    protected $auth_data = [];
    protected $auth_method = null;
    protected $auth_key = null;
    protected $auth_signature = null;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Log::debug(sprintf('Signature: %s.', json_encode(getallheaders() )));
        $raw_auth_data = $request->header('Authorization');
        if (empty($raw_auth_data)) {
            throw new UnauthorizedAccessException();
        }
        $this->initializeAuthData($raw_auth_data);
        if ($this->isValidRequest($request)) {
            $request->attributes->add(['application' => $this->getApplication()]);
            return $next($request);
        }
        throw new UnauthorizedAccessException();
    }

    protected function decodeAuthData($api_key_auth)
    {
        $auth_data = explode(' ', $api_key_auth);
        if (count($auth_data) != 2) {
            throw new UnauthorizedAccessException('Invalid auth format.');
        }
        return [ 'method' => $auth_data[0], 'content' => $auth_data[1]];
    }

    protected function decodeContent($content_data)
    {
        $content = explode(':', $content_data);
        if (count($content) != 2) {
            throw new UnauthorizedAccessException('Invalid auth content format.');
        }
        return [ 'key' => $content[0], 'signature' => $content[1]];
    }

    protected function validateMethod($method)
    {
        if ($method != static::AUTH_METHOD) {
            throw new UnauthorizedAccessException('Unkonwn auth method.');
        }
    }

    protected function initializeAuthData($raw_auth_data)
    {
        $auth_data = $this->decodeAuthData($raw_auth_data);
        $this->validateMethod($auth_data['method']);
        $this->auth_method = $auth_data['method'];
        $auth_data = $this->decodeContent($auth_data['content']);
        $this->auth_key = $auth_data['key'];
        $this->auth_signature = $auth_data['signature'];
    }

    protected function getApplication()
    {
        if (!isset($this->application)) {
            $this->application = $this->retrieveApplication($this->auth_key);
        }
        return $this->application;
    }

    protected function isValidRequest($request)
    {
        $hash = hash_hmac($this->getHmacHashFunction(), $this->getHmacContent($request), $this->getApplication()->api_secret);
        Log::debug(sprintf('Signature: Expected [%s], Received [%s]: %s.', $hash, $this->auth_signature, $this->getHmacContent($request)));
        //Log::debug(sprintf('data: %s', $this->getHmacContent($request)));

        return ($hash == $this->auth_signature);
    }

    protected function getUrl($request)
    {
        $force_ssl = env('FORCE_SSL',false);
        $url = $request->fullUrl();
        if ($force_ssl) {
            $url  = preg_replace('/^http\:/', 'https:', $url );
        }
        
        $force_url = env('FORCE_URL',false);
        $force_url_replace = env('FORCE_URL_REPLACE',false);
        if ($force_url and $force_url_replace) {
            $url  = preg_replace('/^'.$force_url_replace.'/', $force_url, $url );
        }
        
        return $url;
    }
    
    protected function getHmacContent($request)
    {
        $content = [
            'full_url' => $this->getUrl($request),
            'method' => $request->method(),
            'input' => $request->all(),
        ];
        return json_encode($content);
    }

    protected function getHmacHashFunction()
    {
        return 'sha256';
    }

    protected function retrieveApplication($api_key)
    {
        $app = \App\Application::where('api_key', $api_key)->first();
        if (!$app) {
            throw new UnauthorizedAccessException('Application not found.');
        }
        return $app;
    }

}
