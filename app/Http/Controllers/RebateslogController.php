<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Service\Models\UserRebates;

class RebateslogController extends Controller
{
    public function getIndex()
    {
        $data['start_date'] = Carbon::now()->subDay();
        $data['end_date'] = Carbon::tomorrow();
        $data['rebates'] = UserRebates::getRebateConfig();
        return view('rebates-log.index', $data);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            $param = [];
            $param['username'] = (string)$request->get('username', ''); // 会员名
            $param['start_date'] = (string)$request->get('start_date'); // 开始时间
            $param['end_date'] = (string)$request->get('end_date');     // 结束时间
            $param['type'] = $request->get('type', '');             // 类型

            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $where = function ($query) use ($param) {
                if (!empty($param['username'])) {
                    $query->where("users.username", $param['username']);
                }
                if (!empty($param['type'])) {
                    $query->where("user_rebates_log.type", $param['type']);
                }
                if (!empty($param['start_date'])) {
                    $query->where("user_rebates_log.created_at", '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where("user_rebates_log.created_at", '<=', $param['end_date']);
                }
            };

            // 计算过滤后总数
            $count_query = \DB::table('user_rebates_log')
                ->leftJoin('users', 'users.id', 'user_rebates_log.user_id')
                ->where($where);
            $count_sql = vsprintf(str_replace(
                array('?'),
                array('\'\'%s\'\''),
                $count_query->select([DB::raw(1)])->toSql()
            ), $count_query->getBindings());
            $count = DB::selectOne("
                select count_estimate('{$count_sql}') as total
            ");

            if ($count->total > 20000) {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count->total;
            } else {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count_query->count();
            }

            $data['data'] = DB::table('user_rebates_log')
                ->leftJoin('users', 'users.id', 'user_rebates_log.user_id')
                ->leftJoin('users as u', 'u.id', 'user_rebates_log.operator_id')
                ->leftJoin('admin_users', 'admin_users.id', 'user_rebates_log.operator_id')
                ->where($where)
                ->skip($start)->take($length)
                ->orderBy('user_rebates_log.created_at', 'desc')
                ->get([
                    'user_rebates_log.*',
                    'users.username',
                    'u.username as operator',
                    'admin_users.username as operator1'
                ]);
            return response()->json($data);
        }
    }
}
