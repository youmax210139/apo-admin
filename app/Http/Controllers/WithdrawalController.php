<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Service\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Service\API\Withdrawal as ApiWithdrawal;
use Service\Models\Bank as ModelBank;
use Service\Models\WithdrawalChannel;
use Service\Models\UserGroup;
use App\Http\Requests\WithdrawalIndexRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WithdrawalController extends Controller
{
    public function getIndex()
    {
        $data = array();
        $data['banks'] = ModelBank::all();
        $data['status_labels'] = ApiWithdrawal::getStatusLabels();
        $data['risk_status_labels'] = ApiWithdrawal::getRiskStatusLabels();
        $data['start_date'] = date('Y-m-d 00:00:00', strtotime("-2 days"));
        $data['adminuser'] = auth()->user()->username;
        $data['zongdai_list'] = User::select(['id', 'username'])->where('parent_id', 0)->get();
        $data['user_group'] = UserGroup::all();
        return view('withdrawal.index', $data);
    }

    /**
     * 账变列表数据
     *
     * @param OrderIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIndex(WithdrawalIndexRequest $request)
    {
        $export = (int)$request->get('export', 0);//导出Excel

        if ($request->ajax() || $export) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $param = array();
            $param['username'] = $request->get('username', ''); //会员名
            $param['agent'] = $request->get('agent', ''); //总代
            $param['ip'] = $request->get('ip', ''); //要搜索的IP
            $param['start_date'] = $request->get('start_date'); //开始时间
            $param['end_date'] = $request->get('end_date'); //结束时间
            $param['done_start_date'] = $request->get('done_start_date'); //到账开始时间
            $param['done_end_date'] = $request->get('done_end_date'); //到账结束时间
            $param['risk_start_date'] = $request->get('risk_start_date'); //风控开始时间
            $param['risk_end_date'] = $request->get('risk_end_date'); //风控结束时间
            $param['status'] = (int)$request->get('status', 0); //状态
            $param['risk_status'] = (int)$request->get('risk_status', 0); //风控状态
            $param['bank_id'] = (int)$request->get('bank'); //银行
            $param['operate_type'] = (int)$request->get('operate_type'); //提款类型
            $param['cashier'] = $request->get('cashier', ''); //出纳
            $param['calculate_total'] = $request->get('calculate_total');   //计算总计
            $param['withdrawal_id'] = (int)$request->get('withdrawal_id', 0);//订单号
            $param['user_group_id'] = (int)$request->get('user_group_id', 1);//用户组
            //查询条件
            $where = function ($query) use ($param) {
                if (!empty($param['username'])) {
                    $query->where('users.username', $param['username']);
                }
                if (!empty($param['agent'])) {
                    $query->where('top_users.username', $param['agent']);
                }
                if (!empty($param['cashier'])) {
                    $query->where('withdrawals.cashier_username', $param['cashier']);
                }
                if (!empty($param['ip'])) {
                    $query->where('withdrawals.ip', '<<=', "{$param['ip']}/24");
                }
                if ($param['status'] >= 0) {
                    if ($param['status'] == 0) {
                        $query->whereIn('withdrawals.status', [0, 3, 4, 5]);
                    } else {
                        $query->where('withdrawals.status', $param['status']);
                    }
                }
                if ($param['risk_status'] >= 0) {
                    $query->where('withdrawal_risks.status', $param['risk_status']);
                }
                if (!empty($param['start_date'])) {
                    $query->where('withdrawals.created_at', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where('withdrawals.created_at', '<=', $param['end_date']);
                }
                if (!empty($param['done_start_date'])) {
                    $query->where('withdrawals.done_at', '>=', $param['done_start_date']);
                }
                if (!empty($param['done_end_date'])) {
                    $query->where('withdrawals.done_at', '<=', $param['done_end_date']);
                }
                if (!empty($param['risk_start_date'])) {
                    $query->where('withdrawal_risks.done_at', '>=', $param['risk_start_date']);
                }
                if (!empty($param['risk_end_date'])) {
                    $query->where('withdrawal_risks.done_at', '<=', $param['risk_end_date']);
                }
                if (!empty($param['operate_type']) && $param['operate_type'] >= 0) {
                    $query->where('withdrawals.operate_type', $param['operate_type']);
                }
                if (!empty($param['bank_id'])) {
                    $query->where('user_banks.bank_id', $param['bank_id']);
                }

                $param_withdrawal_id = (int)$param['withdrawal_id'];
                if (!empty($param_withdrawal_id)) {
                    if ($param_withdrawal_id > 2147483647 || $param_withdrawal_id < 0) {
                        $param_withdrawal_id = 0;    //超过int4最大值查询会报错
                    }
                    $query->where('withdrawals.id', $param_withdrawal_id);
                }

                if (!empty($param['user_group_id'])) { //用户组
                    $query->where('users.user_group_id', $param['user_group_id']);
                }
            };

            $select_filed = ['withdrawals.id',
                'withdrawals.created_at',
                'withdrawals.done_at',
                'withdrawals.status',
                'withdrawal_risks.status as risk_status',
                'withdrawal_risks.done_at as risk_done_at',
                'withdrawal_risks.verifier_username as verifier_username',
                'withdrawal_risks.risk_remark as risk_remark',
                'withdrawal_risks.refused_msg as refused_msg',
                'withdrawals.operate_type',
                'withdrawals.cashier_username',
                'withdrawals.third_add_count',
                'withdrawals.third_check_count',
                'withdrawals.amount',
                'withdrawals.third_amount',
                'withdrawals.operate_status',
                'withdrawals.operate_status',
                'withdrawals.user_fee',
                'withdrawals.cashier_at',
                'withdrawal_channel.name as third_name',
                'withdrawal_channel.withdrawal_category_ident as third_ident',
                'user_banks.account_name as bank_account_name',
                'user_banks.account as bank_account',
                'banks.name as bank_name',
                'banks.id as bank_id',
                'users.username',
                'user_profile.value as user_observe',
                'top_users.username as top_username',
                'user_group.name as user_group_name',//用户组
            ];

            if (!empty($export)) {
                $select_filed[] = 'remark';
            }

            $data['data'] = \Service\Models\Withdrawal::select($select_filed)
                ->leftJoin('withdrawal_risks', 'withdrawal_risks.withdrawal_id', 'withdrawals.id')
                ->leftJoin('withdrawal_channel', 'withdrawal_channel.id', 'withdrawals.third_id')
                ->leftJoin('users', 'users.id', 'withdrawals.user_id')
                ->leftJoin('user_profile', function ($join) {
                    $join->on('user_profile.user_id', '=', 'users.id')
                        ->where('user_profile.attribute', 'user_observe');
                })
                ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
                ->leftJoin('user_banks', 'user_banks.id', 'withdrawals.user_bank_id')
                ->leftJoin('banks', 'banks.id', 'user_banks.bank_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->where($where);

            if (empty($export)) {
                //计算过滤后总数
                $data['recordsTotal'] = $data['recordsFiltered'] = \Service\Models\Withdrawal
                    ::leftJoin('users', 'users.id', 'withdrawals.user_id')
                    ->leftJoin('withdrawal_risks', 'withdrawal_risks.withdrawal_id', 'withdrawals.id')
                    ->leftJoin('user_banks', 'user_banks.id', 'withdrawals.user_bank_id')
                    ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
                    ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                    ->where($where)->count();

                // 统计所有金额
                if ($param['calculate_total'] == '2' && $data['recordsTotal'] < 1000000) {
                    $sum_amount = \Service\Models\Withdrawal::select([
                        DB::RAW('sum(withdrawals.amount) as total'),
                        DB::RAW('(SUM(CASE WHEN withdrawals.status=1 THEN withdrawals.amount ELSE 0 END)+SUM(CASE WHEN withdrawals.status=1 THEN withdrawals.user_fee ELSE 0 END)) as real_amount')
                    ])
                        ->leftJoin('users', 'users.id', 'withdrawals.user_id')
                        ->leftJoin('withdrawal_risks', 'withdrawal_risks.withdrawal_id', 'withdrawals.id')
                        ->leftJoin('user_banks', 'user_banks.id', 'withdrawals.user_bank_id')
                        ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
                        ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                        ->where($where)
                        ->first();

                    $data['sum_amount'] = ['total' => 0, 'real_amount' => 0];

                    if ($sum_amount) {
                        $data['sum_amount'] = $sum_amount;
                    }
                }

                $data['data'] = $data['data']->skip($start)->take($length)
                    ->orderBy("withdrawals.id", 'DESC')
                    ->get();

                if ($data['data']) {
                    $status_labels = ApiWithdrawal::getStatusLabels();
                    $operate_status_labels = ApiWithdrawal::getOperateStatusLabels();
                    $risk_labels = ApiWithdrawal::getRiskStatusLabels();
                    $withdrawal_third = \Service\Models\WithdrawalChannel::select(['banks', 'amount_min', 'amount_max'])->where('withdrawal_channel.status', true)
                        ->leftJoin('withdrawal_category', 'withdrawal_category.ident', 'withdrawal_channel.withdrawal_category_ident')
                        ->get();

                    foreach ($data['data'] as &$v) {
                        //字符截取
                        $v->bank_name = str_limit(trim($v->bank_name), 10, '..');
                        //隐藏帐号和姓名
                        $v->bank_account = hide_str($v->bank_account, 4, -3);
                        //获取状态标签
                        if (in_array($v->status, array_keys($status_labels))) {
                            $v->status_label = $status_labels[$v->status];
                        } else {
                            $v->status_label = '未知状态[' . $v->status . ']';
                        }
                        if (in_array($v->risk_status_label, array_keys($risk_labels))) {
                            $v->risk_status_label = $risk_labels[$v->risk_status];
                        } else {
                            $v->risk_status_label = '未知状态[' . $v->status . ']';
                        }
                        if (in_array($v->operate_type, array_keys($operate_status_labels)) && (!empty($v->operate_status))) {
                            $v->operate_status_label = $operate_status_labels[$v->operate_status];
                        } else {
                            $v->operate_status_label = '未知状态[' . $v->operate_status . ']';
                        }
                        //判断是否符合第三方条件
                        $v->is_third = false;
                        foreach ($withdrawal_third as $b) {
                            if (in_array($v->bank_id, explode(",", $b->banks))
                                && ($v->amount >= $b->amount_min)
                                && ($v->amount <= $b->amount_max)
                            ) {
                                $v->is_third = true;
                                break;
                            }
                        }
                        //计算虚拟货币汇率
                        if ($v->third_amount > 0) {
                            $v->virtual_rate = round($v->amount / $v->third_amount, 4);
                            $v->real_third_amount = round(($v->amount + $v->user_fee) / $v->virtual_rate, 4);
                        } else {
                            $v->virtual_rate = 0;
                            $v->real_third_amount = 0;
                        }
                    }
                }
                return response()->json($data);
            } else {
                //导出数据
                $file_name = "提现记录.csv";
                $query = $data['data'];
                $response = new StreamedResponse(null, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
                ]);

                $response->setCallback(function () use ($query) {
                    $out = fopen('php://output', 'w');
                    fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                    $first = true;

                    $status_labels = ApiWithdrawal::getStatusLabels();
                    $risk_labels = ApiWithdrawal::getRiskStatusLabels();

                    $query->orderBy("withdrawals.id", 'DESC')->chunk(500, function ($results) use (&$first, $out, $status_labels, $risk_labels) {
                        if ($first) {
                            //列名
                            $columnNames[] = '订单编号';
                            $columnNames[] = '会员';
                            $columnNames[] = '总代';
                            $columnNames[] = '用户组';
                            $columnNames[] = '银行';
                            $columnNames[] = '开户名';
                            //$columnNames[] = '银行卡号';
                            $columnNames[] = '金额';
                            $columnNames[] = '实际虚拟币金额';
                            $columnNames[] = '汇率';
                            $columnNames[] = '实际出款金额';
                            $columnNames[] = '申请时间';
                            $columnNames[] = '风险审核';
                            $columnNames[] = '风控';
                            $columnNames[] = '风控时间';
                            $columnNames[] = '风控耗时';
                            $columnNames[] = '出款状态';
                            $columnNames[] = '出款时间';
                            $columnNames[] = '出款耗时';
                            $columnNames[] = '出款第三方';
                            $columnNames[] = '第三方出款状态';
                            $columnNames[] = '出纳';
                            $columnNames[] = '出纳备注';
                            fputcsv($out, $columnNames);
                            $first = false;
                        }
                        foreach ($results as $item) {
                            //获取状态标签
                            if (in_array($item->status, array_keys($status_labels))) {
                                $item->status_label = $status_labels[$item->status];
                            } else {
                                $item->status_label = '未知状态[' . $item->status . ']';
                            }
                            if (in_array($item->risk_status_label, array_keys($risk_labels))) {
                                $item->risk_status_label = $risk_labels[$item->risk_status];
                            } else {
                                $item->risk_status_label = '未知状态[' . $item->status . ']';
                            }

                            $item->operate_status_label = '';
                            if ($item->operate_type === 2) {
                                switch ($item->operate_status) {
                                    case 11:
                                        $item->operate_status_label = '汇款成功';
                                        break;
                                    case 12:
                                        $item->operate_status_label = '汇款失败';
                                        break;
                                    case 13:
                                        $item->operate_status_label = '排队确认汇款是否成功';
                                        break;
                                    case 14:
                                        $item->operate_status_label = '发送汇款信息失败';
                                        break;
                                    case 15:
                                        $item->operate_status_label = '排队发送汇款信息中';
                                        break;
                                    default:
                                }
                            }
                            //审核耗时
                            if ($item->risk_done_at) {
                                $created_at = Carbon::parse($item->created_at);
                                $risk_done_at = Carbon::parse($item->risk_done_at);
                                $days = $risk_done_at->diffInDays($created_at);
                                $hours = ($risk_done_at->diffInHours($created_at)) % 24;
                                $minutes = $risk_done_at->diffInMinutes($created_at) % 60;
                                $seconds = $risk_done_at->diffInRealSeconds($created_at) % 60;
                                $item->risk_done_time_consuming = $days . '天' . $hours . '时' . $minutes . '分' . $seconds . '秒';
                            } else {
                                $item->risk_done_time_consuming = '';
                            }
                            //出款耗时
                            if ($item->done_at) {
                                $done_at = Carbon::parse($item->done_at);
                                $days = $done_at->diffInDays($risk_done_at);
                                $hours = ($done_at->diffInHours($risk_done_at)) % 24;
                                $minutes = $done_at->diffInMinutes($risk_done_at) % 60;
                                $seconds = $done_at->diffInRealSeconds($risk_done_at) % 60;
                                $item->done_time_consuming = $days . '天' . $hours . '时' . $minutes . '分' . $seconds . '秒';
                            } else {
                                $item->done_time_consuming = '';
                            }

                            //计算虚拟货币汇率
                            if ($item->third_amount > 0) {
                                $item->virtual_rate = round($item->amount / $item->third_amount, 4);
                                $item->real_third_amount = round(($item->amount + $item->user_fee) / $item->virtual_rate, 4);
                            } else {
                                $item->virtual_rate = 0;
                                $item->real_third_amount = 0;
                            }

                            fputcsv($out, [
                                $item->id,
                                $item->username,
                                $item->top_username,
                                $item->user_group_name,
                                $item->bank_name,
                                $item->bank_account_name,
                                //hide_str($item->bank_account, 4, -3),
                                $item->amount,
                                $item->real_third_amount,
                                $item->virtual_rate,
                                ($item->status == 1) ? $item->amount + $item->user_fee : '', //代付金额减手续费为实际出款金额
                                $item->created_at,
                                $item->risk_status_label,
                                $item->verifier_username,
                                $item->risk_done_at,
                                $item->risk_done_time_consuming,
                                $item->status_label,
                                $item->done_at,
                                $item->done_time_consuming,
                                ($item->operate_type === 2) ? $item->third_name : '',
                                $item->operate_status_label,
                                $item->cashier_username,
                                $item->remark,
                            ]);
                        }
                    });
                    fclose($out);
                });
                $response->send();
            }
        }
    }

    /**
     * 出纳手工操作页面
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeal(Request $request)
    {
        $id = (int)$request->get('id', 0);

        DB::beginTransaction();
        try {
            $sub = \Service\Models\Withdrawal::where('withdrawals.id', $id)->lock('for update nowait');

            $row = DB::table(DB::raw("({$sub->toSql()}) as withdrawals"))
                ->select([
                    'withdrawals.*',
                    'users.username',
                    'banks.name as bank_name',
                    'user_banks.account_name',
                    'user_banks.account',
                    'regions.name as province',
                    'reg.name as city',
                    'topusers.username as topusername',
                    'withdrawal_risks.status as risk_status',
                    'bank_virtual.rate',
                    'bank_virtual.ident as bv_ident'
                ])
                ->mergeBindings($sub->getQuery())
                ->leftJoin('withdrawal_risks', 'withdrawal_risks.withdrawal_id', 'withdrawals.id')
                ->leftJoin('users', 'users.id', 'withdrawals.user_id')
                ->leftJoin('users as topusers', 'topusers.id', 'users.top_id')
                ->leftJoin('user_banks', 'user_banks.id', 'withdrawals.user_bank_id')
                ->leftJoin('banks', 'banks.id', 'user_banks.bank_id')
                ->leftJoin('regions', 'regions.id', 'user_banks.province_id')
                ->leftJoin('regions as reg', 'reg.id', 'user_banks.city_id')
                ->leftJoin('bank_virtual', 'bank_virtual.ident', 'banks.ident')
                ->first();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '提现单处理中！'));
        }

        if (!$row) {
            DB::rollBack();
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }
        if ($row->risk_status <> 1) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '该笔提现未通过风控审核'));
        }
        if (in_array($row->status, array(1, 2, 4))) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '该笔提现已处理！'));
        }
        if ($row->status == 3 && $row->cashier_username <> auth()->user()->username) {
            DB::rollBack();
            return response()->json(array('status' => 2, 'msg' => '该笔提现已经被锁定！认领出纳员：' . $row->cashier_username));
        }
        //认领锁定记录防止其他管理员操作
        $res = \Service\Models\Withdrawal::where('withdrawals.id', $id)->update([
            'status' => 3,
            'cashier_username' => auth()->user()->username,
            'cashier_at' => date("Y-m-d H:i:s")
        ]);

        $data = ['status' => 1, 'msg' => ''];
        if ($res) {
            DB::commit();
            // 如果没有通道手续费，则检查是否有全局手续费
            $api_withdrawal = new ApiWithdrawal();
            if ($row->user_fee == 0 && get_config('withdrawal_global_fee_status', 0)) {
                $row->user_fee = $api_withdrawal->calculateGlobalFee($row->amount, $row->user_id, $row->id, $row->created_at);
            }
            $row->this_withdrawal_times = $api_withdrawal->getUserDayWithdrawalTimes($row->user_id, $row->id, $row->created_at);
            $row->this_withdrawal_date = Carbon::parse($row->created_at)->format('Y-m-d');

            $data['status'] = 0;
            $row->banks = \Service\Models\Bank::where('disabled', false)->where('withdraw', true)->get()->toArray();

            //检查是否为虚拟钱包银行(USDT) 如果为空就给预设值
            $row->rate = $row->rate ?? 1;

            //用户信息
            $user_profiles = \Service\Models\UserProfile::where('user_id', $row->user_id)->whereIn('attribute', ['remark', 'user_observe'])->get()->keyBy('attribute');
            $row->user_remark = isset($user_profiles['remark']) ? $user_profiles['remark']->value : '';
            $row->user_observe = isset($user_profiles['user_observe']) ? $user_profiles['user_observe']->value : '';

            $data['data'] = (string)view('withdrawal.deal', (array)$row);
        } else {
            DB::rollBack();
            $data['msg'] = '提现单认领失败！';
        }
        return response()->json($data);
    }

    /**
     * 保存出纳手工操作结果
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putDeal(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = \Service\Models\Withdrawal::select([
            'withdrawals.*',
            'withdrawal_risks.status as risk_status',
            'bank_virtual.rate as virtual_rate',
        ])
            ->leftJoin('withdrawal_risks', 'withdrawal_risks.withdrawal_id', 'withdrawals.id')
            ->leftJoin('user_banks', 'user_banks.id', 'withdrawals.user_bank_id')
            ->leftJoin('banks', 'banks.id', 'user_banks.bank_id')
            ->leftJoin('bank_virtual', 'bank_virtual.ident', 'banks.ident')
            ->where('withdrawals.id', $id)
            ->first();

        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }
        if ($row->risk_status <> 1) {
            return response()->json(array('status' => 1, 'msg' => '该笔提现未通过风控审核'));
        }
        if (!$row->cashier_username) {
            return response()->json(array('status' => 2, 'msg' => '数据错误'));
        }
        if (in_array($row->status, [1, 2, 4])) {
            return response()->json(array('status' => 1, 'msg' => '该笔提现已处理！'));
        }
        if ($row->status == 3 && $row->cashier_username <> auth()->user()->username) {
            return response()->json(array('status' => 2, 'msg' => '该笔提现已经被锁定！认领人：' . $row->cashier_username));
        }
        $user_fee = abs((float)$request->get('user_fee', 0));
        $user_fee_option = (int)$request->get('user_fee_option', 0);
        if ($user_fee_option == 1) {
            $user_fee = -$user_fee;
        }
        $platform_fee = (float)$request->get('platform_fee', 0);
        $bank_id = (int)$request->get('bank_id', 0);
        $bank_order_no = $request->get('bank_order_no', '');
        $remark = $request->get('remark', '');
        $flag = (int)$request->get('flag', 1); //设置结果 2为成功
        if (!in_array($flag, [1, 2])) {
            return response()->json(array('status' => 1, 'msg' => '无效的提现处理状态！'));
        }
        if ($flag == 1) {
            if (empty($bank_id)) {
                return response()->json(array('status' => 1, 'msg' => '请选择出款银行！'));
            }
            if (empty($bank_order_no)) {
                return response()->json(array('status' => 1, 'msg' => '请填写出款交易流水号！'));
            }
            if (strlen($bank_order_no) > 64) {
                return response()->json(array('status' => 1, 'msg' => '出款交易流水号不能超过64个字符！'));
            }
        } else {
            if (empty($remark)) {
                return response()->json(array('status' => 1, 'msg' => '请填写出款备注！'));
            }
        }
        try {
            DB::beginTransaction();
            //认领锁定记录防止其他管理员操作
            $row->cashier_ip = $request->ip();
            $row->user_fee = $user_fee;
            $row->platform_fee = $platform_fee;
            $row->third_amount = empty($row->virtual_rate) ? 0 : round($row->amount / $row->virtual_rate, 4);
            $row->done_at = date("Y-m-d H:i:s");
            $row->remark = $remark;
            $row->status = $flag;
            $row->bank_id = $bank_id;
            $row->bank_order_no = $bank_order_no;
            $row->operate_type = 1;
            if ($row->save()) {
                $apiWithdrawal = new ApiWithdrawal();
                if ($apiWithdrawal->doneWithdrawalOrder($row)) {
                    DB::commit();
                    return response()->json(array('status' => 0, 'msg' => '操作成功！'));
                }
                DB::rollBack();
                return response()->json(array('status' => 1, 'msg' => '操作失败！' . $apiWithdrawal->error_msg));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '操作失败！' . $e->getMessage()));
        }
    }

    /**
     * 出纳将结果提交到第三方
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDealThird(Request $request)
    {
        $id = (int)$request->get('id', 0);

        DB::beginTransaction();
        try {
            $sub = \Service\Models\Withdrawal::where('withdrawals.id', $id)->lock('for update nowait');

            $row = DB::table(DB::raw("({$sub->toSql()}) as withdrawals"))
                ->select([
                    'withdrawals.*',
                    'users.username',
                    'banks.id as bank_id',
                    'banks.name as bank_name',
                    'user_banks.account_name',
                    'user_banks.account',
                    'regions.name as province',
                    'reg.name as city',
                    'topusers.username as topusername',
                    'withdrawal_risks.status as risk_status'
                ])
                ->mergeBindings($sub->getQuery())
                ->leftJoin('withdrawal_risks', 'withdrawal_risks.withdrawal_id', 'withdrawals.id')
                ->leftJoin('users', 'users.id', 'withdrawals.user_id')
                ->leftJoin('users as topusers', 'topusers.id', 'users.top_id')
                ->leftJoin('user_banks', 'user_banks.id', 'withdrawals.user_bank_id')
                ->leftJoin('banks', 'banks.id', 'user_banks.bank_id')
                ->leftJoin('regions', 'regions.id', 'user_banks.province_id')
                ->leftJoin('regions as reg', 'reg.id', 'user_banks.city_id')
                ->first();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '提现单处理中！'));
        }

        if (!$row) {
            DB::rollBack();
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }
        if ($row->risk_status <> 1) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '该笔提现未通过风控审核'));
        }
        if (in_array($row->status, array(1, 2, 4))) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '该笔提现已处理！' . $row->status));
        }
        if ($row->status == 3 && $row->cashier_username <> auth()->user()->username) {
            DB::rollBack();
            return response()->json(array('status' => 2, 'msg' => '该笔提现已经被锁定！认领人：' . $row->cashier_username));
        }

        $api_withdrawal = new ApiWithdrawal();

        $withdrawal_thirds = \Service\Models\WithdrawalChannel::select([
            \DB::raw('withdrawal_channel.id as id'),
            \DB::raw('withdrawal_channel.name as name'),
            'withdrawal_category_ident',
            'banks',
            'amount_min',
            'amount_max',
            'user_fee_status',
            'user_fee_operation',
            'user_fee_step',
            'user_fee_up_type',
            'user_fee_up_value',
            'user_fee_down_type',
            'user_fee_down_value',
        ])
            ->where('withdrawal_channel.status', true)
            ->leftJoin('withdrawal_category', 'withdrawal_category.ident', 'withdrawal_channel.withdrawal_category_ident')
            ->get();

        foreach ($withdrawal_thirds as &$withdrawal_third) {
            $withdrawal_third->fee_exp = $api_withdrawal->calculateFee(
                $row->amount,
                $withdrawal_third->user_fee_status,
                $withdrawal_third->user_fee_operation,
                $withdrawal_third->user_fee_step,
                $withdrawal_third->user_fee_up_type,
                $withdrawal_third->user_fee_up_value,
                $withdrawal_third->user_fee_down_type,
                $withdrawal_third->user_fee_down_value
            );
        }

        //认领锁定记录防止其他管理员操作
        $res = \Service\Models\Withdrawal::where('withdrawals.id', $id)->update([
            'status' => 3,
            'cashier_username' => auth()->user()->username,
        ]);

        $data = ['status' => 1, 'msg' => ''];
        if ($res) {
            DB::commit();
            // 如果没有通道手续费，则检查是否有全局手续费
            $api_withdrawal = new ApiWithdrawal();
            if ($row->user_fee == 0 && get_config('withdrawal_global_fee_status', 0)) {
                $row->user_fee = $api_withdrawal->calculateGlobalFee($row->amount, $row->user_id, $row->id, $row->created_at);
            }

            $data['status'] = 0;
            $row->thirdapis = $withdrawal_thirds->toArray();

            //用户信息
            $user_profiles = \Service\Models\UserProfile::where('user_id', $row->user_id)->whereIn('attribute', ['remark', 'user_observe'])->get()->keyBy('attribute');
            $row->user_remark = isset($user_profiles['remark']) ? $user_profiles['remark']->value : '';
            $row->user_observe = isset($user_profiles['user_observe']) ? $user_profiles['user_observe']->value : '';

            $data['data'] = (string)view('withdrawal.dealthird', (array)$row);
        } else {
            DB::rollBack();
            $data['msg'] = '提现单认领失败！';
        }
        return response()->json($data);
    }

    /**
     * 保存出纳对于第三方出款的操作结果
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putDealThird(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = \Service\Models\Withdrawal::select([
            'withdrawals.*',
            'withdrawal_risks.status as risk_status'
        ])
            ->leftJoin('withdrawal_risks', 'withdrawal_risks.withdrawal_id', 'withdrawals.id')
            ->where('withdrawals.id', $id)->first();
        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }
        if ($row->risk_status <> 1) {
            return response()->json(array('status' => 1, 'msg' => '该笔提现未通过风控审核'));
        }
        if (!$row->cashier_username) {
            return response()->json(array('status' => 2, 'msg' => '数据错误'));
        }
        if (in_array($row->status, array(1, 2, 4))) {
            return response()->json(array('status' => 1, 'msg' => '该笔提现已处理！'));
        }
        if ($row->status == 3 && $row->cashier_username <> auth()->user()->username) {
            return response()->json(array('status' => 2, 'msg' => '该笔提现已经被锁定！认领出纳员：' . $row->cashier_username));
        }
        $third_id = (int)$request->get('withdrawalapi', 0);
        if (empty($third_id)) {
            return response()->json(array('status' => 1, 'msg' => '请选择出款接口！'));
        }

        $withdrawal_third = WithdrawalChannel::find($third_id);
        if (empty($withdrawal_third)) {
            return response()->json(array('status' => 1, 'msg' => '无效的出款接口ID！'));
        }


        //认领锁定记录防止其他管理员操作
        $row->third_id = $third_id;
        $row->cashier_at = date("Y-m-d H:i:s");
        $row->status = 4;
        $row->operate_type = 2;
        $row->operate_status = 15;

        // 重置第三方出款次数和确认次数为0
        $row->third_add_count = 0;
        $row->third_check_count = 0;


        $row->cashier_ip = $request->ip();
        //计算手续费
        $api = new ApiWithdrawal();
        $row->user_fee = $api->calculateFee(
            $row->amount,
            $withdrawal_third->user_fee_status,
            $withdrawal_third->user_fee_operation,
            $withdrawal_third->user_fee_step,
            $withdrawal_third->user_fee_up_type,
            $withdrawal_third->user_fee_up_value,
            $withdrawal_third->user_fee_down_type,
            $withdrawal_third->user_fee_down_value
        );

        // 如果没有通道手续费，则检查是否有全局手续费
        if ($row->user_fee == 0 && get_config('withdrawal_global_fee_status', 0)) {
            $row->user_fee = $api->calculateGlobalFee($row->amount, $row->user_id, $row->id, $row->created_at);
        }

        $row->platform_fee = $api->calculateFee(
            $row->amount,
            $withdrawal_third->platform_fee_status,
            0,
            $withdrawal_third->platform_fee_step,
            $withdrawal_third->platform_fee_up_type,
            $withdrawal_third->platform_fee_up_value,
            $withdrawal_third->platform_fee_down_type,
            $withdrawal_third->platform_fee_down_value
        );
        if ($row->save()) {
            return response()->json(array('status' => 0, 'msg' => '操作成功！'));
        }
    }

    public function getDetail(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = \Service\Models\Withdrawal::select([
            'withdrawals.*',
            'users.username',
            'user_banks.account_name',
            'user_banks.account',
            'banks.name as user_bank_name',
            'cashier_banks.id as cashier_bank_id',//出纳手工出款的银行ID
            'cashier_banks.ident as cashier_bank_ident',//出纳手工出款的银行标识
            'cashier_banks.name as cashier_bank_name',//出纳手工出款的银行名称
            'regions.name as province',
            'reg.name as city',
            'topusers.username as topusername',
            'withdrawal_channel.withdrawal_category_ident as third_ident',
            'withdrawal_channel.name as third_name',
            'withdrawal_risks.id as risk_id',//认领风控ID
            'withdrawal_risks.verifier_username as verifier_username',//认领风控管理员
            'withdrawal_risks.done_at as risk_done_at',//审核时间
            'withdrawal_risks.status as risk_status',//审核状态，0待审核 1审核中 2审核通过 3审核拒绝
        ])
            ->leftJoin('withdrawal_risks', 'withdrawal_risks.withdrawal_id', 'withdrawals.id')
            ->leftJoin('withdrawal_channel', 'withdrawal_channel.id', 'withdrawals.third_id')
            ->leftJoin('users', 'users.id', 'withdrawals.user_id')
            ->leftJoin('users as topusers', 'topusers.id', 'users.top_id')
            ->leftJoin('user_banks', 'user_banks.id', 'withdrawals.user_bank_id')
            ->leftJoin('banks', 'banks.id', 'user_banks.bank_id')
            ->leftJoin('banks as cashier_banks', 'cashier_banks.id', 'withdrawals.bank_id')
            ->leftJoin('regions', 'regions.id', 'user_banks.province_id')
            ->leftJoin('regions as reg', 'reg.id', 'user_banks.city_id')
            ->where('withdrawals.id', $id)->first();

        $risk_status_labels = ApiWithdrawal::getRiskStatusLabels();
        $row->risk_status_label = $risk_status_labels[$row->risk_status];
        $status_labels = ApiWithdrawal::getStatusLabels();
        $row->status_label = $status_labels[$row->status];

        $api_withdrawal = new ApiWithdrawal();
        $row->this_withdrawal_times = $api_withdrawal->getUserDayWithdrawalTimes($row->user_id, $row->id, $row->created_at);
        $row->this_withdrawal_date = Carbon::parse($row->created_at)->format('Y-m-d');

        $data['status'] = 0;
        $data['data'] = (string)view('withdrawal.detail', $row);
        return response()->json($data);
    }
}
