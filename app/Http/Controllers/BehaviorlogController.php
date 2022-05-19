<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\UserBehaviorLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BehaviorlogController extends Controller
{
    public function getIndex()
    {
        $data['start_date'] = Carbon::now()->subDay();
        $data['end_date'] = Carbon::now();

        return view('behavior-log.index', $data);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            $param = [];
            $param['username'] = (string)$request->get('username', ''); // 会员名
            $param['start_date'] = (string)$request->get('start_date');        // 开始时间
            $param['end_date'] = (string)$request->get('end_date');            // 结束时间
            $param['action'] = (string)$request->get('action', '');     // 行为
            $param['level'] = (int)$request->get('level', 0);           // 状态

            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $where = function ($query) use ($param) {
                if (!empty($param['username'])) {
                    $query->where('users.username', $param['username']);
                }
                if (!empty($param['action'])) {
                    $query->where('user_behavior_log.action', $param['action']);
                }
                if ($param['level'] > -1) {
                    $query->where('user_behavior_log.level', $param['level']);
                }

                if (!empty($param['start_date'])) {
                    $query->where('user_behavior_log.created_at', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where('user_behavior_log.created_at', '<', $param['end_date']);
                }
            };

            // 计算过滤后总数
            $count_query = UserBehaviorLog::leftJoin('users', 'users.id', 'user_behavior_log.user_id')
                ->where($where);
            $count_sql = vsprintf(str_replace(array('?'), array('\'\'%s\'\''), $count_query->select([DB::raw(1)])->toSql()), $count_query->getBindings());
            $count = DB::selectOne("
                select count_estimate('{$count_sql}') as total
            ");

            if ($count->total > 20000) {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count->total;
            } else {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count_query->count();
            }

            $data['data'] = UserBehaviorLog::leftJoin('users', 'users.id', 'user_behavior_log.user_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->leftJoin('user_profile', function ($join) {
                    $join->on('user_profile.user_id', '=', 'users.id')
                        ->where('user_profile.attribute', 'user_observe');
                })
                ->where($where)
                ->skip($start)->take($length)
                ->orderBy('user_behavior_log.created_at', 'desc')
                ->get([
                    'user_behavior_log.id',
                    'users.username',
                    'user_profile.value as user_observe',
                    'user_behavior_log.level',
                    'user_behavior_log.action',
                    DB::raw('
                        (CASE
                        WHEN
                            character_length(user_behavior_log.description) > 30
                        THEN
                            left(user_behavior_log.description, 30) || \'...\'
                        ELSE user_behavior_log.description
                        END) AS  description'),
                    'user_behavior_log.created_at',
                    'user_group.id as user_group_id',
                    'user_group.name as user_group_name'
                ]);

            return response()->json($data);
        }
    }

    public function getDetail(Request $request)
    {
        $id = (int)$request->get('id');

        $data = UserBehaviorLog::leftJoin('users', 'users.id', 'user_behavior_log.user_id')
            ->where('user_behavior_log.id', $id)
            ->first([
                'users.username',
                'user_behavior_log.level',
                'user_behavior_log.action',
                'user_behavior_log.description',
                'user_behavior_log.created_at'
            ]);

        return view('behavior-log.detail', $data);
    }
}
