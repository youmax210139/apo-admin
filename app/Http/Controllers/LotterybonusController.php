<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LotterybonusIndexRequest;
use Service\Models\UserGroup;
use Service\Models\User;
use Service\API\User as APIUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LotterybonusController extends Controller
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

    public function postIndex(LotterybonusIndexRequest $request)
    {
        if ($request->ajax()) {
            //获取外部参数
            $param['id'] = (int)$request->post('id', 0);//用户ID
            $param['team_type'] = (int)$request->post('team_type', 0);//0团队下级，1直属下级
            $param['username'] = (string)$request->post('username');//用户名
            $param['start_date'] = $request->post('start_date', Carbon::today());//开始时间
            $param['end_date'] = $request->post('end_date', Carbon::today()->endOfDay());//结束时间
            $param['lottery_id'] = (int)$request->post('lottery_id');//彩种ID
            $param['method_id'] = (int)$request->post('method_id');//玩法ID
            $param['user_group_id'] = (int)$request->post('user_group_id');//用户组
            $param['is_search'] = (int)$request->post('is_search');//是否查询
            $param['order'] = (int)$request->post('order', 0);//排序
            $param['show_zero'] = (int)$request->post('show_zero', 0);//是否显示零用户

            //获取代理数
            if ($param['id'] > 0 && empty($param['is_search'])) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByParentID($param['id'], false);
            }
            //如果用户名不为空则获取用户ID
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

            //查看下级
            if ($param['id']) {
                $level = 0;//用户层级
                $user = User::find($param['id']);
                if (!empty($user)) {
                    $level = count(json_decode($user->parent_tree, true)) + 1;
                }

                $where_rl = '';//子查询 ，主表 report_lottery_compressed
                $where_rlt = '';
                $where_ra = '';
                $where_rat = '';
                $where_rd = '';
                $where_rdt = '';
                $where_rw = '';
                $where_rwt = '';
                $where_rdw = '';
                $where_rdwt = '';
                $where_o = '';

                $where_bind_rl = ['user_id_rl' => $param['id']];
                $where_bind_rlt = ['user_id_rlt' => $param['id']];
                $where_bind_ra = ['user_id_ra' => $param['id']];
                $where_bind_rat = ['user_id_rat' => $param['id']];
                $where_bind_rd = ['user_id_rd' => $param['id']];
                $where_bind_rdt = ['user_id_rdt' => $param['id']];
                $where_bind_rw = ['user_id_rw' => $param['id']];
                $where_bind_rwt = ['user_id_rwt' => $param['id']];
                $where_bind_rdw = ['user_id_rdw' => $param['id']];
                $where_bind_rdwt = ['user_id_rdwt' => $param['id']];
                $where_bind_o = [
                    'user_id_o' => $param['id'],
                    'id_o' => $param['id'],
                ];

                //根据时间查询区间
                if (!empty($param['start_date'])) {
                    $where_rl .= ' and rl.created_at >= :start_date_rl';
                    $where_bind_rl['start_date_rl'] = $param['start_date'];

                    $where_rlt .= ' and rlt.created_at >= :start_date_rlt';
                    $where_bind_rlt['start_date_rlt'] = $param['start_date'];

                    $where_ra .= ' and ra.created_at >= :start_date_ra';
                    $where_bind_ra['start_date_ra'] = $param['start_date'];

                    $where_rat .= ' and rat.created_at >= :start_date_rat';
                    $where_bind_rat['start_date_rat'] = $param['start_date'];

                    $where_rd .= ' and rd.created_at >= :start_date_rd';
                    $where_bind_rd['start_date_rd'] = $param['start_date'];

                    $where_rdt .= ' and rdt.created_at >= :start_date_rdt';
                    $where_bind_rdt['start_date_rdt'] = $param['start_date'];

                    $where_rw .= ' and rw.created_at >= :start_date_rw';
                    $where_bind_rw['start_date_rw'] = $param['start_date'];

                    $where_rwt .= ' and rwt.created_at >= :start_date_rwt';
                    $where_bind_rwt['start_date_rwt'] = $param['start_date'];

                    $where_rdw .= ' and rdw.created_at >= :start_date_rdw';
                    $where_rdwt .= ' and rdwt.created_at >= :start_date_rdwt';

                    if (get_config('calculate_today_wage_to_tomorrow', 0) == 1) {
                        $where_bind_rdw['start_date_rdw'] = (new Carbon($param['start_date']))->subDay();
                        $where_bind_rdwt['start_date_rdwt'] = (new Carbon($param['start_date']))->subDay();

                    } else {
                        $where_bind_rdw['start_date_rdw'] = $param['start_date'];
                        $where_bind_rdwt['start_date_rdwt'] = $param['start_date'];
                    }

                    $where_o .= ' and orders.created_at >= :start_date_o';
                    $where_bind_o['start_date_o'] = $param['start_date'];
                }

                if (!empty($param['end_date'])) {
                    $where_rl .= ' and rl.created_at <= :end_date_rl';
                    $where_bind_rl['end_date_rl'] = $param['end_date'];

                    $where_rlt .= ' and rlt.created_at <= :end_date_rlt';
                    $where_bind_rlt['end_date_rlt'] = $param['end_date'];

                    $where_ra .= ' and ra.created_at <= :end_date_ra';
                    $where_bind_ra['end_date_ra'] = $param['end_date'];

                    $where_rat .= ' and rat.created_at <= :end_date_rat';
                    $where_bind_rat['end_date_rat'] = $param['end_date'];

                    $where_rd .= ' and rd.created_at <= :end_date_rd';
                    $where_bind_rd['end_date_rd'] = $param['end_date'];

                    $where_rdt .= ' and rdt.created_at <= :end_date_rdt';
                    $where_bind_rdt['end_date_rdt'] = $param['end_date'];

                    $where_rw .= ' and rw.created_at <= :end_date_rw';
                    $where_bind_rw['end_date_rw'] = $param['end_date'];

                    $where_rwt .= ' and rwt.created_at <= :end_date_rwt';
                    $where_bind_rwt['end_date_rwt'] = $param['end_date'];

                    $where_rdw .= ' and rdw.created_at <= :end_date_rdw';
                    $where_rdwt .= ' and rdwt.created_at <= :end_date_rdwt';

                    if (get_config('calculate_today_wage_to_tomorrow', 0) == 1) {
                        $where_bind_rdw['end_date_rdw'] = (new Carbon($param['end_date']))->subDay();
                        $where_bind_rdwt['end_date_rdwt'] = (new Carbon($param['end_date']))->subDay();
                    } else {
                        $where_bind_rdw['end_date_rdw'] = $param['end_date'];
                        $where_bind_rdwt['end_date_rdwt'] = $param['end_date'];
                    }

                    $where_o .= ' and orders.created_at <= :end_date_o';
                    $where_bind_o['end_date_o'] = $param['end_date'];
                }

                //判断是查看团队还是直属的数据
                $where_user_parent_tree = ' users.parent_tree @> ';
                if ($param['team_type'] == 1) {
                    $where_user_parent_tree = ' users.parent_id = ';
                }

                $where_bind_cd = [];
                $dividend_report_sql = '';
                $dividend_full_join_field = '';
                $dividend_coalesce_field = '';
                $dividend_to_report = get_config('dividend_to_report', 0);//是否显示分红
                $where_bind_last_cd = [];
                $where_bind_last_o = [];
                $dividend_last_report_sql = '';
                $dividend_last_full_join_field = '';
                $dividend_last_coalesce_field = '';
                $dividend_last_amount_to_report = get_config('dividend_last_amount_to_report', 0);  //是否显示前期分红
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
                    $dividend_report_sql = "
                        full join (
                            select
                                COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                                sum(amount) AS total_dividend
                            from (
                                select
                                    cd.user_id,
                                    SUM(cd.amount) as amount
                                from contract_dividends as cd
                                left join users on users.id = cd.user_id
                                where ({$where_user_parent_tree} :user_id_cd or users.id = :id_cd)
                                {$where_cd}
                                group by cd.user_id
                            ) as rcd
                            left join users on rcd.user_id = users.id
                            group by 1
                        ) as contract_dividend_report using (user_id)
                        ";
                    $dividend_full_join_field = ",total_dividend";
                    $dividend_coalesce_field = "COALESCE(report.total_dividend, 0) +  "
                        . " COALESCE(report.total_fhff, 0) + "
                        . " COALESCE(report.total_cpfs, 0) + "
                        . " COALESCE(report.total_xtyjff, 0)  as total_dividend,";
                }
                if ($dividend_last_amount_to_report) {
                    $where_bind_last_cd = [
                        'user_id_last_cd' => $param['id'],
                        'id_last_cd' => $param['id'],
                    ];
                    $where_bind_last_o = [
                        'user_id_last_o' => $param['id'],
                        'id_last_o' => $param['id'],
                    ];
                    $where_last_o = "";
                    $where_last_cd = " and status = 1 and send_type = 1";
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
                            $where_last_o .= ' and orders.created_at >= :start_date_last_o ';
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
                        $where_last_cd .= ' and users.user_group_id = :user_group_id_last_cd';
                        $where_bind_last_cd['user_group_id_last_cd'] = $param['user_group_id'];
                        $where_last_o .= ' and users.user_group_id = :user_group_id_last_o';
                        $where_bind_last_o['user_group_id_last_o'] = $param['user_group_id'];
                    }
                    // 只显示直属数据
                    $dividend_last_report_sql = "
                        full join (
                            select
                                COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                                sum(last_amount) AS last_total_dividend
                            from (
                                select
                                    last_cd.user_id,
                                    SUM(last_cd.amount) as last_amount
                                from contract_dividends as last_cd
                                left join users on users.id = last_cd.user_id
                                where ({$where_user_parent_tree} :user_id_last_cd or users.id = :id_last_cd)
                                {$where_last_cd}
                                group by last_cd.user_id
                            ) as last_rcd
                            left join users on last_rcd.user_id = users.id
                            group by 1
                        ) as report_contract_last_dividend using (user_id)
                        full join (
                            select
                                COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                                SUM(last_total_fhff) as last_total_fhff,
                                SUM(last_total_xtyjff) as last_total_xtyjff,
                                SUM(last_total_xtjykk) as last_total_xtjykk,
                                SUM(last_total_cpcs) as last_total_cpcs,
                                SUM(last_total_cpfs) as last_total_cpfs
                            from (
                                select
                                    orders.from_user_id,
                                    SUM((CASE WHEN order_type.ident='FHFF' THEN orders.amount ELSE 0 END)) as last_total_fhff,
                                    SUM((CASE WHEN order_type.ident='XTYJFF' THEN orders.amount ELSE 0 END)) as last_total_xtyjff,
                                    SUM((CASE WHEN order_type.ident='XTJYKK' THEN orders.amount ELSE 0 END)) as last_total_xtjykk,
                                    SUM((CASE WHEN order_type.ident='CPCS' THEN orders.amount ELSE 0 END)) as last_total_cpcs,
                                    SUM((CASE WHEN order_type.ident='CPFS' THEN orders.amount ELSE 0 END)) as last_total_cpfs
                                from orders
	                            LEFT JOIN order_type ON order_type.id = orders.order_type_id
                                LEFT JOIN users ON users.id = orders.from_user_id
                                WHERE order_type.ident IN ('FHFF','XTYJFF','XTJYKK','CPCS','CPFS')
                                AND ({$where_user_parent_tree} :user_id_last_o or users.id = :id_last_o)
                                {$where_last_o}
                                group by orders.from_user_id
                            ) as orders_report_last_fh_f
                            left join users on orders_report_last_fh_f.from_user_id = users.id
                            group by 1
                        ) AS orders_report_last_fh USING (user_id)
                        ";
                    $dividend_last_full_join_field = ",last_total_dividend,last_total_fhff,last_total_xtyjff,last_total_xtjykk,last_total_cpcs,last_total_cpfs";
                    //这里的分红报表还要算上人工处理的分红发放，佣金发放
                    $dividend_last_coalesce_field = "COALESCE(report.last_total_xtjykk, 0) + COALESCE(report.last_total_cpcs, 0) as last_total_xtjykk,"
                        . " COALESCE(report.last_total_dividend, 0) +  "
                        . " COALESCE(report.last_total_fhff, 0) + "
                        . " COALESCE(report.last_total_cpfs, 0) + "
                        . " COALESCE(report.last_total_xtyjff, 0)  as last_total_dividend,";
                }
                //根据彩种ID获取
                if (!empty($param['lottery_id'])) {
                    $where_rl .= ' and rl.lottery_id = :lottery_id_rl';
                    $where_bind_rl['lottery_id_rl'] = $param['lottery_id'];
                    //这里的分红报表还要算上人工处理的分红发放，佣金发放
                    $where_rlt .= ' and rlt.lottery_id = :lottery_id_rlt';
                    $where_bind_rlt['lottery_id_rlt'] = $param['lottery_id'];
                }

                //定义是否只显示直属数据
                $report_lottery_total_compressed = 'report_lottery_total_compressed';
                $report_activity_total = 'report_activity_total';
                $report_deposit_total = 'report_deposit_total';
                $report_withdrawal_total = 'report_withdrawal_total';
                $report_daily_wage_total = 'report_daily_wage_total';
                if ($param['team_type'] == 1) {
                    $report_lottery_total_compressed = 'report_lottery_compressed';
                    $report_activity_total = 'report_activity';
                    $report_deposit_total = 'report_deposit';
                    $report_withdrawal_total = 'report_withdrawal';
                    $report_daily_wage_total = 'report_daily_wage';
                }

                $_data = DB::select("
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
                        - COALESCE(report.total_activity, 0)  - COALESCE(report.total_xtsfff, 0) as total_profit
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
                      from (
                          select
                            rl.user_id,
                            SUM(rl.price) as total_price,
                            SUM(rl.bonus) as total_bonus,
                            SUM(rl.rebate) as total_rebate
                          from report_lottery_compressed as rl
                          left join users on users.id = rl.user_id
                          where users.id = :user_id_rl
                          {$where_rl}
                          group by rl.user_id
                          union all
                          select
                            rlt.user_id,
                            SUM(rlt.price) as total_price,
                            SUM(rlt.bonus) as total_bonus,
                            SUM(rlt.rebate) as total_rebate
                          from {$report_lottery_total_compressed} as rlt
                          left join users on users.id = rlt.user_id
                          where users.parent_id = :user_id_rlt
                          {$where_rlt}
                          group by rlt.user_id
                      ) as lottery_report
                      FULL JOIN (
                          select
                            ra.user_id,
                            SUM(ra.bonus) as total_activity
                          from report_activity as ra
                          left join users on users.id = ra.user_id
                          where users.id = :user_id_ra
                          {$where_ra}
                          group by ra.user_id
                          union all
                          select
                            rat.user_id,
                            SUM(rat.bonus) as total_activity
                          from {$report_activity_total} as rat
                          left join users on users.id = rat.user_id
                          where users.parent_id = :user_id_rat
                          {$where_rat}
                          group by rat.user_id
                      ) AS report_activity USING (user_id)
                      FULL JOIN (
                          select
                            rd.user_id,
                            SUM(rd.amount) as total_deposit,
                            SUM(rd.platform_fee) as total_deposit_fee
                          from report_deposit as rd
                          left join users on users.id = rd.user_id
                          where users.id = :user_id_rd
                          {$where_rd}
                          group by rd.user_id
                          union all
                          select
                            rdt.user_id,
                            SUM(rdt.amount) as total_deposit,
                            SUM(rdt.platform_fee) as total_deposit_fee
                          from {$report_deposit_total} as rdt
                          left join users on users.id = rdt.user_id
                          where users.parent_id = :user_id_rdt
                          {$where_rdt}
                          group by rdt.user_id
                      ) AS report_deposit USING (user_id)
                      FULL JOIN (
                          select
                            rw.user_id,
                            SUM(rw.amount) as total_withdrawal,
                            SUM(rw.platform_fee) as total_withdrawal_fee
                          from report_withdrawal as rw
                          left join users on users.id = rw.user_id
                          where users.id = :user_id_rw
                          {$where_rw}
                          group by rw.user_id
                          union all
                          select
                            user_id,
                            SUM(rwt.amount) as total_withdrawal,
                            SUM(rwt.platform_fee) as total_withdrawal_fee
                          from {$report_withdrawal_total} as rwt
                          left join users on users.id = rwt.user_id
                          where users.parent_id = :user_id_rwt
                          {$where_rwt}
                          group by rwt.user_id
                      ) AS report_withdrawal USING (user_id)
                      FULL JOIN (
                          select
                              rdw.user_id,
                              SUM(rdw.amount) as total_wage
                          from report_daily_wage as rdw
                          left join users on users.id = rdw.user_id
                          where users.id = :user_id_rdw
                          {$where_rdw}
                          group by rdw.user_id
                          union all
                          select
                            rdwt.user_id,
                            SUM(rdwt.amount) as total_wage
                          from {$report_daily_wage_total} as rdwt
                          left join users on users.id = rdwt.user_id
                          where users.parent_id = :user_id_rdwt
                          {$where_rdwt}
                          group by rdwt.user_id
                      ) as report_daily_wage  USING (user_id)
                      FULL JOIN (
                          select
                                COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                                SUM(total_fhff) as total_fhff,
                                SUM(total_xtyjff) as total_xtyjff,
                                SUM(total_xtsfff) as total_xtsfff,
                                SUM(total_xtjykk) as total_xtjykk,
                                SUM(total_cpfs) as total_cpfs,
                                SUM(total_cpcs) as total_cpcs
                            from (
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
                                AND ({$where_user_parent_tree} :user_id_o or users.id = :id_o)
                                {$where_o}
                                group by orders.from_user_id
                            ) as orders_report_f
                            left join users on orders_report_f.from_user_id = users.id
                            group by 1
                    ) AS report_orders USING (user_id)
                    {$dividend_report_sql}
                    {$dividend_last_report_sql}
                    ) as report
                    {$join_type} join users on users.id = report.user_id
                    left join user_group on users.user_group_id = user_group.id
                    {$where_id}
                    order by {$order}
                ", array_merge(
                    $where_bind_rl,
                    $where_bind_rlt,
                    $where_bind_ra,
                    $where_bind_rat,
                    $where_bind_rd,
                    $where_bind_rdt,
                    $where_bind_rw,
                    $where_bind_rwt,
                    $where_bind_rdw,
                    $where_bind_rdwt,
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
                $where_rlt = '';
                $where_rat = '';
                $where_rdt = '';
                $where_rwt = '';
                $where_rdwt = '';
                $where_o = '';

                $where_bind_rlt = [];
                $where_bind_rat = [];
                $where_bind_rdt = [];
                $where_bind_rwt = [];
                $where_bind_rdwt = [];
                $where_bind_o = [];

                if (!empty($param['start_date'])) {
                    $where_rlt .= ' and rlt.created_at >= :start_date_rlt';
                    $where_bind_rlt['start_date_rlt'] = $param['start_date'];

                    $where_rat .= ' and rat.created_at >= :start_date_rat';
                    $where_bind_rat['start_date_rat'] = $param['start_date'];

                    $where_rdt .= ' and rdt.created_at >= :start_date_rdt';
                    $where_bind_rdt['start_date_rdt'] = $param['start_date'];

                    $where_rwt .= ' and rwt.created_at >= :start_date_rwt';
                    $where_bind_rwt['start_date_rwt'] = $param['start_date'];

                    $where_rdwt .= ' and rdwt.created_at >= :start_date_rdwt';
                    if (get_config('calculate_today_wage_to_tomorrow', 0) == 1) {
                        $where_bind_rdwt['start_date_rdwt'] = (new Carbon($param['start_date']))->subDay();
                    } else {
                        $where_bind_rdwt['start_date_rdwt'] = $param['start_date'];
                    }
                    //帐变条件-开始时间
                    $where_o .= ' and orders.created_at >= :start_date_o';//帐变开始时间
                    $where_bind_o['start_date_o'] = $param['start_date'];
                }

                if (!empty($param['end_date'])) {
                    $where_rlt .= ' and rlt.created_at <= :end_date_rlt';
                    $where_bind_rlt['end_date_rlt'] = $param['end_date'];

                    $where_rat .= ' and rat.created_at <= :end_date_rat';
                    $where_bind_rat['end_date_rat'] = $param['end_date'];

                    $where_rdt .= ' and rdt.created_at <= :end_date_rdt';
                    $where_bind_rdt['end_date_rdt'] = $param['end_date'];

                    $where_rwt .= ' and rwt.created_at <= :end_date_rwt';
                    $where_bind_rwt['end_date_rwt'] = $param['end_date'];

                    $where_rdwt .= ' and rdwt.created_at <= :end_date_rdwt';
                    if (get_config('calculate_today_wage_to_tomorrow', 0) == 1) {
                        $where_bind_rdwt['end_date_rdwt'] = (new Carbon($param['end_date']))->subDay();
                    } else {
                        $where_bind_rdwt['end_date_rdwt'] = $param['end_date'];
                    }
                    //帐变条件-结束时间
                    $where_o .= ' and orders.created_at <= :end_date_o';//帐变结束时间
                    $where_bind_o['end_date_o'] = $param['end_date'];
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
                    //分红发放，佣金加款计入分红栏
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
                        $where_last_cd .= ' and users.user_group_id = :user_group_id_last_cd';
                        $where_bind_last_cd['user_group_id_last_cd'] = $param['user_group_id'];
                        //算上人工处理的分红发放与佣金发放
                        $where_last_o .= ' and users.user_group_id = :user_group_id_last_o';
                        $where_bind_last_o['user_group_id_last_o'] = $param['user_group_id'];
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
                            SUM((CASE WHEN order_type.ident='CPCS' THEN orders.amount ELSE 0 END)) as last_total_cpcs,
                            SUM((CASE WHEN order_type.ident='CPFS' THEN orders.amount ELSE 0 END)) as last_total_cpfs
                        FROM orders
                        LEFT JOIN users ON users.id = orders.from_user_id
                        LEFT JOIN order_type ON order_type.id = orders.order_type_id
                        WHERE order_type.ident IN ('FHFF','XTYJFF','XTJYKK','CPCS','CPFS')
                        {$where_last_o}
                        GROUP BY users.top_id
                    ) AS orders_report_last_fh USING (user_id)
                    ";
                    //定义查询字段
                    $dividend_last_full_join_field = ",last_total_dividend,last_total_fhff,last_total_xtyjff,last_total_xtjykk,last_total_cpcs,last_total_cpfs";
                    $dividend_last_coalesce_field = "COALESCE(report.last_total_xtjykk, 0) + COALESCE(report.last_total_cpcs, 0)   as  last_total_xtjykk,"
                        . " COALESCE(report.last_total_dividend, 0) "
                        . " + COALESCE(report.last_total_fhff, 0)"
                        . " + COALESCE(report.last_total_cpfs, 0)"
                        . " + COALESCE(report.last_total_xtyjff, 0) as last_total_dividend,";
                }

                if (!empty($param['lottery_id'])) {
                    $where_rlt .= ' and rlt.lottery_id = :lottery_id_rlt';
                    $where_bind_rlt['lottery_id_rlt'] = $param['lottery_id'];
                }
                //用户权限组查询条件
                if (!empty($param['user_group_id'])) {
                    $where_rlt .= ' and users.user_group_id = :user_group_id_rlt';
                    $where_bind_rlt['user_group_id_rlt'] = $param['user_group_id'];

                    $where_rat .= ' and users.user_group_id = :user_group_id_rat';
                    $where_bind_rat['user_group_id_rat'] = $param['user_group_id'];

                    $where_rdt .= ' and users.user_group_id = :user_group_id_rdt';
                    $where_bind_rdt['user_group_id_rdt'] = $param['user_group_id'];

                    $where_rwt .= ' and users.user_group_id = :user_group_id_rwt';
                    $where_bind_rwt['user_group_id_rwt'] = $param['user_group_id'];

                    $where_rdwt .= ' and users.user_group_id = :user_group_id_rdwt';
                    $where_bind_rdwt['user_group_id_rdwt'] = $param['user_group_id'];

                    $where_o .= ' and users.user_group_id = :user_group_id_o';
                    $where_bind_o['user_group_id_o'] = $param['user_group_id'];
                }

                //定义是否只显示直属数据
                $report_lottery_total_compressed = 'report_lottery_total_compressed';
                $report_activity_total = 'report_activity_total';
                $report_deposit_total = 'report_deposit_total';
                $report_withdrawal_total = 'report_withdrawal_total';
                $report_daily_wage_total = 'report_daily_wage_total';
                if ($param['team_type'] == 1) {
                    $report_lottery_total_compressed = 'report_lottery_compressed';
                    $report_activity_total = 'report_activity';
                    $report_deposit_total = 'report_deposit';
                    $report_withdrawal_total = 'report_withdrawal';
                    $report_daily_wage_total = 'report_daily_wage';
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
                            rlt.user_id,
                            SUM(rlt.price) as total_price,
                            SUM(rlt.bonus) as total_bonus,
                            SUM(rlt.rebate) as total_rebate
                          from {$report_lottery_total_compressed} as rlt
                          left join users on users.id = rlt.user_id
                          where users.parent_id = 0
                          {$where_rlt}
                          group by rlt.user_id
                      ) as lottery_report
                      FULL JOIN (
                          select
                            rat.user_id,
                            SUM(rat.bonus) as total_activity
                          from {$report_activity_total} as rat
                          left join users on users.id = rat.user_id
                          where users.parent_id = 0
                          {$where_rat}
                          group by rat.user_id
                      ) AS report_activity USING (user_id)
                      FULL JOIN (
                          select
                            rdt.user_id,
                            SUM(rdt.amount) as total_deposit,
                            SUM(rdt.platform_fee) as total_deposit_fee
                          from {$report_deposit_total} as rdt
                          left join users on users.id = rdt.user_id
                          where users.parent_id = 0
                          {$where_rdt}
                          group by rdt.user_id
                      ) AS report_deposit USING (user_id)
                      FULL JOIN (
                          select
                            rwt.user_id,
                            SUM(rwt.amount) as total_withdrawal,
                            SUM(rwt.platform_fee) as total_withdrawal_fee
                          from {$report_withdrawal_total} as rwt
                          left join users on users.id = rwt.user_id
                          where users.parent_id = 0
                          {$where_rwt}
                          group by rwt.user_id
                      ) AS report_withdrawal USING (user_id)
                      FULL JOIN (
                          select
                            rdwt.user_id,
                            SUM(rdwt.amount) as total_wage
                          from {$report_daily_wage_total} as rdwt
                          left join users on users.id = rdwt.user_id
                          where users.parent_id = 0
                          {$where_rdwt}
                          group by rdwt.user_id
                      ) as report_daily_wage  USING (user_id)
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
                        $where_bind_rlt,
                        $where_bind_rat,
                        $where_bind_rdt,
                        $where_bind_rwt,
                        $where_bind_rdwt,
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
