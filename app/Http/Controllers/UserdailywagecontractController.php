<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\User;
use Service\Models\UserDailywageLine;
use Service\Models\UserType;
use Service\Models\UserGroup;
use Service\Models\UserDailywageContract;
use Service\API\DailyWage\UserDailyWageContract as ApiUserDailyWageContract;
use Carbon\Carbon;
use Service\API\User as APIUser;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserDailyWageContractLineRequest;

class UserdailywagecontractController extends Controller
{
    public function getIndex(Request $request)
    {
        $wage_line_multi_available = get_config('wage_line_multi_available', 0);//是否开启多线模式
        $wage_type_options = [];
        $lines = UserDailywageLine::all();
        if ($lines->isNotEmpty()) {
            foreach ($lines as $tmp_line) {
                $wage_type_options[] = $tmp_line->type;
            }
        }
        $wage_type_options = array_unique($wage_type_options);
        $user_group = UserGroup::all();
        $username = $request->get('username');
        return view('user-dailywage-contract.index', [
            'wage_type_options' => $wage_type_options,
            'user_group' => $user_group,
            'username' => $username,
            'wage_line_multi_available' => $wage_line_multi_available
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
            $param['wage_type'] = (int)$request->get('wage_type', 0);
            $param['is_search'] = $request->get('is_search');

            if ($param['is_search'] == 1) {
                $param['username'] = $request->get('username_input');
            }

            $where = [];
            $self_where = [];

            $param['user_id'] = User::where('users.username', '=', $param['username'])->value('id');
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
                    $order_field = 'user_dailywage_contract.top_rate';
            }

            // 计总
            $query = User::query();
            $query = $query->where($where);
            $data['recordsTotal'] = $data['recordsFiltered'] = $query->count();

            $data['data'] = User::select([
                'users.id as user_id',
                'users.top_id',
                'users.username',
                'user_profile1.value as user_observe',
                'users.parent_tree',
                'users.user_group_id',
                'users.user_type_id',
                'user_group.name as user_group_name',
                'user_dailywage_contract.top_rate',
                'user_dailywage_contract.type as wage_type',
                'user_dailywage_contract.status as wage_status',
                'user_dailywage_contract.created_at',
            ])
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->leftJoin('user_dailywage_contract', function ($join) use ($param) {
                    if ($param['wage_type']) {
                        $join->on('user_dailywage_contract.user_id', '=', "users.id")
                                ->where('user_dailywage_contract.type', '=', $param['wage_type'])
                                ->where('user_dailywage_contract.status', '=', 0);
                    } else {
                        $join->on('user_dailywage_contract.user_id', '=', "users.id")
                            ->where('user_dailywage_contract.status', '=', 0);
                    }
                })
                ->leftJoin('user_profile as user_profile1', function ($join) {
                    $join->on('users.id', '=', 'user_profile1.user_id')
                        ->where('user_profile1.attribute', 'user_observe');
                })
                ->where($where)
                ->skip($start)->take($length)
                ->orderByRaw($order_field . ($order[0]['dir'] == 'asc' ? ' asc nulls last' : ' desc nulls last'))
                ->get();

            $self_list = null;
            if ($self_where) { // 单独查询，这样查看下一页用户的时候，仍然会显示上级用户信息
                $self_list = User::select([
                    'users.id as user_id',
                    'users.top_id',
                    'users.username',
                    'user_profile1.value as user_observe',
                    'users.parent_tree',
                    'users.user_group_id',
                    'users.user_type_id',
                    'user_group.name as user_group_name',
                    'user_dailywage_contract.top_rate',
                    'user_dailywage_contract.type as wage_type',
                    'user_dailywage_contract.status as wage_status',
                    'user_dailywage_contract.created_at',
                ])
                    ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                    ->leftJoin('user_dailywage_contract', function ($join) use ($param) {
                        if ($param['wage_type']) {
                            $join->on('user_dailywage_contract.user_id', '=', "users.id")
                                ->where('user_dailywage_contract.type', '=', $param['wage_type'])
                                ->where('user_dailywage_contract.status', '=', 0);
                        } else {
                            $join->on('user_dailywage_contract.user_id', '=', "users.id")
                                ->where('user_dailywage_contract.status', '=', 0);
                        }
                    })
                    ->leftJoin('user_profile as user_profile1', function ($join) {
                        $join->on('users.id', '=', 'user_profile1.user_id')
                            ->where('user_profile1.attribute', '=', 'user_observe');
                    })
                    ->where($self_where)
                    ->get();
            }

            // 类型、组别
            $user_type = UserType::all();
            $user_type_name = array();
            foreach ($user_type as $item) {
                $user_type_name[$item->id] = $item->name;
            }
            foreach ($data['data'] as $key => $user) {
                $parent_tree_array = json_decode($user->parent_tree);
                if ($data['data'][$key]->wage_type) {
                    $data['data'][$key]->wage_type_label = __('wage.line_type_'.$data['data'][$key]->wage_type);
                } else {
                    $data['data'][$key]->wage_type_label = '';
                }
                $data['data'][$key]->user_level = empty($parent_tree_array) ? '总代' : (count($parent_tree_array)) . ' 级' . $user_type_name[$user->user_type_id];
                if (!$data['data'][$key]->top_rate) {
                    if ($line = ApiUserDailyWageContract::getLine($user->top_id)) {
                        if ($line->content && isset($line->content['default_contract']) && isset($line->content['default_level'])) {
                            $_contract = json_decode($line->content['default_contract']['value'], true);
                            $_level = count($parent_tree_array);
                            $this->_calculate_top_rate($_level,$line,$data['data'][$key],$_contract);
                        }
                    }
                }
                $data['data'][$key]->self = 0;
            }

            $data['data'] = $data['data']->toArray();

            foreach ($self_list as $self) {
                $parent_tree_array = json_decode($self->parent_tree);
                $self->user_level = empty($parent_tree_array) ? '总代' : (count($parent_tree_array)) . ' 级' . $user_type_name[$self->user_type_id];
                if ($self->wage_type) {
                    $self->wage_type_label = __('wage.line_type_'.$self->wage_type);
                } else {
                    $self->wage_type_label = '';
                }

                if (!$self->top_rate) {
                    if ($line = ApiUserDailyWageContract::getLine($self->top_id)) {
                        if ($line->content && isset($line->content['default_contract']) && isset($line->content['default_level'])) {
                            $_contract = json_decode($line->content['default_contract']['value'], true);
                            $_level = count($parent_tree_array);
                            $this->_calculate_top_rate($_level,$line,$self,$_contract);
                        }
                    }
                }
                $self->self = 1;
                array_unshift($data['data'], $self); // 确保是在第一行
            }

            if ($param['username']) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByUserName($param['username'], false);
            }

            return response()->json($data);
        }
    }

    private function _calculate_top_rate($user_level, $custom_line, $custom_user, $_contract) {
        $custom_level = $custom_line->content['default_level']['value'];
        
        if ($user_level <= $custom_level) {
            if (in_array($custom_line->type, [3,5,6,7])) {
                if (isset($_contract['default'])) {
                    if (isset($_contract['level_'.$user_level])) {
                        $_contract_level = $_contract['level_'.$user_level][0];
                        $custom_user->top_rate = isset($_contract_level['win_rate']) ? $_contract_level['win_rate'] : '';
                        $custom_user->top_rate .= '/'.isset($_contract_level['loss_rate']) ? $_contract_level['loss_rate'] : '';
                    } else if ($user_level == $custom_level) {
                        $_contract_default = $_contract['default'][0];
                        $custom_user->top_rate = isset($_contract_default['win_rate']) ? $_contract_default['win_rate'] : '';
                        $custom_user->top_rate .= '/'.isset($_contract_default['loss_rate']) ? $_contract_default['loss_rate'] : '';
                    } else {
                        $custom_user->top_rate = '-';
                    }
                } else {
                    if ($user_level == $custom_level) {
                        $_contract_default = $_contract[0];
                        $custom_user->top_rate = isset($_contract_default['win_rate']) ? $_contract_default['win_rate'] : '';
                        $custom_user->top_rate .= '/'.isset($_contract_default['loss_rate']) ? $_contract_default['loss_rate'] : '';
                    } else {
                        $custom_user->top_rate = '-';
                    }
                }
            } else {
                if (isset($_contract['default'])) {
                    if (isset($_contract['level_'.$user_level])) {
                        $_contract_level = $_contract['level_'.$user_level][0];
                        $custom_user->top_rate = isset($_contract_level['rate']) ? $_contract_level['rate'] : '';
                    } else if ($user_level == $custom_level) {
                        $_contract_default = $_contract['default'][0];
                        $custom_user->top_rate = isset($_contract_default['rate']) ? $_contract_default['rate'] : '';
                    } else {
                        $custom_user->top_rate = '-';
                    }
                } else {
                    if ($user_level == $custom_level) {
                        $_contract_default = $_contract[0];
                        $custom_user->top_rate = isset($_contract_default['rate']) ? $_contract_default['rate'] : '';
                    } else {
                        $custom_user->top_rate = '-';
                    }
                }
            }
        } else {
            $custom_user->top_rate = '-';
        }
    }

    /**
     * 清除团队工资
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelete(Request $request)
    {
        $wage_line_multi_available = get_config('wage_line_multi_available', 0);//是否开启多线模式
        $user_id = (int)$request->get('user_id', 0);
        $wage_type =  (int)$request->get('wage_type', 0);
        if (empty($wage_type)) {
            return response()->json(['status' => -1, 'msg' => '删除工资需要指定工资类型']);
        }
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['status' => -1, 'msg' => '找不到该用户!']);
        }

        UserDailywageContract::join('users', 'users.id', 'user_dailywage_contract.user_id')
            ->where(function ($query) use ($user_id, $wage_type) {
                $query->where('users.parent_tree', '@>', $user_id)
                    ->orWhere('user_id', $user_id);
                if ($wage_type) {
                    $query->where("type", $wage_type);
                }
            })
            ->where('status', 0)
            ->update([
                'status' => 2,
                'stage' => 2,
                'deleted_at' => Carbon::now(),
                'deleted_username' => auth()->user()->username,
            ]);
        return response()->json(['status' => 0, 'msg' => '成功删除 '.$user->username.' 及其下级的【'.__('wage.line_type_'.$wage_type).'】工资契约!']);
    }

    /**
     * 契约调整日志
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getRecord(Request $request)
    {
        $wage_line_create = $wage_line_multi_available = get_config('wage_line_multi_available', 0);//是否开启多线模式
        $user_id = (int)$request->get('user_id', 0);
        $wage_type = (int)$request->get('wage_type', 0);

        $user = User::find($user_id);
        if (!$user) {
            return redirect('/userdailywagecontract\/')->withErrors("找不到该用户");
        }

        //读取当前总代的所有工资线
        $lines = UserDailywageLine::where('top_user_id', $user->top_id)->get();
        $line = ApiUserDailyWageContract::getLine($user->top_id, 0, $wage_type);
        if (!$line) {
            return redirect()->back()->withErrors("请先设置总代日工资线路");
        }
        if (empty($wage_type)) {
            $wage_type = $line->type;
        }
        $contracts = UserDailywageContract::where(function ($query) use ($user_id, $wage_type) {
            $query->where("user_id", $user_id);
            if ($wage_type) {
                //如果指定了类型就获取指定类型的工资线，999是让他为空值
                $query->where("type", $wage_type);
            }
        })->orderBy('id', 'desc')->get();
        $show_conditions = [
            'bet' => 0 , 'active'  => 0, 'profit'   => 0, 'rate'   => 0,'win_rate'   => 0,'loss_rate'   => 0,
        ];
        foreach ($contracts as $key => $contract) {
            $contracts[$key]['content'] = json_decode($contract->content, true);
            foreach ($contracts[$key]['content'] as $content_condition) {
                foreach ($show_conditions as $condition_key => $show_enable) {
                    if (array_has($content_condition, $condition_key)) {
                        $show_conditions[$condition_key] = 1;
                    }
                }
            }
        }
        return view('user-dailywage-contract.record', [
            'wage_line_multi_available' => $wage_line_multi_available,
            'logs' => $contracts,
            'user' => $user,
            'lines' => $lines,
            'line_type' => $line->type,
            'show_conditions' => $show_conditions,
            'wage_line_create' => $wage_line_create,
        ]);
    }

    public function getEdit(Request $request)
    {
        $wage_line_multi_available = get_config('wage_line_multi_available', 0);//是否开启多线模式
        $user_id = (int)$request->get('user_id', 0);
        $wage_type =  (int)$request->get('wage_type', 0);
        $user = User::find($user_id);
        if (!$user) {
            return redirect('/userdailywagecontract\/')->withErrors("找不到该用户");
        }
        //读取当前总代的所有工资线
        $lines = UserDailywageLine::where('top_user_id', $user->top_id)->get();

        //读取本身的契约
        $user_contract = ApiUserDailyWageContract::getUserContract($user_id, '', $wage_type);
        if ((!empty($user_contract)) && empty($wage_type)) {
            //如果本身有契约了，就知道是什么类型了
            $wage_type = $user_contract->type;
        }
        $parent_user = [];
        $parent_contracts = [];

        if ($user->parent_id > 0) {
            $parent_user = $user->parentUser;
            $parent_contracts = ApiUserDailyWageContract::getUserContract($user->parent_id, '', $wage_type);
            if (!$parent_contracts) {
                return redirect()->back()->withErrors("上级未设置".__('wage.line_type_'.$wage_type)."契约[".__LINE__."]");
            }
            if (empty($wage_type)) {
                //如果本身没契约，就从上级拿默认契约类型
                $wage_type = $parent_contracts->type;
            }
        } else {
            return redirect()->back()->withErrors("总代限制设置日工资");
        }

        //读取当前工资线
        $line = ApiUserDailyWageContract::getLine($user->top_id, 0, $wage_type);
        if (!$line) {
            return redirect()->back()->withErrors("请先设置总代日工资线路");
        }
        
        $view_data = [];
        if ($line->type == 3) {
            $check_user_active = isset($line->content['check_user_active']['value']) ? $line->content['check_user_active']['value'] : 0;
            if ($user->parent_id == 0) {
                $default_controller = json_decode($line->content['default_contract']['value'], true);
                $dailywage_win_limit  = max(array_column($default_controller, 'win_rate'));
                $dailywage_loss_limit = max(array_column($default_controller, 'loss_rate'));
            } else {
                $dailywage_win_limit  = max(array_column($parent_contracts->content, 'win_rate'));
                $dailywage_loss_limit = max(array_column($parent_contracts->content, 'loss_rate'));
            }

            $view_data = array_merge($view_data, [
                'dailywage_win_limit'   => $dailywage_win_limit,
                'dailywage_loss_limit'  => $dailywage_loss_limit,
                'check_active_user'     => $check_user_active,
            ]);
        } elseif ($line->type == 5) {
            // 是否检查有效用户
            $check_user_active = $line->content['check_user_active']['value'] ?? 0;
            // 检查中单比例
            $check_win_rate = $line->content['check_win_rate']['value'] ?? 1;
            if ($user->parent_id == 0) {
                $default_controller = json_decode($line->content['default_contract']['value'], true);
                $dailywage_win_limit  = $check_win_rate ? max(array_column($default_controller, 'win_rate')) : 0;
                $dailywage_loss_limit = max(array_column($default_controller, 'loss_rate'));
            } else {
                $dailywage_win_limit  = $check_win_rate ? max(array_column($parent_contracts->content, 'win_rate')) : 0;
                $dailywage_loss_limit = max(array_column($parent_contracts->content, 'loss_rate'));
            }

            $view_data = array_merge($view_data, [
                'dailywage_win_limit'   => $dailywage_win_limit,
                'dailywage_loss_limit'  => $dailywage_loss_limit,
                'check_active_user'     => $check_user_active,
                'check_win_rate' => $check_win_rate,
            ]);
        } elseif (in_array($line->type, [6,7])) {
            // 是否检查销量
            $check_bet = $line->content['check_bet']['value']??1;//默认是开始销量检查
            //是否检查有效用户
            $check_user_active = $line->content['check_user_active']['value']??0;
            //检查中单比例
            $check_win_rate = isset($line->content['check_win_rate']['value']) ? $line->content['check_win_rate']['value'] : 1;
            $dailywage_win_limit = 0.0;
            if ($user->parent_id == 0) {
                $default_controller = json_decode($line->content['default_contract']['value'], true);
                if ($check_win_rate) {
                    $dailywage_win_limit  = max(array_column($default_controller, 'win_rate'));
                }
                
                $dailywage_loss_limit = max(array_column($default_controller, 'loss_rate'));
            } else {
                if ($check_win_rate) {
                    $dailywage_win_limit  = max(array_column($parent_contracts->content, 'win_rate'));
                }
                
                $dailywage_loss_limit = max(array_column($parent_contracts->content, 'loss_rate'));
            }

            $view_data = array_merge($view_data, [
                'dailywage_win_limit'   => $dailywage_win_limit,
                'dailywage_loss_limit'  => $dailywage_loss_limit,
                'check_bet'     => $check_bet,
                'check_active_user'     => $check_user_active,
                'check_win_rate' => $check_win_rate,
            ]);
        } elseif ($line->type == 2) {
            if ($user->parent_id == 0) {
                $default_controller = json_decode($line->content['default_contract']['value'], true);
                $dailywage_limit = max(array_column($default_controller, 'rate'));
            } else {
                $dailywage_limit = max(array_column($parent_contracts->content, 'rate'));
            }
            $dailywage_check_user = get_config('dailywage_check_user', 0) == 0;          //是否检查有效人数指标：0或者1

            $view_data = array_merge($view_data, [
                'dailywage_limit'        => $dailywage_limit,
                'contract_limit'         => json_decode($line->content['contract_limit']['value']),
                'dailywage_check_user'   => $dailywage_check_user,
            ]);
        } elseif ($line->type == 8) {
            // 是否判断销量
            $check_bet = $line->content['check_bet']['value'] ?? 0;
            // 是否判断亏损
            $check_profit = $line->content['check_profit']['value'] ?? 1;
            // 是否判断有效人数
            $check_user_active = $line->content['check_user_active']['value'] ?? 1;
            // 检查盈利工资比例
            $check_win_rate = $line->content['check_win_rate']['value'] ?? 1;
            // 检查亏损工资比例
            $check_loss_rate = $line->content['check_loss_rate']['value'] ?? 1;

            if ($user->parent_id == 0) {
                $default_controller = json_decode($line->content['default_contract']['value'], true);
                $dailywage_win_limit = $check_win_rate ? max(array_column($default_controller, 'win_rate')) : 0;
                $dailywage_loss_limit = $check_loss_rate ? max(array_column($default_controller, 'loss_rate')) : 0;
            } else {
                $dailywage_win_limit = $check_win_rate ? max(array_column($parent_contracts->content, 'win_rate')) : 0;
                $dailywage_loss_limit = $check_loss_rate ? max(array_column($parent_contracts->content, 'loss_rate')) : 0;
            }

            $view_data = array_merge($view_data, [
                'dailywage_win_limit' => $dailywage_win_limit,
                'dailywage_loss_limit' => $dailywage_loss_limit,
                'check_bet' => $check_bet,
                'check_profit' => $check_profit,
                'check_active_user' => $check_user_active,
                'check_win_rate' => $check_win_rate,
                'check_loss_rate' => $check_loss_rate,
            ]);
        } else {
            // 是否检查销量
            $check_bet = isset($line->content['check_bet']) ? $line->content['check_bet']['value'] : 1;
            $dailywage_limit = $user->parent_id ?
                (get_config('dailywage_cross_level') == 0 ? $parent_contracts->content[count($parent_contracts->content)-1]['rate']: $parent_contracts->top_rate )
                : $line->content['max_rate']['value'] ?? get_config('dailywage_limit', 2); //最大日工资比例
                $dailywage_check_user = get_config('dailywage_check_user', 0) == 0;          //是否检查有效人数指标：0或者1          //是否检查有效人数指标：0或者1

            $view_data = array_merge($view_data, [
                'dailywage_limit' => $dailywage_limit,
                'dailywage_check_user' => $dailywage_check_user,
                'check_bet' => $check_bet,
            ]);
        }

        $dailywage_step = get_config('dailywage_step', 0.1);                    // 日工资比例步长
        $dailywage_check_profit = (int)get_config('dailywage_check_profit', 0);  // 是否检查盈亏数据

        if ($line->type == 3) {
            //line-type3 的原始数组数据结构
            $line_type3_data = $parent_contracts['content'];//[['win_rate'=>0,'loss_rate'=>0,'active'=>0]];
            $check_user_active or $line_type3_data = [['win_rate'=>0,'loss_rate'=>0]];
        }

        if ($line->type == 5) {
            //line-type3 的原始数组数据结构
            $line_type3_data = [['win_rate'=>0,'loss_rate'=>0]];
        }

        if ($line->type == 6) {
            //line-type3 的原始数组数据结构
            $line_type3_data = [['bet'=>0,'win_rate'=>0,'loss_rate'=>0]];
        }

        $wage_step_key = 'wage_step';
        if (isset($line->content['hour_dailywage_step'])) {
            //兼容老平台
            $wage_step_key = 'hour_dailywage_step';
        }
        if (isset($line->content['realtime_wage_step'])) {
            //兼容老平台
            $wage_step_key = 'realtime_wage_step';
        }
        if (isset($line->content[$wage_step_key])) {
            $dailywage_step = isset($line->content[$wage_step_key]['value']) ? $line->content[$wage_step_key]['value'] : $dailywage_step;
        }

        //确认条件字段显示
        $demo_contract_content = $parent_contracts->content??$user_contract->content;
        $show_conditions = [
            'bet' => 0 , 'active'  => 0, 'profit'   => 0, 'rate'   => 0,'win_rate'   => 0,'loss_rate'   => 0,
        ];
        foreach ($demo_contract_content as $key => $content_condition) {
            foreach ($show_conditions as $condition_key => $show_enable) {
                if (array_has($content_condition, $condition_key)) {
                    $show_conditions[$condition_key] = 1;
                }
            }
        }
        $col_span_count = array_sum($show_conditions)+1;

        $daily_wage_config = array_merge($view_data, [
            'dailywage_step'        => $dailywage_step,
            'lines'                  => $lines,
            'line'                  => $line,
            'parent_user'           => $parent_user,
            'parent_contracts'      => $parent_contracts ? $parent_contracts->content : [],
            'user'                  => $user,
            'contracts'             => $user_contract ? $user_contract->content : (($line->type == 3) ? $line_type3_data :[]),
            'dailywage_check_profit'=> $dailywage_check_profit,
            'only_show_rate'        => (isset($line->content['only_show_rate']['value'])&&$line->content['only_show_rate']['value'])?true:false,
            'wage_line_multi_available' => $wage_line_multi_available ,
            'show_conditions' => $show_conditions ,
            'col_span_count' => $col_span_count ,
            'user_contract_type' => $user_contract->type??0,

        ]);

        return view('user-dailywage-contract.edit', $daily_wage_config);
    }

    public function putEdit(Request $request)
    {
        $data = $request->all();
        $result = ApiUserDailyWageContract::save($data, 2);
        if ($result['status']) {
            return redirect()->back()->withSuccess('修改成功');
        } else {
            return redirect()->back()->withErrors("修改失败，{$result['msg']}");
        }
    }

    /**
     * 修改成为多工资线，兼容单工资线
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getLine(Request $request)
    {
        $wage_line_create = $wage_line_multi_available = get_config('wage_line_multi_available', 0);//是否开启多线模式
        $all_line_types = $alternative_line_types = [1,6,4,3,2,5,7,8];//所有的工资类型 1：日工资 2：实时工资 3:小时工资-中挂单,4-小时工资-普通,5日工资-中挂单,6实时工资-中挂单,7奖期亏损工资,8日工资-盈亏
        $user_line_types = [];//用户当前拥有的工资线类型
        $user_id = (int)$request->get('user_id', 0);
        $wage_type    = (int)$request->get('daily_wage_line_type', 0);
        if (!$user = User::find($user_id)) {
            return redirect("userdailywagecontract")->withErrors("无效的总代ID");
        }
        if ($user->id <> $user->top_id) {
            return redirect("userdailywagecontract")->withErrors("只有总代可以设置工资线");
        }
        $lines = UserDailywageLine::where('top_user_id', $user_id)->get();
        if ($lines->isNotEmpty()) {
            foreach ($lines as $tmp_line) {
                $user_line_types[] = $tmp_line->type;
            }
        }
        if ($user_line_types) {
            $alternative_line_types = array_diff($all_line_types, $user_line_types);
            if (empty($alternative_line_types)) {
                //不允许显示添加按钮
                $wage_line_create = 0;
            }
        }
        $line_where = function ($query) use ($user_id, $wage_type, $user_line_types) {
            $query->where("top_user_id", $user_id);
            if ($wage_type) {
                //如果指定了类型就获取指定类型的工资线，999是让他为空值
                $query->where("type", $wage_type);
            }
        };
        $line = UserDailywageLine::where($line_where)->orderBy('created_at', 'asc')->first();
        if ($line) {
            $alternative_line_types = [$line->type];
            $is_edit = true;
            $line->content = json_decode($line->content, true);
        } else {
            if (empty($alternative_line_types)) {
                $url = "userdailywagecontract/line?user_id={$user_id}";
                //dd('没有可用的工资类型继续新增工资线');
                return redirect($url)->withErrors("没有可用的工资类型继续新增工资线");
            }

            $line = new UserDailywageLine();
            $line->name = '';
            $line->type = array_first($alternative_line_types);
            // 获取默认工资类型 1:日工资  2：实时工资
            $line->type = ($wage_type!=$line->type)?$wage_type:$line->type;
            $is_edit = false;
            $line->content = $this->getLineWageContent($line->type);
        }
        return view('user-dailywage-contract.line', [
            'user_id' => $user_id,
            'line' => $line,
            'user' => $user,
            'is_edit' => $is_edit,
            'wage_line_multi_available' => $wage_line_multi_available,
            'alternative_line_types' => $alternative_line_types,
            'all_line_types' => $all_line_types,
            'wage_line_create' => $wage_line_create,
            'lines' => $lines,

        ]);
    }

    public function putLine(UserDailyWageContractLineRequest $request)
    {
        $wage_type = (int)$request->post('daily_wage_line_type', get_config('dailywage_default_type', 1));
        $user_id = (int)$request->post('user_id', 0);
        $titles = $request->get('content_title');
        $keys = $request->get('content_key');
        $values = $request->get('content_value');
        $name = $request->post('name', '');
        $url = "userdailywagecontract/line?user_id={$user_id}&daily_wage_line_type=".$wage_type;
        if ($keys) {
            if (!in_array('default_level', $keys)) {
                return redirect($url)->withErrors('工资线配置必须包含 default_level 内容');
            }

            if (!in_array('default_contract', $keys)) {
                return redirect($url)->withErrors('工资线配置必须包含 default_contract 内容');
            }
        }
        unset($titles[0], $keys[0], $values[0]);
        $label = '设置';
        $content = [];
        if ($titles) {
            foreach ($titles as $_k => $_title) {
                $content[$keys[$_k]] = [
                    "title" => $titles[$_k],
                    "value" => $values[$_k]
                ];
            }
        }
        if(empty($content)){
            return redirect($url)->withErrors('工资线参数配置不能为空');
        }else{
            $default_contract = json_decode($content['default_contract']['value'],true);
            if(empty($default_contract)){
                return redirect($url)->withErrors('无效的默认契约 default_contract ');
            }
        }

        $line = UserDailywageLine::where('top_user_id', $user_id)->where('type', $wage_type)->first();
        if (!$line) {
            $label = '新增';
            $line = new UserDailywageLine();
            $line->type = $wage_type;
        }
        $line->top_user_id = $user_id;
        $line->name = $name;
        $line->content = json_encode($content);
        //dd($line->toArray());

        if ($line->content && $line->save()) {
            return redirect($url)->withSuccess($label.'操作成功');
        } else {
            return redirect($url)->withErrors($label."操作失败");
        }
    }

    public function deleteLine(Request $request)
    {
        $delete_line_top_user_id = (int)$request->post('delete_line_top_user_id', 0);
        $delete_line_type = $request->get('delete_line_type', 0);
        $delete_line_id = $request->get('delete_line_id', 0);
        if (empty($delete_line_top_user_id) || empty($delete_line_type)|| empty($delete_line_id)) {
            return redirect("userdailywagecontract")->withErrors("删除工资线各项参数不能为空");
        }

        DB::beginTransaction();
        try {
            $line_af = UserDailywageLine::where('id', '=', $delete_line_id)->where('top_user_id', $delete_line_top_user_id)
                ->where('type', $delete_line_type)->delete();
            $contract_af = UserDailywageContract::join('users', 'users.id', 'user_dailywage_contract.user_id')
                ->where(function ($query) use ($delete_line_top_user_id, $delete_line_type) {
                    $query->where('users.top_id', '=', $delete_line_top_user_id)
                        ->where('type', $delete_line_type);
                })
                ->where('status', 0)
                ->update([
                    'status' => 2,
                    'stage' => 2,
                    'deleted_at' => Carbon::now(),
                    'deleted_username' => auth()->user()->username,
                ]);
            DB::commit();
            $url = "userdailywagecontract/line?user_id={$delete_line_top_user_id}";
            return redirect($url)->withSuccess("成功删除{$line_af}条工资线，{$contract_af}份下级".__('wage.line_type_'.$delete_line_type)."契约");
        } catch (\Exception $e) {
            DB::rollBack();
            $url = "userdailywagecontract/line?user_id={$delete_line_top_user_id}&daily_wage_line_type=".$delete_line_type;
            return redirect($url)->withErrors("工资线删除失败");
        }
    }

    private function getLineWageContent($line_type)
    {

        $dailywage_check_profit = (int)get_config('dailywage_check_profit', 0);
        $max_rate = get_config('dailywage_limit', 3);
        $max_win_rate  = get_config('dailywage_max_win_rate', 1);
        $max_loss_rate = get_config('dailywage_max_loss_rate', 3);
        $line_wage_contents = [
            1 => [
                'unit'=>[
                    'title' =>'金额单位',
                    'value' => 1,
                ],
                'max_rate'=>[
                    'title' =>'最大比例限制',
                    'value' => '1.55',
                ],
                'default_level'=>[
                    'title' =>'默认层级',
                    'value' => '1',
                ],
                'only_show_rate'=>[
                    'title' =>'前台是否只允许调整比例',
                    'value' => '',
                ],
                'settled_rate_level'=>[
                    'title' =>'使用固定工资比例的层级(<=level)',
                    'value' => '-1',
                ],
                'start_deduct_level'=>[
                    'title' =>'开始扣减下级的层级(>level)',
                    'value' => '-1',
                ],
                'default_contract'=>[
                    'title' =>'默认契约',
                    'value' => '[{"bet":0,'.($dailywage_check_profit?'"profit":0,':'').'"active":0,"rate":1.55}]',
                ]
            ],
            2 => [
                'unit'=>[
                    'title' =>'金额单位',
                    'value' => 10000,
                ],
                'max_rate'=>[
                    'title' =>'最大比例限制',
                    'value' => get_config('realtime_wage_max_rate', '2.1'),
                ],
                'default_level'=>[
                    'title' =>'默认层级',
                    'value' => get_config('dailywage_pay_level_max', 1),
                ],
                'only_show_rate'=>[
                    'title' =>'前台是否只允许调整比例',
                    'value' => '1',
                ],
                'default_contract'=>[
                    'title' =>'默认契约',
                    'value' => '[{"bet_5":0,"bet_15":"0","active":0,"rate":'.get_config('realtime_wage_max_rate', 2.1).'}]',
                ],
                'contract_limit' => [
                    'title' =>'契约限制',
                    'value' => '{"1.8":{"bet_5":300,"bet_15":750,"active":10},"1.7":{"bet_5":25,"bet_15":100,"active":3}}',
                ]
            ],
            3 => [
                'default_level'=>[
                    'title' =>'默认层级',
                    'value' => '1',
                ],
                'default_contract'=>[
                    'title' =>'默认契约',
                    'value' => '[{"win_rate":'.$max_win_rate.',"loss_rate":'.$max_loss_rate.'}]',
                ],
                'max_rate'=>[
                    'title' => '最大比例限制',
                    'value' => 2.30,
                ],
                'check_user_active'=>[
                    'title' => '是否按活跃人数确定工资比例(1是|0否)',
                    'value' => 0,
                ],
                'wage_step'=>[
                    'title' => '小时工资步长',
                    'value' => 0.01,
                ],
                'hour_dailywage_multi'=>[
                    'title' => '契约比例是否可交错(1是|0否)',
                    'value' => 1,
                ],
                'hour_dailywage_cross_level'=>[
                    'title' => '工资是否多层条件(1是|0否)',
                    'value' => 1,
                ],
                'self_condtion_then_super'=>[
                    'title' => '限制自身达标条件小于上级(1是|0否)',
                    'value' => 1,
                ],
            ],
            4 => [
                'unit' => [
                    'title' => '金额单位',
                    'value' => 1,
                ],
                'max_rate' => [
                    'title' => '最大比例限制',
                    'value' => $max_rate,
                ],
                'default_level' => [
                    'title' => '默认层级',
                    'value' => '1',
                ],
                'only_show_rate' => [
                    'title' => '前台是否只允许调整比例',
                    'value' => 1,
                ],
                'settled_rate_level' => [
                    'title' => '使用固定工资比例的层级(<=level)',
                    'value' => '-1',
                ],
                'start_deduct_level' => [
                    'title' => '开始扣减下级的层级(>level)',
                    'value' => '-1',
                ],
                'default_contract' => [
                    'title' => '默认契约',
                    'value' => '[{"bet":0,' . ($dailywage_check_profit ? '"profit":0,' : '') . '"active":0,"rate":' . $max_rate . '}]',
                ]
            ],
            5 => [
                'default_level'=>[
                    'title' =>'默认层级',
                    'value' => '1',
                ],
                'default_contract'=>[
                    'title' =>'默认契约',
                    'value' => '[{"win_rate":'.$max_win_rate.',"loss_rate":'.$max_loss_rate.'}]',
                ],
                'max_rate'=>[
                    'title' => '最大比例限制',
                    'value' => 0.15,
                ],
                'wage_step'=>[
                    'title' => '工资步长',
                    'value' => 0.01,
                ],
            ],
            6 => [
                'default_level'=>[
                    'title' =>'默认层级',
                    'value' => '1',
                ],
                'default_contract'=>[
                    'title' =>'默认契约',
                    'value' => '[{"bet":10,"win_rate":'.$max_win_rate.',"loss_rate":'.$max_loss_rate.'}]',//bet：销量 单位：万
                ],
                'max_rate'=>[
                    'title' => '最大比例限制',
                    'value' => 0.40,
                ],

                'check_user_active'=>[
                    'title' => '是否判断活跃用户',
                    'value' => 0,
                ],
                'check_bet'=>[
                    'title' => '是否判断销量',
                    'value' => 1,
                ],
                'sort_condition'=>[
                    'title' => '排序条件(bet,active)',
                    'value' => 'bet',
                ],
                'wage_step'=>[
                    'title' => '实时工资步长',
                    'value' => 0.01,
                ],
                'stop_wage_bet_limit'=>[
                    'title' =>'当日达到多少投注后停发工资',
                    'value' => 9000000,
                ],
                'unit'=>[
                    'title' =>'金额单位',
                    'value' => 10000,
                ],
            ],
            7 => [
                'default_level'=>[
                    'title' =>'默认层级',
                    'value' => '1',
                ],
                'default_contract'=>[
                    'title' =>'默认契约',
                    'value' => '[{"bet":0,"active":0,"win_rate":'.$max_win_rate.',"loss_rate":'.$max_loss_rate.'}]',//bet：销量 单位：万
                ],
                'max_rate'=>[
                    'title' => '最大比例限制',
                    'value' => 0.40,
                ],
                'check_user_active'=>[
                    'title' => '是否判断活跃用户',
                    'value' => 0,
                ],
                'check_bet'=>[
                    'title' => '是否判断销量',
                    'value' => 1,
                ],
                'check_upper_min'=>[
                    'title' => '是否可低于上级最低条件',
                    'value' => '0',
                ],
                'sort_condition'=>[
                    'title' => '排序条件(bet,active)',
                    'value' => 'bet',
                ],
                'wage_step'=>[
                    'title' => '实时工资步长',
                    'value' => 0.01,
                ],
                'unit'=>[
                    'title' =>'金额单位',
                    'value' => 10000,
                ],
            ],
            8 => [
                'unit'=>[
                    'title' =>'金额单位',
                    'value' => 1,
                ],
                'max_rate'=>[
                    'title' =>'最大比例限制',
                    'value' => 0.15,
                ],
                'default_level'=>[
                    'title' =>'默认层级',
                    'value' => 1,
                ],
                'only_show_rate'=>[
                    'title' =>'前台是否只允许调整比例',
                    'value' => 0,
                ],
                'settled_rate_level'=>[
                    'title' =>'使用固定工资比例的层级(<=level)',
                    'value' => '-1',
                ],
                'start_deduct_level'=>[
                    'title' =>'开始扣减下级的层级(>level)',
                    'value' => '-1',
                ],
                'check_bet'=>[
                    'title' => '是否判断销量',
                    'value' => 0,
                ],
                'check_profit'=>[
                    'title' => '是否判断亏损',
                    'value' => 1,
                ],
                'check_user_active'=>[
                    'title' => '是否判断有效人数',
                    'value' => 1,
                ],
                'check_win_rate'=>[
                    'title' => '是否判断盈利工资比例',
                    'value' => 1,
                ],
                'check_loss_rate'=>[
                    'title' => '是否判断亏损工资比例',
                    'value' => 1,
                ],
                'default_contract'=>[
                    'title' =>'默认契约',
                    'value' => '[{"profit":0,"active":0,"win_rate":'.$max_win_rate.',"loss_rate":'.$max_loss_rate.'}]',
                ],
            ],
        ];
        return isset($line_wage_contents[$line_type])?$line_wage_contents[$line_type]:$line_wage_contents[1];
    }
}
