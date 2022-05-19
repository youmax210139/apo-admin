<?php

namespace App\Http\Controllers;

use Service\API\PrivateReturn\UserPrivateReturnContract as UserPrivateReturnAPI;
use Service\Models\UserPrivateReturnContract as UserPrivateReturnModel;
use Service\Models\User as UserModel;
use Service\API\User as UserAPI;
use Service\Models\UserGroup;
use Service\Models\UserType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserprivatereturnController extends Controller
{
    public function getIndex(Request $request)
    {
        $user_group = UserGroup::all();
        $username = $request->get('username');
        return view('user-private-return.index', [
            'user_group' => $user_group,
            'username' => $username
        ]);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['username'] = $request->get('username');
            $param['include_all'] = $request->get('include_all', 0);
            $param['user_group_id'] = (int)$request->get('user_group_id');
            $param['is_search'] = $request->get('is_search');

            if ($param['is_search'] == 1) {
                $param['username'] = $request->get('username_input');
            }

            $where = [];
            $self_where = [];

            $param['user_id'] = UserModel::where('users.username', '=', $param['username'])->value('id');
            if (!empty($param['user_id'])) {
                if ($param['include_all']) {
                    $where[] = ['users.parent_tree', '@>', $param['user_id']];
                } else {
                    $where[] = ['users.parent_id', '=', $param['user_id']];
                }
                $self_where[] = array('users.id', '=', $param['user_id']);
            } else {
                return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
            }

            if (!empty($param['user_group_id'])) {
                $group_where = array('users.user_group_id', '=', $param['user_group_id']);
                $where[] = $group_where;
                $self_where[] = $group_where;
            }

            switch ($columns[$order[0]['column']]['data']) {
                default:
                    $order_field = 'user_private_return_contract.top_rate';
            }

            // 计总
            $query = UserModel::query();
            $query = $query->where($where);
            $data['recordsTotal'] = $data['recordsFiltered'] = $query->count();

            $data['data'] = UserModel::select([
                'users.id as user_id',
                'users.top_id',
                'users.username',
                'user_profile1.value as user_private_return',
                'users.parent_tree',
                'users.user_group_id',
                'users.user_type_id',
                'user_group.name as user_group_name',
                'user_private_return_contract.top_rate',
                'user_private_return_contract.status as status',
                'user_private_return_contract.created_at',
            ])
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->leftJoin('user_private_return_contract', function ($join) use ($param) {
                    $join->on('user_private_return_contract.user_id', '=', "users.id")
                        ->where('user_private_return_contract.status', '=', 0);
                })
                ->leftJoin('user_profile as user_profile1', function ($join) {
                    $join->on('users.id', '=', 'user_profile1.user_id')
                        ->where('user_profile1.attribute', 'user_private_return');
                })
                ->where($where)
                ->skip($start)->take($length)
                ->orderByRaw($order_field . ($order[0]['dir'] == 'asc' ? ' asc nulls last' : ' desc nulls last'))
                ->get();

            $self = null;
            if ($self_where) { // 单独查询，这样查看下一页用户的时候，仍然会显示上级用户信息
                $self = UserModel::select([
                    'users.id as user_id',
                    'users.top_id',
                    'users.username',
                    'user_profile1.value as user_private_return',
                    'users.parent_tree',
                    'users.user_group_id',
                    'users.user_type_id',
                    'user_group.name as user_group_name',
                    'user_private_return_contract.top_rate',
                    'user_private_return_contract.status as status',
                    'user_private_return_contract.created_at',
                ])
                    ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                    ->leftJoin('user_private_return_contract', function ($join) use ($param) {
                        $join->on('user_private_return_contract.user_id', '=', "users.id")
                            ->where('user_private_return_contract.status', '=', 0);
                    })
                    ->leftJoin('user_profile as user_profile1', function ($join) {
                        $join->on('users.id', '=', 'user_profile1.user_id')
                            ->where('user_profile1.attribute', '=', 'user_private_return');
                    })
                    ->where($self_where)
                    ->first();
            }

            // 类型、组别
            $user_type = UserType::all();
            $user_type_name = array();
            foreach ($user_type as $item) {
                $user_type_name[$item->id] = $item->name;
            }
            foreach ($data['data'] as $key => $user) {
                $parent_tree_array = json_decode($user->parent_tree);
                $data['data'][$key]->user_level = empty($parent_tree_array) ? '总代' : (count($parent_tree_array)) . ' 级' . $user_type_name[$user->user_type_id];
                if (!$data['data'][$key]->top_rate) {
                    $data['data'][$key]->top_rate = '-';
                }
                $data['data'][$key]->self = 0;
            }

            $data['data'] = $data['data']->toArray();

            if ($self) {
                $parent_tree_array = json_decode($self->parent_tree);
                $self->user_level = empty($parent_tree_array) ? '总代' : (count($parent_tree_array)) . ' 级' . $user_type_name[$self->user_type_id];

                if (!$self->top_rate) {
                    $self->top_rate = '-';
                }
                $self->self = 1;
                array_unshift($data['data'], $self); // 确保是在第一行
            }

            if ($param['username']) {
                $api_user = new UserAPI();
                $data['parent_tree'] = $api_user->getParentTreeByUserName($param['username'], false);
            }

            return response()->json($data);
        }
    }

    /**
     * 契约调整日志
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getRecord(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $user = UserModel::find($user_id);
        if (!$user) {
            return redirect('/userprivatereturn\/')->withErrors("找不到该用户");
        }

        $contracts = UserPrivateReturnModel::where(function ($query) use ($user_id) {
            $query->where("user_id", $user_id);
        })->orderBy('id', 'desc')->get();
        $money_unit = UserPrivateReturnAPI::$money_unit;
        $show_conditions = ['bet' => 1, 'active' => 1, 'profit' => 1, 'rate' => 1];
        foreach ($contracts as $contract_key => $contract_value) {
            $content = json_decode($contract_value->content, true);
            foreach ($content as $key => $value) {
                $value['bet'] = $value['bet'] / $money_unit;
                $value['profit'] = $value['profit'] / $money_unit;
                $content[$key] = $value;
            }

            $contract_value->time_type = UserPrivateReturnAPI::getTimeType($contract_value->time_type);
            $contract_value->condition_type = UserPrivateReturnAPI::getConditionType($contract_value->condition_type);
            $contract_value->cardinal_type = UserPrivateReturnAPI::getCardinalType($contract_value->cardinal_type);
            $contract_value->content = $content;
        }
        return view('user-private-return.record', [
            'logs' => $contracts,
            'user' => $user,
            'rate_unit' => UserPrivateReturnAPI::getRankUnit(),
            'show_conditions' => $show_conditions,
        ]);
    }

    public function getEdit(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $user = UserModel::find($user_id);
        if (!$user) {
            return redirect('/userprivatereturn\/')->withErrors("找不到该用户");
        }

        //读取本身的契约
        $user_contract = UserPrivateReturnModel::where(function ($query) use ($user_id) {
            $query->where("user_id", $user_id)
                ->where("status", 0);
        })->orderBy('id', 'desc')->first();
        if ($user_contract) {
            $money_unit = UserPrivateReturnAPI::$money_unit;
            $content = json_decode($user_contract->content, true);
            foreach ($content as $key => $value) {
                $value['bet'] = $value['bet'] / $money_unit;
                $value['profit'] = $value['profit'] / $money_unit;
                $content[$key] = $value;
            }
            $user_contract->content = $content;
        }

        $view_data = [];
        //最大私返比例
        $rate_limit = $user_contract->top_rate ?? get_config('private_return_rate_max', 0.05);
        $view_data = array_merge($view_data, [
            'rate_limit' => $rate_limit
        ]);

        // 私返比例步长
        $rate_step = get_config('private_return_rate_step', 0.1);
        $rate_config = array_merge($view_data, [
            'rate_step' => $rate_step,
            'rate_unit' => UserPrivateReturnAPI::getRankUnit(),
            'user' => $user,
            'contracts' => $user_contract ? $user_contract->content : [],
            'time_type' => $user_contract ? $user_contract->time_type : 1,
            'rate_type' => $user_contract ? $user_contract->condition_type : 1,
            'cardinal' => $user_contract ? $user_contract->cardinal_type : 1
        ]);

        return view('user-private-return.edit', $rate_config);
    }

    public function putEdit(Request $request)
    {
        $data = $request->all();
        $result = UserPrivateReturnAPI::save($data, 2);
        if ($result['status']) {
            return redirect()->back()->withSuccess('修改成功');
        } else {
            return redirect()->back()->withErrors("修改失败，{$result['msg']}");
        }
    }

    /**
     * 清除团队私返契约
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete(Request $request)
    {
        $user_id = (int)$request->get('user_id', 0);
        $user = UserModel::find($user_id);
        if (!$user) {
            return response()->json(['status' => -1, 'msg' => '找不到该用户!']);
        }

        UserPrivateReturnModel::where('user_id', $user_id)
            ->where('status', 0)
            ->update([
                'status' => 1,
                'stage' => 2,
                'deleted_at' => Carbon::now(),
                'deleted_username' => auth()->user()->username,
            ]);
        return response()->json(['status' => 0, 'msg' => '成功删除 ' . $user->username . ' 及其下级的私返契约!']);
    }
}
