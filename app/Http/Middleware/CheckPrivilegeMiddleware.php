<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Closure;

class CheckPrivilegeMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        if ($this->granted($request)) {
            return $next($request);
        } else {
            throw new \App\Exceptions\ForbiddenAccessException();
        }
    }

    protected function granted(\Illuminate\Http\Request $request)
    {
        $app = $request->attributes->get('application');
        Log::debug(sprintf('Checking privilege controller action [%s]', static::requestToControllerAction($request)));
        return ($app->has_granted_action(static::requestToControllerAction($request)) != null);
    }

    protected static function requestToControllerAction($request)
    {
        return $request->route()[1]['uses'];
    }

}
