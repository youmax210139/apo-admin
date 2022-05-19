<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyWageIndexRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Service\Models\DailyWage;
use Service\Models\FloatWages;
use Service\Models\RealtimeWage;
use Service\Models\HourlyWage;
use Service\Models\IssueWage;
use Service\Models\User;
use Service\Models\UserGroup;
use Service\API\Lottery as APILottery;

class DailywagereportController extends Controller
{
    protected $fields = [];

    public function getIndex(Request $request)
    {
        $type_page = $request->get('type_page', get_config('dailywage_default_type', 1));
        $view = 'index';
        $start_date = Carbon::yesterday()->format('Y-m-d');
        $end_date = '';
        $lottery_list = [];
        if (in_array($type_page, [2, 3])) {
            $view = 'hourly';
            if ($type_page == 2) {
                $view = 'realtime';
            }
            $start_date = Carbon::now()->subHour()->format('Y-m-d H:00:00');
            $end_date = Carbon::now()->format('Y-m-d H:00:00');
        } elseif ($type_page == 4) {
            $view = 'float';
            $start_date = Carbon::yesterday()->format('Y-m-d 00:00:00');
        } elseif ($type_page == 5) {
            $view = 'winloss';
        } elseif (in_array($type_page, [7])) {
            $view = 'issue';
            $start_date = Carbon::now()->subHour()->format('Y-m-d H:00:00');
            $end_date = Carbon::now()->addHour()->format('Y-m-d H:00:00');
            $lottery_list = APILottery::getAllLotteryGroupByCategory();
        }

        $user_group = UserGroup::all();
        return view(
            'dailywage-report.' . $view,
            [
                'wage_type' => $type_page,
                'user_group' => $user_group,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'lottery_list' => $lottery_list,
            ]
        );
    }

    public function postIndex(DailyWageIndexRequest $request)
    {
        if ($request->ajax()) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['username'] = trim($request->get('username'));
            $param['amount_min'] = $request->get('amount_min');
            $param['amount_max'] = $request->get('amount_max');
            $param['user_group_id'] = $request->get('user_group_id');
            $param['frozen'] = trim($request->get('frozen'));
            $param['search_scope'] = trim($request->get('search_scope'));
            $param['created_start_date'] = $request->get('created_start_date');
            $param['status'] = $request->get('status');

            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');

            $data = [];
            $where = [];
            if ($param['frozen'] == '1') {
                $where[] = ['users.frozen', '>', 0];
            } elseif ($param['frozen'] == '2') {
                $where[] = ['users.frozen', '=', 0];
            }
            if ($param['status'] !== '') {
                $where[] = ['daily_wage.status', '=', $param['status']];
            }
            if ($param['created_start_date']) {
                $where[] = ['daily_wage.date', '=', $param['created_start_date']];
            }
            if ($param['amount_min']) {
                $where[] = ['daily_wage.amount', '>=', $param['amount_min']];
            }
            if ($param['amount_max']) {
                $where[] = ['daily_wage.amount', '<=', $param['amount_max']];
            }
            if ($param['user_group_id']) {
                $where[] = ['users.user_group_id', '=', $param['user_group_id']];
            }
            if ($param['username']) {
                $search_user = User::where('username', $param['username'])->first();
                if (!$search_user) {
                    return response()->json($data);
                }
            }

            $model = DailyWage::select([
                'daily_wage.*',
                'users.username',
                'users.parent_tree',
                'user_group.name as user_group',
                'user_group.name as user_group',
            ])
                ->leftJoin('users', 'users.id', 'daily_wage.user_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->where($where);

            if ($param['username']) {
                $model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }

            //合计
            $total_model = DailyWage::select([
                DB::raw('SUM(amount) as total_amount')
            ])
                ->leftJoin('users', 'users.id', 'daily_wage.user_id')
                ->where($where);
            if ($param['username']) {
                $total_model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }
            $data['totalSum'] = $total_model->first();


            $total = $model->count();
            $data['recordsTotal'] = $data['recordsFiltered'] = $total;
            $columns_orderby = ['username', 'parent_tree', 'amount'];
            if (in_array($columns[$order[0]['column']]['data'], $columns_orderby)) {
                $model->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir']);
            } else {
                $model->orderByRaw("(remark->>'{$columns[$order[0]['column']]['data']}')::FLOAT {$order[0]['dir']}");
            }
            $data['data'] = $model->skip($start)->take($length)->get()->toArray();
            foreach ($data['data'] as $_key => $_row) {
                $level = count(json_decode($_row['parent_tree'], true));
                $_row['parent_tree'] = $level ? "{$level}级代理" : "总代";
                $remark = json_decode($_row['remark'], true);
                $data['data'][$_key] = array_merge($_row, $remark);
            }
            return response()->json($data);
        }
    }

