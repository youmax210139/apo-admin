<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Closure;

class RequestLimit
{


    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), ['POST', 'PUT'], true) && !$request->ajax()) {
            $request_key = 'request_'.md5(auth()->id().$request->method().$request->path());
            if (!Redis::set($request_key, '1', 'EX', 1, 'NX')) {
                return redirect()->back()->withErrors("重复点击提交已被拦截，请检查是否操作成功。");
            }
        }
        return $next($request);
    }
}
