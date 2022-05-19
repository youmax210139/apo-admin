<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LotterybonusrealtimeIndexRequest;
use Service\Models\UserGroup;
use Service\Models\User;
use Service\API\User as APIUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LotterybonusrealtimeController extends Controller
{
    public function getIndex(Request $request)
    {
        $user_group = UserGroup::all();
        $default_search_time = get_config('default_search_time', 0);
        $report_lottery_bonus_team_enable = get_config('report_lottery_bonus_team_enable', 0);
        return view(
            'lottery-bonus/index',
            [
                'dividend_to_report' => get_config('dividend_to_report', 0),
                'dividend_last_amount_to_report' => get_config('dividend_last_amount_to_report', 0),
                'lottery_list' => \Service\API\Lottery::getAllLotteryGroupByCategory(),
                'user_group' => $user_group,
                'id' => (int)$request->get('id', 0),
                'start_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::today()->addHours($default_search_time) :
                    Carbon::yesterday()->addHours($default_search_time),
                'end_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::tomorrow()->addHours($default_search_time)->subSecond(1) :
                    Carbon::today()->addHours($default_search_time)->subSecond(1),
                'report_lottery_bonus_team_enable' => $report_lottery_bonus_team_enable,
            ]
        );
    }

    public function postIndex(LotterybonusrealtimeIndexRequest $request)
    {
        if ($request->ajax()) {
            //获取外部数据
            $param['id'] = (int)$request->post('id', 0);
            $param['username'] = (string)$request->post('username');
            $param['start_date'] = $request->post('start_date', Carbon::today());
            $param['end_date'] = $request->post('end_date', Carbon::today()->endOfDay());
            $param['lottery_id'] = (int)$request->post('lottery_id');
            $param['method_id'] = (int)$request->post('method_id');
            $param['user_group_id'] = (int)$request->post('user_group_id');
            $param['is_search'] = (int)$request->post('is_search');
            $param['order'] = (int)$request->post('order', 0);
            $param['show_zero'] = (int)$request->post('show_zero', 0);
            $param['team_type'] = (int)$request->post('team_type', 0);
            //获取代理数
            if ($param['id'] > 0 && empty($param['is_search'])) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByParentID($param['id'], false);
            }

            //根据用户名获取用户ID
            if ($param['is_search'] && !empty($param['username'])) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByUserName($param['username'], false);

                $param['id'] = 0;
                $user = User::where('username', $param['username'])->first(['id']);

                if (!empty($user)) {
                    $param['id'] = $user->id;
                }
            }

            //排序
            $order = 'total_price desc';
            if ($param['order'] == 1) {
                $order = 'total_profit desc';
            } else if ($param['order'] == 2) {
                $order = 'total_profit asc';
            }

            //显示零结算用户
            $join_type = 'left';
            $where_bind_id = [];
            $where_id = '';
            if ($param['show_zero'] == 1) {
                $join_type = 'right';

                if ($param['id']) {
                    $where_id = 'where users.parent_id = :parent_id or users.id = :user_id';
                    $where_bind_id['parent_id'] = $param['id'];
                    $where_bind_id['user_id'] = $param['id'];
                } else {
                    $where_id = 'where users.parent_id = :parent_id';
                    $where_bind_id['parent_id'] = 0;
                }
            }

            if ($param['id']) {
                $user = User::find($param['id']);

                $level = 0;
                if (!empty($user)) {
                    $level = count(json_decode($user->parent_tree, true)) + 1;
                }

                $where_rl = '';//主表 report_lottery_compressed
                $where_ra = '';//主表 report_activity
                $where_rd = '';//主表 report_deposit
                $where_rw = '';//主表 report_withdrawal
                $where_rdw = '';//主表 report_daily_wage
                $where_o = '';

                $where_bind_rl = [
                    'user_id_rl' => $param['id'],
                    'id_rl' => $param['id']
                ];
                $where_bind_ra = [
                    'user_id_ra' => $param['id'],
                    'id_ra' => $param['id']
                ];
                $where_bind_rd = [
                    'user_id_rd' => $param['id'],
                    'id_rd' => $param['id']
                ];
                $where_bind_rw = [
                    'user_id_rw' => $param['id'],
                    'id_rw' => $param['id']
                ];

                $where_bind_rdw = [
                    'user_id_rdw' => $param['id'],
                    'id_rdw' => $param['id']
                ];
                $where_bind_o = [
                    'user_id_o' => $param['id'],
                    'id_o' => $param['id'],
                ];
                //开始时间
                if (!empty($param['start_date'])) {
                    //彩票
                    $where_rl .= ' and rl.created_at >= :start_date_rl';
                    $where_bind_rl['start_date_rl'] = $param['start_date'];
                    //活动
                    $where_ra .= ' and ra.created_at >= :start_date_ra';
                    $where_bind_ra['start_date_ra'] = $param['start_date'];
                    //充值
                    $where_rd .= ' and rd.created_at >= :start_date_rd';
                    $where_bind_rd['start_date_rd'] = $param['start_date'];
                    //提现
                    $where_rw .= ' and rw.created_at >= :start_date_rw';
                    $where_bind_rw['start_date_rw'] = $param['start_date'];
                    //工资
                    $where_rdw .= ' and rdw.created_at >= :start_date_rdw';
                    if (get_config('calculate_today_wage_to_tomorrow', 0) == 1) {
                        $where_bind_rdw['start_date_rdw'] = (new Carbon($param['start_date']))->subDay();
                    } else {
                        $where_bind_rdw['start_date_rdw'] = $param['start_date'];
                    }
                    //帐变
                    $where_o .= ' and orders.created_at >= :start_date_o';
                    $where_bind_o['start_date_o'] = $param['start_date'];
                }
                //结束时间
                if (!empty($param['end_date'])) {
                    //彩票
                    $where_rl .= ' and rl.created_at <= :end_date_rl';
                    $where_bind_rl['end_date_rl'] = $param['end_date'];
                    //活动
                    $where_ra .= ' and ra.created_at <= :end_date_ra';
                    $where_bind_ra['end_date_ra'] = $param['end_date'];
                    //充值
                    $where_rd .= ' and rd.created_at <= :end_date_rd';
                    $where_bind_rd['end_date_rd'] = $param['end_date'];
                    //提现
                    $where_rw .= ' and rw.created_at <= :end_date_rw';
                    $where_bind_rw['end_date_rw'] = $param['end_date'];
                    //工资
                    $where_rdw .= ' and rdw.created_at <= :end_date_rdw';
                    if (get_config('calculate_today_wage_to_tomorrow', 0) == 1) {
                        $where_bind_rdw['end_date_rdw'] = (new Carbon($param['end_date']))->subDay();
                    } else {
                        $where_bind_rdw['end_date_rdw'] = $param['end_date'];
                    }
                    //帐变
                    $where_o .= ' and orders.created_at <= :end_date_o';
                    $where_bind_o['end_date_o'] = $param['end_date'];
                }
                //默认显示团队用户，为1时显示直属下级用户
                $where_user_parent_tree = ' users.parent_tree @> ';
                if ($param['team_type'] == 1) {
                    $where_user_parent_tree = ' users.parent_id = ';
                }

                $where_bind_cd = [];//主表 contract_dividends
                $dividend_report_sql = '';
                $dividend_full_join = '';
                $dividend_full_join_field = '';
                $dividend_coalesce_field = '';
                $dividend_to_report = get_config('dividend_to_report', 0);
                $where_bind_last_cd = [];
                $where_bind_last_o = [];
                $dividend_last_report_sql = '';
                $dividend_last_full_join = '';
                $dividend_last_full_join_field = '';
                $dividend_last_coalesce_field = '';
                $dividend_last_amount_to_report = get_config('dividend_last_amount_to_report', 0);//盈亏报表是否计算前期分红	，默认0， 0不显示，1显示
                if ($dividend_to_report) {
                    $where_bind_cd = [
                        'user_id_cd' => $param['id'],
                        'id_cd' => $param['id']
                    ];
                    $where_cd = " and status = 1 and send_type = 1";
                    if (!empty($param['start_date'])) {
                        $where_cd .= ' and start_time >= :start_date_cd';
                        $where_bind_cd['start_date_cd'] = $param['start_date'];
                    }
                    if (!empty($param['end_date'])) {
                        $where_cd .= ' and end_time <= :end_date_cd';
                        $where_bind_cd['end_date_cd'] = $param['end_date'];
                    }
                    if (!empty($param['user_group_id'])) {
                        $where_cd .= ' and users.user_group_id = :user_group_id_cd';
                        $where_bind_cd['user_group_id_cd'] = $param['user_group_id'];
                    }
                    //分红查询SQL
                    $dividend_report_sql = "
                        , report_contract_dividend AS (
                            select
                                cd.user_id,
                                SUM(cd.amount) as amount
                            from contract_dividends as cd
                            left join users on users.id = cd.user_id
                            where ( {$where_user_parent_tree} :user_id_cd
                            or users.id = :id_cd)
                            {$where_cd}
                            group by cd.user_id
                        )
                        , report_contract_dividend_total AS (
                            select
                                COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                                sum(amount) AS total_dividend
                            from report_contract_dividend as rcd
                            left join users on rcd.user_id = users.id
                            group by 1
                        )
                    ";

                    $dividend_full_join = "natural full join report_contract_dividend_total";
                    $dividend_full_join_field = ",total_dividend";
                    //$dividend_coalesce_field = "COALESCE(report.total_dividend, 0) as total_dividend,";
                    $dividend_coalesce_field = "COALESCE(report.total_dividend, 0) +  "
                        . " COALESCE(report.total_fhff, 0) + "
                        . " COALESCE(report.total_cpfs, 0) + "
                        . " COALESCE(report.total_xtyjff, 0)  as total_dividend,";
                }
                if ($dividend_last_amount_to_report) {
                    $where_bind_last_cd = [
                        'user_id_cd' => $param['id'],
                        'id_cd' => $param['id']
                    ];
                    $where_last_cd = " and status = 1 and send_type = 1";
                    $where_bind_last_o = [
                        'user_id_last_o' => $param['id'],
                        'id_last_o' => $param['id']
                    ];
                    $where_last_o = '';
                    if (!empty($param['start_date'])) {
                        if (date('d', strtotime($param['start_date'])) !== '01') {
                            $where_last_cd .= ' and start_time >= :start_date_last_cd';
                            $where_bind_last_cd['start_date_last_cd'] = (new Carbon($param['start_date']))->subDay();

                            $where_last_o .= ' and orders.created_at >= :start_date_last_o';
                            $where_bind_last_o['start_date_last_o'] = (new Carbon($param['start_date']))->subDay();
                        } else {
                            $where_last_cd .= ' and (start_time >= :start_date_last_cd or start_time = :start_date_last_month_cd)';
                            $where_bind_last_cd['start_date_last_cd'] = (new Carbon($param['start_date']))->subDay();
                            $where_bind_last_cd['start_date_last_month_cd'] = $this->dealLastMonthDividend($param['start_date'], $level);
                            $where_last_o .= ' and orders.created_at >= :start_date_last_o';
                            $where_bind_last_cd['start_date_last_o'] = (new Carbon($param['start_date']))->subDay();
                        }
                    }
                    if (!empty($param['end_date'])) {
                        $where_last_cd .= ' and end_time <= :end_date_last_cd';
                        $where_bind_last_cd['end_date_last_cd'] = (new Carbon($param['end_date']))->subDay();
                        $where_last_o .= ' and orders.created_at <= :end_date_last_o';
                        $where_bind_last_o['end_date_last_o'] = (new Carbon($param['end_date']))->subDay();
                    }
                    if (!empty($param['user_group_id'])) {
                        $where_last_cd .= ' and users.user_group_id = :user_group_id_cd';
                        $where_bind_last_cd['user_group_id_cd'] = $param['user_group_id'];
                        $where_last_o .= ' and users.user_group_id = :user_group_id_last_o';
                        $where_bind_last_o['user_group_id_last_o'] = $param['user_group_id'];
                    }
                    $dividend_last_report_sql = "
                        , report_contract_last_dividend AS (
                            select
                                last_cd.user_id,
                                SUM(last_cd.amount) as last_amount
                            from contract_dividends as last_cd
                            left join users on users.id = last_cd.user_id
                            where ( {$where_user_parent_tree} :user_id_cd
                            or users.id = :id_cd)
                            {$where_last_cd}
                            group by last_cd.user_id
                        )
                        , report_contract_last_dividend_total AS (
                            select
                                COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                                sum(last_amount) AS last_total_dividend
                            from report_contract_last_dividend as last_rcd
                            left join users on last_rcd.user_id = users.id
                            group by 1
                        ), report_last_order AS (
                            select
                                    orders.from_user_id,
                                    SUM((CASE WHEN order_type.ident='FHFF' THEN orders.amount ELSE 0 END)) as last_total_fhff,
                                    SUM((CASE WHEN order_type.ident='XTYJFF' THEN orders.amount ELSE 0 END)) as last_total_xtyjff,
                                    SUM((CASE WHEN order_type.ident='XTJYKK' THEN orders.amount ELSE 0 END)) as last_total_xtjykk,
                                    SUM((CASE WHEN order_type.ident='CPFS' THEN orders.amount ELSE 0 END)) as last_total_cpfs,
                                    SUM((CASE WHEN order_type.ident='CPCS' THEN orders.amount ELSE 0 END)) as last_total_cpcs
                                from orders
	                            LEFT JOIN order_type ON order_type.id = orders.order_type_id
                                LEFT JOIN users ON users.id = orders.from_user_id
                                WHERE order_type.ident IN ('FHFF','XTYJFF','XTJYKK','CPFS','CPCS')
                                AND ({$where_user_parent_tree} :user_id_last_o
                                or users.id = :id_last_o)
                                {$where_last_o}
                                group by orders.from_user_id
                        )
                        , report_last_order_total AS (
                            select
                                COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                                sum(last_total_fhff) AS last_total_fhff,
                                sum(last_total_xtyjff) AS last_total_xtyjff,
                                sum(last_total_xtjykk) AS last_total_xtjykk,
                                sum(last_total_cpfs) AS last_total_cpfs,
                                sum(last_total_cpcs) AS last_total_cpcs
                            from report_last_order as last_ro
                            left join users on last_ro.from_user_id = users.id
                            group by 1
                        )
                    ";
                    $dividend_last_full_join = "natural full join report_contract_last_dividend_total "
                        . "natural full join report_last_order_total";
                    $dividend_last_full_join_field = ",last_total_dividend,last_total_fhff,last_total_xtyjff,last_total_xtjykk,last_total_cpfs,last_total_cpcs ";
                    $dividend_last_coalesce_field = "COALESCE(report.last_total_xtjykk, 0) + COALESCE(report.last_total_cpcs, 0)   as last_total_xtjykk,"
                        . "COALESCE(report.last_total_dividend, 0) +  "
                        . " COALESCE(report.last_total_fhff, 0) + "
                        . " COALESCE(report.last_total_cpfs, 0) + "
                        . " COALESCE(report.last_total_xtyjff, 0)  as last_total_dividend,";
                }

                if (!empty($param['lottery_id'])) {
                    $where_rl .= ' and rl.lottery_id = :lottery_id_rl';
                    $where_bind_rl['lottery_id_rl'] = $param['lottery_id'];
                }
                $_data = DB::select("
                    WITH report_lottery AS (
                        select
                        rl.user_id,
                        SUM(rl.price) as total_price,
                        SUM(rl.bonus) as total_bonus,
                        SUM(rl.rebate) as total_rebate
                        from report_lottery_compressed as rl
                        left join users on users.id = rl.user_id
                        where ({$where_user_parent_tree} :user_id_rl or users.id = :id_rl)
                        {$where_rl}
                        group by rl.user_id
                    ), report_lottery_total AS (
                        select
                            COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                            SUM(report_lottery.total_price) as total_price,
                            SUM(report_lottery.total_bonus) as total_bonus,
                            SUM(report_lottery.total_rebate) as total_rebate
                        from report_lottery
                        left join users ON user_id = users.id
                        group by 1
                    ), report_activity AS (
                        select
                            ra.user_id,
                            SUM(ra.bonus) as total_activity
                        from report_activity as ra
                        left join users on users.id = ra.user_id
                        where ({$where_user_parent_tree} :user_id_ra
                        or users.id = :id_ra)
                        {$where_ra}
                        group by ra.user_id
                    ), report_activity_total AS (
                        select
                            COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                            SUM(report_activity.total_activity) as total_activity
                        from report_activity
                        left join users ON user_id = users.id
                        group by 1
                    ), report_deposit AS (
                        select
                            rd.user_id,
                            SUM(rd.amount) as total_deposit,
                            SUM(rd.platform_fee) as total_deposit_fee
                        from report_deposit as rd
                        left join users on users.id = rd.user_id
                        where ({$where_user_parent_tree} :user_id_rd
                        or users.id = :id_rd)
                        {$where_rd}
                        group by rd.user_id
                    ), report_deposit_total AS (
                         select
                            COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                            SUM(report_deposit.total_deposit) as total_deposit,
                            SUM(report_deposit.total_deposit_fee) as total_deposit_fee
                         from report_deposit
                         left join users ON user_id = users.id
                         group by 1
                    ), report_withdrawal AS (
                        select
                            rw.user_id,
                            SUM(rw.amount) as total_withdrawal,
                            SUM(rw.platform_fee) as total_withdrawal_fee
                        from report_withdrawal as rw
                        left join users on users.id = rw.user_id
                        where ({$where_user_parent_tree} :user_id_rw
                        or users.id = :id_rw)
                        {$where_rw}
                        group by rw.user_id
                    ), report_withdrawal_total AS (
                         select
                            COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                            SUM(report_withdrawal.total_withdrawal) as total_withdrawal,
                            SUM(report_withdrawal.total_withdrawal_fee) as total_withdrawal_fee
                         from report_withdrawal
                         left join users ON user_id = users.id
                         group by 1
                    ), report_daily_wage AS (
                        select
                            rdw.user_id,
                            SUM(rdw.amount) as total_wage
                        from report_daily_wage as rdw
                        left join users on users.id = rdw.user_id
                        where ({$where_user_parent_tree} :user_id_rdw
                        or users.id = :id_rdw)
                        {$where_rdw}
                        group by rdw.user_id
                    ), report_daily_wage_total AS (
                        select
                            COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                            SUM(report_daily_wage.total_wage) as total_wage
                        from report_daily_wage
                        left join users ON user_id = users.id
                        group by 1
                    ), report_order AS (
                            select
                                    orders.from_user_id,
                                    SUM((CASE WHEN order_type.ident='FHFF' THEN orders.amount ELSE 0 END)) as total_fhff,
                                    SUM((CASE WHEN order_type.ident='XTYJFF' THEN orders.amount ELSE 0 END)) as total_xtyjff,
                                    SUM((CASE WHEN order_type.ident='XTSFFF' THEN orders.amount ELSE 0 END)) as total_xtsfff,
                                    SUM((CASE WHEN order_type.ident='XTJYKK' THEN orders.amount ELSE 0 END)) as total_xtjykk,
                                    SUM((CASE WHEN order_type.ident='CPFS' THEN orders.amount ELSE 0 END)) as total_cpfs,
                                    SUM((CASE WHEN order_type.ident='CPCS' THEN orders.amount ELSE 0 END)) as total_cpcs
                                from orders
	                            LEFT JOIN order_type ON order_type.id = orders.order_type_id
                                LEFT JOIN users ON users.id = orders.from_user_id
                                WHERE order_type.ident IN ('FHFF','XTYJFF','XTSFFF','XTJYKK','CPFS','CPCS')
                                AND ({$where_user_parent_tree} :user_id_o
                                or users.id = :id_o)
                                {$where_o}
                                group by orders.from_user_id
                        )
                        , report_order_total AS (
                            select
                                COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                                sum(total_fhff) AS total_fhff,
                                sum(total_xtyjff) AS total_xtyjff,
                                sum(total_xtsfff) AS total_xtsfff,
                                sum(total_xtjykk) AS total_xtjykk,
                                sum(total_cpfs) AS total_cpfs,
                                sum(total_cpcs) AS total_cpcs
                            from report_order as last_ro
                            left join users on last_ro.from_user_id = users.id
                            group by 1
                        )
                    {$dividend_report_sql}
                    {$dividend_last_report_sql}
                    select
                      users.id as user_id,
                      users.username,
                      user_group.id as user_group_id,
                      user_group.name as user_group_name,
                      COALESCE(report.total_price, 0) as total_price,
                      COALESCE(report.total_rebate, 0) as total_rebate,
                      COALESCE(report.total_price, 0) - COALESCE(report.total_rebate, 0) as total_real_price,
                      COALESCE(report.total_bonus, 0) as total_bonus,
                      COALESCE(report.total_activity, 0) as total_activity,
                      COALESCE(report.total_deposit, 0) as total_deposit,
                      COALESCE(report.total_deposit_fee, 0) as total_deposit_fee,
                      COALESCE(report.total_withdrawal, 0) as total_withdrawal,
                      COALESCE(report.total_withdrawal_fee, 0) as total_withdrawal_fee,
                      COALESCE(report.total_wage, 0) + COALESCE(report.total_xtsfff, 0) as total_wage,
                      COALESCE(report.total_fhff, 0) as total_fhff,
                      COALESCE(report.total_cpfs, 0) as total_cpfs,
                      COALESCE(report.total_cpcs, 0) as total_cpcs,
                      COALESCE(report.total_xtyjff, 0) as total_xtyjff,
                      COALESCE(report.total_xtsfff, 0) as total_xtsfff,
                      COALESCE(report.total_xtjykk, 0) + COALESCE(report.total_cpcs, 0) as total_xtjykk,
                      {$dividend_coalesce_field}
                      {$dividend_last_coalesce_field}
                      COALESCE(report.total_price, 0) - COALESCE(report.total_bonus, 0)
                        - COALESCE(report.total_rebate, 0) - COALESCE(report.total_wage, 0)
                        - COALESCE(report.total_activity, 0) - COALESCE(report.total_xtsfff, 0) as total_profit
                      from (
                        select
                            user_id,
                            total_price,
                            total_bonus,
                            total_rebate,
                            total_activity,
                            total_deposit,
                            total_deposit_fee,
                            total_withdrawal,
                            total_withdrawal_fee,
                            total_wage,
                            total_fhff,
                            total_cpfs,
                            total_cpcs,
                            total_xtyjff,
                            total_xtsfff,
                            total_xtjykk
                            {$dividend_full_join_field}
                            {$dividend_last_full_join_field}
                        from report_lottery_total
                        natural full join report_activity_total
                        natural full join report_deposit_total
                        natural full join report_withdrawal_total
                        natural full join report_daily_wage_total
                        natural full join report_order_total
                        {$dividend_full_join}
                        {$dividend_last_full_join}
                    ) as report
                    {$join_type} join users on users.id = report.user_id
                    left join user_group on users.user_group_id = user_group.id
                    {$where_id}
                    order by {$order}
                ", array_merge(
                    $where_bind_rl,
                    $where_bind_ra,
                    $where_bind_rd,
                    $where_bind_rw,
                    $where_bind_rdw,
                    $where_bind_id,
                    $where_bind_cd,
                    $where_bind_o,
                    $where_bind_last_cd,
                    $where_bind_last_o
                ));

                $self = null;
                foreach ($_data as $key => $__data) {
                    if ($__data->user_id == $param['id']) {
                        $self = $__data;
                        unset($_data[$key]);
                        break;
                    }
                }

                if ($self) {
                    $self->self = 1;
                    array_unshift($_data, $self);
                }
                $data['data'] = $_data;
            } else {
                $where_rl = '';
                $where_ra = '';
                $where_rd = '';
                $where_rw = '';
                $where_rdw = '';
                $where_o = '';

                $where_bind_rl = [];
                $where_bind_ra = [];
                $where_bind_rd = [];
                $where_bind_rw = [];
                $where_bind_rdw = [];
                $where_bind_o = [];
                //开始时间
                if (!empty($param['start_date'])) {
                    //彩票
                    $where_rl .= ' and rl.created_at >= :start_date_rl';
                    $where_bind_rl['start_date_rl'] = $param['start_date'];
                    //活动
                    $where_ra .= ' and ra.created_at >= :start_date_ra';
                    $where_bind_ra['start_date_ra'] = $param['start_date'];
                    //充值
                    $where_rd .= ' and rd.created_at >= :start_date_rd';
                    $where_bind_rd['start_date_rd'] = $param['start_date'];
                    //提现
                    $where_rw .= ' and rw.created_at >= :start_date_rw';
                    $where_bind_rw['start_date_rw'] = $param['start_date'];
                    //工资
                    $where_rdw .= ' and rdw.created_at >= :start_date_rdw';
                    if (get_config('calculate_today_wage_to_tomorrow', 0) == 1) {
                        $where_bind_rdw['start_date_rdw'] = (new Carbon($param['start_date']))->subDay();
                    } else {
                        $where_bind_rdw['start_date_rdw'] = $param['start_date'];
                    }
                    //帐变
                    $where_o .= ' and orders.created_at >= :start_date_o';//帐变开始时间
                    $where_bind_o['start_date_o'] = $param['start_date'];
                }
                //结束时间
                if (!empty($param['end_date'])) {
                    //彩票
                    $where_rl .= ' and rl.created_at <= :end_date_rl';
                    $where_bind_rl['end_date_rl'] = $param['end_date'];
                    //活动
                    $where_ra .= ' and ra.created_at <= :end_date_ra';
                    $where_bind_ra['end_date_ra'] = $param['end_date'];
                    //充值
                    $where_rd .= ' and rd.created_at <= :end_date_rd';
                    $where_bind_rd['end_date_rd'] = $param['end_date'];
                    //提现
                    $where_rw .= ' and rw.created_at <= :end_date_rw';
                    $where_bind_rw['end_date_rw'] = $param['end_date'];
                    //工资
                    $where_rdw .= ' and rdw.created_at <= :end_date_rdw';
                    if (get_config('calculate_today_wage_to_tomorrow', 0) == 1) {
                        $where_bind_rdw['end_date_rdw'] = (new Carbon($param['end_date']))->subDay();
                    } else {
                        $where_bind_rdw['end_date_rdw'] = $param['end_date'];
                    }
                    //帐变
                    $where_o .= ' and orders.created_at <= :end_date_o';//帐变结束时间
                    $where_bind_o['end_date_o'] = $param['end_date'];
                }
                //彩种
                if (!empty($param['lottery_id'])) {
                    $where_rl .= ' and rl.lottery_id = :lottery_id_rl';
                    $where_bind_rl['lottery_id_rl'] = $param['lottery_id'];
                }
                //用户组别
                if (!empty($param['user_group_id'])) {
                    //彩票
                    $where_rl .= ' and users.user_group_id = :user_group_id_rl';
                    $where_bind_rl['user_group_id_rl'] = $param['user_group_id'];
                    //活动
                    $where_ra .= ' and users.user_group_id = :user_group_id_ra';
                    $where_bind_ra['user_group_id_ra'] = $param['user_group_id'];
                    //充值
                    $where_rd .= ' and users.user_group_id = :user_group_id_rd';
                    $where_bind_rd['user_group_id_rd'] = $param['user_group_id'];
                    //提现
                    $where_rw .= ' and users.user_group_id = :user_group_id_rw';
                    $where_bind_rw['user_group_id_rw'] = $param['user_group_id'];
                    //工资
                    $where_rdw .= ' and users.user_group_id = :user_group_id_rdw';
                    $where_bind_rdw['user_group_id_rdw'] = $param['user_group_id'];
                    //帐变
                    $where_o .= ' and users.user_group_id = :user_group_id_o';
                    $where_bind_o['user_group_id_o'] = $param['user_group_id'];
                }

                $where_bind_cd = [];
                $dividend_report_sql = '';
                $dividend_full_join_field = '';
                $dividend_coalesce_field = '';
                $dividend_to_report = get_config('dividend_to_report', 0);
                $where_bind_last_cd = [];
                $where_bind_last_o = [];
                $dividend_last_report_sql = '';
                $dividend_last_full_join_field = '';
                $dividend_last_coalesce_field = '';
                $dividend_last_amount_to_report = get_config('dividend_last_amount_to_report', 0);
                if ($dividend_to_report) {
                    $where_cd = " status = 1 and send_type = 1";
                    if (!empty($param['start_date'])) {
                        $where_cd .= ' and start_time >= :start_date_cd';
                        $where_bind_cd['start_date_cd'] = $param['start_date'];
                    }
                    if (!empty($param['end_date'])) {
                        $where_cd .= ' and end_time <= :end_date_cd';
                        $where_bind_cd['end_date_cd'] = $param['end_date'];
                    }
                    if (!empty($param['user_group_id'])) {
                        $where_cd .= ' and users.user_group_id = :user_group_id_cd';
                        $where_bind_cd['user_group_id_cd'] = $param['user_group_id'];
                    }
                    $dividend_report_sql = "
                        FULL JOIN (
                          select
                                users.top_id as user_id,
                                sum(amount) AS total_dividend
                            from contract_dividends as cd
                            left join users on users.id = cd.user_id
                            where
                            {$where_cd}
                            group by users.top_id
                      ) AS report_contract_dividend USING (user_id)
                    ";

                    $dividend_full_join_field = ",total_dividend";
                    //$dividend_coalesce_field = "COALESCE(report.total_dividend, 0) as total_dividend,";
                    $dividend_coalesce_field = "COALESCE(report.total_dividend, 0) +  "
                        . " COALESCE(report.total_fhff, 0) + "
                        . " COALESCE(report.total_cpfs, 0) + "
                        . " COALESCE(report.total_xtyjff, 0)  as total_dividend,";
                }
                if ($dividend_last_amount_to_report) {
                    $where_last_o = "";
                    $where_last_cd = " status = 1 and send_type = 1";
                    if (!empty($param['start_date'])) {
                        if (date('d', strtotime($param['start_date'])) !== '01') {
                            $where_last_cd .= ' and start_time >= :start_date_last_cd';
                            $where_bind_last_cd['start_date_last_cd'] = (new Carbon($param['start_date']))->subDay();
                            //算上人工处理的分红发放与佣金发放
                            $where_last_o .= ' and orders.created_at >= :start_date_last_o';
                            $where_bind_last_o['start_date_last_o'] = (new Carbon($param['start_date']))->subDay();
                        } else {
                            $where_last_cd .= ' and (start_time >= :start_date_last_cd or start_time = :start_date_last_month_cd)';
                            $where_bind_last_cd['start_date_last_cd'] = (new Carbon($param['start_date']))->subDay();
                            $where_bind_last_cd['start_date_last_month_cd'] = (new Carbon($param['start_date']))->subMonth();
                            //算上人工处理的分红发放与佣金发放
                            $where_last_o .= ' and orders.created_at >= :start_date_last_o ';
                            $where_bind_last_o['start_date_last_o'] = (new Carbon($param['start_date']))->subDay();
                        }
                    }
                    if (!empty($param['end_date'])) {
                        $where_last_cd .= ' and end_time <= :end_date_last_cd';
                        $where_bind_last_cd['end_date_last_cd'] = (new Carbon($param['end_date']))->subDay();
                        //算上人工处理的分红发放与佣金发放
                        $where_last_o .= ' and orders.created_at <= :end_date_last_o';
                        $where_bind_last_o['end_date_last_o'] = (new Carbon($param['end_date']))->subDay();
                    }
                    if (!empty($param['user_group_id'])) {
                        $where_last_cd .= ' and users.user_group_id = :user_group_id_cd';
                        $where_bind_last_o['user_group_id_cd'] = $param['user_group_id'];
                        //算上人工处理的分红发放与佣金发放
                        $where_last_cd .= ' and users.user_group_id = :user_group_id_o';
                        $where_bind_last_o['user_group_id_o'] = $param['user_group_id'];
                    }
                    $dividend_last_report_sql = "
                        FULL JOIN (
                          select
                                users.top_id as user_id,
                                sum(amount) AS last_total_dividend
                            from contract_dividends as last_cd
                            left join users on users.id = last_cd.user_id
                            where
                            {$where_last_cd}
                            group by users.top_id
                      ) AS report_contract_last_dividend USING (user_id)
                      FULL JOIN (
                        SELECT
                            users.top_id as user_id,
                            SUM((CASE WHEN order_type.ident='FHFF' THEN orders.amount ELSE 0 END)) as last_total_fhff,
                            SUM((CASE WHEN order_type.ident='XTYJFF' THEN orders.amount ELSE 0 END)) as last_total_xtyjff,
                            SUM((CASE WHEN order_type.ident='XTJYKK' THEN orders.amount ELSE 0 END)) as last_total_xtjykk,
                            SUM((CASE WHEN order_type.ident='CPFS' THEN orders.amount ELSE 0 END)) as last_total_cpfs,
                            SUM((CASE WHEN order_type.ident='CPCS' THEN orders.amount ELSE 0 END)) as last_total_cpcs
                        FROM orders
                        LEFT JOIN users ON users.id = orders.from_user_id
                        LEFT JOIN order_type ON order_type.id = orders.order_type_id
                        WHERE order_type.ident IN ('FHFF','XTYJFF','XTJYKK','CPFS','CPCS')
                        {$where_last_o}
                        GROUP BY users.top_id
                    ) AS orders_report_last_fh USING (user_id)
                    ";
                    //定义查询字段
                    $dividend_last_full_join_field = ",last_total_dividend,last_total_fhff,last_total_xtyjff,last_total_xtjykk,last_total_cpfs,last_total_cpcs";
                    $dividend_last_coalesce_field = "COALESCE(report.last_total_xtjykk, 0) + COALESCE(report.last_total_cpcs, 0) as last_total_xtjykk,"
                        . "COALESCE(report.last_total_dividend, 0) "
                        . " + COALESCE(report.last_total_fhff, 0)"
                        . " + COALESCE(report.last_total_cpfs, 0)"
                        . " + COALESCE(report.last_total_xtyjff, 0) as last_total_dividend,";
                }

                $data['data'] = DB::select("
                    select
                      users.id as user_id,
                      users.username,
                      user_group.id as user_group_id,
                      user_group.name as user_group_name,
                      COALESCE(report.total_price, 0) as total_price,
                      COALESCE(report.total_rebate, 0) as total_rebate,
                      COALESCE(report.total_price, 0) - COALESCE(report.total_rebate, 0) as total_real_price,
                      COALESCE(report.total_bonus, 0) as total_bonus,
                      COALESCE(report.total_activity, 0) as total_activity,
                      COALESCE(report.total_deposit, 0) as total_deposit,
                      COALESCE(report.total_deposit_fee, 0) as total_deposit_fee,
                      COALESCE(report.total_withdrawal, 0) as total_withdrawal,
                      COALESCE(report.total_withdrawal_fee, 0) as total_withdrawal_fee,
                      COALESCE(report.total_wage, 0) + COALESCE(report.total_xtsfff, 0) as total_wage,
                      COALESCE(report.total_fhff, 0) as total_fhff,
                      COALESCE(report.total_cpfs, 0) as total_cpfs,
                      COALESCE(report.total_cpcs, 0) as total_cpcs,
                      COALESCE(report.total_xtyjff, 0) as total_xtyjff,
                      COALESCE(report.total_xtsfff, 0) as total_xtsfff,
                      COALESCE(report.total_xtjykk, 0) + COALESCE(report.total_cpcs, 0) as total_xtjykk,
                      {$dividend_coalesce_field}
                      {$dividend_last_coalesce_field}
                      COALESCE(report.total_price, 0) - COALESCE(report.total_bonus, 0)
                        - COALESCE(report.total_rebate, 0) - COALESCE(report.total_wage, 0)
                        - COALESCE(report.total_activity, 0) - COALESCE(report.total_xtsfff, 0) as total_profit
                    from (
                      select
                        user_id,
                        total_price,
                        total_bonus,
                        total_rebate,
                        total_activity,
                        total_deposit,
                        total_deposit_fee,
                        total_withdrawal,
                        total_withdrawal_fee,
                        total_wage,
                        total_fhff,
                        total_xtyjff,
                        total_xtsfff,
                        total_xtjykk,
                        total_cpfs,
                        total_cpcs
                        {$dividend_full_join_field}
                        {$dividend_last_full_join_field}
                      from (
                          select
                            users.top_id as user_id,
                            SUM(rl.price) as total_price,
                            SUM(rl.bonus) as total_bonus,
                            SUM(rl.rebate) as total_rebate
                          from report_lottery_compressed as rl
                          left join users on users.id = rl.user_id
                          where true
                          {$where_rl}
                          group by users.top_id
                      ) as lottery_report
                      FULL JOIN (
                          select
                            users.top_id as user_id,
                            SUM(ra.bonus) as total_activity
                          from report_activity as ra
                          left join users on users.id = ra.user_id
                          where true
                          {$where_ra}
                          group by users.top_id
                      ) AS report_activity USING (user_id)
                      FULL JOIN (
                          select
                            users.top_id as user_id,
                            SUM(rd.amount) as total_deposit,
                            SUM(rd.platform_fee) as total_deposit_fee
                          from report_deposit as rd
                          left join users on users.id = rd.user_id
                          where true
                          {$where_rd}
                          group by users.top_id
                      ) AS report_deposit USING (user_id)
                      FULL JOIN (
                          select
                            users.top_id as user_id,
                            SUM(rw.amount) as total_withdrawal,
                            SUM(rw.platform_fee) as total_withdrawal_fee
                          from report_withdrawal as rw
                          left join users on users.id = rw.user_id
                          where true
                          {$where_rw}
                          group by users.top_id
                      ) AS report_withdrawal USING (user_id)
                      FULL JOIN (
                          select
                                users.top_id as user_id,
                                SUM(rdw.amount) as total_wage
                            from report_daily_wage as rdw
                            left join users on users.id = rdw.user_id
                            where true
                            {$where_rdw}
                            group by users.top_id
                      ) AS report_daily_wage USING (user_id)
                      FULL JOIN (
                        SELECT
                            users.top_id as user_id,
                            SUM((CASE WHEN order_type.ident='FHFF' THEN orders.amount ELSE 0 END)) as total_fhff,
                            SUM((CASE WHEN order_type.ident='XTYJFF' THEN orders.amount ELSE 0 END)) as total_xtyjff,
                            SUM((CASE WHEN order_type.ident='XTSFFF' THEN orders.amount ELSE 0 END)) as total_xtsfff,
                            SUM((CASE WHEN order_type.ident='XTJYKK' THEN orders.amount ELSE 0 END)) as total_xtjykk,
                            SUM((CASE WHEN order_type.ident='CPFS' THEN orders.amount ELSE 0 END)) as total_cpfs,
                            SUM((CASE WHEN order_type.ident='CPCS' THEN orders.amount ELSE 0 END)) as total_cpcs
                        FROM orders
                        LEFT JOIN users ON users.id = orders.from_user_id
                        LEFT JOIN order_type ON order_type.id = orders.order_type_id
                        WHERE order_type.ident IN (
                            'FHFF','XTYJFF','XTSFFF','XTJYKK','CPFS','CPCS'
                        )
                        {$where_o}
                        GROUP BY users.top_id
                    ) AS report_orders USING (user_id)
                      {$dividend_report_sql}
                      {$dividend_last_report_sql}
                    ) as report
                    {$join_type} join users on users.id = report.user_id
                    left join user_group on users.user_group_id = user_group.id
                    {$where_id}
                    order by {$order}
                ",
                    array_merge(
                        $where_bind_rl,
                        $where_bind_ra,
                        $where_bind_rd,
                        $where_bind_rw,
                        $where_bind_rdw,
                        $where_bind_id,
                        $where_bind_cd,
                        $where_bind_last_cd,
                        $where_bind_o,
                        $where_bind_last_o
                    ));
            }

            return $data;
        }
    }

    // 处理报表前期分红计算目前有使用到的后台为光辉, 大秦
    private function dealLastMonthDividend($start_date, $level)
    {
        // 若输入的用户为无月分红层级，则搜寻日期扣减一天
        if ($level - 1 > 2 && get_config('dividend_type_ident', ['Guanghui', 'Daqin'])) {
            // 光辉,大秦月分红代理皆为 2级代理才有
            return (new Carbon($start_date))->subDay();
        }

        return (new Carbon($start_date))->subMonth();
    }
}
