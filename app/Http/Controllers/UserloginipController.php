<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Service\Models\UserLoginLog;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserloginipController extends Controller
{
    public function getIndex()
    {
        return view('user-login-ip.index', [
            'start_date' => Carbon::yesterday(),
            'end_date' => Carbon::now(),
        ]);
    }

    public function postIndex(Request $request)
    {
        $data = ['recordsTotal' => 0, 'data' => []];
        $order = $request->get('order');
        $columns = $request->get('columns');
        $param['start_date'] = $request->get('start_date');
        $param['end_date'] = $request->get('end_date');
        $param['is_search'] = $request->get('is_search');
        $start = $request->get('start');
        $length = $request->get('length');
        $export = (int)$request->get('export', 0);
        if (empty($param['start_date'])) {
            $param['start_date'] = Carbon::yesterday();
        }
        if (empty($param['end_date'])) {
            $param['end_date'] = Carbon::now();
        }
        $where = function ($query) use ($param) {
            if (!empty($param['start_date'])) {
                $query->where('user_login_log.created_at', '>=', $param['start_date']);
            }
            if (!empty($param['end_date'])) {
                $query->where('user_login_log.created_at', '<=', $param['end_date']);
            }
        };
        $sub_table = UserLoginLog::select(['ip as ips', DB::raw('1 as times')])
            ->where($where)
            ->groupBy('ip', 'user_id');

        $data['data'] = DB::table(DB::raw("({$sub_table->toSql()}) as t"));

        if (empty($export)) {
            $data['recordsTotal'] = $data['recordsFiltered'] = DB::table(DB::raw("(select ips, sum(times) from ({$sub_table->toSql()} )as t group by ips )as t"))
                ->mergeBindings($sub_table->getQuery())
                ->count('ips');
            $data['data'] = $data['data']->select([
                'ips',
                DB::raw('sum(times)')
            ])
                ->mergeBindings($sub_table->getQuery())
                ->groupBy('ips')->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->skip($start)->take($length)
                ->get();
            return response()->json($data);
        } else {
            //导出数据
            $name = '同IP统计报表';
            $start = date("m_dHi", strtotime($param['start_date']));
            $end = date("m_dHi", strtotime($param['end_date']));
            $file_name = "{$name}{$start}-{$end}.csv";
            $response = new StreamedResponse(null, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ]);
            $results = $data['data']->select([
                'ips',
                DB::raw('sum(times)'),
                DB::raw("(select string_agg(DISTINCT username, ', ') from users left join user_login_log on users.id = user_login_log.user_id where user_login_log.ip = ips) as usernames")
            ])
                ->mergeBindings($sub_table->getQuery())
                ->groupBy('ips')
                ->get();
            $response->setCallback(function () use ($results) {
                $out = fopen('php://output', 'w');
                fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM

                $columns = [
                    'default' => [
                        'ips' => 'IP',
                        'sum' => '用此 IP 登入帐号数',
                        'usernames' => '账号',
                    ]
                ];
                $colum = $columns['default'];
                fputcsv($out, $colum);
                foreach ($results as $_row) {
                    $item = [];
                    foreach ($colum as $_k => $_v) {
                        $item[] = $_row->$_k;
                    }
                    fputcsv($out, $item);
                }
                fclose($out);
            });
            $response->send();
        }
    }

    public function getDetail(Request $request)
    {
        $param['start_date'] = $request->get('start_date', '');
        $param['end_date'] = $request->get('end_date', '');
        $param['ip'] = $request->get('ip', '');
        $where = function ($query) use ($param) {
            if (!empty($param['start_date'])) {
                $query->where('user_login_log.created_at', '>=', str_replace('_', ' ', $param['start_date']));
            }
            if (!empty($param['end_date'])) {
                $query->where('user_login_log.created_at', '<=', str_replace('_', ' ', $param['end_date']));
            }
            if (!empty($param['end_date'])) {
                $query->where('user_login_log.ip', '=', $param['ip']);
            }
        };
        $list = UserLoginLog::select(['users.id', 'users.username', 'users.last_time', 'user_login_log.user_id'])->leftjoin('users', 'users.id', 'user_login_log.user_id')->where($where)->distinct('users.id')->get();
        return view('user-login-ip.detail', [
            'list' => $list,
        ]);
    }
}
