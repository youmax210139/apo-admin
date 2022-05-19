<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LotteryprofitIndexRequest;
use Service\Models\UserGroup;
use Service\Models\User;
use Service\API\User as APIUser;
use Service\Models\Lottery as LotteryModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LotteryprofitController extends Controller
{
    public function getIndex(Request $request)
    {
        $user_group = UserGroup::all();
        $default_search_time = get_config('default_search_time', 0);
        return view(
            'lottery-profit/index',
            [
                'lottery_list' => \Service\API\Lottery::getAllLotteryGroupByCategory(),
                'lottery_method_list' => json_encode(\Service\API\Lottery::getAllLotteryMethodMapping()),
                'user_group' => $user_group,
                'id' => (int)$request->get('id', 0),
                'start_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::today()->addHours($default_search_time) :
                    Carbon::yesterday()->addHours($default_search_time),
                'end_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::tomorrow()->addHours($default_search_time)->subSecond(1) :
                    Carbon::today()->addHours($default_search_time)->subSecond(1),
            ]
        );
    }

    public function postIndex(LotteryprofitIndexRequest $request)
    {
        if ($request->ajax()) {
            $param['id'] = (int)$request->post('id');
            $param['username'] = (string)$request->post('username');
            $param['start_date'] = $request->post('start_date', Carbon::today());
            $param['end_date'] = $request->post('end_date', Carbon::today()->endOfDay());
            $param['lottery_id'] = (int)$request->post('lottery_id');
            $param['method_id'] = (int)$request->post('method_id');
            $param['user_group_id'] = (int)$request->post('user_group_id');
            $param['is_search'] = (int)$request->post('is_search');
            if ($param['id'] > 0 && empty($param['is_search'])) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByParentID($param['id'], false);
            }

            if ($param['is_search'] && !empty($param['username'])) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByUserName($param['username'], false);

                $param['id'] = 0;
                $user = User::where('username', $param['username'])->first(['id']);

                if (!empty($user)) {
                    $param['id'] = $user->id;
                }
            }

            if ($param['id']) {
                $where_1 = '';
                $where_2 = '';

                $where_bind_1 = [];
                $where_bind_2 = [];

                if (!empty($param['start_date'])) {
                    $where_1 .= ' and report.created_at >= :start_date_1';
                    $where_bind_1['start_date_1'] = $param['start_date'];

                    $where_2 .= ' and report.created_at >= :start_date_2';
                    $where_bind_2['start_date_2'] = $param['start_date'];
                }

                if (!empty($param['end_date'])) {
                    $where_1 .= ' and report.created_at <= :end_date_1';
                    $where_bind_1['end_date_1'] = $param['end_date'];

                    $where_2 .= ' and report.created_at <= :end_date_2';
                    $where_bind_2['end_date_2'] = $param['end_date'];
                }

                if (!empty($param['lottery_id'])) {
                    $where_1 .= ' and report.lottery_id = :lottery_id_1';
                    $where_bind_1['lottery_id_1'] = $param['lottery_id'];

                    $where_2 .= ' and report.lottery_id = :lottery_id_2';
                    $where_bind_2['lottery_id_2'] = $param['lottery_id'];
                }

                if (!empty($param['method_id'])) {
                    $where_1 .= ' and report.lottery_method_id = :method_id_1';
                    $where_bind_1['method_id_1'] = $param['method_id'];

                    $where_2 .= ' and report.lottery_method_id = :method_id_2';
                    $where_bind_2['method_id_2'] = $param['method_id'];
                }

                if (!empty($param['user_group_id'])) {
                    $where_1 .= ' and users.user_group_id = :user_group_id_1';
                    $where_bind_1['user_group_id_1'] = $param['user_group_id'];

                    $where_2 .= ' and users.user_group_id = :user_group_id_2';
                    $where_bind_2['user_group_id_2'] = $param['user_group_id'];
                }

                $_data = DB::select("
                    select report_lottery.user_id,
                      users.username,
                      user_group.id as user_group_id,
                      user_group.name as user_group_name,
                      report_lottery.total_price,
                      report_lottery.total_rebate,
                      (report_lottery.total_price - report_lottery.total_rebate) as total_real_price,
                      report_lottery.total_bonus,
                      (report_lottery.total_price - report_lottery.total_bonus - report_lottery.total_rebate) as total_profit
                    from (
                      select
                        user_id,
                        SUM(report.price) as total_price,
                        SUM(report.bonus) as total_bonus,
                        SUM(report.rebate) as total_rebate
                      from report_lottery as report
                      left join users on users.id = report.user_id
                      where users.id = :user_id_1
                      {$where_1}
                      group by user_id
                      union all
                      select
                        user_id,
                        SUM(report.price) as total_price,
                        SUM(report.bonus) as total_bonus,
                        SUM(report.rebate) as total_rebate
                      from report_lottery_total as report
                      left join users on users.id = report.user_id
                      where users.parent_id = :user_id_2
                      {$where_2}
                      group by user_id
                    ) as report_lottery
                    left join users on users.id = report_lottery.user_id
                    left join user_group on users.user_group_id = user_group.id
                ", array_merge([
                    'user_id_1' => $param['id'],
                    'user_id_2' => $param['id']
                ], $where_bind_1, $where_bind_2));

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
                $where = '';
                $where_bind = [];
                if (!empty($param['start_date'])) {
                    $where .= ' and report.created_at >= :start_date';
                    $where_bind['start_date'] = $param['start_date'];
                }

                if (!empty($param['end_date'])) {
                    $where .= ' and report.created_at <= :end_date';
                    $where_bind['end_date'] = $param['end_date'];
                }

                if (!empty($param['lottery_id'])) {
                    $where .= ' and report.lottery_id = :lottery_id';
                    $where_bind['lottery_id'] = $param['lottery_id'];
                }

                if (!empty($param['method_id'])) {
                    $where .= ' and report.lottery_method_id = :method_id';
                    $where_bind['method_id'] = $param['method_id'];
                }

                if (!empty($param['user_group_id'])) {
                    $where .= ' and users.user_group_id = :user_group_id';
                    $where_bind['user_group_id'] = $param['user_group_id'];
                }


                $data['data'] = DB::select(
                    "
                    select report_lottery.user_id,
                      users.username,
                      user_group.id as user_group_id,
                      user_group.name as user_group_name,
                      report_lottery.total_price,
                      report_lottery.total_rebate,
                      (report_lottery.total_price - report_lottery.total_rebate) as total_real_price,
                      report_lottery.total_bonus,
                      (report_lottery.total_price - report_lottery.total_bonus - report_lottery.total_rebate) as total_profit
                    from (
                      select
                        user_id,
                        SUM(report.price) as total_price,
                        SUM(report.bonus) as total_bonus,
                        SUM(report.rebate) as total_rebate
                      from report_lottery_total as report
                      left join users on users.id = report.user_id
                      where users.parent_id = 0
                      {$where}
                      group by user_id
                    ) as report_lottery
                    left join users on users.id = report_lottery.user_id
                    left join user_group on users.user_group_id = user_group.id
                ",
                    $where_bind
                );
            }

            return $data;
        }
    }

    /**
     * 游戏明细
     * @param Request $request
     * @return View
     */
    public function getDetail(LotteryprofitIndexRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $start_date = $request->get('start_date', '');
        $end_date = $request->get('end_date', '');

        $user = User::find($id);
        if (!$user) {
            return redirect('/lotteryprofit\/')->withErrors("找不到该用户");
        }

        return view('lottery-profit.detail', [
            'id' => $id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    public function postDetail(LotteryprofitIndexRequest $request)
    {
        if ($request->ajax()) {
            $param['id'] = (int)$request->post('id');
            $param['start_date'] = $request->post('start_date');
            $param['end_date'] = $request->post('end_date');

            $data = $ret_data = $where_bind = [];
            $where = '';
            if (!empty($param['start_date'])) {
                $where .= ' and report_lottery_total.created_at >= :start_date';
                $where_bind['start_date'] = $param['start_date'];
            }

            if (!empty($param['end_date'])) {
                $where .= ' and report_lottery_total.created_at <= :end_date';
                $where_bind['end_date'] = $param['end_date'];
            }

            if (!empty($param['id'])) {
                $where_bind['user_id'] = $param['id'];
            }

            $data['data'] = DB::select("
                select report.lottery_id,
                    lottery.name as lottery_name,
                    (plm.name || ' - ' || lm.name) as method_name,
                    report.total_price,
                    report.total_bonus,
                    report.total_rebate
                from (
                    select report_lottery_total.lottery_id,
                        report_lottery_total.lottery_method_id,
                        SUM(report_lottery_total.price) as total_price,
                        SUM(report_lottery_total.bonus) as total_bonus,
                        SUM(report_lottery_total.rebate) as total_rebate
                     from report_lottery_total
                     where report_lottery_total.user_id = :user_id
                     {$where}
                     group by report_lottery_total.lottery_id,
                        report_lottery_total.lottery_method_id
                ) as report
                left join lottery on lottery.id = report.lottery_id
                left join lottery_method as lm on lm.id = report.lottery_method_id
                left join lottery_method as plm on lm.parent_id = plm.id
                order by report.lottery_id
            ", $where_bind);

            if ($data['data']) {
                foreach ($data['data'] as $k => $v) {
                    $data['data'][$k]->total_real_price = $v->total_price - $v->total_rebate;
                    $data['data'][$k]->total_profit = $v->total_price - $v->total_bonus - $v->total_rebate;
                }

                foreach (LotteryModel::pluck('id') as $v1) {
                    $total_bonus = $total_price = $total_rebate = 0;
                    foreach ($data['data'] as $v2) {
                        if ($v2->lottery_id === $v1) {
                            $total_bonus += $v2->total_bonus;
                            $total_price += $v2->total_price;
                            $total_rebate += $v2->total_rebate;
                        }
                    }
                    if ($total_price !== 0) {
                        $ret_data[$v1] = [
                            'total_bonus' => $total_bonus,
                            'total_price' => $total_price,
                            'total_rebate' => $total_rebate,
                            'total_real_price' => $total_price - $total_rebate,
                            'total_profit' => $total_price - $total_bonus - $total_rebate,
                        ];
                    }
                }
                $data['group_total'] = $ret_data;
            }

            return $data;
        }
    }
}
