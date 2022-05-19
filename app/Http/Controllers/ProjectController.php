<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectIndexRequest;
use Service\API\Project;
use Service\Models\Issue;
use Service\Models\ProjectsAlert as ModelProjectsAlert;
use Service\Models\ProjectsRebate;
use Service\Models\User as ModelUser;
use Service\Models\UserGroup;
use Service\Models\Projects as ModelProjects;
use Service\Models\Orders as ModelOrders;
use Service\Models\ProjectsExpandcode as ModelProjectsExpandcode;
use Service\API\Project as ApiProject;
use Service\API\ProjectsAlert as APIProjectsAlert;
use Service\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectController extends Controller
{
    public function getIndex()
    {
        $param_list = ApiProject::getParamList();
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
        $data['source_list'] = $param_list['source_list'];
        $data['mode_list'] = $param_list['mode_list'];
        $data['user_group'] = UserGroup::all();

        return view('project.index', $data);
    }

    public function postIndex(ProjectIndexRequest $request)
    {
        if ($request->ajax() || $request->post('export', 0)) {
            if ($request->get('get_issue') == 1) {
                $start_date = $request->get('start_date', '');     //开始时间
                $end_date = $request->get('end_date', '');         //结束时间
                if (empty($start_date)) {
                    $start_date = Carbon::today();
                }
                if (empty($end_date)) {
                    $end_date = Carbon::now();
                }
                $ident = $request->get('ident');    //彩种ident

                $data = Issue::select('issue.issue')->leftJoin('lottery', 'lottery.id', 'issue.lottery_id')->where('lottery.ident', $ident)
                    ->whereBetween('issue.sale_start', [$start_date, $end_date])->orderBy('issue.sale_start', 'desc')->limit(100)->get();
                return response()->json($data);
            }
            $export = (int)$request->get('export', 0);
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            if (($start + 1) * $length >= 50000) {
                return response()->json([
                    'errors' => [
                        'start_date' => ['请缩小查询范围'],
                    ],
                    'message' => 'The given data was invalid.',
                ], 422);
            }

            $param = array();
            $param['project_no'] = id_decode($request->get('project_no', ''));//注单ID
            $param['ip'] = $request->get('ip');//用户IP
            $param['start_date'] = $request->get('start_date');//开始时间
            $param['end_date'] = $request->get('end_date');//结束时间
            $param['amount_min'] = $request->get('amount_min');//投注金额大小
            $param['amount_max'] = $request->get('amount_max');//投注金额大小
            $param['bonus_min'] = $request->get('bonus_min');//派奖金额大小
            $param['bonus_max'] = $request->get('bonus_max');//派奖金额大小
            $param['user_group_id'] = (int)$request->get('user_group_id');//用户组别
            $param['mode'] = $request->get('mode');//元角分厘模式，0不限，1元2角3分4厘
            $param['client_type'] = $request->get('client_type');//来源 -1 不限 0 Unknown 1 WEB 2IOS 3 Android 4  AIR客户端 5 WAP
            $param['lottery_id'] = $request->get('lottery_id');//彩种ID
            $param['method_id'] = $request->get('method_id');//玩法ID
            $param['issue'] = $request->get('issue');//奖期
            $param['page'] = $request->get('page');//当前页
            $param['calculate_total'] = $request->get('calculate_total');//计算合计
            $param['project_status'] = $request->get('project_status', '');//计算合计
            $param['deduct_start_date'] = $request->get('deduct_start_date');//开始结算时间
            $param['deduct_end_date'] = $request->get('deduct_end_date');//结束结算时间
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

            if ($param['included_sub_agent']) {
                DB::select('set enable_nestloop to false');
            }

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
                //注单ID
                if (!empty($param['project_no'])) {
                    $query->where('projects.id', $param['project_no']);
                }
                //用户IP
                if (!empty($param['ip'])) {
                    $query->where('projects.ip', '<<=', "{$param['ip']}/24");
                }
                //下单时间对比
                if (!empty($param['start_date'])) {
                    $query->where('projects.created_at', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where('projects.created_at', '<=', $param['end_date']);
                }

                //投注金额对比
                if (!empty($param['amount_min'])) {
                    $query->where('projects.total_price', '>=', $param['amount_min']);
                }
                if (!empty($param['amount_max'])) {
                    $query->where('projects.total_price', '<=', $param['amount_max']);
                }

                //派奖金额
                if (!empty($param['bonus_min'])) {
                    $query->where('projects.bonus', '>=', $param['bonus_min']);
                }
                if (!empty($param['bonus_max'])) {
                    $query->where('projects.bonus', '<=', $param['bonus_max']);
                }

                //用户查询条件
                if ($param['search_type'] == 1 && !empty($param['username'])) {
                    if ($param['included_sub_agent'] == 1) {
                        $query->where(function ($query) use ($param) {
                            $query->where('users.id', $param['user_id'])
                                ->orWhere('users.parent_tree', '@>', $param['user_id']);
                        });
                    } else {
                        $query->where('projects.user_id', function ($query) use ($param) {
                            $query->select('id')->from('users')->where('username', $param['username']);
                        });
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

                if (empty($param['project_no']) && empty($param['username']) && !empty($param['user_group_id'])) {
                    $query->where('users.user_group_id', $param['user_group_id']);
                }

                //元角分模条件查询
                if (!empty($param['mode'])) {
                    $query->where('projects.mode', $param['mode']);
                }

                //彩种查询
                if (!empty($param['lottery_id'])) {
                    $query->where('projects.lottery_id', "{$param['lottery_id']}");
                }
                //玩法查询
                if (!empty($param['method_id'])) {
                    $query->where('projects.lottery_method_id', $param['method_id']);
                }
                //奖期
                if (!empty($param['issue'])) {
                    $query->where('projects.issue', $param['issue']);
                }
                //客户端类型
                if (!empty($param['client_type'])) {
                    $query->where('projects.client_type', $param['client_type']);
                }
                //是否结算
                if (!empty($param['project_status'])) {
                    //未开奖
                    if ($param['project_status'] == 1) {
                        $query->where('projects.is_get_prize', 0)->where('is_cancel', 0);
                    } elseif ($param['project_status'] == 2) { //未中奖
                        $query->where('projects.is_get_prize', 2)->where('is_cancel', 0);
                    } elseif ($param['project_status'] == 3) { //已中奖
                        $query->where('projects.is_get_prize', 1)->where('is_cancel', 0);
                    } elseif ($param['project_status'] == 4) { //已撤单
                        $query->where('is_cancel', '>', 0);
                    }
                }
                //结算时间对比
                if (!empty($param['deduct_start_date'])) {
                    $query->where('projects.deduct_at', '>=', $param['deduct_start_date']);
                }
                if (!empty($param['deduct_end_date'])) {
                    $query->where('projects.deduct_at', '<=', $param['deduct_end_date']);
                }
            };

            // 计算过滤后总数
            $count_query = ModelProjects::leftJoin('users', 'users.id', 'projects.user_id')->where($where);
            $count_sql = vsprintf(str_replace(array('?'), array('\'\'%s\'\''), $count_query->select([DB::raw(1)])->toSql()), $count_query->getBindings());
            $count = DB::selectOne("
                select count_estimate('{$count_sql}') as total
            ");

            if ($count->total > 20000) {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count->total;
            } else {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count_query->count();
            }

            $data['data'] = ModelProjects::select(
                ['projects.id as project_no',
                    DB::raw('
                    (CASE
                    WHEN
                        character_length(projects.code) > 18 THEN left(projects.code, 18) || \'...\'
                    ELSE projects.code
                    END) AS  code'),
                    'projects.id',
                    'projects.is_get_prize',
                    'projects.multiple',
                    'projects.prize_status',
                    'projects.is_deduct',
                    'projects.created_at',
                    'projects.lottery_id',
                    'projects.total_price',
                    'projects.is_cancel',
                    'projects.user_id',
                    'projects.lottery_method_id',
                    'projects.mode',
                    'projects.bonus',
                    'projects.issue',
                    'projects.client_type',
                    'users.id as uid',
                    'users.username',
                    'users.user_group_id',
                    'i.code as bonus_code',
                    'package.code as mmc_code',
                    'l.name as lottery_name',
                    DB::raw('(plm.name || \' - \' || lm.name) as method_name'),
                    'user_profile.value as user_observe'
                ]
            )
                ->leftJoin('users', 'users.id', 'projects.user_id')
                ->leftJoin('package', 'package.id', 'projects.package_id')
                ->leftJoin('lottery as l', 'l.id', 'projects.lottery_id')
                ->leftJoin('lottery_method as lm', 'lm.id', 'projects.lottery_method_id')
                ->leftJoin('lottery_method as plm', 'lm.parent_id', 'plm.id')
                ->leftJoin('issue as i', function ($join) {
                    $join->on('i.lottery_id', 'projects.lottery_id')->on('i.issue', 'projects.issue');
                })
                ->leftJoin('user_profile', function ($join) {
                    $join->on('user_profile.user_id', '=', 'users.id')
                        ->where('user_profile.attribute', 'user_observe');
                })
                ->where($where);

            if (empty($export)) {
                //合计
                if ($param['calculate_total'] == '2' && $data['recordsTotal'] < 1000000) {
                    $data['totalSum'] = ModelProjects::select([
                        DB::raw('SUM(total_price) as sum_price'),
                        DB::raw('SUM(bonus) as sum_bonus'),
                    ])->leftJoin('users', 'users.id', 'projects.user_id')
                        ->where($where)->first();
                }
                $data['data'] = $data['data']->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
                if (!$data['data']->isEmpty()) {
                    $param_list = ApiProject::getParamList();
                    $source_list = $param_list['source_list'];
                    $mode_list = $param_list['mode_list'];
                    $get_prize_list = $param_list['get_prize_list'];
                    $cancel_list = $param_list['cancel_list'];

                    foreach ($data['data'] as $k => $v) {
                        $data['data'][$k]->bet_count = round($v->total_price / $v->multiple / $param_list['mode_list'][$v->mode]['cost']);
                        $data['data'][$k]->project_no = id_encode($v->project_no);
                        $data['data'][$k]->client_type_label = isset($source_list[$v->client_type]) ? $source_list[$v->client_type] : $v->client_type;
                        $data['data'][$k]->mode = isset($mode_list[$v->mode]['name']) ? $mode_list[$v->mode]['name'] : $v->mode;
                        $data['data'][$k]->getprize_label = isset($get_prize_list[$v->is_get_prize]) ? $get_prize_list[$v->is_get_prize] : $v->is_get_prize;
                        $data['data'][$k]->status_label = isset($cancel_list[$v->is_cancel]) ? $cancel_list[$v->is_cancel] : $v->is_cancel;
                        if (empty($v->bonus_code) && !empty($v->mmc_code)) {
                            $data['data'][$k]->bonus_code = $v->mmc_code;
                        }
                    }
                }

                return response()->json($data);
            } else {
                if ($data['recordsTotal'] >= 10000) {
                    return '数据超过 10000 条记录，无法导出！';
                }

                //导出数据
                $file_name = date('Ymd-H_i_s') . "-投注数据.csv";
                $query = $data['data'];
                $param_list = ApiProject::getParamList();
                $response = new StreamedResponse(null, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
                ]);
                $response->setCallback(function () use ($query, $param_list) {
                    $out = fopen('php://output', 'w');
                    fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                    $first = true;
                    $query->chunk(500, function ($results) use (&$first, $out, $param_list) {
                        if ($first) {
                            //列名
                            $columnNames[] = '注单编号';
                            $columnNames[] = '用户名';
                            $columnNames[] = '投注时间';
                            $columnNames[] = '彩种';
                            $columnNames[] = '玩法';
                            $columnNames[] = '期号';
                            $columnNames[] = '模式';
                            $columnNames[] = '倍数';
                            $columnNames[] = '注数';
                            $columnNames[] = '投注金额';
                            $columnNames[] = '中奖金额';
                            $columnNames[] = '中奖号码';
                            fputcsv($out, $columnNames);
                            $first = false;
                        }
                        $datas = [];
                        foreach ($results as $item) {
                            $datas[] = [
                                id_encode($item->project_no),
                                $item->username,
                                $item->created_at,
                                $item->lottery_name,
                                $item->method_name,
                                $item->issue,
                                $param_list['mode_list'][$item->mode]['name'],
                                $item->multiple,
                                $item->total_price / $item->multiple / $param_list['mode_list'][$item->mode]['cost'],
                                $item->total_price,
                                $item->bonus,
                                $item->bonus_code,
                            ];
                        }
                        foreach ($datas as $item) {
                            fputcsv($out, $item);
                        }
                    });
                    fclose($out);
                });
                $response->send();
            }
        }
    }

    public function getDetail(Request $request)
    {
        $id = id_decode($request->get('id', ''));
        $project = ModelProjects::select(
            [
                'projects.*',
                'projects.id as project_no',
                'users.id as uid',
                'users.username',
                'i.code as bonus_code',
                'i.sale_end',
                'i.sale_start',
                'package.code as mmc_code',
                'package.process_time',
                'l.name as lottery_name',
                'l.lottery_category_id',
                'lm.prize_level_name',
                DB::raw('(plm.name || \' - \' || lm.name) as method_name'),
                'pf.amount as  project_fee',
                'pf.rate as project_fee_rate',
                'pf.status as  project_fee_status',

            ]
        )
            ->leftJoin('users', 'users.id', 'projects.user_id')
            ->leftJoin('package', 'package.id', 'projects.package_id')
            ->leftJoin('lottery as l', 'l.id', 'projects.lottery_id')
            ->leftJoin('lottery_method as lm', 'lm.id', 'projects.lottery_method_id')
            ->leftJoin('lottery_method as plm', 'lm.parent_id', 'plm.id')
            ->leftJoin('issue as i', function ($join) {
                $join->on('i.lottery_id', 'projects.lottery_id')->on('i.issue', 'projects.issue');
            })
            ->leftJoin('projects_fee as pf', 'pf.project_id', 'projects.id')
            ->where('projects.id', $id)
            ->first();

        if (empty($project)) {
            return redirect('/project\/')->withErrors("找不到该纪录");
        }
        //获取可能中奖情况
        $project->prize_level = json_decode($project->prize_level, true);
        if ($project->prize_level) {
            $prize_level = $project->prize_level;
        } else {
            $prize_level = ModelProjectsExpandcode::where('project_id', $id)->get();
        }
        if ($project->task_id !== 0) {
            $project->task_id = id_encode($project->task_id);
        }
        $project->prize_level_name = json_decode($project->prize_level_name, true);
        $data['project'] = $project;

        $param_list = ApiProject::getParamList();

        $source_list = $param_list['source_list'];
        $mode_list = $param_list['mode_list'];
        $get_prize_list = $param_list['get_prize_list'];
        $cancel_list = $param_list['cancel_list'];
        $cost = get_mode($project->mode, 'cost');

        $data['project']->bet_count = round($project->total_price / $project->multiple / $cost);
        $data['project']->project_no = id_encode($project->project_no);
        $data['project']->client_type_label = array_key_exists($project->client_type, $source_list) ? $source_list[$project->client_type] : $$project->client_type;
        $data['project']->mode = array_key_exists($project->mode, $mode_list) ? $mode_list[$project->mode]['name'] : $project->mode;
        $data['project']->getprize_label = array_key_exists($project->is_get_prize, $get_prize_list) ? $get_prize_list[$project->is_get_prize] : $project->is_get_prize;
        $data['project']->status_label = array_key_exists($project->is_cancel, $cancel_list) ? $cancel_list[$project->is_cancel] : $project->is_cancel;

        $iTempLimitMinute = get_config('admin_cancel_limit', 30);//超时多奖后还可以那撤单
        $data['canceltime'] = $iTempLimitMinute;
        $data['prizelevel'] = $prize_level;
        $data['levelcount'] = count($prize_level);
        //返点信息
        $data['project_rebates'] = ProjectsRebate::select([
            'projects_rebate.*',
            'users.username'
        ])
            ->leftJoin('users', 'users.id', 'projects_rebate.user_id')
            ->where('projects_rebate.project_id', $id)
            ->get();
        //同一订单注单
        $data['order_project'] = ModelProjects::select(
            [
                'projects.*',
                'projects.id as project_no',
                'l.name as lottery_name',
                'l.lottery_category_id',
                'lm.prize_level_name',
                DB::raw('(plm.name || \' - \' || lm.name) as method_name')
            ]
        )
            ->leftJoin('package', 'package.id', 'projects.package_id')
            ->leftJoin('lottery as l', 'l.id', 'projects.lottery_id')
            ->leftJoin('lottery_method as lm', 'lm.id', 'projects.lottery_method_id')
            ->leftJoin('lottery_method as plm', 'lm.parent_id', 'plm.id')
            ->where('projects.package_id', $project->package_id)
            ->where('projects.id', '<>', $project->id)
            ->get();
        foreach ($data['order_project'] as &$p) {
            $p->project_no = id_encode($p->project_no);
        }
        //相关联的帐变信息
        $data['orders'] = ModelOrders::select([
            'orders.*', 'ot.name as type_name', 'ot.operation',
            'ot.hold_operation', 'u.username',
        ])
            ->leftJoin('order_type as ot', 'ot.id', 'orders.order_type_id')
            ->leftJoin('users as u', 'u.id', 'orders.from_user_id')
            ->orderBy('orders.created_at', 'desc')
            ->where('orders.project_id', $project->id)
            ->get();
        return view('project.detail', $data);
    }

    /**
     * 撤单
     * @param Request $request
     */
    public function postCancel(Request $request)
    {
        $project_id = (int)$request->get('project_id');
        $user_id = (int)$request->get('user_id');
        $cancel = Project::cancelProject($user_id, $project_id, auth()->id());
        if ($cancel === true) {
            return redirect()->back()->withSuccess('撤单成功！');
        } else {
            return redirect()->back()->withErrors('撤单失败！原因：' . $cancel);
        }
    }

    /**
     * 高额中奖提示
     * @param Request $request
     */
    public function getAlert(Request $request)
    {
        //删除30天前的数据
        ModelProjectsAlert::where('created_at', '<', Carbon::now()->subDay(30))->delete();
        $data['start_date'] = Carbon::yesterday();
        $data['end_date'] = Carbon::tomorrow();
        return view('project.alert-list', $data);
    }

    public function postAlert(Request $request)
    {
        if (!APIProjectsAlert::isEnableAutoRiskAlert()) {
            return response()->json(array('recordsTotal' => 0, 'data' => []));
        }
        
        $data = array();
        $data['draw'] = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $param = array();
        $param['start_date'] = $request->get('start_date', Carbon::yesterday());
        $param['end_date'] = $request->get('end_date', Carbon::tomorrow());
        $param['username'] = $request->get('username', '');
        $param['alert_type'] = $request->get('alert_type', -1);
        $param['read_type'] = $request->get('read_type', -1);
        //查询条件
        $where = function ($query) use ($param) {
            //下单时间对比
            if (!empty($param['start_date'])) {
                $query->where('projects_alert.created_at', '>=', $param['start_date']);
            }
            if (!empty($param['end_date'])) {
                $query->where('projects_alert.created_at', '<=', $param['end_date']);
            }
            if (!empty($param['username'])) {
                $query->where('users.username', $param['username']);
            }
            if ($param['alert_type'] >= 0) {
                $query->where('projects_alert.type', $param['alert_type']);
            }

            if ($param['read_type'] == 0) {
                $query->whereRaw('not "projects_alert"."admin_ids" @> ?', auth()->id());
            } elseif ($param['read_type'] == 1)  {
                $query->whereRaw('"projects_alert"."admin_ids" @> ?', auth()->id());
            }
        };
        $data['recordsTotal'] = $data['recordsFiltered'] = 
            ModelProjectsAlert::leftJoin('projects', 'projects.id', 'projects_alert.project_id')
            ->leftJoin('users', 'users.id', 'projects_alert.user_id')
            ->where(function ($query) {
                $query->where('users.user_group_id', 1)
                    ->orwhere('projects_alert.type', APIProjectsAlert::ALERT_LOGIN_MUCH);
            })
            ->where($where)->count();

        $data['data'] = ModelProjectsAlert::select([
            'projects_alert.id',
            'projects_alert.type',
            'projects_alert.created_at',
            'projects_alert.project_id',
            'projects_alert.extend',
            'projects_alert.admin_ids',
            DB::raw("COALESCE(projects.bonus, 0) as bonus"),
            DB::raw("COALESCE(projects.total_price, 0) as total_price"),
            DB::raw("COALESCE(projects.issue, '-') as issue"),
            DB::raw("COALESCE(lottery.name, '-') as lottery_name"),
            DB::raw("COALESCE(users.username, '-') as username"),
            DB::raw('(m2.name || \' - \' || m1.name) as method_name')
        ])
            ->leftJoin('projects', 'projects.id', 'projects_alert.project_id')
            ->leftJoin('users', 'users.id', 'projects_alert.user_id')
            ->leftJoin('lottery', 'lottery.id', 'projects.lottery_id')
            ->leftJoin('lottery_method as m1', 'm1.id', 'projects.lottery_method_id')
            ->leftJoin('lottery_method as m2', 'm2.id', 'm1.parent_id')
            ->where(function ($query) {
                $query->where('users.user_group_id', 1)
                    ->orwhere('projects_alert.type', APIProjectsAlert::ALERT_LOGIN_MUCH);
            })
            ->where($where)
            ->skip($start)->take($length)
            ->orderBy('projects_alert.id', 'desc')
            ->get();
        
        $temp_alert_data = [];
        foreach ($data['data'] as $key => $alert) {
            
            // 处理未读ID
            $admin_ids = json_decode($alert->admin_ids);
            if (!in_array(auth()->id(), $admin_ids)) {
                $admin_ids[] = auth()->id();
                $temp_alert_data['admin']["{$alert->id}"] = $admin_ids;
                $alert->status = 0;
            }
            
            // 下单前余额
            $extend_array = json_decode($alert->extend, true);
            $alert->pre_balance = isset($extend_array['user_pre_balance']) ? $extend_array['user_pre_balance'] : '-';;

            // 处理注单ID
            if ($alert->project_id > 0) {
                if ($alert->pre_balance == '-') {
                    $temp_alert_data['project'][] = $alert->project_id;
                } else {
                    $alert->project_id = id_encode($alert->project_id);
                }
            } else {
                $alert->project_id = '-';
            }

            // 处理金额
            switch ($alert->type) {
                case APIProjectsAlert::ALERT_OBSERVE_IN:
                case APIProjectsAlert::ALERT_LOGIN_MUCH:
                    $alert->bonus = '-';
                    $alert->total_price = '-';
                    $alert->method_name = $alert->method_name ?? '-';
                    if($alert->type == APIProjectsAlert::ALERT_LOGIN_MUCH){
                        $alert->username = $extend_array['user_login_count'];
                        if(isset($extend_array['conf_login_count'])){
                            $alert->username .= ' > '.$extend_array['conf_login_count'];
                        }
                    }
                    break;
                default:
                    break;
            }
        }

        // 设置通知单已读
        if (isset($temp_alert_data['admin']) && count($temp_alert_data['admin']) > 0) {
            APIProjectsAlert::setAlertRead($temp_alert_data['admin']);
        }

        // 处理下单前余额数据
        if (isset($temp_alert_data['project']) && count($temp_alert_data['project']) > 0) {
            $user_pre_balances = APIProjectsAlert::getPreBalancesByIDs($temp_alert_data['project']);
            foreach ($data['data'] as $key => $alert) {
                if (isset($user_pre_balances["{$alert->project_id}"])) {
                    $alert->pre_balance = $user_pre_balances["{$alert->project_id}"]['pre_balance'];
                    $alert->project_id = id_encode($alert->project_id);
                }
            }
        }

        return response()->json($data);
    }

    /**
     * 高额中奖提示
     * @param Request $request
     */
    public function putAlert(Request $request)
    {
        if (!APIProjectsAlert::isEnableAutoRiskAlert()) {
            return response()->json(array('total' => 0, 'list' => []));
        }
        $toast = get_config('auto_risk_notice_toast',0);//弹窗业务范围
        $toast = ($toast<>'')?explode(',',$toast):[0];
        APIProjectsAlert::fillUserWithHighBonus();
        $count = ModelProjectsAlert::leftJoin('projects', 'projects.id', 'projects_alert.project_id')
            ->leftJoin('users', 'users.id', 'projects_alert.user_id')
            ->where(function ($query) {
                $query->where('users.user_group_id', 1)
                    ->orwhere('projects_alert.type', APIProjectsAlert::ALERT_LOGIN_MUCH);
            })
            ->whereRaw('not "projects_alert"."admin_ids" @> ?', auth()->id())
            ->where('projects_alert.created_at', '>=', Carbon::yesterday())
            ->count();

        $project_alerts = ModelProjectsAlert::select([
            'projects_alert.id',
            'projects_alert.type',
            'projects_alert.created_at',
            'projects_alert.extend',
            'projects_alert.project_id',
            'projects_alert.toast_admin_ids',
            'users.username',
            'projects.bonus',
            'lottery.name as lottery_name',
            'm2.name as method_kind',
            'm1.name as method_name'
        ])
            ->leftJoin('projects', 'projects.id', 'projects_alert.project_id')
            ->leftJoin('users', 'users.id', 'projects_alert.user_id')
            ->leftJoin('lottery', 'lottery.id', 'projects.lottery_id')
            ->leftJoin('lottery_method as m1', 'm1.id', 'projects.lottery_method_id')
            ->leftJoin('lottery_method as m2', 'm2.id', 'm1.parent_id')
            ->where(function ($query) {
                $query->where('users.user_group_id', 1)
                    ->orwhere('projects_alert.type', APIProjectsAlert::ALERT_LOGIN_MUCH);
            })
            ->whereRaw('not "projects_alert"."admin_ids" @> ?', auth()->id())
            ->where('projects_alert.created_at', '>=', Carbon::yesterday())
            ->orderBy('projects_alert.id', 'desc')
            ->limit(5)
            ->get()->toArray();

        foreach ($project_alerts as $key => $alert) {
            $toast_admin_ids = $alert['toast_admin_ids']?json_decode($alert['toast_admin_ids'], true):[];
            //如果已经推名单里没有他，则加上
            if($toast_admin_ids && in_array(auth()->id(),$toast_admin_ids)){
                //如果已经推送名单里有他，则不弹
                $project_alerts[$key]['toast'] = 0;
            }else{
                //如果没他，则判断类型弹
                if(in_array($alert['type'],$toast)){
                    $project_alerts[$key]['toast'] = 1;
                }else{
                    $project_alerts[$key]['toast'] = 0;
                }
                //弹窗只显示一次
                array_push($toast_admin_ids,auth()->id());
                ModelProjectsAlert::where('id',$alert['id'])->update([
                    'toast_admin_ids' => json_encode($toast_admin_ids)
                ]);

            }
            $extend_array = json_decode($alert['extend'], true);
            $_hour_min = date('H:i',strtotime($alert['created_at']));
            $temp_msg = "[{$_hour_min}]";
            switch ($alert['type']) {
                case APIProjectsAlert::ALERT_INACTIVITY:
                    $balance = isset($extend_array['user_pre_balance']) ? $extend_array['user_pre_balance'] : 0;
                    $temp_msg .= "久未活跃用户【{$alert['username']}】持有馀额大于{$balance}元，开始投注。";
                    break;
                
                case APIProjectsAlert::ALERT_OBSERVE_IN:
                    $temp_msg .= "重点观察用户【{$alert['username']}】上线了，请注意！";
                    break;

                case APIProjectsAlert::ALERT_LOGIN_MUCH:
                    $user_login_count = isset($extend_array['user_login_count']) ? $extend_array['user_login_count'] : 0;
                    $conf_login_count = isset($extend_array['conf_login_count']) ? $extend_array['conf_login_count'] : 0;
                    if($conf_login_count){
                        $temp_msg .= "今日登录用户数 {$user_login_count} 超过 {$conf_login_count}，请注意！";
                    }else{
                        $temp_msg .= "今日登录用户数 {$user_login_count} ，请注意！";
                    }
                    break;
                case APIProjectsAlert::ALERT_HIGH_BONUS:
                    $temp_msg .= "用户{$alert['username']},在{$alert['lottery_name']} {$alert['method_kind']}_{$alert['method_name']},中奖<code>{$alert['bonus']}</code>元";
                    break;
                default:
                    $temp_msg .= "未知类型[".$alert['type']."]推送";
                    break;
            }
            $project_alerts[$key]['msg'] = $temp_msg;
            $project_alerts[$key]['project_id'] = id_encode($alert['project_id']);
        }

        return response()->json(array('total' => $count, 'list' => $project_alerts,'toast' => $toast));
    }
}
