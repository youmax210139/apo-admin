<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\OrderIndexRequest;
use Service\Models\Admin\AdminUser;
use Service\Models\Orders;
use Service\Models\OrderType;
use Service\Models\User;
use Service\Models\UserGroup;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function getIndex(Request $request)
    {
        $order_type = OrderType::select(['id', 'name', 'category', 'operation', 'hold_operation'])
            ->orderBy('category')->get();
        //订单类型分类，游戏帐变(category=1),充提帐变(category=2),三方帐变(category=3)
        $sub_order_type = [];
        foreach ($order_type as $k => $v) {
            if ($v['category'] == '1') {
                $sub_order_type['lottery_order_type'] = isset($sub_order_type['lottery_order_type']) ?
                    $sub_order_type['lottery_order_type'] . ',' . $v['id'] : $v['id'];
            } elseif ($v['category'] == '2') {
                $sub_order_type['chongti_order_type'] = isset($sub_order_type['chongti_order_type']) ?
                    $sub_order_type['chongti_order_type'] . ',' . $v['id'] : $v['id'];
            } elseif ($v['category'] == '3') {
                $sub_order_type['third_game_order_type'] = isset($sub_order_type['third_game_order_type']) ?
                    $sub_order_type['third_game_order_type'] . ',' . $v['id'] : $v['id'];
            }
        }
        $default_search_time = get_config('default_search_time', 0);
        $data = [
            'start_date' => Carbon::now()->hour >= $default_search_time ?
                Carbon::today()->addHours($default_search_time) :
                Carbon::yesterday()->addHours($default_search_time),
            'end_date' => Carbon::now()->hour >= $default_search_time ?
                Carbon::tomorrow()->addHours($default_search_time)->subSecond(1) :
                Carbon::today()->addHours($default_search_time)->subSecond(1),
        ];
        $data['sub_order_type'] = $sub_order_type;
        $data['order_type'] = $order_type;
        $data['lottery_list'] = \Service\API\Lottery::getAllLotteryGroupByCategory();

        $lottery_methods = \Service\API\Lottery::getAllLotteryMethodMapping();
        $data['lottery_method_list'] = json_encode($lottery_methods);

        $data['admin_list'] = AdminUser::all(['id', 'username']);
        $data['zongdai_list'] = User::select(['id', 'username'])->where('parent_id', 0)->get();

        $param_list = $this->_paramList();
        $data['source_list'] = $param_list['source_list'];
        $data['mode_list'] = $param_list['mode_list'];
        $data['user_group'] = UserGroup::all();
        $data['username'] = $request->get('username', '');
        return view('order.index', $data);
    }

    /**
     * 账变列表数据
     *
     * @param OrderIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIndex(OrderIndexRequest $request)
    {
        if ($request->ajax() || $request->post('export', 0)) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $search = $request->get('search');
            $export = (int)$request->get('export', 0);
            $param = array();
            $param['order_no'] = id_decode($request->get('order_no'), ''); //订单号
            $param['admin_user_id'] = (int)$request->get('admin_user_id');//管理员ID
            $param['ip'] = $request->get('ip');//要搜索的IP
            $param['start_date'] = $request->get('start_date');//开始时间
            $param['end_date'] = $request->get('end_date');//结束时间
            $param['mode'] = (int)$request->get('mode');//圆角分厘模式
            $param['client_type'] = (int)$request->get('client_type');//客户端来源
            $param['user_group_id'] = (int)$request->get('user_group_id');//用户组别
            $param['lottery_id'] = (int)$request->get('lottery_id');//彩种

            $param['method_category'] = (int)$request->get('method_category');//玩法分类
            $param['method_id'] = (int)$request->get('method_id');//玩法
            $param['calculate_total'] = $request->get('calculate_total');//计算合计

            if (($start + 1) * $length >= 50000) {
                return response()->json([
                    'errors' => [
                        'start_date' => ['请缩小查询范围'],
                    ],
                    'message' => 'The given data was invalid.',
                ], 422);
            }

            $order_type_ids = $request->get('order_type_ids');
            if (is_array($order_type_ids)) {
                $tmp = array();
                foreach ($order_type_ids as $otid) {
                    if (!empty($otid) && $otid >= 0) {
                        $tmp[] = $otid;
                    }
                }
                $param['order_type_ids'] = array_unique($tmp);//账变类型
            }

            $param['amount_min'] = $request->get('amount_min', '');
            $param['amount_max'] = $request->get('amount_max', '');

            $param['search_type'] = (int)$request->get('search_type');
            if ($param['search_type'] == 1) {
                if (!empty($request->post('username'))) {
                    $param['username'] = (string)$request->post('username');
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

            if (!empty($param['order_type_ids'])) {
                //合并撤单返款和真实扣款后的撤单返款
                //如果查询类型中有撤单返款，则追加 真实扣款后的撤单返款
                $cdfk = OrderType::select('id')->where('ident', 'CDFK')->first();

                if (!empty($cdfk['id']) && in_array($cdfk['id'], $param['order_type_ids'])) {
                    $cdfksp = OrderType::select('id')->where('ident', 'CDFKSP')->first();
                    $param['order_type_ids'][] = strval($cdfksp['id']);
                }
            }

            //查询条件
            $where = function ($query) use ($param) {
                if (!empty($param['order_type_ids'])) {
                    $query->whereIn('orders.order_type_id', $param['order_type_ids']);
                }
                if (!empty($param['order_no'])) {
                    $query->where('orders.id', $param['order_no']);
                }
                if (!empty($param['ip'])) {
                    $query->where("orders.ip", '<<=', "{$param['ip']}/24");
                }
                if (!empty($param['admin_user_id'])) {
                    $query->where('orders.admin_user_id', $param['admin_user_id']);
                }

                if (!empty($param['start_date'])) {
                    $query->where('orders.created_at', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where('orders.created_at', '<', $param['end_date']);
                }

                if ($param['amount_min'] != '' && $param['amount_min'] >= 0) {
                    $query->where('orders.amount', ">=", $param['amount_min']);
                }

                if ($param['amount_max'] != '' && $param['amount_max'] >= 0) {
                    $query->where('orders.amount', "<=", $param['amount_max']);
                }
                if (!empty($param['mode']) && $param['mode'] > 0) {
                    $query->where('orders.mode', $param['mode']);
                }
                if (!empty($param['client_type']) && $param['client_type'] >= 0) {
                    $query->where('orders.client_type', $param['client_type']);
                }
                if (empty($param['order_no']) && empty($param['username']) && !empty($param['user_group_id'])) {
                    $query->where('users.user_group_id', $param['user_group_id']);
                }
                if (!empty($param['lottery_id']) && $param['lottery_id'] > 0) {
                    $query->where('orders.lottery_id', $param['lottery_id']);
                }

                if (!empty($param['method_id']) && $param['method_id'] > 0) {
                    $query->where('orders.lottery_method_id', $param['method_id']);
                }

                //用户查询条件
                if ($param['search_type'] == 1 && !empty($param['username'])) {
                    if ($param['included_sub_agent'] == 1) {
                        $query->where(function ($query) use ($param) {
                            $query->where('users.id', $param['user_id'])
                                ->orWhere('users.parent_tree', '@>', $param['user_id']);
                        });
                    } else {
                        $query->where('orders.from_user_id', function ($query) use ($param) {
                            $query->select('id')->from('users')->where('username', $param['username']);
                        });
                    }
                }

                if ($param['search_type'] == 2 && !empty($param['zongdai'])) {
                    if ($param['included_sub_agent'] == 1) { // 包含下级
                        // 不包含自身
                        if ($param['no_included_zongdai'] == 1) {
                            $query->where(function ($query) use ($param) {
                                $query->where('users.parent_tree', '@>', $param['zongdai']);
                            });
                        } else {
                            $query->where(function ($query) use ($param) {
                                $query->where('users.id', $param['zongdai'])
                                    ->orWhere('users.parent_tree', '@>', $param['zongdai']);
                            });
                        }
                    } else {
                        $query->where('orders.from_user_id', $param['zongdai']);
                    }
                }
            };

            // 计算过滤后总数
            $count_query = Orders::leftJoin('users', 'users.id', 'orders.from_user_id')->where($where);
            $count_sql = vsprintf(str_replace(array('?'), array('\'\'%s\'\''), $count_query->select([DB::raw(1)])->toSql()), $count_query->getBindings());
            $count = DB::selectOne("
                select count_estimate('{$count_sql}') as total
            ");

            if ($count->total > 20000) {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count->total;
            } else {
                if ($param['included_sub_agent']) {
                    DB::select('set enable_nestloop to false');
                }

                $data['recordsTotal'] = $data['recordsFiltered'] = $count_query->count();

                if ($param['included_sub_agent']) {
                    DB::select('set enable_nestloop to true');
                }
            }

            $data['data'] = Orders::select([
                'orders.id as order_id_raw',
                'orders.id as order_id',
                'orders.created_at',
                'orders.mode',
                'orders.client_type',
                'orders.amount',
                'orders.balance',
                'orders.pre_balance',
                'orders.hold_balance',
                'orders.pre_hold_balance',
                'orders.ip',
                'orders.order_type_id',
                'orders.comment',
                'orders.project_id',
                'projects.issue',
                'lottery.name as lottery_name',
                'orders.lottery_method_id as method_id',
                DB::raw('(parent_lottery_method.name || \' - \' || lottery_method.name) as method_name'),
                DB::raw('COALESCE(admin_users.username, \'-\') as adminname'),
                'users.username',
                'order_type.name as order_name',
                'order_type.operation',
                'order_type.hold_operation',
                'users.user_group_id',
                'user_profile.value as user_observe'
            ])
                ->leftJoin('users', 'users.id', 'orders.from_user_id')
                ->leftJoin('admin_users', 'admin_users.id', 'orders.admin_user_id')
                ->leftJoin('projects', 'projects.id', 'orders.project_id')
                ->leftJoin('lottery', 'lottery.id', 'orders.lottery_id')
                ->leftJoin('lottery_method', 'lottery_method.id', 'orders.lottery_method_id')
                ->leftJoin('lottery_method as parent_lottery_method', 'lottery_method.parent_id', 'parent_lottery_method.id')
                ->leftJoin('order_type', 'order_type.id', 'orders.order_type_id')
                ->leftJoin('user_profile', function ($join) {
                    $join->on('user_profile.user_id', '=', 'users.id')
                        ->where('user_profile.attribute', 'user_observe');
                })
                ->where($where);
            if (empty($export)) {
                $data['data'] = $data['data']->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();

                // 统计所有信息
                if ($param['calculate_total'] == '2' && $data['recordsTotal'] < 1000000) {
                    $sum_amount = Orders::select([
                        DB::RAW('sum(case when order_type.operation = 1 then orders.amount else 0 end) as inmoney'),
                        DB::RAW('sum(case when order_type.operation = 2 then orders.amount else 0 end) as outmoney')])
                        ->leftJoin('users', 'users.id', 'orders.from_user_id')
                        ->leftJoin('admin_users', 'admin_users.id', 'orders.admin_user_id')
                        ->leftJoin('lottery', 'lottery.id', 'orders.lottery_id')
                        ->leftJoin('lottery_method', 'lottery_method.id', 'orders.lottery_method_id')
                        ->leftJoin('order_type', 'order_type.id', 'orders.order_type_id')
                        ->where($where)
                        ->first();

                    $data['sum_amount'] = ['inmoney' => 0, 'outmoney' => 0];

                    if ($sum_amount) {
                        $data['sum_amount'] = $sum_amount;
                    }
                }


                if ($data['data']) {
                    $param_list = $this->_paramList();
                    $source_list = $param_list['source_list'];
                    $mode_list = $param_list['mode_list'];
                    if ($order[0]['dir'] == 'desc') {
                        $data['data'] = $data['data']->sortByDesc('order_id')->values()->all();
                    } else {
                        $data['data'] = $data['data']->sortBy('order_id')->values()->all();
                    }
                    foreach ($data['data'] as $k => $v) {
                        if ($v->project_id) {
                            $data['data'][$k]->project_id = id_encode($v->project_id);
                        }
                        $data['data'][$k]->order_id = id_encode($v->order_id);
                        $data['data'][$k]->client_type = isset($v->client_type, $source_list) ? $source_list[$v->client_type] : '-';
                        $data['data'][$k]->mode = isset($mode_list[$v->mode]['name']) ? $mode_list[$v->mode]['name'] : '-';
                    }
                }
                return response()->json($data);
            } else {
                if ($data['recordsTotal'] >= 10000) {
                    return '数据超过 10000 条记录，无法导出！';
                }

                //导出数据
                $file_name = "账变数据.csv";
                $query = $data['data'];
                $param_list = $this->_paramList();
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
                            $columnNames[] = '订单编号';
                            $columnNames[] = '用户名';
                            $columnNames[] = '账变时间';
                            $columnNames[] = '账变类型';
                            $columnNames[] = '彩种';
                            $columnNames[] = '玩法';
                            $columnNames[] = '期号';
                            $columnNames[] = '模式';
                            $columnNames[] = '支出';
                            $columnNames[] = '收入';
                            $columnNames[] = '前余额';
                            $columnNames[] = '后余额';
                            $columnNames[] = 'IP地址';
                            $columnNames[] = '备注';
                            $columnNames[] = '管理员';
                            fputcsv($out, $columnNames);
                            $first = false;
                        }
                        $datas = [];
                        foreach ($results as $item) {
                            $datas[] = [
                                id_encode($item->order_id),
                                $item->username,
                                $item->created_at,
                                $item->order_name,
                                $item->lottery_name,
                                $item->method_name,
                                $item->issue,
                                empty($item->mode) ? '-' : $param_list['mode_list'][$item->mode]['name'],
                                ($item->operation == 2 || ($item->hold_operation == 2 && $item->operation == 0)) ? $item->amount : 0,
                                ($item->operation == 1) ? $item->amount : 0,
                                $item->balance,
                                $item->pre_balance,
                                $item->ip,
                                $item->comment,
                                $item->adminname,
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

    /**
     * 查询搜索框和数据列表显示的参数列表
     *
     * @return array
     */
    private function _paramList()
    {
        return [
            'source_list' => get_client_types(),
            'mode_list' => get_mode(),
        ];
    }
}
