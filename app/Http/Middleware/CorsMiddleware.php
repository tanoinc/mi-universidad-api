<?php

// Credits: https://gist.github.com/danharper/06d2386f0b826b669552

namespace App\Http\Middleware;

class CorsMiddleware
{

    public function handle($request, \Closure $next)
    {
        //https://gist.github.com/danharper/06d2386f0b826b669552#gistcomment-2013919
        //Intercepts OPTIONS requests
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            // Pass the request to the next middleware
            $response = $next($request);
        }
        $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE');
        $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));
        $response->header('Access-Control-Allow-Origin', '*');
        return $response;
    }

}
