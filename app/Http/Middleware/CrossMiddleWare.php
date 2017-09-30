<?php

namespace App\Http\Middleware;

use Closure;

class CrossMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', 'http://admin.arch-seu.com');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept');
        $response->header('Access-Control-Allow-Methods', 'OPTIONS,GET, POST, PATCH, PUT, DELETE');
        $response->header('Access-Control-Allow-Credentials', 'true');
        return $response;
    }
}
