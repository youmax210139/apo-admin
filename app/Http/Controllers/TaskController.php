<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\TaskIndexRequest;
use Service\Models\Tasks;
use Service\Models\User as ModelUser;
use Service\Models\Tasks as ModelTasks;
use Service\Models\Taskdetails as ModelTaskdetails;
use Service\Models\UserGroup;
use Service\API\Project as ApiProject;
use Service\Models\User;
use Service\Models\Orders as ModelOrders;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function getIndex()
    {
        $default_search_time = get_config('default_search_time', 0);
        $data = [
            'start_date' => Carbon::now()->hour >= $default_search_time ?
                Carbon::today()->addHours($default_search_time) :
                Carbon::yesterday()->addHours($default_search_time),
            'end_date' => Carbon::now()->hour >= $default_search_time ?
                Carbon::tomorrow()->addHours($default_search_time)->subSecond(1) :
                Carbon::today()->addHours($default_search_time)->subSecond(1),
        ];
        //获取所有总代
        $data['users_top'] = ModelUser::where('parent_id', 0)->orderBy('id', 'desc')->get();
        //获取彩种信息
        $data['lottery_list'] = \Service\API\Lottery::getAllLotteryGroupByCategory();
        $lottery_methods = \Service\API\Lottery::getAllLotteryMethodMapping();
        $data['lottery_method_list'] = json_encode($lottery_methods);
        $data['zongdai_list'] = User::select(['id', 'username'])->where('parent_id', 0)->get();
        $param_list = ApiProject::getParamList();
        $data['source_list'] = $param_list['source_list'];
        $data['mode_list'] = $param_list['mode_list'];
        $data['user_group'] = UserGroup::all();
        return view('task.index', $data);
    }

    public function postIndex(TaskIndexRequest $request)
    {
        if ($request->ajax()) {
            if ($request->get('get_issue') == 1) {
                $start_date = $request->get('start_date');//开始时间
                $end_date = $request->get('end_date');//结束时间
                $ident = $request->get('ident');//彩种ident

                $data = \Service\API\Lottery::getOneLotteryIssueByTime($ident, $start_date, $end_date);
                return response()->json($data);
            }

            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param = array();
            $param['start_date'] = $request->get('start_date');//开始时间
            $param['end_date'] = $request->get('end_date');//结束时间
            $param['amount_min'] = $request->get('amount_min');//金额大小
            $param['amount_max'] = $request->get('amount_max');//金额大小
            $param['ip'] = $request->get('ip');//用户IP
            $param['mode'] = $request->get('mode');//元角分厘模式，0不限，1元2角3分4厘
            $param['client_type'] = $request->get('client_type');//来源 -1 不限 0 Unknown 1 WEB 2IOS 3 Android 4  AIR客户端 5 WAP
            $param['user_group_id'] = (int)$request->get('user_group_id');//用户组别
            $param['lottery_id'] = $request->get('lottery_id');//彩种ID
            $param['method_id'] = $request->get('method_id');//玩法ID
            $param['issue'] = $request->get('issue');//奖期
            $param['task_no'] = id_decode($request->get('task_no', ''));//追号单
            $param['page'] = $request->get('page');//当前页

            $param['search_type'] = (int)$request->get('search_type');
            if ($param['search_type'] == 1) {
                if (!empty($request->get('username'))) {
                    $param['username'] = (string)$request->get('username');
                }
            }

            if ($param['search_type'] == 2) {
                if (!empty($request->get('zongdai'))) {
                    $param['zongdai'] = (int)$request->get('zongdai');

                    $param['no_included_zongdai'] = (int)$request->get('no_included_zongdai');//不包含总代=1，包含=0
                }
            }
            $param['included_sub_agent'] = (int)$request->get('included_sub_agent');//是否包含下级，包含=1，未包含=0

            //如果有用户名的搜索，判断是否用户存在，不存在直接返回空数据，不查询数据库
            if ($param['search_type'] == 1 && !empty($param['username']) && $param['included_sub_agent'] == 1) {
                $exist_user = User::select(['id'])->where('username', $param['username'])->first();

                if (empty($exist_user)) {
                    $data['recordsTotal'] = $data['recordsFiltered'] = 0;
                    $data['data'] = [];
                    return response()->json($data);
                } else {
                    $param['user_id'] = $exist_user->id;
                }
            }

            //查询条件
            $where = function ($query) use ($param) {
                //追号单ID
                if (!empty($param['task_no'])) {
                    $query->where('tasks.id', $param['task_no']);
                }

                if (!empty($param['ip'])) {
                    $query->where('tasks.ip', '<<=', "{$param['ip']}/24");
                }

                //下单时间对比
                if (!empty($param['start_date'])) {
                    $query->where('tasks.created_at', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where('tasks.created_at', '<=', $param['end_date']);
                }

                //追号金额对比
                if (!empty($param['amount_min'])) {
                    $query->where('tasks.task_price', '>=', $param['amount_min']);
                }
                if (!empty($param['amount_max'])) {
                    $query->where('tasks.task_price', '<=', $param['amount_max']);
                }

                //用户查询条件
                if ($param['search_type'] == 2 && (!empty($param['proxy_id']))) {//指定总代
                    $query->where('users.lvtopid', 'IN', $param['proxy_id']);
                } elseif ($param['search_type'] == 1 && (!empty($param['username']))) {//指定用户名
                    $query->where('users.username', $param['username']);
                }
                //元角分模条件查询
                if (!empty($param['mode'])) {
                    $query->where('tasks.mode', $param['mode']);
                }
                //来源
                if (!empty($param['client_type'])) {
                    $query->where('tasks.client_type', $param['client_type']);
                }
                //用户组别
                if (!empty($param['user_group_id'])) {
                    $query->where('users.user_group_id', $param['user_group_id']);
                }
                //彩种查询
                if (!empty($param['lottery_id'])) {
                    $query->where('tasks.lottery_id', "{$param['lottery_id']}");
                }
                //玩法查询
                if (!empty($param['method_id'])) {
                    $query->where('tasks.lottery_method_id', $param['method_id']);
                }
                //奖期
                if (!empty($param['issue'])) {
                    $query->where('tasks.begin_issue', $param['issue']);
                }
                //用户查询条件
                if ($param['search_type'] == 1 && !empty($param['username'])) {
                    if ($param['included_sub_agent'] == 1) {
                        $query->where(function ($query) use ($param) {
                            $query->where('users.id', $param['user_id'])
                                ->orWhere('users.parent_tree', '@>', $param['user_id']);
                        });
                    } else {
                        $query->where('users.username', $param['username']);
                    }
                }

                if ($param['search_type'] == 2 && !empty($param['zongdai'])) {
                    if ($param['included_sub_agent'] == 1) { // 包含下级
                        $query->where(function ($query) use ($param) {
                            $query->where('users.id', $param['zongdai'])
                                ->orWhere('users.parent_tree', '@>', $param['zongdai']);
                        });
                    } else {
                        $query->where('users.id', $param['zongdai']);
                    }

                    // 不包含自身
                    if ($param['no_included_zongdai'] == 1) {
                        $query->where('users.id', '!=', $param['zongdai']);
                    }
                }
            };

            // 计算过滤后总数
            $count_query = ModelTasks::leftJoin('users', 'users.id', 'tasks.user_id')->where($where);
            $count_sql = vsprintf(str_replace(array('?'), array('\'\'%s\'\''), $count_query->select([DB::raw(1)])->toSql()), $count_query->getBindings());
            $count = DB::selectOne("
                select count_estimate('{$count_sql}') as total
            ");

            if ($count->total > 20000) {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count->total;
            } else {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count_query->count();
            }

            $data['data'] = ModelTasks::select(
                ['tasks.id as task_no',
                    'tasks.created_at',
                    'tasks.mode',
                    'tasks.begin_issue',
                    'tasks.issue_count',
                    'tasks.finished_count',
                    'tasks.cancel_count',
                    'tasks.task_price',
                    'tasks.finish_price',
                    'tasks.cancel_price',
                    'tasks.stop_on_win',
                    'tasks.status',
                    'tasks.client_type',
                    'users.id as userid',
                    'users.username',
                    'l.name as lottery_name',
                    'users.user_group_id',
                    DB::raw('(plm.name || \' - \' || lm.name) as method_name'),
                    'user_profile.value as user_observe'
                ]
            )
                ->leftJoin('users', 'users.id', 'tasks.user_id')
                ->leftJoin('lottery as l', 'l.id', 'tasks.lottery_id')
                ->leftJoin('lottery_method as lm', 'lm.id', 'tasks.lottery_method_id')
                ->leftJoin('lottery_method as plm', 'lm.parent_id', 'plm.id')
                ->leftJoin('user_profile', function ($join) {
                    $join->on('user_profile.user_id', '=', 'users.id')
                        ->where('user_profile.attribute', 'user_observe');
                })
                ->where($where)
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();

            if (!$data['data']->isEmpty()) {
                $param_list = ApiProject::getParamList();
                $source_list = $param_list['source_list'];
                $mode_list = $param_list['mode_list'];

                foreach ($data['data'] as $k => $v) {
                    $data['data'][$k]->task_no = id_encode($v->task_no);
                    $data['data'][$k]->client_type_label = array_key_exists($v->play_source, $source_list) ? $source_list[$v->play_source] : $v->play_source;
                    $data['data'][$k]->mode = array_key_exists($v->mode, $mode_list) ? $mode_list[$v->mode]['name'] : $v->mode;
                }
            }

            return response()->json($data);
        }
    }


    public function getDetail(Request $request)
    {
        $id = id_decode($request->get('id', ''));
        $task = ModelTasks::select(
            [
                'tasks.*',
                'tasks.id as task_no',
                'users.id as uid',
                'users.username',
                'l.name as lottery_name',
                'l.lottery_category_id as lottery_category_id',
                DB::raw('(plm.name || \' - \' || lm.name) as method_name')
            ]
        )
            ->leftJoin('users', 'users.id', 'tasks.user_id')
            ->leftJoin('lottery as l', 'l.id', 'tasks.lottery_id')
            ->leftJoin('lottery_method as lm', 'lm.id', 'tasks.lottery_method_id')
            ->leftJoin('lottery_method as plm', 'lm.parent_id', 'plm.id')
            ->where('tasks.id', $id)
            ->first();
        if (empty($task)) {
            return redirect('/task\/')->withErrors("找不到该纪录");
        }

        //相关联的帐变信息
        $data['orders'] = ModelOrders::select([
            'orders.*', 'ot.name as type_name', 'ot.operation',
            'ot.hold_operation', 'u.username',
        ])
            ->leftJoin('order_type as ot', 'ot.id', 'orders.order_type_id')
            ->leftJoin('users as u', 'u.id', 'orders.from_user_id')
            ->orderBy('orders.created_at', 'desc')
            ->where('orders.task_id', $task['id'])
            ->get();


        //获取可能中奖情况
        $task_detail = ModelTaskdetails::select(['*'])
            ->where(['task_id' => $id])
            ->get();

        $task_tmp = explode('-', $task["beginissue"]);
        foreach ($task_detail as $key => $aDetail) {
            if (empty($aDetail['issue'])) {
                if (count($task_tmp) == 1) {
                    $tmp_num = substr($task_tmp[0], 0, 1);
                    if ($tmp_num == 0) {
                        $task_detail[$key]['issue'] = '0' . ($task_tmp[0] + $key);
                    } else {
                        $task_detail[$key]['issue'] = $task_tmp[0] + $key;
                    }
                } else {
                    $tmp = $task_tmp;
                    $tmp_num = substr($tmp[count($tmp) - 1], 0, 1);

                    if ($tmp_num == 0) {
                        $tmp[count($tmp) - 1] = '0' . (end($tmp) + $key);
                    } else {
                        $tmp[count($tmp) - 1] = end($tmp) + $key;
                    }

                    $task_detail[$key]['issue'] = implode('-', $tmp);
                    unset($tmp);
                }
            }
        }
        $data['task'] = $task;
        $data['task_detail'] = $task_detail;

        $param_list = ApiProject::getParamList();

        $source_list = $param_list['source_list'];
        $mode_list = $param_list['mode_list'];
        $task_status_list = $param_list['task_status_list'];
        $task_detail_status_list = $param_list['task_detail_status_list'];

        $data['task']->task_no = id_encode($data['task']->task_no);

        $data['task']->client_type_label = array_key_exists($data['task']->play_source, $source_list) ? $source_list[$data['task']->play_source] : $data['task']->play_source;
        $data['task']->mode = array_key_exists($data['task']->mode, $mode_list) ? $mode_list[$data['task']->mode]['name'] : $data['task']->mode;
        $data['task']->status_label = array_key_exists($data['task']->status, $task_status_list) ? $task_status_list[$data['task']->status] : $data['task']->status;

        foreach ($data['task_detail'] as $ktd => $td) {
            $data['task_detail'][$ktd]['status_label'] = array_key_exists($td->status, $task_detail_status_list) ? $task_detail_status_list[$td->status] : $td->status;
        }

        return view('task.detail', $data);
    }

    /**
     * 终止追号
     */
    public function postCancel(Request $request)
    {
        $id = intval($request->get('id'));
        $task_data = $id ? Tasks::select(['user_id'])
            ->where([
                ['id', '=', $id],
                ['status', '=', 0]
            ])
            ->first() : null;
        if (empty($task_data)) {
            return response()->json(array('status' => 1, 'msg' => '找不到对应的追号单！'));
        }

        $result = ApiProject::cancelTask($task_data->user_id, $id, [], auth()->user()->id);
        if ($result === true) {
            return response()->json(array('status' => 0, 'msg' => '操作成功'));
        } else {
            return response()->json(array('status' => 1, 'msg' => "操作失败{$result}"));
        }
    }
}
