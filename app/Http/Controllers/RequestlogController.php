<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RequestlogController extends Controller
{
    private $alias_path_list = [
        'funds/deposit' => '充值',
        'funds/withdrawal' => '提现',
        'team/SubRecharge' => '上下级转账',
        'user/AddBank' => '添加银行卡',
        'user/Password' => '修改登录密码',
        'user/SecurityPassword' => '修改资金密码',
        'user/SecurityQuestion' => '密保问题',
        'ForgotPassword' => '忘记密码',
    ];

    public function getIndex()
    {
        $data['start_date'] = Carbon::now()->subDay();
        $data['end_date'] = Carbon::tomorrow();
        $data['alias_path_list'] = $this->alias_path_list;

        return view('request-log.index', $data);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            $param = [];
            $param['username'] = (string)$request->get('username', ''); // 会员名
            $param['start_date'] = (string)$request->get('start_date'); // 开始时间
            $param['end_date'] = (string)$request->get('end_date');     // 结束时间
            $param['path'] = (string)$request->get('path', '');         // 路径
            $param['type'] = (int)$request->get('type', 0);             // 类型

            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $model = \Service\Models\UserBetRequestLog::class;
            $table = 'user_bet_request_log';
            if ($param['type'] == 0) {
                $model = \Service\Models\UserRequestLog::class;
                $table = 'user_request_log';
            } elseif ($param['type'] == 1) {
                $model = \Service\Models\AdminRequestLog::class;
                $table = 'admin_request_log';
            }

            $where = function ($query) use ($param, $table) {
                if (!empty($param['username'])) {
                    $query->where("{$table}.username", $param['username']);
                }
                if (!empty($param['path'])) {
                    $query->where("{$table}.path", $param['path']);
                }

                if (!empty($param['start_date'])) {
                    $query->where("{$table}.created_at", '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where("{$table}.created_at", '<', $param['end_date']);
                }
            };

            $query = new $model;

            // 计算过滤后总数
            $count_query = $query->where($where);
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
                $data['data'] = $query->where($where)
                    ->skip($start)->take($length)
                    ->orderBy('created_at', 'desc')
                    ->get([
                        'id',
                        'username',
                        'path',
                        DB::raw('
                        (CASE
                        WHEN
                            character_length(request) > 60
                        THEN
                            left(request, 80) || \'...\'
                        ELSE request
                        END) AS  request'),
                        'created_at',
                        DB::raw("{$param['type']} as type")
                    ]);
            } else {
                $data['data'] = $query::leftJoin('users', 'users.username', "{$table}.username")
                    ->leftJoin('user_profile', function ($join) {
                        $join->on('user_profile.user_id', '=', "users.id")
                            ->where('user_profile.attribute', 'user_observe');
                    })
                    ->where($where)
                    ->skip($start)->take($length)
                    ->orderBy("{$table}.created_at", 'desc')
                    ->get([
                        "{$table}.id",
                        "{$table}.username",
                        'user_profile.value as user_observe',
                        'path',
                        DB::raw('
                        (CASE
                        WHEN
                            character_length(request) > 60
                        THEN
                            left(request, 80) || \'...\'
                        ELSE request
                        END) AS  request'),
                        "{$table}.created_at",
                        DB::raw("{$param['type']} as type")
                    ]);
            }

            foreach ($data['data'] as &$row) {
                preg_match('/"__extend_info":\{"ip":"([a-z0-9:.]+)","method":"([a-z]+)"\}/i', $row->request, $matches);
                if (!empty($matches[1])) {
                    $row->ip = $matches[1];
                    $row->method = $matches[2];
                } else {
                    $row->ip = '';
                    $row->method = '';
                }
            }

            return response()->json($data);
        }
    }

    public function getDetail(Request $request)
    {
        $id = (int)$request->get('id');
        $type = (int)$request->get('type');

        $model = \Service\Models\UserBetRequestLog::class;
        if ($type == 0) {
            $model = \Service\Models\UserRequestLog::class;
        } elseif ($type == 1) {
            $model = \Service\Models\AdminRequestLog::class;
        }

        $query = new $model;

        $data = $query->where('id', $id)
            ->first([
                'username',
                'path',
                'request',
                'created_at',
            ]);

        return view('request-log.detail', $data);
    }
}
