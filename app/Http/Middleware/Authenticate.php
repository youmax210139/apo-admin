<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;

class Authenticate extends BaseAuthenticate
{
    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @param string[] $guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        URL::forceScheme(Str::startsWith($request->server->get('HTTP_REFERER'), 'https') ? 'https' : 'http');

        $this->authenticate($request, $guards);
        //检查用户是否在别处登陆
        if (Cookie::get('last_session') != auth()->user()->last_session) {
            auth()->guard()->logout();
            $request->session()->invalidate();

            if ($request->ajax() && ($request->getMethod() != 'GET')) {
                return response()->json([
                    'status' => -1,
                    'code' => 403,
                    'msg' => '被迫下线，你的账号在别处登陆！',
                    'data' => []
                ])->setStatusCode(402);
            } else {
                return response()->redirectTo("/login")->withErrors("被迫下线，你的账号在别处登陆！");
            }
        }
        //检查用户是否已被冻结
        if (auth()->user()->is_locked) {
            auth()->guard()->logout();
            $request->session()->invalidate();
            if ($request->ajax()) {
                return response()->json([
                    'status' => -2,
                    'code' => 403,
                    'msg' => '你的账号在已被冻结！',
                    'data' => []
                ])->setStatusCode(401);
            } else {
                return response()->redirectTo("/login")->withErrors("你的账号在已被冻结！");
            }
        }
        //强制绑定谷歌验证器
        if (get_config('admin_google_key', 0)) {
            if (empty(auth()->user()->google_key) && $request->path() != 'profile/googlekey' && auth()->id() !== 1) {
                return response()->redirectTo('profile/googlekey');
            }
        }

        $previousUrl = URL::previous();

        $rule = $request->method() == 'DELETE'
            ? $request->get('__pathinfo__') . '/delete'
            : $request->get('__pathinfo__');

        $pathinfo_arr = explode('/', $rule);

        if (!isset($pathinfo_arr[1])) {
            $rule .= '/index';
        }
        if ($pathinfo_arr[0] == 'index' || Gate::check($rule)) {
            return $next($request);
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => -1,
                'code' => 403,
                'msg' => '您没有权限执行此操作！',
                'data' => []
            ])->setStatusCode('403');
        } else {
            return response()->view('errors.403', compact('previousUrl', 'rule'));
        }
    }
}
