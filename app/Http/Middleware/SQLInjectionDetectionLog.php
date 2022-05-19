<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class SQLInjectionDetectionLog
{
    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $obj = new \Service\API\SQLInjectionDetect();
        $check = $obj->check(1);
        if(empty($check)) {
            return $obj->response();
        }

        return $next($request);
    }
}
