<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Service\Models\AdminRequestLog;

class RequestLog
{
    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request_data = $request->all();
        if (!auth()->user()                   // 未登录
            || empty($request_data)           // 未提交数据
            || in_array($request->path(), ['index/tasks','project/alert','chatdeposit/newMessage'])
        ) {
            // 不记录日志
            return $next($request);
        }

        $log = new AdminRequestLog;

        // if (app()->isLocal()) {
        //    $log->setConnection(null);        // 本地环境使用默认的数据库连接
        // }

        //参数过滤
        $path = strtolower($request->path());
        $request_data = $this->requestFilter($path, $request_data);

        $extend_data = [
            '__extend_info'=>[
                'ip'=>$request->ip(),
                'method'=>$request->method(),
            ],
        ];
        $request_data = array_merge($extend_data, $request_data);

        $log->username = auth()->user()->username;
        $log->path     = $request->path();
        $log->request  = json_encode($request_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        register_shutdown_function(function ($log) {
            $log->save();          // 结束请求后再保存日志
        }, $log);

        return $next($request);
    }

    /**
     * 参数过滤
     * @param string $path          请求路径
     * @param array  $request_data  请求参数
     * @return mixed                过滤后的参数
     */
    private function requestFilter($path, $request_data)
    {
        switch ($path) {
            case 'paymentchannel/create':
            case 'paymentchannel/edit':
                $filter_keys = [
                    'account_key',
                    'account_key2'
                ];
                break;

            case 'withdrawalchannel/create':
            case 'withdrawalchannel/edit':
                $filter_keys = [
                    'key1',
                    'key2',
                    'key3'
                ];
                break;

            case 'smschannel/create':
            case 'smschannel/edit':
                $filter_keys = [
                    'key',
                    'key2'
                ];
                break;

            case 'admin/create':
            case 'admin/edit':
                if (isset($request_data['password'])) {
                    $request_data['password'] = '*********';
                }
                if (isset($request_data['password_confirmation'])) {
                    $request_data['password_confirmation'] = '*********';
                }
                break;
            case 'profile/password':
                if (isset($request_data['old_password'])) {
                    $request_data['old_password'] = '*********';
                }
                if (isset($request_data['new_password'])) {
                    $request_data['new_password'] = '*********';
                }
                if (isset($request_data['new_password_confirmation'])) {
                    $request_data['new_password_confirmation'] = '*********';
                }
                break;
            case 'login':
                if (isset($request_data['password'])) {
                    $request_data['password'] = '*********';
                }
                if (isset($request_data['code'])) {
                    $request_data['code'] = '******';
                }
                break;
            case 'user/changepass':
                if (isset($request_data['password'])) {
                    $request_data['password'] = '*********';
                }
                if (isset($request_data['comfirmpassword'])) {
                    $request_data['comfirmpassword'] = '******';
                }
                if (isset($request_data['security_password'])) {
                    $request_data['security_password'] = '*********';
                }
                if (isset($request_data['comfirm_security_password'])) {
                    $request_data['comfirm_security_password'] = '******';
                }
                break;

            default:
                $filter_keys = ['password'];
        }

        if (!empty($filter_keys)) {
            foreach ($filter_keys as $key) {
                unset($request_data[$key]);
            }
        }

        return $request_data;
    }
}
