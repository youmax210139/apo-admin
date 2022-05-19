<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use itbdw\Ip\IpLocation;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginlogIndexRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoginlogController extends Controller
{
    public function getIndex()
    {
        $data['start_date'] = Carbon::now()->subDay()->format('Y-m-d 00:00:00');
        $data['end_date'] = Carbon::now()->format('Y-m-d 23:59:59');

        return view('login-log.index', $data);
    }

    public function postIndex(LoginlogIndexRequest $request)
    {
        if ($request->ajax() || $request->get('export', 0)) {
            $data = [];
            $param = [];
            $param['username'] = (string)$request->get('username', ''); // 会员名
            $param['start_date'] = (string)$request->get('start_date');        // 开始时间
            $param['end_date'] = (string)$request->get('end_date');            // 结束时间
            $param['ip'] = (string)$request->get('ip', '');             // IP
            $param['type'] = (int)$request->get('type', 0);             // 类型
            $param['os'] = (string)$request->get('os', '');             // 操作系统
            $export = (int)$request->get('export', 0);                  //导出标识

            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $user_table = 'users';
            $table = 'user_login_log';
            $model = \Service\Models\UserLoginLog::class;
            if ($param['type'] == 1) {
                $user_table = 'admin_users';
                $table = 'admin_login_log';
                $model = \Service\Models\AdminLoginLog::class;
            }

            $where = function ($query) use ($param, $table, $user_table) {
                if (!empty($param['username'])) {
                    $query->where("{$user_table}.username", $param['username']);
                }
                if (!empty($param['ip'])) {
                    $query->where("{$table}.ip", '<<=', "{$param['ip']}/24");
                }
                if (!empty($param['os'])) {
                    $query->where("{$table}.os", $param['os']);
                }
                if (!empty($param['start_date'])) {
                    $query->where("{$table}.created_at", '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where("{$table}.created_at", '<=', $param['end_date']);
                }
            };

            // 计算过滤后总数
            $count_query = $model::leftJoin($user_table, "{$user_table}.id", "{$table}.user_id")->where($where);
            $count_sql = vsprintf(str_replace(array('?'), array('\'\'%s\'\''), $count_query->select([DB::raw(1)])->toSql()), $count_query->getBindings());
            $count = DB::selectOne("
                select count_estimate('{$count_sql}') as total
            ");

            if ($count->total > 20000) {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count->total;
            } else {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count_query->count();
            }

            if ($param['type'] == 1) {
                $data['data'] = $model::select([
                    "{$table}.id",
                    "{$user_table}.username",
                    "{$table}.domain",
                    "{$table}.province",
                    "{$table}.browser",
                    "{$table}.browser_version",
                    "{$table}.os",
                    "{$table}.device",
                    "{$table}.ip",
                    "{$table}.created_at"
                ])
                    ->leftJoin($user_table, "{$user_table}.id", "{$table}.user_id")
                    ->where($where);
            } else {
                $data['data'] = $model::select([
                    "{$table}.id",
                    "{$user_table}.username",
                    "user_profile.value as user_observe",
                    "{$table}.domain",
                    "{$table}.province",
                    "{$table}.browser",
                    "{$table}.browser_version",
                    "{$table}.os",
                    "{$table}.device",
                    "{$table}.ip",
                    "{$table}.created_at"
                ])
                    ->leftJoin($user_table, "{$user_table}.id", "{$table}.user_id")
                    ->leftJoin('user_profile', function ($join) use ($user_table) {
                        $join->on('user_profile.user_id', '=', "{$user_table}.id")
                            ->where('user_profile.attribute', 'user_observe');
                    })
                    ->where($where);
            }

            if (empty($export)) {
                $data['data'] = $data['data']->skip($start)->take($length)
                    ->orderBy("{$table}.created_at", 'desc')
                    ->get();

                foreach ($data['data'] as &$v) {
                    $v->ip = self::getIpLocation($v->ip);
                }
                return response()->json($data);
            } else {
                if ($data['recordsTotal'] > 10000) {
                    return '数据超过 10000 条记录，无法导出！';
                }

                //导出数据
                if ($param['type'] == 1) {
                    $file_name = "管理员登录日志.csv";
                } else {
                    $file_name = "用户登录日志.csv";
                }

                $query = $data['data'];
                $response = new StreamedResponse(null, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
                ]);
                $response->setCallback(function () use ($query, $table) {
                    $out = fopen('php://output', 'w');
                    fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                    $first = true;
                    $query->orderBy("{$table}.id", 'desc')->chunk(500, function ($results) use (&$first, $out, $table) {
                        if ($first) {
                            //列名
                            $columnNames[] = '用户名';
                            if ($table == 'user_login_log') {
                                $columnNames[] = '重点观察';
                            }
                            $columnNames[] = '域名';
                            $columnNames[] = '国家/地区';
                            $columnNames[] = '浏览器';
                            $columnNames[] = '浏览器版本';
                            $columnNames[] = '操作系统';
                            $columnNames[] = '设备';
                            $columnNames[] = 'IP';
                            $columnNames[] = '时间';
                            fputcsv($out, $columnNames);
                            $first = false;
                        }
                        $datas = [];
                        foreach ($results as $item) {
                            if ($table == 'user_login_log') {
                                $datas[] = [
                                    $item->username,
                                    $item->user_observe ? '是' : '',
                                    $item->domain,
                                    $item->province,
                                    $item->browser,
                                    $item->browser_version,
                                    $item->os,
                                    $item->device,
                                    self::getIpLocation($item->ip),
                                    $item->created_at,
                                ];
                            } else {
                                $datas[] = [
                                    $item->username,
                                    $item->domain,
                                    $item->province,
                                    $item->browser,
                                    $item->browser_version,
                                    $item->os,
                                    $item->device,
                                    self::getIpLocation($item->ip),
                                    $item->created_at,
                                ];
                            }
                        }
                        foreach ($datas as $item) {
                            fputcsv($out, $item);
                        }
                    });
                    fclose($out);
                });
                $response->send();
            }
        }
    }

    //获取ip详情
    private static function getIpLocation($ip)
    {
        if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $location = IpLocation::getLocation($ip);
            return implode(" ", $location);
        }
        return $ip;
    }
}
