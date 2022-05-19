<?php

namespace App\Listeners;

use Browser;
use itbdw\Ip\IpLocation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Auth\Events\Login;
use Service\Models\AdminLoginLog;

class LogSuccessfulLogin
{

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $user->last_time = (string) now();
        $user->last_ip = request()->ip();
        $user->last_session = Str::random(32);
        Cookie::queue('last_session', $user->last_session, 30 * 24 * 3600);
        $user->save();

        $request = request();
        // $browser = new Browser();
        // $os = new Os();
        // $device = new Device();
        $ip = $request->ip();

        $location = IpLocation::getLocation($ip);
        $admin_login_log = new AdminLoginLog();
        $request_data = request()->all();
        unset($request_data['password']);  // 密码不保存
        $admin_login_log->user_id = $user->id;
        $admin_login_log->province = !empty($location['province']) ?
            $location['province'] : (!empty($location['country']) ? $location['country'] : '');
        $admin_login_log->domain = request()->getHost();
        $admin_login_log->browser = Browser::browserName();
        $admin_login_log->browser_version = Browser::browserVersion();
        $admin_login_log->os = Browser::platformName();
        $admin_login_log->device = Browser::deviceFamily();
        $admin_login_log->ip = $ip;
        $admin_login_log->request = json_encode($request_data);
        $admin_login_log->created_at = (string) now();
        $admin_login_log->save();
    }
}
