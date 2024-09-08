<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', 'http://192.168.10.211:3000');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization');

        // Handle preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            $response->headers->set('Access-Control-Max-Age', '3600');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            return $response;
        }
        return $response;
    }
}
