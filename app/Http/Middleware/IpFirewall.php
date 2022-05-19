<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Service\Models\IpFirewall as IpFirewallModel;

class IpFirewall
{
    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $client_ip = $request->getClientIp();
        $user_agent = $request->server('HTTP_USER_AGENT');

        if (get_config('admin_ip_whitelist_switch', 0)) {
            $check = Cache::remember(
                'Middleware::AdminIpFirewall' . md5($client_ip),
                1 / 2,  // 缓存 1/2 分钟
                function () use ($client_ip) {
                    $row = IpFirewallModel::where('type', 'admin')
                        ->where('ip', '>>=', DB::raw("inet '{$client_ip}'"))
                        ->first(['id']);
                    return $row;
                }
            );
            if (empty($check)) {
                return response()->view('errors.forbidden', ['client_ip' => $client_ip, 'user_agent' => $user_agent]);
            }
        } else {
            $check = Cache::remember(
                "Middleware::AdminBlackIp" . md5($client_ip),
                1 / 2,      //缓存30秒
                function () use ($client_ip) {
                    $row = IpFirewallModel::where('type', 'admin_black')
                        ->where('ip', '>>=', DB::raw("inet '{$client_ip}'"))
                        ->first(['id']);
                    return $row;
                }
            );
            if ($check) {
                return response()->view('errors.forbidden', ['client_ip' => $client_ip, 'user_agent' => $user_agent]);
            }
        }
        return $next($request);
    }
}
