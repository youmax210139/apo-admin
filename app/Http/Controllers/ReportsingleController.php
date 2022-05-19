<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Service\Models\User;

class ReportsingleController extends Controller
{
    public function getIndex()
    {
        return view('report-single.index');
    }

    public function postIndex(Request $request)
    {
        $start = $request->get('start');
        $length = $request->get('length');
        $order = $request->get('order');
        $columns = $request->get('columns');

        $param['data_type'] = (int)$request->get('data_type');
        $param['search_type'] = (int)$request->get('search_type');
        $param['username'] = $request->get('username');
        $param['include_all'] = $request->get('include_all');
        $param['user_group_id'] = $request->get('user_group_id');
        $param['start_time'] = $request->get('start_time');
        $param['end_time'] = $request->get('end_time');
        $param['amount_min'] = $request->get('amount_min');
        $param['amount_max'] = $request->get('amount_max');

        if ($param['username']) {
            $param['user_id'] = User::where('username', $param['username'])->value('id');
        }

        if ($param['username'] && !$param['user_id']) {
            return [
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => 0,
                'sum' => 0,
            ];
        }

        $table = $this->getTableInfo($param);
        $table_name = $table->table_name;
        $query_field = $table->query_field;

        //查询条件
        $where = function ($query) use ($param, $table_name, $query_field) {
            if (!empty($param['user_id'])) {
                if ($param['include_all']) {
                    $query->where(function ($query) use ($param) {
                        $query->where('users.id', '=', $param['user_id'])
                            ->orWhere('users.parent_tree', '@>', $param['user_id']);
                    });
                } else {
                    $query->where($table_name . '.user_id', $param['user_id']);
                }
            }
            if (!empty($param['user_group_id'])) {
                $query->where('users.user_group_id', '=', $param['user_group_id']);
            }
            if (!empty($param['start_time'])) {
                $query->where($table_name . '.created_at', '>=', $param['start_time']);
            }
            if (!empty($param['end_time'])) {
                $query->where($table_name . '.created_at', '<=', $param['end_time']);
            }
            if (!empty($param['amount_min'])) {
                $query->where($table_name . '.' . $query_field, '>=', $param['amount_min']);
            }
            if (!empty($param['amount_max'])) {
                $query->where($table_name . '.' . $query_field, '<=', $param['amount_max']);
            }
        };
        $query = DB::table($table_name)
            ->leftJoin('users', 'users.id', $table_name . '.user_id')
            ->where($where);
        $data['recordsTotal'] = $data['recordsFiltered'] = $query->count();

        foreach ($table->sum_fields as $val) {
            $data[$val] = $query->sum($table_name . '.' . $val);
        }

        $data['data'] = $query->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
            ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
            ->skip($start)->take($length)
            ->orderBy($table_name . '.' . $columns[$order[0]['column']]['data'], $order[0]['dir'])
            ->get([
                $table_name . '.*', 'users.username', 'users.user_type_id', 'user_group.name as user_group_name',
                DB::raw("CASE WHEN users.user_type_id = 2 THEN concat(jsonb_array_length(users.parent_tree),'级',user_type.name) ELSE user_type.name END as user_type_name"),
            ]);

        return $data;
    }

    //获取表信息
    private function getTableInfo($param)
    {
        $obj = new \stdClass();

        $search_type = $param['search_type'];
        switch ($param['data_type']) {
            case 0:     //工资
                $obj->table_name = $search_type == 1 ? 'report_daily_wage_total' : 'report_daily_wage';
                $obj->query_field = 'amount';
                $obj->sum_fields = ['amount'];
                break;
            case 1:     //彩票[原始]
                $obj->table_name = $search_type == 1 ? 'report_lottery_total' : 'report_lottery';
                $obj->query_field = 'price';
                $obj->sum_fields = ['price', 'bonus', 'rebate'];
                break;
            case 2:     //彩票[压缩]
                $obj->table_name = $search_type == 1 ? 'report_lottery_total_compressed' : 'report_lottery_compressed';
                $obj->query_field = 'price';
                $obj->sum_fields = ['price', 'bonus', 'rebate'];
                break;
            case 3:     //活动
                $obj->table_name = $search_type == 1 ? 'report_activity_total' : 'report_activity';
                $obj->query_field = 'bonus';
                $obj->sum_fields = ['bonus'];
                break;
            case 4:     //充值
                $obj->table_name = $search_type == 1 ? 'report_deposit_total' : 'report_deposit';
                $obj->query_field = 'amount';
                $obj->sum_fields = ['amount', 'platform_fee'];
                break;
            case 5:     //提现
                $obj->table_name = $search_type == 1 ? 'report_withdrawal_total' : 'report_withdrawal';
                $obj->query_field = 'amount';
                $obj->sum_fields = ['amount', 'platform_fee'];
                break;
        }

        return $obj;
    }
}
