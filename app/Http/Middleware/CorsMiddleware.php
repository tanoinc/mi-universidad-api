<?php

// Credits: https://gist.github.com/danharper/06d2386f0b826b669552

namespace App\Http\Middleware;

class CorsMiddleware
{

    public function handle(\Symfony\Component\HttpFoundation\Request $request, \Closure $next)
    {
        //https://gist.github.com/danharper/06d2386f0b826b669552#gistcomment-2013919
        //Intercepts OPTIONS requests
        //\Illuminate\Support\Facades\Log::debug(str_replace("\n", "", strval($request->getBasePath())));
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            // Pass the request to the next middleware
            $response = $next($request);
        }
        //Modified to support Symfony's Response class (from Passport plugin)
        $response->headers->set('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'));
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:8100');
        return $response;
    }

}
