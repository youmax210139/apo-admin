<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfitIndexRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Service\Models\User;
use Service\Models\UserGroup;

class UserprofitController extends Controller
{
    public function getIndex()
    {
        $user_group = UserGroup::all();
        $default_search_time = get_config('default_search_time', 0);
        return view(
            'user-profit/index',
            [
                'lottery_list' => \Service\API\Lottery::getAllLotteryGroupByCategory(),
                'user_group' => $user_group,
                'start_date' => \Carbon\Carbon::now()->hour >= $default_search_time ?
                    Carbon::today()->addHours($default_search_time) :
                    Carbon::yesterday()->addHours($default_search_time),
                'end_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::tomorrow()->addHours($default_search_time)->subSecond(1) :
                    Carbon::today()->addHours($default_search_time)->subSecond(1),
            ]
        );
    }

    public function postIndex(UserProfitIndexRequest $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');

            $start_time = $request->get('start_time');
            if (empty($start_time)) {
                $start_time = date('Y-m-d H:i:s', strtotime('-1 months'));
            }
            $end_time = $request->get('end_time');
            if (empty($end_time)) {
                $end_time = date('Y-m-d H:i:s');
            }

            $where = "";
            $whereBind = [];

            $profit_min = $request->get('profit_min');
            if (!empty($profit_min)) {
                $where .= " AND total_profit>=:profit_min ";
                $whereBind['profit_min'] = $profit_min;
            }
            $profit_max = $request->get('profit_max');
            if (!empty($profit_max)) {
                $where .= " AND total_profit<=:profit_max ";
                $whereBind['profit_max'] = $profit_max;
            }

            $deposit_min = $request->get('deposit_min');
            if (!empty($deposit_min)) {
                $where .= " AND total_deposit>=:deposit_min ";
                $whereBind['deposit_min'] = $deposit_min;
            }
            $deposit_max = $request->get('deposit_max');
            if (!empty($deposit_max)) {
                $where .= " AND total_deposit<=:deposit_max ";
                $whereBind['deposit_max'] = $deposit_max;
            }

            $rebate_min = $request->get('rebate_min');
            if (!empty($rebate_min)) {
                $where .= " AND total_rebate>=:rebate_min ";
                $whereBind['rebate_min'] = $rebate_min;
            }
            $rebate_max = $request->get('rebate_max');
            if (!empty($rebate_max)) {
                $where .= " AND total_rebate<=:rebate_max ";
                $whereBind['rebate_max'] = $rebate_max;
            }

            $data['data'] = [];

            $username = $request->get('username');

            $data['recordsTotal'] = $data['recordsFiltered'] = 0;
            if (!empty($username)) {
                $user = User::select(['id', 'username'])->where('username', $username)->first();
                if (!empty($user) && !empty($user->id)) {
                    DB::statement("
                        CREATE TEMP VIEW user_profit_total as(
                            SELECT
                                user_id,
                                created_at,
                                COALESCE(lottery.price,0) as total_price,
                                COALESCE(lottery.bonus,0) as total_bonus,
                                COALESCE(lottery.rebate,0) as total_rebate,
                                COALESCE(deposit.amount,0) as total_deposit,
                                COALESCE(deposit.platform_fee,0) as total_deposit_fee,
                                COALESCE(withdrawal.amount,0) as total_withdrawal,
                                COALESCE(withdrawal.platform_fee,0) as total_withdrawal_fee,
                                COALESCE(daily_wage.amount,0) as total_wage,
                                COALESCE(activity.bonus,0) as total_activity,
                                (
                                    COALESCE(lottery.price,0)
                                    -COALESCE(lottery.bonus,0)
                                    -COALESCE(lottery.rebate,0)
                                    -COALESCE(daily_wage.amount,0)
                                    -COALESCE(activity.bonus,0)
                                ) as total_profit
                            FROM (
                                SELECT
                                    user_id,
                                    cast(rlc.created_at as date) as created_at,
                                    SUM(rlc.price) as price,
                                    SUM(rlc.bonus) as bonus,
                                    SUM(rlc.rebate) as rebate
                                FROM report_lottery_compressed as rlc
                                WHERE rlc.created_at BETWEEN '{$start_time}' AND '{$end_time}'
                                AND rlc.user_id={$user->id}
                                GROUP BY 2,rlc.user_id
                            )as lottery
                            FULL JOIN (
                                SELECT
                                    user_id,
                                    cast(rd.created_at as date) as created_at,
                                    SUM(rd.amount) as amount,
                                    SUM(rd.platform_fee) as platform_fee
                                FROM report_deposit as rd
                                WHERE rd.created_at BETWEEN '{$start_time}' AND '{$end_time}'
                                AND rd.user_id={$user->id}
                                GROUP BY 2,user_id
                            ) as deposit USING(user_id,created_at)
                            FULL JOIN (
                                SELECT
                                    user_id,
                                    cast(rw.created_at as date) as created_at,
                                    SUM(rw.amount) as amount,
                                    SUM(rw.platform_fee) as platform_fee
                                FROM report_withdrawal as rw
                                WHERE rw.created_at BETWEEN '{$start_time}' AND '{$end_time}'
                                AND rw.user_id={$user->id}
                                GROUP BY 2,user_id
                            ) as withdrawal USING(user_id,created_at)
                            FULL JOIN (
                                SELECT
                                    user_id,
                                    cast(rdw.created_at as date) as created_at,
                                    SUM(rdw.amount) as amount
                                FROM report_daily_wage AS rdw
                                WHERE rdw.created_at BETWEEN '{$start_time}' AND '{$end_time}'
                                AND rdw.user_id={$user->id}
                                GROUP BY 2,user_id
                            ) as daily_wage USING(user_id,created_at)
                            FULL JOIN (
                                SELECT
                                    user_id,
                                    cast(ra.created_at as date) as created_at,
                                    SUM(ra.bonus) as bonus
                                FROM report_activity AS ra
                                WHERE ra.created_at BETWEEN '{$start_time}' AND '{$end_time}'
                                AND ra.user_id={$user->id}
                                GROUP BY 2,user_id
                            )as activity USING(user_id,created_at)
                        )
                    ");

                    $where2 = "";
                    if (!empty($where)) {
                        $where2 = 'WHERE true ' . $where;
                    }
                    $day_data = DB::select("
                        SELECT
                            *
                        FROM user_profit_total
                        {$where2}
                        ORDER BY created_at asc
                        LIMIT :limit OFFSET :offset
                    ", array_merge($whereBind, [
                        'limit' => $length,
                        'offset' => $start,
                    ]));

                    //显示零结算日期
                    $show_all = $request->get('show_all');
                    $new_day_data = [];
                    if ($show_all == 1) {
                        foreach ($day_data as $key => $_item) {
                            $next_day = date('Y-m-d', strtotime($_item->created_at . ' +1 days'));
                            $new_day_data[] = $_item;

                            // 如果当前日期+1天不等于下一天，则填补中间天数
                            if (!empty($day_data[$key + 1]) && $next_day != $day_data[$key + 1]->created_at) {
                                do {
                                    $new_day_data[] = [
                                        "created_at" => $next_day,
                                        "total_activity" => "0",
                                        "total_bonus" => "0",
                                        "total_deposit" => "0",
                                        "total_deposit_fee" => "0",
                                        "total_price" => "0",
                                        "total_profit" => "0",
                                        "total_rebate" => "0",
                                        "total_wage" => "0",
                                        "total_withdrawal" => "0",
                                        "total_withdrawal_fee" => "0",
                                        "user_id" => $_item->user_id,
                                        "username" => $user->username,
                                    ];
                                    $next_day = date('Y-m-d', strtotime($next_day . ' +1 days'));
                                } while ($next_day != $day_data[$key + 1]->created_at);
                            }
                        }
                    } else {
                        $new_day_data = $day_data;
                    }

                    $data['data'] = $new_day_data;

                    $count_data = DB::SELECT("
                        SELECT
                            users.id as user_id,
                            users.username,
                            COALESCE(SUM(total_price),0) as total_price,
                            COALESCE(SUM(total_bonus),0) as total_bonus,
                            COALESCE(SUM(total_rebate),0) as total_rebate,
                            COALESCE(SUM(total_deposit),0) as total_deposit,
                            COALESCE(SUM(total_deposit_fee),0) as total_deposit_fee,
                            COALESCE(SUM(total_withdrawal),0) as total_withdrawal,
                            COALESCE(SUM(total_withdrawal_fee),0) as total_withdrawal_fee,
                            COALESCE(SUM(total_wage),0) as total_wage,
                            COALESCE(SUM(total_activity),0) as total_activity,
                            COALESCE(SUM(total_profit),0) as total_profit
                        FROM users
                        LEFT JOIN user_profit_total ON (users.id=user_profit_total.user_id {$where})
                        WHERE users.id = :user_id
                        GROUP BY users.id
                    ", array_merge($whereBind, [
                        'user_id' => $user->id,
                    ]));

                    $data['total'] = [];
                    if (!empty($count_data)) {
                        $data['total'] = $count_data[0];
                    }

                    // 统计用户团队数据
                    $count = DB::select("
                        SELECT count(1) FROM user_profit_total
                    ");

                    $data['recordsTotal'] = $data['recordsFiltered'] = $count[0]->count;
                }
            }

            return response()->json($data);
        }
    }
}
