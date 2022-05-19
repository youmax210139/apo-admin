<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserReportIndexRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Service\Models\ReportTeamTotalDaily;
use Service\Models\UserGroup;
use Service\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserreportController extends Controller
{
    public function getIndex()
    {
        $default_search_time = get_config('default_search_time', 0);
        return view(
            'user-report/index',
            [
                'user_group' => UserGroup::all(),
                'start_date' => \Carbon\Carbon::now()->hour >= $default_search_time ?
                    Carbon::today()->addHours($default_search_time) :
                    Carbon::yesterday()->addHours($default_search_time),
                'end_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::tomorrow()->addHours($default_search_time)->subSecond() :
                    Carbon::today()->addHours($default_search_time)->subSecond(),
                'agent_level' => get_config('reg_proxy_user_max', 3)
            ]
        );
    }

    public function postIndex(UserReportIndexRequest $request)
    {
        if ($request->ajax() || $request->get('export', 0)) {
            $data = array();
            $export = (int)$request->get('export', 0);
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');

            $where = '';

            $param = [];
            $param['start_time'] = $request->get('start_time');
            if (!empty($param['start_time'])) {
                $start_time = $param['start_time'];
            } else {
                $start_time = date('Y-m-d H:i:s', strtotime('-1 months'));
            }
            $param['end_time'] = $request->get('end_time');
            if (!empty($param['end_time'])) {
                $end_time = $param['end_time'];
            } else {
                $end_time = date('Y-m-d H:i:s');
            }

            $param['username'] = $request->get('username');
            $param['include_all'] = $request->get('include_all', 0);
            if (!empty($param['username'])) {
                if (!empty($param['include_all'])) {
                    $param['id'] = User::where('username', $param['username'])->value('id');
                    $where .= $param['id']
                        ? " AND (users.id = '{$param['id']}' or users.parent_tree @> '{$param['id']}') "
                        : " AND false ";
                } else {
                    $where .= " AND users.username ='{$param['username']}' ";
                }
            }

            // 充值
            $param['deposit_min'] = $request->get('deposit_min');
            if (!empty($param['deposit_min'])) {
                $where .= " AND report.total_deposit>='{$param['deposit_min']}' ";
            }
            $param['deposit_max'] = $request->get('deposit_max');
            if (!empty($param['deposit_max'])) {
                $where .= " AND report.total_deposit<='{$param['deposit_max']}' ";
            }

            // 日工资
            $param['wage_min'] = $request->get('wage_min');
            if (!empty($param['wage_min'])) {
                $where .= " AND report.total_wage>='{$param['wage_min']}' ";
            }
            $param['wage_max'] = $request->get('wage_max');
            if (!empty($param['wage_max'])) {
                $where .= " AND report.total_wage<='{$param['wage_max']}' ";
            }

            // 投注
            $param['bet_min'] = $request->get('bet_min');
            if (!empty($param['bet_min'])) {
                $where .= " AND report.total_price>='{$param['bet_min']}' ";
            }
            $param['bet_max'] = $request->get('bet_max');
            if (!empty($param['bet_max'])) {
                $where .= " AND report.total_price<='{$param['bet_max']}' ";
            }

            // 提现
            $param['withdrawal_min'] = $request->get('withdrawal_min');
            if (!empty($param['withdrawal_min'])) {
                $where .= " AND report.total_withdrawal>='{$param['withdrawal_min']}' ";
            }
            $param['withdrawal_max'] = $request->get('withdrawal_max');
            if (!empty($param['withdrawal_max'])) {
                $where .= " AND report.total_withdrawal<='{$param['withdrawal_max']}' ";
            }

            // 余额
            $param['balance_min'] = $request->get('balance_min');
            if (!empty($param['balance_min'])) {
                $where .= " AND user_fund.balance>='{$param['balance_min']}' ";
            }
            $param['balance_max'] = $request->get('balance_max');
            if (!empty($param['balance_max'])) {
                $where .= " AND user_fund.balance<='{$param['balance_max']}' ";
            }

            // 返点
            $param['rebate_min'] = $request->get('rebate_min');
            if (!empty($param['rebate_min'])) {
                $where .= " AND report.total_rebate>='{$param['rebate_min']}' ";
            }
            $param['rebate_max'] = $request->get('rebate_max');
            if (!empty($param['rebate_max'])) {
                $where .= " AND report.total_rebate<='{$param['rebate_max']}' ";
            }

            // 活动奖励
            $param['activity_min'] = $request->get('activity_min');
            if (!empty($param['activity_min'])) {
                $where .= " AND report.total_activity>='{$param['activity_min']}' ";
            }
            $param['activity_max'] = $request->get('activity_max');
            if (!empty($param['activity_max'])) {
                $where .= " AND report.total_activity<='{$param['activity_max']}' ";
            }

            $param['user_group_id'] = (int)$request->post('user_group_id');
            if (!empty($param['user_group_id'])) {
                $where .= " AND user_group.id='{$param['user_group_id']}' ";
            }
            $param['agent_level'] = $request->post('agent_level');
            if ($param['agent_level'] === 'root') {
                $where .= " AND  user_type_id = 1 ";
            } else {
                $param['agent_level'] = (int)$request->post('agent_level');
                if (!empty($param['agent_level'])) {
                    $where .= " AND  jsonb_array_length(parent_tree) = '{$param['agent_level']}' AND user_type_id != 3 ";
                }
            }

            if (!empty($where)) {
                $where = " WHERE true " . $where;
            }
            $columns = $request->get('columns');
            if ($columns) {
                switch ($columns[$order[0]['column']]['data']) {
                    case 'balance':
                        $sort_field = 'balance';
                        break;
                    case 'total_profit':
                        $sort_field = 'total_profit';
                        break;
                    case 'total_deposit':
                        $sort_field = 'total_deposit';
                        break;
                    case 'total_withdrawal':
                        $sort_field = 'total_withdrawal';
                        break;
                    case 'total_price':
                        $sort_field = 'total_price';
                        break;
                    case 'total_bonus':
                        $sort_field = 'total_bonus';
                        break;
                    case 'total_rebate':
                        $sort_field = 'total_rebate';
                        break;
                    case 'total_activity':
                        $sort_field = 'total_activity';
                        break;
                    case 'total_wage':
                        $sort_field = 'total_wage';
                        break;
                    default:
                        $sort_field = 'user_id';
                }
            } else {
                $sort_field = 'user_id';
            }

            $sort_type = ($order[0]['dir'] ?? 0) ?: 'desc';

            $show_zero = (int)$request->post('show_zero', 0);
            $join_type = 'left';
            if ($show_zero == 1) {
                $join_type = 'right';
            }

            DB::statement("
                CREATE TEMPORARY TABLE IF NOT EXISTS temp_report_total(
                    user_id integer NOT NULL UNIQUE,
                    total_price numeric(14,4) DEFAULT 0,
                    total_bonus numeric(14,4) DEFAULT 0,
                    total_rebate numeric(14,4) DEFAULT 0,
                    total_deposit numeric(14,4) DEFAULT 0,
                    total_deposit_fee numeric(14,4) DEFAULT 0,
                    total_withdrawal numeric(14,4) DEFAULT 0,
                    total_withdrawal_fee numeric(14,4) DEFAULT 0,
                    total_wage numeric(14,4) DEFAULT 0,
                    total_activity numeric(14,4) DEFAULT 0,
                    total_profit numeric(14,4) DEFAULT 0
                )
            ");

            if ($sort_field != 'balance') {
                DB::statement("
                    CREATE INDEX temp_report_total_{$sort_field}_index
                    ON temp_report_total USING btree
                    ({$sort_field});
                ");
            }

            $data['data'] = DB::statement("
                WITH lottery AS (
                    SELECT
                        user_id,
                        SUM(rlc.price) as price,
                        SUM(rlc.bonus) as bonus,
                        SUM(rlc.rebate) as rebate
                    FROM report_lottery_compressed as rlc
                    WHERE rlc.created_at BETWEEN '{$start_time}' AND '{$end_time}'
                    GROUP BY 1
                ),
                deposit AS (
                    SELECT
                        user_id,
                        SUM(rd.amount) as amount,
                        SUM(rd.platform_fee) as platform_fee
                    FROM report_deposit as rd
                    WHERE rd.created_at BETWEEN '{$start_time}' AND '{$end_time}'
                    GROUP BY 1
                ),
                withdrawal AS (
                    SELECT
                        user_id,
                        SUM(rw.amount) as amount,
                        SUM(rw.platform_fee) as platform_fee
                    FROM report_withdrawal as rw
                    WHERE rw.created_at BETWEEN '{$start_time}' AND '{$end_time}'
                    GROUP BY 1
                ),
                daily_wage AS (
                    SELECT
                        user_id,
                        SUM(rdw.amount) as amount
                    FROM report_daily_wage AS rdw
                    WHERE rdw.created_at BETWEEN '{$start_time}' AND '{$end_time}' AND rdw.amount > 0
                    GROUP BY 1
                ),
                activity AS (
                    SELECT
                        user_id,
                        SUM(ra.bonus) as bonus
                    FROM report_activity AS ra
                    WHERE ra.created_at BETWEEN '{$start_time}' AND '{$end_time}'
                    GROUP BY 1
                ),
                report AS (
                    SELECT
                        user_id,
                        lottery.price as total_price,
                        lottery.bonus as total_bonus,
                        lottery.rebate as total_rebate,
                        deposit.amount as total_deposit,
                        deposit.platform_fee as total_deposit_fee,
                        withdrawal.amount as total_withdrawal,
                        withdrawal.platform_fee as total_withdrawal_fee,
                        daily_wage.amount as total_wage,
                        activity.bonus as total_activity
                    FROM lottery
                    FULL JOIN deposit USING(user_id)
                    FULL JOIN withdrawal USING(user_id)
                    FULL JOIN daily_wage USING(user_id)
                    FULL JOIN activity USING(user_id)
                )
                INSERT INTO temp_report_total
                SELECT
                    users.id as user_id,
                    COALESCE(report.total_price,0) AS total_price,
                    COALESCE(report.total_bonus,0) AS total_bonus,
                    COALESCE(report.total_rebate,0) AS total_rebate,
                    COALESCE(report.total_deposit,0) AS total_deposit,
                    COALESCE(report.total_deposit_fee,0) AS total_deposit_fee,
                    COALESCE(report.total_withdrawal,0) AS total_withdrawal,
                    COALESCE(report.total_withdrawal_fee,0) AS total_withdrawal_fee,
                    COALESCE(report.total_wage,0) as total_wage,
                    COALESCE(report.total_activity,0) AS total_activity,
                    (
                        COALESCE(report.total_price,0)
                        -COALESCE(report.total_bonus,0)
                        -COALESCE(report.total_rebate,0)
                        -COALESCE(report.total_wage,0)
                        -COALESCE(report.total_activity,0)
                    ) as total_profit
                FROM report
                {$join_type} JOIN users ON(users.id = report.user_id)
                LEFT JOIN user_fund ON(user_fund.user_id = users.id)
                LEFT JOIN user_group ON users.user_group_id = user_group.id
                {$where}
            ");

            // 盈亏
            $report_total_where = '';
            $report_total_bind = [];
            $param['profit_min'] = $request->get('profit_min');
            if (!empty($param['profit_min'])) {
                $report_total_where .= ' AND temp_report_total.total_profit>=:profit_min ';
                $report_total_bind['profit_min'] = $param['profit_min'];
            }
            $param['profit_max'] = $request->get('profit_max');
            if (!empty($param['profit_max'])) {
                $report_total_where .= ' AND temp_report_total.total_profit<=:profit_max ';
                $report_total_bind['profit_max'] = $param['profit_max'];
            }

            if ($sort_field == 'balance') {
                $sort_field = 'user_fund.' . $sort_field;
            } else {
                $sort_field = 'temp_report_total.' . $sort_field;
            }
            if (empty($export)) {
                $data['data'] = DB::select("
                    SELECT
                        temp_report_total.*,
                        users.username,
                        user_fund.balance as balance,
                        user_profile.value as user_observe,
                        user_group.id as user_group_id,
                        user_group.name as user_group_name,
                        user_prize_level.level || '-' || round((user_rebates.value * 2000 + user_prize_level.level)) as prize_level
                    FROM temp_report_total
                    LEFT JOIN users ON(users.id = temp_report_total.user_id)
                    LEFT JOIN user_fund ON(user_fund.user_id = temp_report_total.user_id)
                    LEFT JOIN user_profile ON user_profile.user_id = users.id AND user_profile.attribute = 'user_observe'
                    LEFT JOIN user_prize_level ON users.top_id = user_prize_level.user_id
                    LEFT JOIN user_rebates ON user_rebates.user_id = users.id AND user_rebates.type = 'lottery'
                    LEFT JOIN user_group ON users.user_group_id = user_group.id
                    WHERE true {$report_total_where}
                    ORDER BY {$sort_field} {$sort_type}
                    LIMIT :limit OFFSET :offset
                ", array_merge($report_total_bind, [
                    'limit' => $length,
                    'offset' => $start,
                ]));

                $count = DB::select("
                    select count(1) from temp_report_total WHERE true {$report_total_where}
                ", $report_total_bind);

                $data['recordsTotal'] = $data['recordsFiltered'] = $count[0]->count;

                return response()->json($data);
            } else {
                if ($export == 1) {
                    $query = DB::table(DB::raw("
                        (SELECT
                            temp_report_total.*,
                            users.username,
                            user_fund.balance as balance,
                            user_profile.value as user_observe,
                            user_group.id as user_group_id,
                            user_group.name as user_group_name,
                            user_prize_level.level || '-' || round((user_rebates.value * 2000 + user_prize_level.level)) as prize_level
                        FROM temp_report_total
                        LEFT JOIN users ON(users.id = temp_report_total.user_id)
                        LEFT JOIN user_fund ON(user_fund.user_id = temp_report_total.user_id)
                        LEFT JOIN user_profile ON user_profile.user_id = users.id AND user_profile.attribute = 'user_observe'
                        LEFT JOIN user_prize_level ON users.top_id = user_prize_level.user_id
                        LEFT JOIN user_rebates ON user_rebates.user_id = users.id AND user_rebates.type = 'lottery'
                        LEFT JOIN user_group ON users.user_group_id = user_group.id
                        WHERE true {$report_total_where}) tab
                    "))->addBinding($report_total_bind);

                    $count = DB::select("
                        select count(1) from temp_report_total WHERE true {$report_total_where}
                    ", $report_total_bind);
                    $file_name = date('Ymd-H_i_s') . "-个人盈亏排行 .csv";
                }

                if ($count[0]->count >= 10000) {
                    return '数据超过 10000 条记录，无法导出！';
                }

                //导出数据
                $response = new StreamedResponse(null, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
                ]);

                $response->setCallback(function () use ($query, $export) {
                    $out = fopen('php://output', 'w');
                    fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                    $first = true;
                    $query->orderBy('user_id')->chunk(500, function ($results) use (&$first, $out, $export) {
                        if ($first) {
                            $columnNames[] = '用户名';
                            $columnNames[] = '用户组';
                            $columnNames[] = '奖金组';
                            $columnNames[] = '余额';
                            $columnNames[] = '最终盈亏';
                            $columnNames[] = '提存差';
                            $columnNames[] = '充值';
                            $columnNames[] = '提款';
                            $columnNames[] = '投注';
                            $columnNames[] = '派奖';
                            $columnNames[] = '返点';
                            if ($export != 1) {
                                $columnNames[] = '手续费(存)';
                                $columnNames[] = '手续费(取)';
                            } else {
                                $columnNames[] = '手续费(存/取)';
                            }
                            $columnNames[] = '活动奖励';
                            $columnNames[] = '日工资';
                            fputcsv($out, $columnNames);
                            $first = false;
                        }
                        $datas = [];
                        foreach ($results as $item) {
                            if ($export != 1) {
                                $datas[] = [
                                    $item->username,
                                    $item->user_group_name,
                                    $item->prize_level,
                                    $item->balance,
                                    $item->total_profit,
                                    $item->total_deposit - $item->total_withdrawal,
                                    $item->total_deposit,
                                    $item->total_withdrawal,
                                    $item->total_price,
                                    $item->total_bonus,
                                    $item->total_rebate,
                                    $item->total_deposit_fee,
                                    $item->total_withdrawal_fee,
                                    $item->total_activity,
                                    $item->total_wage,
                                ];
                            } else {
                                $datas[] = [
                                    $item->username,
                                    $item->user_group_name,
                                    $item->prize_level,
                                    $item->balance,
                                    $item->total_profit,
                                    $item->total_deposit - $item->total_withdrawal,
                                    $item->total_deposit,
                                    $item->total_withdrawal,
                                    $item->total_price,
                                    $item->total_bonus,
                                    $item->total_rebate,
                                    $item->total_deposit_fee . '/' . $item->total_withdrawal_fee,
                                    $item->total_activity,
                                    $item->total_wage,
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
}
