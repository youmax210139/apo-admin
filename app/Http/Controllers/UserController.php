<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Service\API\Dividend\Dividend;
use Service\Facades\Message;
use Service\Models\Activity;
use Service\Models\Orders;
use Service\Models\OrderType;
use Service\Models\PaymentCategory;
use Service\Models\PaymentChannel;
use Service\Models\Projects;
use Service\Models\ThirdGamePlatform;
use Service\Models\User;
use Service\API\User as UserAPI;
use Service\Models\UserFreezeLog;
use Service\Models\UserFund;
use Service\Models\UserLevel;
use Service\Models\UserPrizeLevel;
use Service\Models\UserProfile;
use Service\Models\UserRebates;
use Service\Models\UserQuotas;
use Service\Models\UserRebatesLog;
use Service\Models\UserType;
use Service\Models\UserGroup;
use Service\Models\UserBanks;
use Service\Models\UserDomains;
use Service\API\User as APIUser;
use Illuminate\Support\Facades\DB;
use Cache;

class UserController extends Controller
{
    protected $fields = [];

    public function getIndex(Request $request)
    {
        $user_type = UserType::all();
        $user_group = UserGroup::all();
        return view('user.index', [
            'user_type' => $user_type,
            'user_group' => $user_group,
            'id' => (int)$request->get('id', 0)
        ]);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['id'] = (int)$request->get('id');
            $param['username'] = strtolower(trim($request->get('username')));
            $param['user_type_id'] = (int)$request->get('user_type_id');
            $param['user_group_id'] = (int)$request->get('user_group_id');
            $param['created_start_date'] = $request->get('created_start_date');
            $param['created_end_date'] = $request->get('created_end_date');
            $param['frozen_start_date'] = $request->get('frozen_start_date');
            $param['frozen_end_date'] = $request->get('frozen_end_date');
            $param['balance_min'] = $request->get('balance_min');
            $param['balance_max'] = $request->get('balance_max');
            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');
            $param['is_search'] = $request->get('is_search');
            $param['include_all'] = $request->get('include_all', 0);
            $param['sub_recharge_status'] = $request->get('sub_recharge_status', 0);
            $data = [];
            $where = [];
            $self_where = [];

            $team_count_where = [];
            $team_count_query = User::query();
            $team_count_sql = '';
            if (!empty($param['username'])) {
                $user_where = [];
                if (is_numeric($param['username']) && !User::where('username', '=', $param['username'])->exists()) {
                    if ($param['username'] > 2147483647 || $param['username'] < 0) {
                        $param['username'] = 0;    //超过int4最大值查询会报错
                    }
                    $user_where[] = ['users.id', '=', $param['username']];
                } else {
                    $user_where[] = ['users.username', '=', $param['username']];
                }

                $param['id'] = User::where($user_where)->value('id');

                $team_count_where[] = ['users.parent_tree', '@>', $param['id']];
                $team_count_query = $team_count_query->where($team_count_where);

                if ($param['include_all']) {
                    $team_count_sql = $team_count_query->select(DB::raw('jsonb_array_elements_text(parent_tree) as user_id'))->toSql();

                    $where[] = ['users.parent_tree', '@>', $param['id']];
                } else {
                    $parent_tree = User::where('id', $param['id'])->value('parent_tree');
                    $level = $parent_tree ? count(json_decode($parent_tree, true)) : 0;
                    $level_2 = $level + 1;
                    $team_count_sql = $team_count_query
                        ->select(DB::raw("unnest(array[parent_tree->>{$level}, parent_tree->>{$level_2}]) as user_id"))
                        ->toSql();
                    $where[] = ['users.parent_id', '=', $param['id']];
                }
                $self_where[] = array('users.id', '=', $param['id']);
            }

            if (!empty($param['user_type_id']) && $param['user_type_id'] != 'all') {
                $where[] = ['users.user_type_id', '=', $param['user_type_id']];
            }
            if (!empty($param['user_group_id']) && $param['user_group_id'] != 'all') {
                $where[] = ['users.user_group_id', '=', $param['user_group_id']];
            }
            if ($param['created_start_date']) {
                $where[] = ['users.created_at', '>=', $param['created_start_date']];
            }
            if ($param['created_end_date']) {
                $where[] = ['users.created_at', '<=', $param['created_end_date']];
            }
            if ($param['frozen_start_date']) {
                $where[] = ['users.frozen_at', '>=', $param['frozen_start_date']];
            }
            if ($param['frozen_end_date']) {
                $where[] = ['users.frozen_at', '<=', $param['frozen_end_date']];
            }
            if ($param['frozen_start_date'] || $param['frozen_end_date']) {
                $where[] = ['users.frozen', '>', 0];
            }

            $count_join_user_fund = false;
            if ($param['balance_min']) {
                $count_join_user_fund = true;
                $where[] = ['user_fund.balance', '>=', $param['balance_min']];
            }

            if ($param['balance_max']) {
                $count_join_user_fund = true;
                $where[] = ['user_fund.balance', '<=', $param['balance_max']];
            }
            if ($param['sub_recharge_status']) {
                $where[] = ['users.sub_recharge_status', '>', 0];
            }
            if (empty($param['is_search'])) {
                $where[] = ['users.parent_id', '=', $param['id']];

                if ($param['id'] > 0) {
                    $team_count_where[] = ['users.parent_tree', '@>', $param['id']];
                    $team_count_query = $team_count_query->where($team_count_where);
                    $parent_tree = User::where('id', $param['id'])->value('parent_tree');
                    $level = count(json_decode($parent_tree), true);
                    $level_2 = $level + 1;
                    $team_count_sql = $team_count_query
                        ->select(DB::raw("unnest(array[parent_tree->>{$level}, parent_tree->>{$level_2}]) as user_id"))
                        ->toSql();
                    $self_where[] = array('users.id', '=', $param['id']);
                } else {
                    $team_count_sql = $team_count_query->select(DB::raw('parent_tree->>0 as user_id'))->toSql();
                }
            }

            switch ($columns[$order[0]['column']]['data']) {
                case 'username':
                    $order_field = 'users.username';
                    break;
                case 'lottery_rebate':
                    $order_field = 'user_rebates.value';
                    break;
                case 'balance':
                    $order_field = 'user_fund.balance';
                    break;
                case 'created_at':
                    $order_field = 'users.created_at';
                    break;
                case 'last_time':
                    $order_field = 'users.last_time';
                    break;
                case 'user_id':
                default:
                    $order_field = 'users.id';
            }

            // 有效团队人数
            $team_count_data = [];
            if (!empty($team_count_sql)) {
                $team_count = DB::select(
                    "
                    select
                        user_id,
                        count(1) as count
                    from (
                         {$team_count_sql}
                    ) as users
                    where
                          user_id is not null
                    group by
                          user_id;
                  ",
                    $team_count_query->getBindings()
                );

                foreach ($team_count as $count) {
                    // 团队人数(因为包含自身+1)
                    $team_count_data[$count->user_id] = $count->count + 1;
                }
            }

            // 计总
            $query = User::query();
            if ($count_join_user_fund) {
                $query = $query->leftJoin('user_fund', 'users.id', 'user_fund.user_id')
                    ->where($where);
            } else {
                $query = $query->where($where);
            }
            $data['recordsTotal'] = $data['recordsFiltered'] = $query->count();

            // 彩种返点
            $where[] = ['user_rebates.type', '=', 'lottery'];

            $data['data'] = User::select(
                ['users.id as user_id',
                    'users.user_group_id',
                    'users.username',
                    'users.parent_tree',
                    'users.created_at',
                    'users.frozen',
                    'users.user_type_id',
                    DB::raw('user_group.name as user_group_name'),
                    'users.is_pay_whitelist',
                    'users.sub_recharge_status',
                    'users.last_time',
                    'users.last_active',
                    'users.last_session',
                    'user_fund.balance',
                    'user_fund.hold_balance',
                    'user_fund.points',
                    DB::raw('"user_profile"."value" as "google_key"'),
                    'user_profile1.value as user_observe',
                    'user_profile2.value as dividend_lock',
                    'user_rebates.value as lottery_rebate',
                    'user_profile3.value as telephone',
                    'user_profile4.value as ban_add_user',
                ]
            )
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->leftJoin('user_fund', 'users.id', 'user_fund.user_id')
                ->leftJoin('user_profile', function ($join) {
                    $join->on('users.id', '=', 'user_profile.user_id')
                        ->on('user_profile.attribute', '=', DB::raw("'google_key'"));
                })
                ->leftJoin('user_profile as user_profile1', function ($join) {
                    $join->on('users.id', '=', 'user_profile1.user_id')
                        ->where('user_profile1.attribute', 'user_observe');
                })
                ->leftJoin('user_profile as user_profile2', function ($join) {
                    $join->on('users.id', '=', 'user_profile2.user_id')
                        ->where('user_profile2.attribute', 'dividend_lock');
                })
                ->leftJoin('user_profile as user_profile3', function ($join) {
                    $join->on('users.id', '=', 'user_profile3.user_id')
                        ->where('user_profile3.attribute', 'telephone');
                })->leftJoin('user_profile as user_profile4', function ($join) {
                    $join->on('users.id', '=', 'user_profile4.user_id')
                        ->where('user_profile4.attribute', 'ban_add_user');
                })
                ->leftJoin('user_rebates', 'users.id', 'user_rebates.user_id')
                ->where($where)
                ->skip($start)->take($length)
                //->orderBy($order_field, $order[0]['dir'])
                ->orderByRaw($order_field . ($order[0]['dir'] == 'asc' ? ' asc nulls last' : ' desc nulls last'))
                ->get();

            $self = null;
            if ($self_where) { // 单独查询，这样查看下一页用户的时候，仍然会显示上级用户信息
                $self = User::select(
                    ['users.id as user_id',
                        'users.user_group_id',
                        'users.username',
                        'users.parent_tree',
                        'users.created_at',
                        'users.frozen',
                        'users.user_type_id',
                        DB::raw('user_group.name as user_group_name'),
                        'users.is_pay_whitelist',
                        'users.sub_recharge_status',
                        'users.last_time',
                        'users.last_active',
                        'users.last_session',
                        'user_fund.balance',
                        'user_fund.hold_balance',
                        'user_fund.points',
                        DB::raw('"user_profile"."value" as "google_key"'),
                        'user_profile1.value as user_observe',
                        'user_profile2.value as dividend_lock',
                        'user_rebates.value as lottery_rebate',
                        'user_profile3.value as telephone',
                        'user_profile4.value as ban_add_user',
                    ]
                )
                    ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                    ->leftJoin('user_fund', 'users.id', 'user_fund.user_id')
                    ->leftJoin('user_profile', function ($join) {
                        $join->on('users.id', '=', 'user_profile.user_id')
                            ->on('user_profile.attribute', '=', DB::raw("'google_key'"));
                    })
                    ->leftJoin('user_profile as user_profile1', function ($join) {
                        $join->on('users.id', '=', 'user_profile1.user_id')
                            ->where('user_profile1.attribute', 'user_observe');
                    })
                    ->leftJoin('user_profile as user_profile2', function ($join) {
                        $join->on('users.id', '=', 'user_profile2.user_id')
                            ->where('user_profile2.attribute', 'dividend_lock');
                    })
                    ->leftJoin('user_profile as user_profile3', function ($join) {
                        $join->on('users.id', '=', 'user_profile3.user_id')
                            ->where('user_profile3.attribute', 'telephone');
                    })
                    ->leftJoin('user_profile as user_profile4', function ($join) {
                        $join->on('users.id', '=', 'user_profile4.user_id')
                            ->where('user_profile4.attribute', 'ban_add_user');
                    })
                    ->leftJoin('user_rebates', function ($join) {
                        $join->on('users.id', '=', 'user_rebates.user_id')
                            ->where('user_rebates.type', 'lottery');
                    })
                    ->where($self_where)
                    ->first();
            }

            if ($param['id'] > 0 && empty($param['is_search'])) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByParentID($param['id'], false);
            } elseif ($param['username']) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByUserName($param['username'], false);
            }


            // 类型、组别
            $user_type = UserType::all();
            $user_type_id2name = array();
            foreach ($user_type as $item) {
                $user_type_id2name[$item->id] = $item->name;
            }

            foreach ($data['data'] as $key => $user) {
                $parent_tree_array = json_decode($user->parent_tree);
                $data['data'][$key]->user_level = empty($parent_tree_array) ? '总代' : (count($parent_tree_array)) . ' 级' . $user_type_id2name[$user->user_type_id];
                $data['data'][$key]->lottery_rebate = ($data['data'][$key]->lottery_rebate * 100) . '%';
                $data['data'][$key]->online_status = ($user->last_active > (string)Carbon::now()->subMinutes(5) && !empty($user->last_session)) ? '在线' : '离线';
                $data['data'][$key]->self = 0;
                $data['data'][$key]->telephone = $data['data'][$key]->telephone ? true : false;
                if (isset($team_count_data[$user->user_id])) {
                    $data['data'][$key]->team_count = $team_count_data[$user->user_id];
                } elseif (!empty($team_count_sql)) {
                    // 团队人数(因为包含自身+1)
                    $data['data'][$key]->team_count = 1;
                }
                // 如果用户未锁定则检查用户是否有未发放的分红
                if (empty($data['data'][$key]->dividend_lock) && Dividend::hasUnReviewed($user->user_id)) {
                    $data['data'][$key]->dividend_lock = -1;
                }
            }

            $data['data'] = $data['data']->toArray();

            if ($self) {
                $parent_tree_array = json_decode($self->parent_tree);
                $self->user_level = empty($parent_tree_array) ? '总代' : (count($parent_tree_array)) . ' 级' . $user_type_id2name[$self->user_type_id];
                $self->lottery_rebate = ($self->lottery_rebate * 100) . '%';
                $self->online_status = ($self->last_active > (string)Carbon::now()->subMinutes(5) && !empty($self->last_session)) ? '在线' : '离线';
                $self->self = 1;
                if (isset($team_count_data[$self->user_id])) {
                    $self->team_count = $team_count_data[$self->user_id];
                } elseif (!empty($team_count_sql)) {
                    // 团队人数(因为包含自身+1)
                    $self->team_count = 1;
                }
                // 如果用户未锁定则检查用户是否有未发放的分红
                if (empty($self->dividend_lock) && Dividend::hasUnReviewed($self->user_id)) {
                    $self->dividend_lock = -1;
                }

                array_unshift($data['data'], $self); // 确保是在第一行
            }

            return response()->json($data);
        }
    }

    public function getDetail(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        if (!empty($user->parent_id) && is_numeric($user->parent_id)) {
            $user->parent_username = User::find($user->parent_id)->username;
        }
        if (!empty($user->top_id)) {
            $user->top_username = User::find($user->top_id)->username;
        }
        //今日充值金额
        $user->today_deposit = Orders::where('from_user_id', $user->id)
            ->where('created_at', '>=', Carbon::today())
            ->whereIn('order_type_id', [16, 17, 18, 19, 20, 21, 22])->sum('amount');
        //今日提现金额
        $user->today_withdrawal = Orders::where('from_user_id', $user->id)
            ->where('created_at', '>=', Carbon::today())
            ->where('order_type_id', 29)->sum('amount');
        //今日投注
        $user->today_bet = Projects::where('user_id', $user->id)->where('created_at', '>=', Carbon::today())
            ->where('is_cancel', 0)->sum('total_price');
        //今日派奖
        $user->today_bonus = Projects::where('user_id', $user->id)->where('created_at', '>=', Carbon::today())
            ->where('is_cancel', 0)->sum('bonus');
        // 团队余额
        $user->teambalance = user::
        where('parent_tree', '@>', $id)
            ->leftJoin('user_fund', 'users.id', 'user_fund.user_id')
            ->sum("user_fund.balance");

        $user->teambalance += $user->fund->balance; // 加上自身的

        $user->prize_level = UserAPI::getTopPrizeLevel($user->top_id);

        $user->lottery_rebate = $user->rebates()->where('type', 'lottery')->first()->value;

        $parent_tree_array = json_decode($user->parent_tree);
        $user->user_level = empty($parent_tree_array) ? '总代' : (count($parent_tree_array)) . ' 级' . $user->type->name;

        $user_profile = UserAPI::getProfile($id);
        foreach ($user_profile as $_k => $_v) {
            if ($_k == 'telephone') {
                $adminids_array = array_filter(explode(',', get_config('visible_telephone_adminids', '')), 'trim');
                if (!in_array(auth()->id(), $adminids_array)) {
                    $_v = hide_str($_v, 3, 4);
                }
            }
            if ($_k == 'email') {
                $adminids_array = array_filter(explode(',', get_config('visible_email_adminids', '')), 'trim');
                if (!in_array(auth()->id(), $adminids_array)) {
                    $_v = hide_str(strstr($_v, '@', true), 3, 4) . strstr($_v, '@');
                }
            }
            if ($_k == 'weixin') {
                $adminids_array = array_filter(explode(',', get_config('visible_weixin_adminids', '')), 'trim');
                if (!in_array(auth()->id(), $adminids_array)) {
                    $_v = hide_str($_v, 3, 4);
                }
            }
            if ($_k == 'qq') {
                $adminids_array = array_filter(explode(',', get_config('visible_qq_adminids', '')), 'trim');
                if (!in_array(auth()->id(), $adminids_array)) {
                    $_v = hide_str($_v, 3, 4);
                }
            }
            $user->$_k = $_v;
        }
        $freeze_log = UserFreezeLog::where('user_id', $id)->orderBy('id', 'desc')->limit(25)->get();
        return view('user.detail', [
            'user' => $user,
            'id' => $id,
            'freeze_log' => $freeze_log
        ]);
    }

    /**
     * 用户银行卡信息
     * @param Request $request
     * @return type
     */
    public function getBanks(Request $request)
    {
        $id = (int)$request->get('id', 0);

        $user = User::find($id);
        if (empty($user)) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $apiUser = new \Service\API\User();
        $userBanks = $apiUser->getUserBanksByUid($id);
        $visible_bank_adminids = get_config('visible_bank_account_adminids', ''); //银行卡号可见的管理员id
        $visible_name_adminids = get_config('visible_bank_account_name_adminids', ''); //银行卡开户姓名可见的管理员id
        $visible_bank_adminids_arr = $visible_bank_adminids ? explode(',', $visible_bank_adminids) : [];
        $visible_name_adminids_arr = $visible_name_adminids ? explode(',', $visible_name_adminids) : [];
        foreach ($userBanks as &$v) {
            if (!in_array(auth()->id(), $visible_name_adminids_arr)) {
                $v->account_name = hide_str($v->account_name, -1, 1);
            }
            if (!in_array(auth()->id(), $visible_bank_adminids_arr)) {
                $v->account = hide_str($v->account, 0, strlen($v->account) - 4);
            }
        }
        return view('user.banks', [
            'user_banks' => $userBanks,
            'user' => $user,
        ]);
    }

    /**
     * 修改银行卡
     * @param Request $request
     * @return type
     */
    public function getEditBank(Request $request)
    {
        $id = (int)$request->get('id', 0);

        $bank = UserBanks::find($id);
        if (empty($bank)) {
            return redirect('/user\/')->withErrors("该银行卡信息不存在");
        }
        $user = User::select(['id', 'username'])->find($bank->user_id);
        if (empty($user)) {
            return redirect('/user\/banks\/?id=' . $bank->user_id)->withErrors("找不到该用户");
        }
        $banks = \Service\Models\Bank::select(['id', 'name'])->where('disabled', false)->get();
        $regions = \Service\Models\Region::select(['id', 'name', 'parent_id'])->get();
        $regions2 = [];
        foreach ($regions as $v) {
            $regions2[$v->parent_id][] = $v;
        }
        return view('user.editbank', [
            'bank' => $bank,
            'user' => $user,
            'banks' => $banks,
            'regions' => json_encode($regions2, JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * 修改银行卡
     * @param Request $request
     * @return type
     */
    public function putEditBank(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $bank_id = (int)$request->get('bank_id', 0);
        $province_id = (int)$request->get('province_id', 0);
        $city_id = (int)$request->get('city_id', 0);
        $district_id = (int)$request->get('district_id', 0);
        $branch = trim($request->get('branch', ''));
        $account_name = trim($request->get('account_name', ''));
        $account = trim($request->get('account', ''));

        if (empty($id)) {
            return redirect('/user\/editbank\/?id=' . $id)->withErrors("该银行卡信息不存在");
        }
        if (empty($bank_id)) {
            return redirect('/user\/editbank\/?id=' . $id)->withErrors("请选择开户银行");
        }
        if (empty($branch)) {
            return redirect('/user\/editbank\/?id=' . $id)->withErrors("请填写支行名称");
        }
        if (empty($account_name)) {
            return redirect('/user\/editbank\/?id=' . $id)->withErrors("请填写持卡人姓名");
        }
        if (empty($account)) {
            return redirect('/user\/editbank\/?id=' . $id)->withErrors("请填写银行卡号");
        }
        $bank = UserBanks::find($id);
        if (empty($bank)) {
            return redirect('/user\/editbank\/?id=' . $id)->withErrors("该银行卡信息不存在");
        }
        $banks = \Service\Models\Bank::select(['id'])->where('disabled', false)->where('id', $bank_id)->first();
        if (empty($banks)) {
            return redirect('/user\/editbank\/?id=' . $id)->withErrors("开户银行不可用");
        }
        $bank->bank_id = $bank_id;
        $bank->province_id = $province_id;
        $bank->city_id = $city_id;
        $bank->district_id = $district_id;
        $bank->branch = $branch;
        $bank->account_name = $account_name;
        $bank->account = $account;
        if ($bank->save()) {
            return redirect('/user\/banks\/?id=' . $bank->user_id)->withSuccess("修改银行卡成功");
        } else {
            return redirect('/user\/editbank\/?id=' . $id)->withErrors("保存失败");
        }
    }

    /**
     * 解除绑定银行卡
     * @param Request $request
     * @return type
     */
    public function putUnbundleBank(Request $request)
    {
        $id = (int)$request->get('unbundleid', 0);
        $userid = (int)$request->get('userid', 0);
        $reason = $request->get('reason', '');
        if (empty($reason)) {
            return redirect('/user/banks\/?id=' . $userid)->withErrors("请输入解绑原因");
        }
        $userbank = UserBanks::find($id);
        if (!$userbank || $userbank->status != 1) {
            return redirect('/user/banks\/?id=' . $userid)->withErrors("找不到该银行卡记录");
        }
        $userbank->status = 2;
        $userbank->reason = $reason;
        $userbank->save();
        return redirect("/user/banks\/?id={$userbank->user_id}")->withSuccess('解绑成功');
    }

    /**
     * 删除用户银行卡
     * @param Request $request
     * @return type
     */
    public function putDelBank(Request $request)
    {
        $id = (int)$request->get('delid', 0);
        $userid = (int)$request->get('userid', 0);
        $reason = $request->get('reason', '');
        if (empty($reason)) {
            return redirect('/user/banks\/?id=' . $userid)->withErrors("请输入删除原因");
        }
        $userbank = UserBanks::find($id);
        if (!$userbank || $userbank->status != 2) {
            return redirect('/user/banks\/?id=' . $userid)->withErrors("找不到该银行卡记录");
        }
        $userbank->status = 3;
        $userbank->reason = $reason;
        $userbank->save();
        return redirect("/user/banks\/?id={$userbank->user_id}")->withSuccess('删除成功');
    }

    /**
     * 冻结或者解冻用户
     * @param Request $request
     */
    public function getFreeze(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $user_freeze_log = UserProfile::where('user_id', $user->id)->where('attribute', 'user_freeze_log')->value('value');
        $user->freeze_value = $user_freeze_log;
        return view('user.freeze', [
            'user' => $user,
            'id' => (int)$request->get('id', 0)
        ]);
    }

    /**
     * 冻结或者解冻用户(执行)
     * @param Request $request
     */
    public function putFreeze(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $flag = $request->get('flag', '');
        $reason = $request->get('reason', '');
        $user = User::find($id);
        if (!$user || empty($flag)) {
            return redirect('/user/freeze\/?id=' . $id)->withErrors("找不到该用户");
        }
        if ($flag == 'freeze' && $user->frozen <> 0) {
            return redirect('/user/freeze\/?id=' . $id)->withErrors("用户已经冻结！");
        }
        if ($flag == 'unfreeze' && $user->frozen == 0) {
            return redirect('/user/freeze\/?id=' . $id)->withErrors("用户已经解冻！");
        }
        if ($flag == 'freeze') {
            $freeze = (int)$request->get('freeze', 0);
            $freeze_type = (int)$request->get('freezetype', 0);
            $user->frozen = $freeze_type;
            $user->frozen_reason = $reason;
            $user->frozen_at = date("Y-m-d H:i:s");
            $user->save();
            //所有下级
            if ($freeze == 2) {
                $underlings = user::where('parent_tree', '@>', $id)->where('frozen', 0)->get();
                $insert_log = [];
                $user_profile_log = [];
                foreach ($underlings as $underling) {
                    $insert_log[] = [
                        'user_id' => $underling->id,
                        'freeze_type' => $freeze_type,
                        'reason' => $reason . ' [所有下级]',
                        'admin' => auth()->user()->username,
                        'created_at' => Carbon::now()
                    ];
                    $user_profile_log[] = [
                        'user_id' => $underling->id,
                        'attribute' => 'user_freeze_log',
                        'value' => '--用户:' . $user->username . '--时间:' . Carbon::now()
                    ];
                }
                DB::table('user_freeze_log')->insert($insert_log);
                DB::table('user_profile')->insert($user_profile_log);
                user::where('parent_tree', '@>', $id)
                    ->where('frozen', 0)
                    ->update(
                        ['frozen' => $freeze_type,
                            'frozen_reason' => $reason,
                            'frozen_at' => date("Y-m-d H:i:s")
                        ]
                    );
            } elseif ($freeze == 3) {//直属下级
                $underlings = user::where('parent_id', $id)->where('frozen', 0)->get();
                $insert_log = [];
                $user_profile_log = [];
                foreach ($underlings as $underling) {
                    $insert_log[] = [
                        'user_id' => $underling->id,
                        'freeze_type' => $freeze_type,
                        'reason' => $reason . ' [直属下级]',
                        'admin' => auth()->user()->username,
                        'created_at' => Carbon::now()
                    ];
                    $user_profile_log[] = [
                        'user_id' => $underling->id,
                        'attribute' => 'user_freeze_log',
                        'value' => '--用户:' . $user->username . '--时间:' . Carbon::now()
                    ];
                }
                DB::table('user_freeze_log')->insert($insert_log);
                DB::table('user_profile')->insert($user_profile_log);
                user::where('parent_id', $id)
                    ->where('frozen', 0)
                    ->update(
                        ['frozen' => $freeze_type,
                            'frozen_reason' => $reason,
                            'frozen_at' => date("Y-m-d H:i:s")
                        ]
                    );
            }
            $user_freeze_log = new UserFreezeLog();
            $user_freeze_log->user_id = $id;
            $user_freeze_log->freeze_type = $freeze_type;
            $user_freeze_log->reason = $reason;
            $user_freeze_log->admin = auth()->user()->username;
            $user_freeze_log->save();
            DB::table('user_profile')->insert([
                'user_id' => $id,
                'attribute' => 'user_freeze_log',
                'value' => '--用户:' . $user->username . '--时间:' . Carbon::now()
            ]);
            return redirect("/user/freeze\/?id={$id}")->withSuccess('冻结用户成功！');
        } elseif ($flag == 'unfreeze') {
            $freeze = (int)$request->get('freeze', 0);
            $freeze_type = 0;
            $user->frozen = $freeze_type;
            $user->unfrozen_reason = $reason;
            $user->save();
            if ($freeze == 2) {
                $underlings = user::where('parent_tree', '@>', $id)->where('frozen', '>', 0)->get();
                $insert_log = [];
                $children = [];
                foreach ($underlings as $underling) {
                    $insert_log[] = [
                        'user_id' => $underling->id,
                        'freeze_type' => $freeze_type,
                        'reason' => $reason . ' [所有下级]',
                        'admin' => auth()->user()->username,
                        'created_at' => Carbon::now()
                    ];
                    $children[] = $underling->id;
                }
                DB::table('user_profile')->where('attribute', 'user_freeze_log')->whereIn('user_id', $children)->delete();
                DB::table('user_freeze_log')->insert($insert_log);
                user::where('parent_tree', '@>', $id)
                    ->where('frozen', '>', 0)
                    ->update(
                        ['frozen' => 0,
                            'unfrozen_reason' => $reason,
                        ]
                    );
            } elseif ($freeze == 3) {
                $underlings = user::where('parent_id', $id)->where('frozen', '>', 0)->get();
                $insert_log = [];
                $children = [];
                foreach ($underlings as $underling) {
                    $insert_log[] = [
                        'user_id' => $underling->id,
                        'freeze_type' => $freeze_type,
                        'reason' => $reason . ' [直属下级]',
                        'admin' => auth()->user()->username,
                        'created_at' => Carbon::now()
                    ];
                    $children[] = $underling->id;
                }
                DB::table('user_freeze_log')->insert($insert_log);
                DB::table('user_profile')->where('attribute', 'user_freeze_log')->whereIn('user_id', $children)->delete();
                user::where('parent_id', $id)
                    ->where('frozen', '>', 0)
                    ->update(
                        ['frozen' => $freeze_type,
                            'frozen_reason' => $reason,
                            'frozen_at' => date("Y-m-d H:i:s")
                        ]
                    );
            }
            $user_freeze_log = new UserFreezeLog();
            $user_freeze_log->user_id = $id;
            $user_freeze_log->freeze_type = $freeze_type;
            $user_freeze_log->reason = $reason;
            $user_freeze_log->admin = auth()->user()->username;
            $user_freeze_log->save();
            DB::table('user_profile')->where('user_id', $id)->where('attribute', 'user_freeze_log')->delete();
            return redirect("/user/freeze\/?id={$id}")->withSuccess('解冻用户成功！');
        }
    }

    /**
     * 人工充值
     * @param Request $request
     */
    public function getRecharge(Request $request)
    {
        //用户人工充值选项
        $recharge_options = get_user_hand_options('recharge');
        $order_types = OrderType::whereIn('ident', $recharge_options)
            ->where(function ($query) {
                $query->where('operation', '=', 1)
                    ->orWhere('hold_operation', '=', 1);
            })
            ->get(['ident','name']);
        if ($order_types->isEmpty()) {
            return redirect()->back()->withErrors("无效的人工充值帐变类型");
        }
        //整理充值渠道与通道
        $payment_categories = PaymentCategory::where('status', true)->orderBy('id', 'asc')->get(['id','name']);
        $payment_channels = PaymentChannel::where('status', true)->orderBy('id', 'asc')->get(['id','name','front_name','payment_category_id']);
        //查询用户数据
        $id = (int)$request->get('id', 0);//用户ID
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        return view('user.recharge', [
            'user' => $user,
            'activities' => Activity::all(['id', 'name']),
            'order_types' => $order_types,
            'payment_categories' => $payment_categories,
            'payment_channels' => $payment_channels,

        ]);
    }

    /**
     * 人工充值(执行)
     * @param Request $request
     */
    public function putRecharge(Request $request)
    {
        $money = (float)$request->get('money', 0);
        $user_id = (int)$request->get('userid', 0);
        $order_type = $request->get('ordertype', '');
        $description = $request->get('description', '');
        $cardnotice = $request->get('cardnotice', '');
        $activity_id = $request->get('activity_id', 0);
        $payment_category_id = $request->get('payment_category_id', 0);//三方充值
        $payment_channel_id = $request->get('payment_channel_id', 0);

        if (empty($user_id) || empty($order_type) || empty($money) || empty($description)) {
            return redirect()->back()->withErrors("信息数据不完整！");
        }
        //判断是否为允许的人工充值帐变类型
        $recharge_options = get_user_hand_options('recharge');
        if(!in_array($order_type,$recharge_options)){
            return redirect()->back()->withErrors("无效的人工充值帐变类型！");
        }
        //促销充值必须指定活动ID
        if ($order_type == 'CXCZ' && empty($activity_id)) {
            return redirect()->back()->withErrors("请选择相关活动！");
        }
        //三方人工充值必须指定充值渠道与充值通道
        if ($order_type == 'SFRGCZ' && ( empty($payment_category_id) || empty($payment_channel_id)) ) {
            return redirect()->back()->withErrors("三方人工充值请指定充值渠道/通道！");
        }
        //查询用户数据
        $user = User::find($user_id);
        if (!$user) {
            return redirect()->back()->withErrors("找不到该用户");
        }
        $ordertype = \Service\Models\OrderType::where('ident', $order_type)->first();
        if (!$ordertype) {
            return redirect()->back()->withErrors("找不到账变类型");
        }
        if($ordertype->operation <> 1 && $ordertype->hold_operation <> 1 ){
            return redirect()->back()->withErrors("当前帐变类型不包含金额增加操作");
        }
        $second_verify = new \Service\Models\SecondVerifyList();
        $second_verify->user_id = $user_id;
        $second_verify->created_admin_id = Auth()->id();
        $second_verify->verify_type = 'recharge';
        $second_verify->data = json_encode($request->all());
        if (!$second_verify->save()) {
            return redirect()->back()->withErrors("充值失败！");
        }
        return redirect()->back()->withSuccess("操作成功，请联系管理员审核后生效");
    }

    /**
     * 扣款
     * @param Request $request
     */
    public function getDeduct(Request $request)
    {
        //用户人工充值选项
        $deduct_options = get_user_hand_options('deduct');
        $order_types = OrderType::whereIn('ident', $deduct_options)
            ->where(function ($query) {
                $query->where('operation', '=', 2)
                    ->orWhere('hold_operation', '=', 2);
            })
            ->get(['ident','name']);
        if ($order_types->isEmpty()) {
            return redirect()->back()->withErrors("无效的人工扣减帐变类型");
        }

        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }

        $third_games = ThirdGamePlatform::select(['id', 'name'])->whereNotIn('ident', ['AvCloud', 'Wmc'])->get();

        return view('user.deduct', [
            'user' => $user,
            'activities' => Activity::all(['id', 'name']),
            'thirdgames' => $third_games,
            'order_types' => $order_types
        ]);
    }

    /**
     * 扣款执行
     * @param Request $request
     */
    public function putDeduct(Request $request)
    {
        $money = (float)$request->get('money', 0);
        $user_id = (int)$request->get('userid', 0);
        $order_type = $request->get('ordertype', '');
        $description = $request->get('description', '');
        $activity_id = $request->get('activity_id', 0);
        $third_game_platform_id = $request->get('third_game_platform_id', 0);
        if (empty($user_id) || empty($order_type) || empty($money) || empty($description)) {
            return redirect('/user\/')->withErrors("信息数据不完整！");
        }
        $deduct_options = get_user_hand_options('deduct');
        if(!in_array($order_type,$deduct_options)){
            return redirect()->back()->withErrors("无效的人工扣款选项");
        }
        if ($order_type == 'YCYLKJ' && empty($activity_id)) {
            return redirect()->back()->withErrors("请选择相关活动！");
        }
        if ($order_type == 'SFYCYLKJ' && empty($third_game_platform_id)) {
            return redirect()->back()->withErrors("请选择相关三方游戏平台！");
        }
        $user = User::find($user_id);
        if (!$user) {
            return redirect('/user\/deduct?id=' . $user_id)->withErrors("找不到该用户");
        }
        if ($user->fund->balance < $money) {
            return redirect('/user\/deduct?id=' . $user_id)->withErrors("用户余额不足！");
        }
        $ordertype = \Service\Models\OrderType::where('ident', $order_type)->first();
        if (!$ordertype) {
            return redirect('/user\/deduct?id=' . $user_id)->withErrors("找不到账变类型");
        }
        $second_verify = new \Service\Models\SecondVerifyList();
        $second_verify->user_id = $user_id;
        $second_verify->created_admin_id = Auth()->id();
        $second_verify->verify_type = 'deduct';
        $second_verify->data = json_encode($request->all());
        if (!$second_verify->save()) {
            return redirect('/user\/deduct?id=' . $user_id)->withErrors("扣款失败！");
        }
        return redirect('/user\/deduct?id=' . $user_id)->withSuccess("操作成功，请联系管理员审核后生效");
    }

    public function getChangepass(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        return view('user.changepass', [
            'user' => $user
        ]);
    }

    public function putChangepass(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $flag = $request->get('flag', '');
        $user = User::find($user_id);
        if (!$user) {
            return redirect('/user/changepass\/?id=' . $user_id)->withErrors("找不到该用户");
        }

        if ($flag == 'loginpass') {
            if (Hash::check($request->get('password'), $user->security_password)) {
                return redirect('/user/changepass\/?id=' . $user_id)->withErrors("登陆密码不能和资金密码一样！");
            }
        } else {
            if (Hash::check($request->get('security_password'), $user->password)) {
                return redirect('/user/changepass\/?id=' . $user_id)->withErrors("资金密码不能和登陆密码一样！");
            }
        }
        $second_verify = new \Service\Models\SecondVerifyList();
        $second_verify->user_id = $user_id;
        $second_verify->created_admin_id = Auth()->id();
        $second_verify->verify_type = 'changepass';
        $second_verify->data = json_encode($request->all());
        if (!$second_verify->save()) {
            return redirect('/user/changepass\/?id=' . $user_id)->withErrors("修改失败！");
        }
        return redirect('/user/changepass\/?id=' . $user_id)->withSuccess("操作成功，请联系管理员审核后生效");
    }

    public function getRebates(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $rebates_map = UserRebates::getRebateConfig();
        if ($user->parent_id) {
            $parent_rebates = APIUser::getRebates($user->parent_id);
            $top_user_level = APIUser::getTopPrizeLevel($user->top_id);
        } else {
            $top_user_level = APIUser::getTopPrizeLevel($user->id);
            foreach ($rebates_map as $k => $v) {
                if ($k == 'lottery') {
                    $rebates_map[$k]['limit'] = (2000 - $top_user_level) / 2000;
                } else {
                    $rebates_map[$k]['limit'] = get_config('third_game_rebate_limit', 0.012);
                }
                $rebates_map[$k]['limit'] *= 100;
            }
            $parent_rebates = $rebates_map;
        }
        $rebates = array();
        foreach ($user->rebates as $v) {
            $rebates[$v->type] = $v->value;
        }

        $operation_lottery_rebate_min_scale = get_config('operation_lottery_rebate_min_scale', 0.1);      //设置彩票返点滑条的最小刻度
        $operation_third_rebate_min_scale = get_config('operation_third_rebate_min_scale', 0.1);          //设置第三方游戏返点滑条的最小刻度

        return view('user.rebates', [
            'user' => $user,
            'rebates' => $rebates,
            'top_user_level' => $top_user_level,
            'parent_rebates' => $parent_rebates,
            'operation_lottery_rebate_min_scale' => $operation_lottery_rebate_min_scale,
            'operation_third_rebate_min_scale' => $operation_third_rebate_min_scale,
        ]);
    }

    public function putRebates(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $new_rebates = $request->get('rebates', []);
        $rebates_config = UserRebates::getRebateConfig();

        foreach ($new_rebates as $type => $value) {
            $new_rebates[$type] = floatval($value) ? round($value / 100, 4) : 0;
            if (!array_key_exists($type, $rebates_config)) {
                return redirect("/user/rebates\/?id={$id}")->withErrors("操作类型错误！");
            }
        }

        $user = User::find($id);
        if (!$user) {
            return redirect("/user/rebates\/?id={$id}")->withErrors("找不到该用户");
        }

        $tips = [];

        $lottery_rebate_min_diff = APIUser::getLotteryRebateMinDiff($id);

        //非总代
        if ($user->parent_id) {
            $parent_rebates = APIUser::getRebates($user->parent_id, '', 'decimal');
            foreach ($new_rebates as $k => $v) {
                if (!isset($parent_rebates[$k]) || !$parent_rebates[$k]['limit']) {
                    $new_rebates[$k] = 0;
                } else {
                    if ($k == "lottery") {
                        $lottery_rebate_max = $parent_rebates['lottery']['limit'] - $lottery_rebate_min_diff['parent'];
                        if ($v > $lottery_rebate_max) {
                            $tips[] = "[彩票]设置用户返点不能大于 " . $lottery_rebate_max * 100 . "%";
                        }
                    } else {
                        if ($v > $parent_rebates[$k]['limit']) {
                            $tips[] = "[{$parent_rebates[$k]['name']}]设置用户返点{$v}不能大于上级返点{$parent_rebates[$k]['limit']}！";
                        }
                    }
                }
            }
        } else {
            $top_user_level = APIUser::getTopPrizeLevel($user->id);
            foreach ($new_rebates as $type => $value) {
                if ($type == 'lottery') {
                    $limit = (2000 - $top_user_level) / 2000;
                } else {
                    $limit = get_config('third_game_rebate_limit', 0.012);
                }
                if ($limit < $value) {
                    $tips[] = "[{$rebates_config[$type]['name']}]总代返点不能大于系统设置的最大返点！";
                }
            }
        }

        $child_rebates = user::select(['user_rebates.type', DB::raw('max(user_rebates.value) as max_value')])
            ->leftjoin('user_rebates', 'users.id', 'user_rebates.user_id')
            ->where('users.parent_tree', '@>', $id)
            ->groupBy('user_rebates.type')->get();

        foreach ($child_rebates as $rebate) {
            if ($rebate->type == "lottery") {
                $lottery_rebate_min = $rebate->max_value + $lottery_rebate_min_diff['child'];
                if ($new_rebates['lottery'] < $lottery_rebate_min) {
                    $tips[] = "[彩票]设置用户返点不能小于 " . $lottery_rebate_min * 100 . "%";
                }
            } else {
                if (isset($new_rebates[$rebate->type]) && $new_rebates[$rebate->type] < $rebate->max_value) {
                    $tips[] = "[{$rebates_config[$rebate->type]['name']}]设置用户返点不能小于下级返点！";
                }
            }
        }

        if ($tips) {
            return redirect("/user/rebates\/?id={$id}")->withErrors($tips);
        }

        foreach ($new_rebates as $type => $value) {
            $user_rebate = UserRebates::where(['type' => $type, 'user_id' => $id])->first();
            if ($user_rebate) {
                $user_rebate->value = $value;
            } else {
                $user_rebate = new UserRebates;
                $user_rebate->user_id = $id;
                $user_rebate->type = $type;
                $user_rebate->value = $value;
            }
            $user_rebate->save();
        }
        return redirect("/user/rebates\/?id={$id}")->withSuccess("修改用户返点成功！");
    }

    public function postDelete(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            $data = [
                'status' => 1,
                'msg' => '用户不存在!',
            ];
            return response()->json($data);
        }
        if (count($user->childs)) {
            $data = [
                'status' => 1,
                'msg' => '不能删除！该用户存在下级！!',
            ];
            return response()->json($data);
        }
        $user->delete();
        if ($user->trashed()) {
            $data = [
                'status' => 0,
                'msg' => "删除用户【{$user->username}】成功！",
            ];
            return response()->json($data);
        }
    }

    public function getSecurityQuestion(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        return view('user.securitysuestion', [
            'user' => $user
        ]);
    }

    public function putSecurityQuestion(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $user->security_question()->delete();
        return redirect('/user\/SecurityQuestion?id=' . $id)->withSuccess("清空密保成功");
    }

    public function getPoints(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        return view('user.points', [
            'user' => $user
        ]);
    }

    public function putPoints(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $point = abs((int)$request->get('point', 0));
        $order_type = $request->get('order_type', 0);
        $description = $request->get('description', '');

        if (empty($point) || !in_array($order_type, array(0, 1)) || empty($description)) {
            return redirect('/user\/points?id=' . $id)->withErrors("信息数据不完整！");
        }
        $point_order = new \Service\Models\PointOrders;
        $point_order->relate_type = 2;
        $point_order->user_id = $id;
        $point_order->admin_id = auth()->id();
        $point_order->order_type = $order_type;
        $point_order->amount = $point;
        $point_order->description = $description;
        if (\Service\API\UserFund::modifyPoints($point_order, true)) {
            return redirect('/user\/?id=' . $user->parent_id)->withSuccess("操作成功!");
        }
        return redirect('/user\/?id=' . $user->parent_id)->withErrors("操作失败！");
    }

    public function getWithdrawallimit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        return view('user.withdrawallimit', [
            'user' => $user
        ]);
    }

    public function putWithdrawallimit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $num = (int)$request->get('num', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $user->withdrawal_num = $num;
        $user->save();
        return redirect('/user\/withdrawallimit?id=' . $id)->withSuccess("操作成功!");
    }

    public function getQuota(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $user_quotas = UserQuotas::leftjoin('quotas', 'user_quotas.quota_id', 'quotas.id')
            ->where('user_quotas.user_id', $id)
            ->get()
            ->toArray();
        $quotas = \Service\Models\Quotas::get()->toArray();
        foreach ($quotas as $key => $val) {
            foreach ($user_quotas as $k => $v) {
                if ($v['quota_id'] == $val['id']) {
                    if ($v['num'] > 0) {
                        $quotas[$key]['num'] = $v['num'];
                    } else {
                        $quotas[$key]['num'] = 0;
                    }
                    $quotas[$key]['user_quota_id'] = $v['id'];
                }
            }
        }

        return view('user.quota', [
            'user' => $user,
            'quotas' => $quotas
        ]);
    }

    public function putQuota(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $num = $request->get('num', '');
        $uagid = $request->get('uagid', '');
        $agid = $request->get('agid', '');
        foreach ($uagid as $key => $val) {
            if (empty($val)) {
                $acc = intval($num[$key]);
                $user_quotas = new UserQuotas;
                $user_quotas->quota_id = $agid[$key];
                $user_quotas->num = $acc;
                $user_quotas->user_id = $id;
                $user_quotas->save();
            } else {
                $acc = intval($num[$key]);
                \Illuminate\Support\Facades\DB::update("update user_quotas set num = num + ? where id = ?", [$acc, $uagid[$key]]);
            }
        }
        return redirect('/user/quota\/?id=' . $id)->withSuccess("操作成功!");
    }

    public function getSendMsg(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        return view('user.sendmsg', [
            'user' => $user,

        ]);
    }

    public function putSendmsg(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        $subject = $request->get('subject', '');
        $content = $request->get('content', '');
        $send_type = (int)$request->get('send_type', 0);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $msg = (object)[];
        $msg->sender_id = auth()->id();
        $msg->sender_name = auth()->user()->username;
        $msg->receiver_id = $id;
        $msg->subject = $subject;
        $msg->content = $content;
        $msg->sender_type = 1;
        $msg->send_type = $send_type;
        $msg->message_type = 0;
        $msg->send_at = date('Y-m-d H:i:s');
        if (Message::adminSend($msg)) {
            return redirect('/user/sendmsg?id=' . $id)->withSuccess("消息发送成功!");
        }
        return redirect('/user\/sendmsg?id=' . $id)->withErrors("消息发送失败！");
    }

    public function getDepositwhitelist(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        return view('user.depositwhitelist', [
            'user' => $user,
        ]);
    }

    public function putDepositwhitelist(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $type = (int)$request->get('type', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $user->is_pay_whitelist = $user->is_pay_whitelist ? false : true;
        $user->save();
        //所有下级操作
        if ($type) {
            User::where('parent_tree', '@>', $id)
                ->update(['is_pay_whitelist' => $user->is_pay_whitelist]);
        }
        return redirect('/user/depositwhitelist?id=' . $id)->withSuccess(($user->is_pay_whitelist ? '添加' : '取消') . "白名单成功!");
    }

    public function getDomains(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        if ($user->parent_id) {
            return redirect('/user\/')->withErrors("非总代不能分配域名");
        }
        $domains = \Service\Models\Domains::all();
        $user_domains = \Service\Models\UserDomains::select(['user_domains.*', 'domains.domain'])
            ->leftjoin('domains', 'domains.id', 'user_domains.domain_id')
            ->where('user_id', $id)
            ->get();
        return view('user.domains', [
            'user' => $user,
            'domains' => $domains,
            'user_domains' => $user_domains
        ]);
    }

    public function putAssignDomain(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        if ($user->parent_id) {
            return redirect('/user\/')->withErrors("非总代不能分配域名");
        }
        $domain_id = $request->get('domain_id', 0);
        if (!$domain_id) {
            return redirect('/user/domains?id=' . $id)->withErrors('请选择要分配的域名！');
        }
        $user_domain = UserDomains::where('domain_id', $domain_id)
            ->where('user_id', $id)
            ->first();
        if ($user_domain) {
            return redirect('/user/domains?id=' . $id)->withErrors("域名已经分配过了");
        }
        $user_domain = new UserDomains;
        $user_domain->user_id = $id;
        $user_domain->domain_id = $domain_id;
        $user_domain->save();
        return redirect('/user/domains?id=' . $id)->withSuccess("分配域名成功！!");
    }

    public function putRecoveryDomain(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        if ($user->parent_id) {
            return redirect('/user\/')->withErrors("非总代不能分配域名");
        }
        $domain_id = $request->get('domain_id', 0);
        if (!$domain_id) {
            return redirect('/user/domains?id=' . $id)->withErrors('请选择要回收的域名！');
        }
        UserDomains::where('user_id', $id)->where('domain_id', $domain_id)->delete();
        return redirect('/user/domains?id=' . $id)->withSuccess("域名回收成功！!");
    }

    public function getSubRechargeWhitelist(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        return view('user.subrechargewhitelist', [
            'user' => $user,
        ]);
    }

    public function putSubRechargeWhitelist(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $type = (int)$request->get('type', 0);
        $status = (int)$request->get('status', 0);
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => -1, 'msg' => '用户不存在！']);
        }
        //取消
        if ($user->sub_recharge_status) {
            if ($type == 1) {//自己和直属
                User::where('parent_id', $user->id)->update(['sub_recharge_status' => 0]);
            } elseif ($type == 2) {//自己和所有下级
                User::where('parent_tree', '@>', $user->id)->update(['sub_recharge_status' => 0]);
            }
            $user->sub_recharge_status = 0;
        } else {
            if ($status == 0) {
                $user->sub_recharge_status = 1;
            } else {
                $user->sub_recharge_status = 2;
            }
            if ($type == 1) {//自己和直属
                User::where('parent_id', $user->id)->update(['sub_recharge_status' => $user->sub_recharge_status]);
            } elseif ($type == 2) {//自己和所有下级
                User::where('parent_tree', '@>', $user->id)->update(['sub_recharge_status' => $user->sub_recharge_status]);
            }
        }
        $user->save();
        return response()->json(['status' => 0, 'msg' => '操作成功！']);
    }

    public function putGoogleKey(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = UserProfile::where('user_id', $id)->where('attribute', 'google_key')->first();
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户谷歌验证器");
        }
        $user->value = '';
        if ($user->save()) {
            return redirect('/user\/')->withSuccess("清除用户谷歌验证器成功！");
        }
        return redirect('/user\/')->withErrors("删除失败！");
    }

    public function getUserlevel(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'users.usernick',
            DB::raw("COALESCE(user_lottery_total.price,0) as price"),
            DB::raw("COALESCE(user_lottery_total.user_level_id,-1) as user_level_id"),
        ])
            ->leftJoin('user_lottery_total', 'users.id', 'user_lottery_total.user_id')
            ->where('users.id', $user_id)
            ->first();

        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }

        $user_data['user_level_list'] = UserLevel::all(['id', 'name', 'status']);
        $api_user = new APIUser();
        $user_data['user_level'] = $api_user->getUserLevel($user_id);
        return view('user.userlevel', $user_data);
    }

    public function postUserlevel(Request $request)
    {
        $user_id = (int)$request->get('user_id');
        $level_id = (int)$request->get('user_level_id');
        $lock = $request->get('lock_user_level', '');

        $api_user = new APIUser();
        $api_user->setUserLevel($user_id, $level_id, $lock);

        $data = [
            'status' => 0,
            'msg' => '修改成功!',
        ];

        return response()->json($data);
    }

    public function getUserobserve(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'users.usernick',
            'user_profile.value as comment',
        ])
            ->leftJoin('user_profile', function ($join) {
                $join->on('user_profile.user_id', '=', 'users.id')
                    ->where('user_profile.attribute', '=', 'user_observe');
            })
            ->where('users.id', $user_id)
            ->first();

        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }
        return view('user.userobserve', $user_data);
    }

    public function postUserobserve(Request $request)
    {
        $user_id = (int)$request->get('user_id');
        $comment = $request->get('observe_comment', '');
        $child = $request->get('child', '');
        APIUser::setProfile($user_id, 'user_observe', $comment);
        if ($child) {
            $users = User::where('parent_tree', '@>', $user_id)
                ->get();
            foreach ($users as $user) {
                try {
                    if ($comment) {
                        $user_profile = new UserProfile();
                        $user_profile->user_id = $user->id;
                        $user_profile->attribute = 'user_observe';
                        $user_profile->value = $comment;
                        $user_profile->save();
                    } else {
                        UserProfile::where('user_id', $user->id)->where('attribute', 'user_observe')->delete();
                    }
                } catch (\Exception $exception) {
                }
            }
        }

        $data = [
            'status' => 0,
            'msg' => '修改成功!',
        ];

        return response()->json($data);
    }

    /**
     * 生成试玩用户
     * @param Request $request
     */
    public function postCreateShiwan(Request $request)
    {
        set_time_limit(120);
        $top_user = new \Service\Models\User();
        $top_user->username = 'shiwan';
        $top_user->password = bcrypt('qazwsx1234');
        $top_user->usernick = '试玩总代';
        $top_user->user_group_id = 3;
        $top_user->user_type_id = 1;

        try {
            if ($top_user->save()) {
                //总代默认总代本身
                $top_user->top_id = $top_user->id;
                $top_user->save();
                //账户
                $user_fund = new \Service\Models\UserFund;
                $user_fund->user_id = $top_user->id;
                $user_fund->save();
                //保存返点
                $rebate = new \Service\Models\UserRebates;
                $rebate->user_id = $top_user->id;
                $rebate->type = 'lottery';
                $rebate->value = 0;
                $rebate->save();
                //奖级
                $prizelevel = new \Service\Models\UserPrizeLevel();
                $prizelevel->user_id = $top_user->id;
                $prizelevel->level = 1900;
                $prizelevel->save();
                for ($i = 1; $i <= 1000; $i++) {
                    $user = new \Service\Models\User();
                    $user->username = 'shiwan' . $i;
                    $user->password = bcrypt('a123456');
                    $user->usernick = '试玩用户' . $i;
                    $user->user_group_id = 3;
                    $user->user_type_id = 3;
                    $user->top_id = $top_user->id;
                    $user->parent_id = $top_user->id;
                    $user->parent_tree = json_encode([$top_user->id]);
                    if ($user->save()) {
                        //账户
                        $user_fund = new \Service\Models\UserFund;
                        $user_fund->user_id = $user->id;
                        $user_fund->save();
                        //保存返点
                        $rebate = new \Service\Models\UserRebates;
                        $rebate->user_id = $user->id;
                        $rebate->type = 'lottery';
                        $rebate->value = 0;
                        $rebate->save();
                    }
                }
            }
        } catch (\Exception $e) {
            $data = [
                'status' => -1,
                'msg' => '生成失败!' . $e->getMessage(),
            ];
            return response()->json($data);
        }

        $data = [
            'status' => 0,
            'msg' => '生成成功!',
        ];

        return response()->json($data);
    }

    public function postKickOut(Request $request)
    {
        $user = User::find($request->get('id'));

        if (!empty($user)) {
            $user->last_session = '';

            try {
                $user->save();
            } catch (\Exception $user) {
                $data = [
                    'status' => -1,
                    'msg' => '踢人失败!',
                ];

                return response()->json($data);
            }

            $data = [
                'status' => 0,
                'msg' => '踢人成功!',
            ];

            return response()->json($data);
        }

        $data = [
            'status' => -1,
            'msg' => '踢人失败!',
        ];

        return response()->json($data);
    }

    public function getPrizelevel(Request $request)
    {
        $user = User::find($request->get('user_id', 0));
        if (!$user || $user->parent_id != 0) {
            return redirect('/user\/')->withErrors("找不到用户或该用户不是总代");
        }
        $level = UserPrizeLevel::where('user_id', $user->id)->value('level');
        $rebate = UserRebates::where('type', 'lottery')->where('user_id', $user->id)->value('value');
        return view('user.prizelevel', [
            'user' => $user,
            'rebate' => $rebate,
            'level' => $level,
        ]);
    }

    public function postPrizelevel(Request $request)
    {
        $data = [
            'status' => -1,
            'msg' => '系统错误',
        ];
        $user = User::find($request->get('user_id', 0));
        if (!$user || $user->parent_id != 0) {
            $data['msg'] = '找不到用户或该用户不是总代';
            return response()->json($data);
        }
        $level_model = UserPrizeLevel::where('user_id', $user->id);
        $level = (int)$level_model->value('level');
        $new_level = $request->input('user_prize_level');
        $rebate_diff = ($level - $new_level) / 2000;
        if ($rebate_diff == 0) {
            $data['msg'] = '奖级没有发生变化';
            return response()->json($data);
        }
        $rebate_diff_abs = abs($rebate_diff);
        $rebate_condition = $rebate_diff > 0 ? "value + {$rebate_diff_abs}" : "value - {$rebate_diff_abs}";

        DB::beginTransaction();
        $level_model->update(['level' => $new_level]);
        $time = date("YmdHis");
        $sql = "CREATE TABLE user_rebates_backup_{$time} AS TABLE user_rebates";
        DB::query($sql);
        $sql = "
            WITH update_users AS(
                SELECT id FROM users WHERE parent_tree @> '{$user->id}' OR id = {$user->id}
            )
            UPDATE user_rebates
            SET value = (CASE WHEN {$rebate_condition} > 0 THEN {$rebate_condition} ELSE 0 END)
            FROM update_users
            WHERE
                user_rebates.user_id = update_users.id AND user_rebates.type = 'lottery';
        ";
        $result = DB::update($sql);
        if (empty($result)) {
            DB::rollBack();
            $data['msg'] = '更新返点数据失败';
            return response()->json($data);
        }
        DB::commit();

        $data['status'] = 0;
        $data['msg'] = '操作成功';
        return response()->json($data);
    }

    /**
     * 设置用户分红锁状态
     * @param Request $request
     */
    public function putDividendLock(Request $request)
    {
        $user_id = $request->get('user_id');
        $type = $request->get('type', 0);

        if (!empty($type) && $type == 'lock') {
            if (Dividend::lockUser($user_id)) {
                return response()->json([
                    'status' => 0,
                    'msg' => '分红锁 锁定成功！',
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'msg' => '分红锁 锁定失败！',
                ]);
            }
        } else {
            if (Dividend::unlockUser($user_id)) {
                return response()->json([
                    'status' => 0,
                    'msg' => '分红锁 解锁成功！',
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'msg' => '分红锁 解锁失败！',
                ]);
            }
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUnbindtelephone(Request $request)
    {
        $user_id = (int)$request->get('user_id', '');
        try {
            UserProfile::where('user_id', $user_id)->where('attribute', 'telephone')->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'msg' => '操作失败！',
            ]);
        }
        return response()->json([
            'status' => 0,
            'msg' => '解绑用户手机成功！',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function postGooglekey(Request $request)
    {
        $user_id = (int)$request->get('user_id', '');
        try {
            UserProfile::where('user_id', $user_id)->where('attribute', 'google_key')->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'msg' => '操作失败！',
            ]);
        }
        return response()->json([
            'status' => 0,
            'msg' => '解绑用户谷歌登录器成功！',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUnbindweixin(Request $request)
    {
        $user_id = (int)$request->get('user_id', '');
        try {
            UserProfile::where('user_id', $user_id)->where('attribute', 'weixin')->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'msg' => '操作失败！',
            ]);
        }
        return response()->json([
            'status' => 0,
            'msg' => '解绑用户微信成功！',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUnbindqq(Request $request)
    {
        $user_id = (int)$request->get('user_id', '');
        try {
            UserProfile::where('user_id', $user_id)->where('attribute', 'qq')->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'msg' => '操作失败！',
            ]);
        }
        return response()->json([
            'status' => 0,
            'msg' => '解绑用户QQ号码成功！',
        ]);
    }

    public function postUnbindemail(Request $request)
    {
        $user_id = (int)$request->get('user_id', '');
        try {
            UserProfile::where('user_id', $user_id)->where('attribute', 'email')->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'msg' => '操作失败！',
            ]);
        }
        return response()->json([
            'status' => 0,
            'msg' => '解绑用户Email成功！',
        ]);
    }

    public function getSetadduser(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'user_profile.value',
        ])
            ->leftJoin('user_profile', function ($join) {
                $join->on('user_profile.user_id', '=', 'users.id')
                    ->where('user_profile.attribute', '=', 'ban_add_user');
            })
            ->where('users.id', $user_id)
            ->first();

        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }
        return view('user.set-add-user', $user_data);
    }

    public function postSetadduser(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $type = (int)$request->get('type', 0);
        $ban = UserProfile::where('user_id', $user_id)->where('attribute', 'ban_add_user')->first();
        if ($ban) {
            //解除限制
            UserProfile::where('user_id', $user_id)
                ->where('attribute', 'ban_add_user')
                ->delete();
            if ($type) {
                UserProfile::leftJoin('users', 'users.id', 'user_profile.user_id')
                    ->where('users.parent_tree', '@>', $user_id)
                    ->where('user_profile.attribute', 'ban_add_user')
                    ->delete();
            }
        } else {
            //添加限制
            $user_data = User::select(['username'])->where('id', $user_id)->first();
            $user_profile = new UserProfile();
            $user_profile->user_id = $user_id;
            $user_profile->attribute = 'ban_add_user';
            $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
            $user_profile->save();
            if ($type) {
                $users = User::where('parent_tree', '@>', $user_id)
                    ->get();
                foreach ($users as $user) {
                    try {
                        $user_profile = new UserProfile();
                        $user_profile->user_id = $user->id;
                        $user_profile->attribute = 'ban_add_user';
                        $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
                        $user_profile->save();
                    } catch (\Exception $exception) {
                    }
                }
            }
        }
        $data = [
            'status' => 0,
            'msg' => '操作成功',
        ];
        return response()->json($data);
    }

    public function getAdduser(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $parent_rebates = APIUser::getRebates($user->id);
        $top_user_level = APIUser::getTopPrizeLevel($user->top_id);

        $rebates = array();
        foreach ($user->rebates as $v) {
            $rebates[$v->type] = $v->value;
        }

        $operation_lottery_rebate_min_scale = get_config('operation_lottery_rebate_min_scale', 0.1);      //设置彩票返点滑条的最小刻度
        $operation_third_rebate_min_scale = get_config('operation_third_rebate_min_scale', 0.1);          //设置第三方游戏返点滑条的最小刻度

        return view('user.adduser', [
            'user' => $user,
            'rebates' => $rebates,
            'top_user_level' => $top_user_level,
            'parent_rebates' => $parent_rebates,
            'operation_lottery_rebate_min_scale' => $operation_lottery_rebate_min_scale,
            'operation_third_rebate_min_scale' => $operation_third_rebate_min_scale,
        ]);
    }

    public function postAdduser(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return redirect('/user\/')->withErrors("找不到该用户");
        }
        $user_type = $request->get('user_type', 0);
        $usernames = strtolower($request->get('username', ''));
        $password = $request->get('password', '');
        $rebates = $request->get('rebates', []);
        if (!in_array($user_type, array(2, 3))) {
            return redirect()->back()->withErrors("请选择用户类型");
        }

        if (!preg_match('/^[\w\_]{4,16}$/u', $password)) {
            return redirect()->back()->withErrors("密码应为数字/字母/下划线组合的4-16个字符");
        }
        if (empty($rebates)) {
            return redirect()->back()->withErrors("返点输入不正确");
        }
        $usernames = explode(',', $usernames);
        foreach ($usernames as $username) {
            $username = strtolower(trim($username));
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9\_]{5,15}$/u', $username)) {
                return redirect()->back()->withErrors("用户名必须以字母开头，数字/字母/下划线组合的6-16个字符");
            }
            //检查用户名是否存在
            if (User::withTrashed()->where('username', $username)->count()) {
                return redirect()->back()->withErrors("用户名已被注册");
            }
        }
        //检查返点

        $check_rebate = APIUser::checkNewUserRebates($user->id, $rebates);
        if ($check_rebate !== true) {
            return redirect()->back()->withErrors($check_rebate);
        }
        DB::beginTransaction();
        $sub_recharge_status = (int)get_config('sub_recharge_status', 0);// 新开用户是否默认开启下级充值权限

        foreach ($usernames as $username) {
            $username = strtolower(trim($username));

            $parent_tree = json_decode($user->parent_tree);
            $parent_tree[count($parent_tree)] = $user->id;
            $new_user = new User;
            $new_user->top_id = $user->top_id == 0 ? $user->id : $parent_tree[0];
            $new_user->user_type_id = $user_type;
            $new_user->username = $username;
            $new_user->usernick = $username;
            $new_user->password = bcrypt($password);
            $new_user->parent_id = $user->id;
            $new_user->user_group_id = $user->user_group_id;
            $new_user->parent_tree = json_encode($parent_tree);
            $new_user->created_ip = '8.8.8.8';
            if (in_array($sub_recharge_status, [1, 2])) {
                $new_user->sub_recharge_status = $sub_recharge_status;//新开用户是否默认开启下级充值权限
            }
            try {
                if ($new_user->save()) {
                    //资金
                    $user_fund = new UserFund;
                    $user_fund->user_id = $new_user->id;
                    $user_fund->save();

                    //返点
                    $data = [];
                    foreach ($rebates as $type => $rebate) {
                        $data[$type]['user_id'] = $new_user->id;
                        $data[$type]['type'] = $type;
                        $data[$type]['value'] = $rebate['value'] / 100;
                    }
                    $res = UserRebates::insert($data);

                    if (!$res) {
                        DB::rollBack();
                        return redirect()->back()->withErrors("注册失败,写入第三方游戏返点失败！");
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->withErrors("注册失败请重试" . $e->getMessage());
            }
        }
        DB::commit();
        return redirect()->back()->withSuccess("添加成功");
    }

    /**
     * 单期投注限额
     * @param Request $request
     */
    public function getIssuelimitbet(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'user_profile.value',
        ])
            ->leftJoin('user_profile', function ($join) {
                $join->on('user_profile.user_id', '=', 'users.id')
                    ->where('user_profile.attribute', '=', 'issue_limit_bet');
            })
            ->where('users.id', $user_id)
            ->first();

        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }
        return view('user.issue-limit-bet', $user_data);
    }

    public function postIssuelimitbet(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $value = (float)$request->get('value', 0);
        $issue_limit_bet = UserProfile::where('user_id', $user_id)->where('attribute', 'issue_limit_bet')->first();
        if ($issue_limit_bet) {
            //解除限制
            $issue_limit_bet->value = $value;
            $issue_limit_bet->save();
        } else {
            //添加限制
            $user_profile = new UserProfile();
            $user_profile->user_id = $user_id;
            $user_profile->attribute = 'issue_limit_bet';
            $user_profile->value = $value;
            $user_profile->save();
        }
        $data = [
            'status' => 0,
            'msg' => '操作成功',
        ];
        return response()->json($data);
    }

    /**
     * 用户权限转账
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBantransfer(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'user_profile.value',
        ])
            ->leftJoin('user_profile', function ($join) {
                $join->on('user_profile.user_id', '=', 'users.id')
                    ->where('user_profile.attribute', '=', 'ban_transfer');
            })
            ->where('users.id', $user_id)
            ->first();
        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }
        return view('user.set-transfer', $user_data);
    }

    public function postBantransfer(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $type = (int)$request->get('type', 0);
        $ban = UserProfile::where('user_id', $user_id)->where('attribute', 'ban_transfer')->first();
        if ($ban) {
            //解除限制
            UserProfile::where('user_id', $user_id)
                ->where('attribute', 'ban_transfer')
                ->delete();
            if ($type) {
                UserProfile::leftJoin('users', 'users.id', 'user_profile.user_id')
                    ->where('users.parent_tree', '@>', $user_id)
                    ->where('user_profile.attribute', 'ban_transfer')
                    ->delete();
            }
        } else {
            //添加限制
            $user_data = User::select(['username'])->where('id', $user_id)->first();
            $user_profile = new UserProfile();
            $user_profile->user_id = $user_id;
            $user_profile->attribute = 'ban_transfer';
            $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
            $user_profile->save();
            if ($type) {
                $users = User::where('parent_tree', '@>', $user_id)
                    ->get();
                foreach ($users as $user) {
                    try {
                        $user_profile = new UserProfile();
                        $user_profile->user_id = $user->id;
                        $user_profile->attribute = 'ban_transfer';
                        $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
                        $user_profile->save();
                    } catch (\Exception $exception) {
                    }
                }
            }
        }
        $data = [
            'status' => 0,
            'msg' => '操作成功',
        ];
        return response()->json($data);
    }

    /**
     * 用户权限转账
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllowTransferToParent(Request $request)
    {

        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'user_profile.value',
        ])
            ->leftJoin('user_profile', function ($join) {
                $join->on('user_profile.user_id', '=', 'users.id')
                    ->where('user_profile.attribute', '=', 'allow_transfer_to_parent');
            })
            ->where('users.id', $user_id)
            ->first();
        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }
        return view('user.set-transfer-to-parent', $user_data);
    }

    public function postAllowTransferToParent(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $type = (int)$request->get('type', 0);
        $allow = UserProfile::where('user_id', $user_id)->where('attribute', 'allow_transfer_to_parent')->first();
        if ($allow) {
            //删除转账权限
            UserProfile::where('user_id', $user_id)
                ->where('attribute', 'allow_transfer_to_parent')
                ->delete();
            //添加禁止记录
            if ($type) {
                UserProfile::leftJoin('users', 'users.id', 'user_profile.user_id')
                    ->where('users.parent_tree', '@>', $user_id)
                    ->where('user_profile.attribute', 'allow_transfer_to_parent')
                    ->delete();
                $users = User::where('parent_tree', '@>', $user_id)
                    ->get();
            }
        } else {
            //添加转账权限
            $user_data = User::select(['username'])->where('id', $user_id)->first();
            $user_profile = new UserProfile();
            $user_profile->user_id = $user_id;
            $user_profile->attribute = 'allow_transfer_to_parent';
            $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
            $user_profile->save();
            if ($type) {
                $users = User::where('parent_tree', '@>', $user_id)
                    ->get();
                foreach ($users as $user) {
                    try {
                        $user_profile = new UserProfile();
                        $user_profile->user_id = $user->id;
                        $user_profile->attribute = 'allow_transfer_to_parent';
                        $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
                        $user_profile->save();
                    } catch (\Exception $exception) {
                    }
                }
            }
        }
        $data = [
            'status' => 0,
            'msg' => '操作成功',
        ];
        return response()->json($data);
    }

    /**
     * 用户权限提款
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBanwithdrawal(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'user_profile.value',
        ])
            ->leftJoin('user_profile', function ($join) {
                $join->on('user_profile.user_id', '=', 'users.id')
                    ->where('user_profile.attribute', '=', 'ban_withdrawal');
            })
            ->where('users.id', $user_id)
            ->first();

        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }
        return view('user.set-withdrawal', $user_data);
    }

    public function postBanwithdrawal(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $type = (int)$request->get('type', 0);
        $ban = UserProfile::where('user_id', $user_id)->where('attribute', 'ban_withdrawal')->first();
        if ($ban) {
            //解除限制
            UserProfile::where('user_id', $user_id)
                ->where('attribute', 'ban_withdrawal')
                ->delete();
            if ($type) {
                UserProfile::leftJoin('users', 'users.id', 'user_profile.user_id')
                    ->where('users.parent_tree', '@>', $user_id)
                    ->where('user_profile.attribute', 'ban_withdrawal')
                    ->delete();
            }
        } else {
            //添加限制
            $user_data = User::select(['username'])->where('id', $user_id)->first();
            $user_profile = new UserProfile();
            $user_profile->user_id = $user_id;
            $user_profile->attribute = 'ban_withdrawal';
            $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
            $result = $user_profile->save();
            if (empty($result)) {
                $data = [
                    'status' => 1,
                    'msg' => '保存数据失败',
                ];
                return response()->json($data);
            }
            if ($type) {
                $users = User::where('parent_tree', '@>', $user_id)
                    ->get();
                foreach ($users as $user) {
                    try {
                        $user_profile = new UserProfile();
                        $user_profile->user_id = $user->id;
                        $user_profile->attribute = 'ban_withdrawal';
                        $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
                        $user_profile->save();
                    } catch (\Exception $exception) {
                        $data = [
                            'status' => 1,
                            'msg' => '保存数据失败',
                        ];
                        return response()->json($data);
                    }
                }
            }
        }
        $data = [
            'status' => 0,
            'msg' => '操作成功',
        ];
        return response()->json($data);
    }

    /**
     * 用户开户限额
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdduserlimit(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user = User::find($user_id);
        if (!$user) {
            return '用户不存在！';
        }
        $level = count(json_decode($user->parent_tree));
        $default_limit = get_config('reg_limit_level_' . $level, 0);
        if (empty($default_limit)) {
            return '用户所在层级无限制！';
        }
        $user_data = (int)\Service\API\User::getProfile($user_id, 'adduserlimit');
        $total_limit = $default_limit + $user_data;
        $used_limit = User::where('parent_id', $user_id)->count();
        return view('user.adduserlimit', ['user' => $user, 'total_limit' => $total_limit, 'used_limit' => $used_limit]);
    }

    public function postAdduserlimit(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $num = (int)$request->get('num', 0);
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => 1,
                'msg' => '用户不存在',
            ]);
        }
        $level = count(json_decode($user->parent_tree));
        $default_limit = get_config('reg_limit_level_' . $level, 0);
        if (empty($default_limit)) {
            return response()->json([
                'status' => 1,
                'msg' => '用户所在层级无限制',
            ]);
        }
        $user_data = (int)\Service\API\User::getProfile($user_id, 'adduserlimit');
        \Service\API\User::setProfile($user_id, 'adduserlimit', $user_data + $num);
        return response()->json([
            'status' => 0,
            'msg' => '操作成功',
        ]);
    }

    public function getSkip_diff_ip_verify(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'user_profile.value',
        ])
            ->leftJoin('user_profile', function ($join) {
                $join->on('user_profile.user_id', '=', 'users.id')
                    ->where('user_profile.attribute', '=', 'skip_diff_ip_verify');
            })
            ->where('users.id', $user_id)
            ->first();

        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }
        return view('user.skip_diff_ip_verify', $user_data);
    }

    public function postSkip_diff_ip_verify(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $type = (int)$request->get('type', 0);
        $res = UserAPI::postSkipDiffIpVerify($user_id, $type);
        if (!$res) {
            return response()->json([
                'status' => 1,
                'msg' => '保存数据失败'
            ]);
        }
        return response()->json([
            'status' => 0,
            'msg' => '操作成功'
        ]);
    }

    public function getEditRemark(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'user_profile.value',
        ])
            ->leftJoin('user_profile', function ($join) {
                $join->on('user_profile.user_id', '=', 'users.id')
                    ->where('user_profile.attribute', '=', 'remark');
            })
            ->where('users.id', $user_id)
            ->first();

        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }
        return view('user.set-remark', $user_data);
    }

    public function postEditRemark(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $remark = trim($request->get('remark', ''));
        $remark = str_replace("\r\n", "\n", $remark);
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'status' => 1,
                'msg' => '用户不存在',
            ]);
        }
        if (mb_strlen($remark, 'UTF-8') > 64) {
            return response()->json([
                'status' => 1,
                'msg' => '备注内容不能超过64个字。保存文本字数：' . mb_strlen($remark, 'UTF-8'),
            ]);
        }
        $result = \Service\API\User::setProfile($user_id, 'remark', $remark);
        if ($result) {
            return response()->json([
                'status' => 0,
                'msg' => '保存成功',
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'msg' => '保存失败',
            ]);
        }
    }

    /**
     * 清零指定用户名下已经所有下级的指定游戏的返水
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function putClearTeamThirdRebate(Request $request)
    {
        $ident = (string)$request->get('ident', false);
        if ($ident === 'lottery') {
            return response()->json(['status' => '1', 'msg' => '彩票返点不可批量清零！']);
        }
        $user_id = (int)$request->get('user_id', false);
        if ($ident && $user_id) {
            DB::transaction(function () use ($user_id, $ident) {
                //手动写日志
                $user_rebate_log_model = new UserRebatesLog();
                $created_at = Carbon::now()->toDateTimeString();
                UserRebates::where('type', $ident)->whereIn('user_id', function ($query) use ($user_id) {
                    $query->select('id')->from('users')->where('parent_tree', '@>', $user_id)->orWhere('id', $user_id);
                })->chunk(1000, function ($users_rebate) use ($ident, $created_at, $user_rebate_log_model) {
                    $user_rebate_log_data = [];
                    foreach ($users_rebate as $user_rebate) {
                        $user_rebate_log = [];
                        $user_rebate_log['created_at'] = $created_at;
                        $user_rebate_log['old_value'] = $user_rebate->value;
                        $user_rebate_log['new_value'] = 0;
                        $user_rebate_log['type'] = $ident;
                        $user_rebate_log['user_id'] = $user_rebate->user_id;
                        $user_rebate_log['operator_type'] = 1;
                        $user_rebate_log['operator_id'] = isset(auth()->user()->id) ? auth()->user()->id : 0;
                        $user_rebate_log_data[] = $user_rebate_log;
                    }
                    $user_rebate_log_model->insert($user_rebate_log_data);
                });
                //批量更新

                UserRebates::where('type', $ident)->whereIn('user_id', function ($query) use ($user_id) {
                    $query->select('id')->from('users')->where('parent_tree', '@>', $user_id)->orWhere('id', $user_id);
                })->update(['value' => 0]);
            });

            return response()->json(['status' => '0', 'msg' => $ident . '清零成功']);
        }
        return response()->json(['status' => '1', 'msg' => '请稍后重试']);
    }

    public function getChangeToAgent(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user = User::find($user_id, ['id', 'username', 'user_type_id']);

        if (!$user) {
            return '用户不存在！';
        }

        if ($user->user_type_id != 3) {
            return '只有会员才可以转换身份';
        }

        $user_type = UserType::find($user->user_type_id);
        return view('user.change-to-agent', ['user' => $user, 'user_type' => $user_type]);
    }

    public function postChangeToAgent(Request $request)
    {
        try {
            $user_id = (int)$request->get('user_id', 0);

            DB::beginTransaction();
            $user = User::lockForUpdate()->find($user_id);

            if (!$user) {
                DB::rollBack();
                return response()->json(['status' => 1, 'msg' => '用户不存在']);
            }

            if ($user->user_type_id != 3) {
                DB::rollBack();
                return response()->json(['status' => 1, 'msg' => '该用户已经是代理']);
            }

            $user->user_type_id = 2;
            if ($user->save()) {
                DB::commit();
                return response()->json(['status' => 0, 'msg' => '操作成功']);
            }

            DB::rollBack();
            return response()->json(['status' => 1, 'msg' => '操作失败']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 1, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * get 开通或是关闭私返
     * 参考AllowTransferToParent与getBanwithdrawal这些类似功能
     * @param Request $request
     */
    public function getUserPrivateReturn(Request $request)
    {
        $user_id = (int)$request->get('id', 0);
        $user_data = User::select([
            'users.id',
            'users.username',
            'user_profile.value',
        ])
            ->leftJoin('user_profile', function ($join) {
                $join->on('user_profile.user_id', '=', 'users.id')
                    ->where('user_profile.attribute', '=', 'user_private_return');
            })
            ->where('users.id', $user_id)
            ->first();
        if (empty($user_data)) {
            return response()->json(array('status' => -1, 'msg' => '用户不存在！'));
        }
        return view('user.set-private-return', $user_data);
    }

    /**
     * post 开通或是关闭私返
     * @param Request $request
     */
    public function postUserPrivateReturn(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $type = (int)$request->get('type', 0);
        $allow = UserProfile::where('user_id', $user_id)->where('attribute', 'user_private_return')->first();
        if ($allow) {
            //删除转账权限
            UserProfile::where('user_id', $user_id)
                ->where('attribute', 'user_private_return')
                ->delete();
            //添加禁止记录
            if ($type) {
                UserProfile::leftJoin('users', 'users.id', 'user_profile.user_id')
                    ->where('users.parent_tree', '@>', $user_id)
                    ->where('user_profile.attribute', 'user_private_return')
                    ->delete();
                $users = User::where('parent_tree', '@>', $user_id)
                    ->get();
            }
        } else {
            //添加转账权限
            $user_data = User::select(['username'])->where('id', $user_id)->first();
            $user_profile = new UserProfile();
            $user_profile->user_id = $user_id;
            $user_profile->attribute = 'user_private_return';
            $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
            $user_profile->save();
            if ($type) {
                $users = User::where('parent_tree', '@>', $user_id)
                    ->get();
                foreach ($users as $user) {
                    try {
                        $user_profile = new UserProfile();
                        $user_profile->user_id = $user->id;
                        $user_profile->attribute = 'user_private_return';
                        $user_profile->value = '用户 ' . $user_data->username . ' · 时间 ' . date('Y-m-d H:i:s');
                        $user_profile->save();
                    } catch (\Exception $exception) {
                    }
                }
            }
        }
        $data = [
            'status' => 0,
            'msg' => '操作成功',
        ];
        return response()->json($data);
    }

    /**
     * 收回三方余额
     * @param Request $request
     */
    public function postRecyThirdBalance(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => -1, 'msg' => '找不到该用户!']);
        }
        if ($user->user_group_id != 1) {
            return response()->json(['status' => -1, 'msg' => '只能操作正式组用户!']);
        }

        $lock_key = 'recy_third_balance_' . $user->id;
        if (Cache::store('redis')->has($lock_key)) {
            return response()->json(['status' => -1, 'msg' => '处理中，请稍后!']);
        } else {
            Cache::store('redis')->put($lock_key, 1, 1);            //缓存1分钟
        }

        $third_games = \Service\API\ThirdGame\ThirdGame::getThirdGame('', 0, 'array', 0);
        $platforms = \Service\API\ThirdGame\ThirdGame::getPlatform();
        $err_msg = '';    //错误信息
        foreach ($third_games as $third_game) {
            if (!isset($platforms[$third_game['platform_ident']])) {
                $err_msg .= '|' . $third_game['platform_name'] . '平台被禁用';
                continue;
            }
            $platform_from = \Service\API\ThirdGame\Platform\Creator::factory($third_game['ident']);
            $balance = $platform_from->getUserBalance($user->id, true);
            if (empty($balance) || intval($balance) < 1) {
                continue;
            }

            $order = new \Service\API\ThirdGame\ThirdGameOrder();
            $order->setAttr([
                'from' => $platforms[$third_game['platform_ident']],
                'to' => $platforms['Master'],
                'user_id' => $user->id,
                'user_name' => $user->username,
                'amount' => $balance,
                'remark' => $third_game['platform_ident'] . '->Master',
            ]);
            if (!$order->transfer()) {
                $err_msg .= '|' . $third_game['platform_name'] . ':' . $order->getLastErrorMsg();
            }
        }

        Cache::store('redis')->forget($lock_key);

        if (empty($err_msg)) {
            return response()->json(['status' => 0, 'msg' => '操作完成!']);
        } else {
            return response()->json(['status' => -1, 'msg' => $err_msg]);
        }
    }
}