    public function postHourly(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['username'] = trim($request->get('username'));
            $param['amount_min'] = $request->get('amount_min');
            $param['amount_max'] = $request->get('amount_max');
            $param['user_group_id'] = $request->get('user_group_id');
            $param['frozen'] = trim($request->get('frozen'));
            $param['search_scope'] = trim($request->get('search_scope'));
            $param['start_date'] = $request->get('start_date');
            $param['end_date'] = $request->get('end_date');
            $param['status'] = $request->get('status');
            $param['type'] = $request->get('type');

            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');

            $data = [];
            $where = [];
            if ($param['frozen'] == '1') {
                $where[] = ['users.frozen', '>', 0];
            } elseif ($param['frozen'] == '2') {
                $where[] = ['users.frozen', '=', 0];
            }
            if ($param['status'] !== '') {
                $where[] = ['hourly_wage.status', '=', $param['status']];
            }
            if ($param['start_date']) {
                $where[] = ['hourly_wage.start_date', '>=', $param['start_date']];
            }
            if ($param['end_date']) {
                $where[] = ['hourly_wage.end_date', '<=', $param['end_date']];
            }
            if ($param['amount_min']) {
                $where[] = ['hourly_wage.amount', '>=', $param['amount_min']];
            }
            if ($param['amount_max']) {
                $where[] = ['hourly_wage.amount', '<=', $param['amount_max']];
            }
            if ($param['type']) {
                $where[] = ['hourly_wage.type', '=', $param['type']];
            }
            if ($param['user_group_id']) {
                $where[] = ['users.user_group_id', '=', $param['user_group_id']];
            }
            if ($param['username']) {
                $search_user = User::where('username', $param['username'])->first();
                if (!$search_user) {
                    return response()->json($data);
                }
            }
            $model = HourlyWage::select([
                'hourly_wage.*',
                'users.username',
                DB::raw("CASE WHEN users.user_type_id!=1 THEN concat(jsonb_array_length(users.parent_tree),'级',user_type.name) ELSE user_type.name END as user_type_name"),
                DB::raw("jsonb_array_length(users.parent_tree) as user_level"),
                'user_group.name as user_group',
            ])
                ->leftJoin('users', 'users.id', 'hourly_wage.user_id')
                ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->where($where);

            if ($param['username']) {
                $model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }

            //合计
            $total_model = HourlyWage::select([
                DB::raw("COALESCE(SUM(amount),0) as total_amount"),
                DB::raw("COALESCE(SUM((remark->>'total_bet')::numeric),0) as total_bet"),
                DB::raw("COALESCE(SUM((remark->>'bet_rebate')::numeric),0) as bet_rebate"),
                DB::raw("COALESCE(SUM((remark->>'child_wage')::numeric),0) as child_wage"),
                DB::raw("COALESCE(SUM((remark->>'calculate_amount')::numeric),0) as calculate_amount"),
                DB::raw("COALESCE(SUM((remark->>'user_active')::numeric),0) as user_active"),
            ])
                ->leftJoin('users', 'users.id', 'hourly_wage.user_id')
                ->where($where);
            if ($param['username']) {
                $total_model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }
            $data['totalSum'] = $total_model->first();

            $total = $model->count();
            $data['recordsTotal'] = $data['recordsFiltered'] = $total;
            $columns_orderby = ['username', 'amount'];
            if (in_array($columns[$order[0]['column']]['data'], $columns_orderby)) {
                $model->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir']);
            } else {
                $model->orderBy("created_at", 'desc');
            }
            $data['data'] = $model->skip($start)->take($length)->get()->toArray();
            foreach ($data['data'] as $_key => $_row) {
                $remark = json_decode($_row['remark'], true);
                $remark['bet_rebate'] = $remark['bet_rebate'] ?? ($remark['lottery_rebate'] ?? 0);
                $remark['child_wage'] = $remark['child_wage'] ?? ($remark['deduct'] ?? 0);
                $remark['calculate_amount'] = $remark['calculate_amount'] ?? 0;
                $remark['total_amount'] = $remark['total_amount'] ?? ($remark['before_deduct'] ?? 0);
                $remark['user_active'] = $remark['user_active'] ?? 0;
                $data['data'][$_key] = array_merge($_row, $remark);
            }
            return response()->json($data);
        }
    }

    public function postRealtime(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['username'] = trim($request->get('username'));
            $param['amount_min'] = $request->get('amount_min');
            $param['amount_max'] = $request->get('amount_max');
            $param['user_group_id'] = $request->get('user_group_id');
            $param['frozen'] = trim($request->get('frozen'));
            $param['search_scope'] = trim($request->get('search_scope'));
            $param['start_date'] = $request->get('start_date');
            $param['end_date'] = $request->get('end_date');
            $param['status'] = $request->get('status');
            $param['project_code'] = $request->get('project_code', '');
            $param['project_id'] = $param['project_code'] ? id_decode($param['project_code']) : 0;
            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');

            $data = [];
            $where = [];
            if ($param['frozen'] == '1') {
                $where[] = ['users.frozen', '>', 0];
            } elseif ($param['frozen'] == '2') {
                $where[] = ['users.frozen', '=', 0];
            }
            if ($param['project_id']) {
                $where[] = ['realtime_wage.project_id', '=', $param['project_id']];
            }
            if ($param['status'] !== '') {
                $where[] = ['realtime_wage.status', '=', $param['status']];
            }
            if ($param['start_date']) {
                $where[] = ['realtime_wage.created_at', '>=', $param['start_date']];
            }
            if ($param['end_date']) {
                $where[] = ['realtime_wage.created_at', '<=', $param['end_date']];
            }
            if ($param['amount_min']) {
                $where[] = ['realtime_wage.amount', '>=', $param['amount_min']];
            }
            if ($param['amount_max']) {
                $where[] = ['realtime_wage.amount', '<=', $param['amount_max']];
            }
            if ($param['user_group_id']) {
                $where[] = ['users.user_group_id', '=', $param['user_group_id']];
            }
            if ($param['username']) {
                $search_user = User::where('username', $param['username'])->first();
                if (!$search_user) {
                    return response()->json($data);
                }
            }

            $model = RealtimeWage::select([
                'realtime_wage.*',
                'users.username',
                'users.parent_tree',
                'user_group.name as user_group',
                'user_group.name as user_group',
            ])
                ->leftJoin('users', 'users.id', 'realtime_wage.user_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->where($where);

            if ($param['username']) {
                $model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }

            //合计
            $total_model = RealtimeWage::select([
                DB::raw("COALESCE(SUM(realtime_wage.amount),0) as amount")
            ])
                ->leftJoin('users', 'users.id', 'realtime_wage.user_id')
                ->where($where);
            if ($param['username']) {
                $total_model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }
            $data['totalSum'] = $total_model->first();

            $total = $model->count();
            $data['recordsTotal'] = $data['recordsFiltered'] = $total;
            $columns_orderby = ['username', 'parent_tree', 'amount', 'created_at'];
            if (in_array($columns[$order[0]['column']]['data'], $columns_orderby)) {
                $model->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir']);
            } else {
                $model->orderBy('id', 'desc');
            }
            $data['data'] = $model->skip($start)->take($length)->get()->toArray();
            foreach ($data['data'] as $_key => $_row) {
                $level = count(json_decode($_row['parent_tree'], true));
                $data['data'][$_key]['parent_level'] = $level ? "{$level}级代理" : "总代";
                $data['data'][$_key]['project_id'] = id_encode($_row['project_id']);
            }
            return response()->json($data);
        }
    }

    /**
     * 浮动工资
     */
    public function postFloat(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['username'] = trim($request->get('username'));
            $param['amount_min'] = $request->get('amount_min');
            $param['amount_max'] = $request->get('amount_max');
            $param['user_group_id'] = $request->get('user_group_id');
            $param['frozen'] = trim($request->get('frozen'));
            $param['search_scope'] = trim($request->get('search_scope'));
            $param['start_date'] = $request->get('start_date');
            $param['status'] = $request->get('status');
            $param['userlevel'] = (int)$request->get('userlevel', '0');

            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');

            $data = [];
            $where = [];
            if ($param['frozen'] == '1') {
                $where[] = ['users.frozen', '>', 0];
            } elseif ($param['frozen'] == '2') {
                $where[] = ['users.frozen', '=', 0];
            }
            if ($param['status'] !== '') {
                $where[] = ['float_wages.status', '=', $param['status']];
            }
            if ($param['start_date']) {
                $where[] = ['float_wages.created_at', '>=', $param['start_date']];
            }
            if ($param['amount_min']) {
                $where[] = ['float_wages.amount', '>=', $param['amount_min']];
            }
            if ($param['amount_max']) {
                $where[] = ['float_wages.amount', '<=', $param['amount_max']];
            }
            if ($param['user_group_id']) {
                $where[] = ['users.user_group_id', '=', $param['user_group_id']];
            }
            if ($param['userlevel']) {
                $where[] = [DB::raw('jsonb_array_length(users.parent_tree)'), '=', $param['userlevel']];
            }
            if ($param['username']) {
                $search_user = User::where('username', $param['username'])->first();
                if (!$search_user) {
                    return response()->json($data);
                }
            }

            $model = FloatWages::select([
                'float_wages.*',
                'users.username',
                'users.parent_tree',
                DB::raw("CASE WHEN users.user_type_id!=1 THEN concat(jsonb_array_length(users.parent_tree),'级',user_type.name) ELSE user_type.name END as user_level_name"),
                DB::raw("jsonb_array_length(users.parent_tree) as user_level"),
                'user_group.name as user_group_name',
            ])
                ->leftJoin('users', 'users.id', 'float_wages.user_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
                ->where($where);

            if ($param['username']) {
                $model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }

            //合计
            $total_model = FloatWages::select([
                DB::raw("COALESCE(SUM(float_wages.total_price),0) as total_price"),
                DB::raw("COALESCE(SUM(float_wages.total_rebate),0) as total_rebate"),
                DB::raw("COALESCE(SUM(float_wages.child_amount),0) as child_amount"),
                DB::raw("COALESCE(SUM(float_wages.amount),0) as amount"),
                DB::raw("COALESCE(SUM(float_wages.activity),0) as total_activity"),
            ])
                ->leftJoin('users', 'users.id', 'float_wages.user_id')
                ->where($where);
            if ($param['username']) {
                $total_model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }
            $data['totalSum'] = $total_model->first();

            $total = $model->count();
            $data['recordsTotal'] = $data['recordsFiltered'] = $total;
            $columns_orderby = ['username', 'parent_tree', 'amount'];
            if (in_array($columns[$order[0]['column']]['data'], $columns_orderby)) {
                $model->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir']);
            } else {
                $model->orderBy('id', 'desc');
            }
            $data['data'] = $model->skip($start)->take($length)->get()->toArray();

            return response()->json($data);
        }
    }

    public function postIssue(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['username'] = trim($request->get('username'));
            $param['source_username'] = trim($request->get('source_username'));
            $param['amount_min'] = $request->get('amount_min');
            $param['amount_max'] = $request->get('amount_max');
            $param['user_group_id'] = $request->get('user_group_id');
            $param['frozen'] = trim($request->get('frozen'));
            $param['search_scope'] = trim($request->get('search_scope'));
            $param['start_date'] = $request->get('start_date');
            $param['end_date'] = $request->get('end_date');
            $param['status'] = $request->get('status');
            $param['type'] = $request->get('type');
            $param['show_type'] = $request->get('show_type', 0);
            $param['lottery_id'] = $request->get('lottery_id', 0);
            $param['issue'] = $request->get('issue', '');

            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');

            $data = [];
            $where = [];
            if ($param['frozen'] == '1') {
                $where[] = ['users.frozen', '>', 0];
            } elseif ($param['frozen'] == '2') {
                $where[] = ['users.frozen', '=', 0];
            }
            if ($param['status'] !== '') {
                $where[] = ['issue_wage.status', '=', $param['status']];
            }
            if ($param['show_type'] == 1) {
                $where[] = ['issue_wage.user_id', '=', DB::raw('issue_wage.source_user_id')];
            } elseif ($param['show_type'] == 2) {
                $where[] = ['issue_wage.user_id', '<>', DB::raw('issue_wage.source_user_id')];
            }
            if ($param['lottery_id']) {
                $where[] = ['issue_wage.lottery_id', '=', $param['lottery_id']];
            }

            if ($param['issue']) {
                $where[] = ['issue_wage.issue', '=', $param['issue']];
            }

            if ($param['start_date']) {
                $where[] = ['issue_wage.sale_start', '>=', $param['start_date']];
            }
            if ($param['end_date']) {
                $where[] = ['issue_wage.sale_end', '<=', $param['end_date']];
            }
            if ($param['amount_min']) {
                $where[] = ['issue_wage.amount', '>=', $param['amount_min']];
            }
            if ($param['amount_max']) {
                $where[] = ['issue_wage.amount', '<=', $param['amount_max']];
            }
            if ($param['type']) {
                $where[] = ['issue_wage.type', '=', $param['type']];
            }
            if ($param['user_group_id']) {
                $where[] = ['users.user_group_id', '=', $param['user_group_id']];
            }
            if ($param['username']) {
                $search_user = User::where('username', $param['username'])->first();
                if (!$search_user) {
                    return response()->json($data);
                }
            }
            //查询源用户
            if ($param['source_username']) {
                $where[] = ['su.username', '=', $param['source_username']];
            }

            $model = IssueWage::select([
                'issue_wage.*',
                'lottery.name as lottery_name',
                'su.username as source_username',
                'users.username',
                DB::raw("CASE WHEN users.user_type_id!=1 THEN concat(jsonb_array_length(users.parent_tree),'级',user_type.name) ELSE user_type.name END as user_type_name"),
                DB::raw("jsonb_array_length(users.parent_tree) as user_level"),
                'user_group.name as user_group',
            ])
                ->leftJoin('users', 'users.id', 'issue_wage.user_id')
                ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->leftJoin('users as su', 'su.id', 'issue_wage.source_user_id')
                ->leftJoin('lottery', 'lottery.id', 'issue_wage.lottery_id')
                ->where($where);

            if ($param['username']) {
                $model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }


            //合计
            $total_model = IssueWage::select([
                DB::raw("COALESCE(SUM(amount),0) as total_amount"),
                DB::raw("COALESCE(SUM(price),0) as price"),
                DB::raw("COALESCE(SUM(bonus),0) as bonus"),
                DB::raw("COALESCE(SUM(rebate),0) as rebate"),
                DB::raw("COALESCE(SUM(profit),0) as profit"),
            ])
                ->leftJoin('users', 'users.id', 'issue_wage.user_id')
                ->leftJoin('users as su', 'su.id', 'issue_wage.source_user_id')
                ->where($where);
            if ($param['username']) {
                $total_model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }
            $data['totalSum'] = $total_model->first();

            $total = $model->count();
            $data['recordsTotal'] = $data['recordsFiltered'] = $total;
            $model->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir']);
            $data['data'] = $model->skip($start)->take($length)->get()->toArray();
            foreach ($data['data'] as $_key => $_row) {
                $remark = json_decode($_row['remark'], true);
                $remark['order_id'] = $remark['order_id'] ? id_encode($remark['order_id']) : '';
                $data['data'][$_key] = array_merge($_row, $remark);
            }
            return response()->json($data);
        }
    }

    private function _usernameWhere($query, $param, $search_user)
    {
        if ($param['search_scope'] == 'directly') {
            $query->where('users.parent_id', '=', $search_user->id);
            $query->orWhere('users.id', '=', $search_user->id);
        } elseif ($param['search_scope'] == 'team') {
            $query->where('users.parent_tree', '@>', $search_user->id);
            $query->orWhere('users.id', '=', $search_user->id);
        } else {
            $query->where('users.username', '=', $param['username']);
        }
    }

    public function postDelete(Request $request)
    {
        $delete_by = $request->get('delete_by', 'select');
        $result = false;
        $msg = '';
        if ($delete_by == 'select') {
            $ids = array_filter(explode(",", $request->input('select_ids', '')));
            if ($ids) {
                $result = DailyWage::whereIn('id', $ids)->whereIN('status', [0, 1])->delete();
            } else {
                $msg = "请选择需要删除的记录";
            }
        } else {
            $start_time = $request->input('start_time', '');
            if ($start_time) {
                $result = DailyWage::where('date', '=', $start_time)->whereIN('status', [0, 1])->delete();
            } else {
                $msg = "时间不正确";
            }
        }
        if ($result) {
            return redirect()->back()->withSuccess("删除成功,共删除 {$result} 条");
        } else {
            return redirect()->back()->withErrors("删除失败或没有数据可被删除" . ($msg ? ',' . $msg : ''));
        }
    }

    public function postCheck(Request $request)
    {
        $check_by = $request->get('check_by', 'select');
        $status = $request->get('status', 1);
        $type = $request->get('type', 1);
        if ($status != 1 && $status != 3) {
            return redirect()->back()->withSuccess("操作失败");
        }

        $Model = DailyWage::class;
        if ($type == 2) {
            $Model = RealtimeWage::class;
        } elseif ($type == 3) {
            $Model = HourlyWage::class;
        } elseif ($type == 4) {
            $Model = FloatWages::class;
        }

        $txt = $status == 1 ? '确认' : '拒绝';
        $result = false;
        $msg = '';
        if ($check_by == 'select') {
            $ids = array_filter(explode(",", $request->input('select_ids', '')));
            if ($ids) {
                $result = $Model::whereIn('id', $ids)->where('status', 0)->update(['status' => $status]);
            } else {
                $msg = "请选择需要{$txt}的记录";
            }
        } else {
            $start_time = $request->input('start_time', '');
            if ($start_time) {
                $result = $Model::where('date', '=', $start_time)->where('status', 0)->update(['status' => $status]);
            } else {
                $msg = "时间不正确";
            }
        }
        if ($result) {
            return redirect()->back()->withSuccess("确认成功,共{$txt} {$result} 条");
        } else {
            return redirect()->back()->withErrors("{$txt}失败或没有数据可被{$txt}" . ($msg ? ',' . $msg : ''));
        }
    }
}
