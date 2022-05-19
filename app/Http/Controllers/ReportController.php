<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\API\Report;
use Service\API\ThirdGame\ThirdGame;
use Service\Models\ActivityRecord;
use Service\Models\ReportLotteryCompressed;
use Service\Models\Deposit;
use Service\Models\UserBanks;
use Service\Models\UserDepositTotal;
use Service\Models\UserLoginLog;
use Service\Models\Withdrawal;
use Service\Models\User;
use Carbon\Carbon;
use Cache;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getIndex()
    {
        $default_search_time = get_config('default_search_time', 0);
        $data = [
            'start_time' => Carbon::now()->hour >= $default_search_time ?
                Carbon::today()->addHours($default_search_time) :
                Carbon::yesterday()->addHours($default_search_time),
            'end_time' => Carbon::now()->hour >= $default_search_time ?
                Carbon::tomorrow()->addHours($default_search_time)->subSecond(1) :
                Carbon::today()->addHours($default_search_time)->subSecond(1),
        ];
        $created_ats = [];
        for ($i = 9; $i >= 0; $i--) {
            $created_ats[] = date('Y-m-d', strtotime(Carbon::today()->subDays($i)));
        }
        $data['today_created_last10'] = Cache::remember(
            'Report::today_created_last10',
            30,  // 缓存 30 分钟
            function () use ($created_ats) {
                $data = User::where('user_group_id', 1)->where('created_at', '>', Carbon::today()->subDays(10))
                    ->groupBy(DB::raw("to_char(created_at,'YYYY-MM-DD')"))
                    ->orderBy('created_at')
                    ->get([
                        DB::raw("to_char(created_at,'YYYY-MM-DD') as created_at"),
                        DB::raw('count(1) as count')
                    ]);
                $data_arr = $data->toArray();
                $counts = [];
                $created_at = array_column($data_arr, 'created_at');
                $count = array_column($data_arr, 'count');
                foreach ($created_ats as $key => $val) {
                    $counts[$key] = 0;
                    foreach ($created_at as $k => $v) {
                        if (strtotime($val) == strtotime($v)) {
                            $counts[$key] = $count[$k];
                        }
                    }
                }
                return json_encode($counts);
            }
        );
        //登录人数
        $data['today_login_last10'] = Cache::remember(
            'UserLoginLog::today_login_last10',
            10,  // 缓存 10 分钟
            function () use ($created_ats) {
                $data = UserLoginLog::leftJoin('users', 'users.id', 'user_login_log.user_id')
                    ->where('user_login_log.created_at', '>', Carbon::today()->subDays(10))
                    ->where('users.user_group_id', 1)
                    ->groupBy(DB::raw(1))
                    ->orderBy('created_at')
                    ->get([
                        DB::raw("to_char(user_login_log.created_at,'YYYY-MM-DD') as created_at"),
                        DB::raw('count(distinct user_id) as count')
                    ]);
                $data_arr = $data->toArray();
                $counts = [];
                $created_at = array_column($data_arr, 'created_at');
                $count = array_column($data_arr, 'count');
                foreach ($created_ats as $key => $val) {
                    $counts[$key] = 0;
                    foreach ($created_at as $k => $v) {
                        if (strtotime($val) == strtotime($v)) {
                            $counts[$key] = $count[$k];
                        }
                    }
                }
                return json_encode($counts);
            }
        );
        //游戏人数
        $data['today_play_last10'] = Cache::remember(
            'Projects::today_play_last10',
            10,  // 缓存 10 分钟
            function () use ($created_ats) {
                $data = ReportLotteryCompressed::leftJoin('users', 'users.id', 'report_lottery_compressed.user_id')
                    ->where('report_lottery_compressed.created_at', '>', Carbon::today()->subDays(10))
                    ->where('price', '>', 0)
                    ->where('users.user_group_id', '=', '1')
                    ->groupBy(DB::raw(1))
                    ->orderBy('created_at')
                    ->get([
                        DB::raw("to_char(report_lottery_compressed.created_at,'YYYY-MM-DD') as created_at"),
                        DB::raw('count(distinct user_id) as count')
                    ]);
                $data_arr = $data->toArray();
                $counts = [];
                $created_at = array_column($data_arr, 'created_at');
                $count = array_column($data_arr, 'count');
                foreach ($created_ats as $key => $val) {
                    $counts[$key] = 0;
                    foreach ($created_at as $k => $v) {
                        if (strtotime($val) == strtotime($v)) {
                            $counts[$key] = $count[$k];
                        }
                    }
                }
                return json_encode($counts);
            }
        );
        $data['deposits_today_amount_last10_everyday'] = Cache::remember(
            'Report::deposits_today_amount_last10_everyday',
            11,  // 缓存 24 小时
            function () use ($created_ats) {
                $data = UserDepositTotal::where('created_at', '>', Carbon::today()->subDays(10))
                    ->groupBy(DB::raw("to_char(created_at,'YYYY-MM-DD')"))
                    ->orderBy('created_at')
                    ->get([
                        DB::raw("to_char(created_at,'YYYY-MM-DD') as created_at"),
                        DB::raw("count(distinct user_id) as count")
                    ]);
                $data_arr = $data->toArray();
                $counts = [];
                $created_at = array_column($data_arr, 'created_at');
                $count = array_column($data_arr, 'count');
                foreach ($created_ats as $key => $val) {
                    $counts[$key] = 0;
                    foreach ($created_at as $k => $v) {
                        if (strtotime($val) == strtotime($v)) {
                            $counts[$key] = $count[$k];
                        }
                    }
                }
                return json_encode($counts);
            }
        );

        $data['deposits_today_amount_last10'] = Cache::remember(
            'Report::deposits_today_amount_last10',
            12,  // 缓存 24 小时
            function () use ($created_ats) {
                $data = Deposit::leftJoin('users', 'users.id', 'deposits.user_id')
                    ->where('users.user_group_id', 1)
                    ->where('deposits.created_at', '>', Carbon::today()->subDays(10))
                    ->where('deposits.status', 2)
                    ->groupBy(DB::raw("1"))
                    ->orderBy('created_at')
                    ->get([
                        DB::raw("to_char(deposits.created_at,'YYYY-MM-DD') as created_at"),
                        DB::raw('ceil(sum(deposits.amount)) as amount')
                    ]);
                $data_arr = $data->toArray();
                $amounts = [];
                $created_at = array_column($data_arr, 'created_at');
                $amount = array_column($data_arr, 'amount');

                foreach ($created_ats as $key => $val) {
                    $amounts[$key] = 0;
                    foreach ($created_at as $k => $v) {
                        if (strtotime($val) == strtotime($v)) {
                            $amounts[$key] = $amount[$k];
                        }
                    }
                }
                return [
                    'keys' => json_encode($created_ats),
                    'data' => json_encode($amounts)
                ];
            }
        );

        $data['withdrawal_today_amount_last10'] = Cache::remember(
            'Report::withdrawal_today_amount_last10',
            13,  // 缓存 24 小时
            function () use ($created_ats) {
                $data = Withdrawal::leftJoin('users', 'users.id', 'withdrawals.user_id')
                    ->where('users.user_group_id', 1)
                    ->where('withdrawals.created_at', '>', Carbon::today()->subDays(10))
                    ->where('withdrawals.status', 1)
                    ->groupBy(DB::raw("1"))
                    ->orderBy('created_at')
                    ->get([
                        DB::raw("to_char(withdrawals.created_at,'YYYY-MM-DD') as created_at"),
                        DB::raw('ceil(sum(withdrawals.amount)) as amount')
                    ]);
                $data_arr = $data->toArray();
                $created_at = array_column($data_arr, 'created_at');
                $amount = array_column($data_arr, 'amount');
                foreach ($created_ats as $key => $val) {
                    $amounts[$key] = 0;
                    foreach ($created_at as $k => $v) {
                        if ($val == $v) {
                            $amounts[$key] = $amount[$k];
                        }
                    }
                }
                return json_encode($amounts);
            }
        );

        return view('report/index', $data);
    }

    public function postIndex(Request $request)
    {
        $param['username'] = $request->get('username', '');
        $param['start_time'] = $request->get('start_time', Carbon::today());
        $param['end_time'] = $request->get('end_time', Carbon::tomorrow()->subSecond(1));
        $hot_key = $request->get('hot_key', 1);
        if ($hot_key == 1) { //今天
            $param['start_time'] = Carbon::today();
            $param['end_time'] = Carbon::tomorrow()->subSecond(1);
        } elseif ($hot_key == 2) { //昨天
            $param['end_time'] = Carbon::today()->subSecond(1);
            $param['start_time'] = Carbon::yesterday();
        } elseif ($hot_key == 3) { //前天
            $param['end_time'] = Carbon::today()->subDays(1)->subSecond(1);
            $param['start_time'] = Carbon::today()->subDays(2);
        } elseif ($hot_key == 4) { //最近三天
            $param['end_time'] = Carbon::now();
            $param['start_time'] = Carbon::today()->subDays(2);
        } elseif ($hot_key == 5) { //最近7天
            $param['end_time'] = Carbon::now();
            $param['start_time'] = Carbon::today()->subDays(6);
        } elseif ($hot_key == 6) { //最近15天
            $param['end_time'] = Carbon::now();
            $param['start_time'] = Carbon::today()->subDays(14);
        }
        if (empty($param['start_time'])) {
            $param['start_time'] = !empty($param['end_time']) ? Carbon::parse($param['end_time'])->startOfDay() : Carbon::today();
        }
        if (empty($param['end_time'])) {
            $param['end_time'] = !empty($param['start_time']) ? Carbon::parse($param['start_time'])->endOfDay() : Carbon::tomorrow()->subSecond(1);
        }
        $parent_user_id = 0;
        if (!empty($param['username'])) {
            $parent_user_id = User::where('username', $param['username'])->value('id');
            if (!$parent_user_id) {
                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'msg' => '用户不存在',
                    'data' => []
                ]);
            }
        }
        //查询条件
        $where = function ($query) use ($parent_user_id) {
            $query->where('users.user_group_id', 1);
            if (!empty($parent_user_id)) {
                $query->where(function ($q) use ($parent_user_id) {
                    $q->where('users.parent_tree', '@>', $parent_user_id)->orWhere('users.id', $parent_user_id);
                });
            }
        };
        //总充值人次
        $data['total_deposit_count'] = Cache::remember(
            'Report::total_deposit_count' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                return Deposit::leftJoin('users', 'users.id', 'deposits.user_id')
                    ->whereBetween('deposits.created_at', [$param['start_time'], $param['end_time']])
                    ->where('deposits.status', 2)
                    ->where($where)->count();
            }
        );
        //总充值人数
        $data['total_deposit_times'] = Cache::remember(
            'Report::total_deposit_times' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                return Deposit::select(['deposits.user_id'])
                    ->leftJoin('users', 'users.id', 'deposits.user_id')
                    ->whereBetween('deposits.created_at', [$param['start_time'], $param['end_time']])
                    ->where('deposits.status', 2)
                    ->where($where)->distinct('deposits.user_id')->count('deposits.user_id');
            }
        );
        //首充人数
        $data['total_deposit_first'] = Cache::remember(
            'Report::total_deposit_first' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                return UserDepositTotal::leftJoin('users', 'users.id', 'user_deposit_total.user_id')
                    ->whereBetween('user_deposit_total.created_at', [$param['start_time'], $param['end_time']])
                    ->where($where)
                    ->count();
            }
        );
        //2充人数
        $data['total_deposit_second'] = Cache::remember(
            'Report::total_deposit_second' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                $sub_query = Deposit::leftJoin('users', 'users.id', 'deposits.user_id')
                    ->select('deposits.user_id')
                    ->where('deposits.created_at', '<=', $param['end_time'])
                    ->where($where)
                    ->groupBy('deposits.user_id')
                    ->havingRaw('COUNT(*) = 2');
                $query = DB::table(DB::raw("({$sub_query->toSql()}) as sub"));
                $query->mergeBindings($sub_query->getQuery());
                return $query->count();
            }
        );
        //3充以上人数
        $data['total_deposit_third'] = Cache::remember(
            'Report::total_deposit_third' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                $sub_query = Deposit::leftJoin('users', 'users.id', 'deposits.user_id')
                    ->select('deposits.user_id')
                    ->where('deposits.created_at', '<=', $param['end_time'])
                    ->where($where)
                    ->groupBy('deposits.user_id')
                    ->havingRaw('COUNT(*) > 2');
                $query = DB::table(DB::raw("({$sub_query->toSql()}) as sub"));
                $query->mergeBindings($sub_query->getQuery());
                return $query->count();
            }
        );
        //注册人数
        $data['total_user_reg'] = Cache::remember(
            'Report::total_user_reg' . md5(json_encode($param)),
            2,  // 缓存 2 分钟
            function () use ($where, $param) {
                return User::whereBetween('users.created_at', [$param['start_time'], $param['end_time']])
                    ->where($where)
                    ->count();
            }
        );
        //登录人数
        $data['total_users_login'] = Cache::remember(
            'Report::total_users_login' . md5(json_encode($param)),
            2,  // 缓存 2 分钟
            function () use ($where, $param) {
                return UserLoginLog::leftJoin('users', 'users.id', 'user_login_log.user_id')
                    ->whereBetween('user_login_log.created_at', [$param['start_time'], $param['end_time']])
                    ->where($where)
                    ->distinct('user_login_log.user_id')
                    ->count('user_login_log.user_id');
            }
        );
        //在线人数
        $data['total_users_online'] = Cache::remember(
            'Report::total_users_online' . md5(json_encode($param)),
            3,  // 缓存 3 分钟
            function () use ($where, $param) {
                return User::where('users.last_active', '>', Carbon::now()->subMinute(5))
                    ->where($where)
                    ->count();
            }
        );
        //有效人数
        $data['total_user_real'] = Cache::remember(
            'Report::total_user_real' . md5(json_encode($param)),
            2,  // 缓存 2 分钟
            function () use ($where, $param) {
                return User::leftJoin('user_profile', 'user_profile.user_id', 'users.id')
                    ->leftJoin('deposits', 'deposits.user_id', 'users.id')
                    ->whereBetween('users.created_at', [$param['start_time'], $param['end_time']])
                    ->where(function ($query) {
                        $query->where('user_profile.attribute', 'telephone')->orWhere('deposits.status', 2);
                    })
                    ->where($where)->distinct('users.id')
                    ->count('users.id');
            }
        );
        //游戏人数
        $data['total_users_play'] = Cache::remember(
            'Report::total_users_play' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                return ReportLotteryCompressed::leftJoin('users', 'users.id', 'report_lottery_compressed.user_id')
                    ->where('report_lottery_compressed.price', '>', 0)
                    ->whereBetween('report_lottery_compressed.created_at', [$param['start_time'], $param['end_time']])
                    ->where($where)
                    ->distinct('report_lottery_compressed.user_id')
                    ->count('report_lottery_compressed.user_id');
            }
        );
        //总在线充值金额
        $data['deposit_amount'] = Cache::remember(
            'Report::deposit_amount' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                $amount = Deposit::leftJoin('users', 'users.id', 'deposits.user_id')
                    ->whereBetween('deposits.created_at', [$param['start_time'], $param['end_time']])
                    ->where($where)
                    ->where('deposits.status', 2)->sum('amount');
                return number_format($amount, 2);
            }
        );
        //提现金额
        $data['withdrawal_amount'] = Cache::remember(
            'Report::withdrawal_amount' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                $amount = Withdrawal::leftJoin('users', 'users.id', 'withdrawals.user_id')
                    ->whereBetween('withdrawals.created_at', [$param['start_time'], $param['end_time']])
                    ->where($where)
                    ->where('withdrawals.status', 1)->sum('amount');
                return number_format($amount, 2);
            }
        );
        //活动金额
        $data['activity_amount'] = Cache::remember(
            'Report::activity_amount' . md5(json_encode($param)),
            3,  // 缓存 3 分钟
            function () use ($where, $param) {
                $amount = ActivityRecord::leftJoin('users', 'users.id', 'activity_record.user_id')
                    ->whereBetween('activity_record.record_time', [$param['start_time'], $param['end_time']])
                    ->where('activity_record.status', 1)->where($where)->sum('draw_money');
                return number_format($amount, 2);
            }
        );
        $data['lottery_projects_prices'] = Cache::remember(
            'Report::lottery_projects_prices' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                $amount = ReportLotteryCompressed::leftJoin('users', 'users.id', 'report_lottery_compressed.user_id')
                    ->where($where)
                    ->whereBetween('report_lottery_compressed.created_at', [$param['start_time'], $param['end_time']])
                    ->sum('price');
                return number_format($amount, 2);
            }
        );

        $data['lottery_projects_bonus'] = Cache::remember(
            'Report::lottery_projects_bonus' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                $amount = ReportLotteryCompressed::leftJoin('users', 'users.id', 'report_lottery_compressed.user_id')
                    ->where($where)
                    ->whereBetween('report_lottery_compressed.created_at', [$param['start_time'], $param['end_time']])
                    ->sum('bonus');
                return number_format($amount, 2);
            }
        );
        $data['lottery_projects_rebate'] = Cache::remember(
            'Report::lottery_projects_rebate' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                $amount = ReportLotteryCompressed::leftJoin('users', 'users.id', 'report_lottery_compressed.user_id')
                    ->where($where)
                    ->whereBetween('report_lottery_compressed.created_at', [$param['start_time'], $param['end_time']])
                    ->sum('rebate');
                return number_format($amount, 2);
            }
        );
        //彩票销量
        $data['lottery_sales'] = Cache::remember(
            'Report::lottery_sales' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                //总销量
                $data['legend']['data'] = [];
                $data['legend']['selected'] = [];
                $data['series'] = [];
                $data1['series'] = [];
                $sales = ReportLotteryCompressed::select(
                    [
                        'lottery_id',
                        'lottery.name as lottery_name',
                        DB::raw('SUM(price) as price'),
                        DB::raw('SUM(rebate) as rebate'),
                        DB::raw('SUM(bonus) as bonus'),
                    ]
                )
                    ->leftJoin('lottery', 'lottery.id', 'report_lottery_compressed.lottery_id')
                    ->leftJoin('users', 'users.id', 'report_lottery_compressed.user_id')
                    ->where($where)
                    ->whereBetween('report_lottery_compressed.created_at', [$param['start_time'], $param['end_time']])
                    ->groupBy(['lottery_id', 'lottery.name'])
                    ->orderBy('price', 'desc')
                    ->get();
                $i = 0;
                foreach ($sales as $item) {
                    $data['legend']['data'][] = $item->lottery_name;
                    $data['legend']['selected'][$item->lottery_name] = $i < 5;
                    $data['series'][] = ['name' => $item->lottery_name, 'value' => $item->price];
                    if ($i < 10) {
                        $data1['xAxis'][] = $item->lottery_name;
                        $data1['series']['total'][] = round($item->price - $item->bonus - $item->rebate, 3);
                        $data1['series']['prize'][] = round($item->price, 3);
                        $data1['series']['bonus'][] = round($item->bonus, 3);
                        $data1['series']['rebate'][] = round($item->rebate, 3);
                    }
                    $i++;
                }
                return ['chart1' => $data, 'chart2' => $data1];
            }
        );
        //第三方销量
        $data['thirdgame_sales'] = Cache::remember(
            'Report::thirdgame_sales' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param, $parent_user_id) {
                //总销量
                $data['legend']['data'] = [];
                $data['legend']['selected'] = [];
                $data['series'] = [];
                $third_games = ThirdGame::getPlatform($ident = '', $return = 'array', 0);
                $sales = Report::getGameTotalBet(
                    $param['start_time'],
                    $param['end_time'],
                    array_keys($third_games),
                    $parent_user_id,
                    true
                );
                $data['total_bet'] = $sales['total'];
                $i = 0;
                $items = collect($sales['item']);
                $items = $items->sortByDesc('total_bets')->toArray();
                foreach ($items as $item) {
                    if ($item['total_bets'] <= 0) {
                        continue;
                    }
                    $data['legend']['data'][] = $third_games[$item['platform']]['name'];
                    $data['legend']['selected'][$third_games[$item['platform']]['name']] = $i < 5;
                    $data['series'][] = [
                        'name' => $third_games[$item['platform']]['name'],
                        'value' => $item['total_bets']
                    ];
                    $i++;
                }
                return $data;
            }
        );
        //提现人数
        $data['withdrawals_user'] = Cache::remember(
            'Report::withdrawals_user' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                return Withdrawal::select(['withdrawals.user_id'])
                    ->leftJoin('users', 'users.id', 'withdrawals.user_id')
                    ->whereBetween('withdrawals.created_at', [$param['start_time'], $param['end_time']])
                    ->where('withdrawals.status', 1)
                    ->where('withdrawals.amount', '>=', 1)
                    ->where($where)->distinct('withdrawals.user_id')->count('withdrawals.user_id');
            }
        );
        //首次绑卡人数
        $data['first_user_bind_bank'] = Cache::remember(
            'Report::first_user_bind_bank' . md5(json_encode($param)),
            1,  // 缓存 1 分钟
            function () use ($where, $param) {
                $data = UserBanks::select(['user_banks.user_id'])
                    ->leftJoin('users', 'users.id', 'user_banks.user_id')
                    ->whereBetween('user_banks.created_at', [$param['start_time'], $param['end_time']])
                    ->where('user_banks.status', 1)
                    ->whereIn('user_banks.user_id', function ($query) {
                        $query->select('user_id')
                            ->from('user_banks')
                            ->where('status', 1)
                            ->groupBy('user_id')
                            ->havingRaw('count(*) = 1')
                            ->get();
                    })
                    ->where($where)
                    ->distinct('user_banks.user_id')
                    ->count('user_banks.user_id');
                return $data;
            }
        );

        return response()->json([
            'status' => 0,
            'code' => 200,
            'msg' => '',
            'data' => $data
        ]);
    }
}
