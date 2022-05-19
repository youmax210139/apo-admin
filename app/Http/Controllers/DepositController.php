<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Service\API\Deposit as ApiDeposit;
use Service\Models\UserGroup;
use Service\Models\User;

class DepositController extends Controller
{
    public function getIndex()
    {
        $data = array();
        $data['banks'] = \Service\Models\PaymentChannel::
        select(['payment_channel.*', 'payment_category.name as payment_category_name'])
            ->leftJoin('payment_category', 'payment_category.id', 'payment_channel.payment_category_id')
            ->orderBy('payment_channel.status', 'desc')
            ->orderBy('payment_channel.id', 'desc')
            ->get();
        $data['start_date'] = date('Y-m-d 00:00:00', strtotime("-2 days"));
        $data['adminuser'] = auth()->user()->username;
        $data['user_group'] = UserGroup::all();
        return view('deposit.index', $data);
    }

    /**
     * 账变列表数据
     *
     * @param OrderIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIndex(Request $request)
    {
        $export = (int)$request->get('export', 0);//导出Excel

        if ($request->ajax() || $export) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $param = array();
            $param['username'] = $request->get('username', ''); //会员名
            $param['order_id'] = $request->get('order_id', ''); //附言
            $param['start_date'] = $request->get('start_date'); //申请时间
            $param['end_date'] = $request->get('end_date'); //申请时间
            $param['done_start_date'] = $request->get('done_start_date'); //到帐时间
            $param['done_end_date'] = $request->get('done_end_date'); //到帐时间
            $param['deal_start_date'] = $request->get('deal_start_date'); //到帐时间
            $param['deal_end_date'] = $request->get('deal_end_date'); //到帐时间
            $param['status'] = (int)$request->get('status', 0); //状态
            $param['payment_channel_id'] = $request->get('payment_channel_id'); //支付通道
            $param['include_next_level'] = $request->get('include_next_level', 0);
            $param['calculate_total'] = $request->get('calculate_total');   //计算合计
            $param['user_group_id'] = (int)$request->get('user_group_id', 1);//用户组

            if ($param['include_next_level'] == 2) {
                DB::select('set enable_nestloop to false');
            }

            //查询条件
            $where = function ($query) use ($param) {
                if (!empty($param['username'])) {//会员名
                    if ($param['include_next_level']) {
                        $user_id = User::where('users.username', $param['username'])->value('id');
                        if (!empty($user_id)) {
                            if ($param['include_next_level'] == 1) {
                                $_where = function ($query) use ($user_id) {
                                    $query->where('users.parent_id', $user_id)
                                        ->orWhere('users.id', $user_id);
                                };
                            } else {
                                $_where = function ($query) use ($user_id) {
                                    $query->where('users.id', $user_id)
                                        ->orWhere('users.parent_tree', '@>', $user_id);
                                };
                            }

                            $query->where($_where);
                        } else {
                            $query->where('users.username', $param['username']);
                        }
                    } else {
                        $query->where('users.username', $param['username']);
                    }
                }
                if (!empty($param['order_id'])) {
                    $param['order_id'] = id_decode($param['order_id']);
                    if (!empty($param['order_id']) && is_numeric($param['order_id'])) {
                        $query->where('deposits.id', $param['order_id']);
                    } else {
                        $query->where('deposits.remark', $param['order_id']);
                    }
                }
                if ($param['status'] >= 0) {//状态
                    $query->where('deposits.status', $param['status']);
                }
                if (!empty($param['start_date'])) {//最小申请时间
                    $query->where('deposits.created_at', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {//最大申请时间
                    $query->where('deposits.created_at', '<=', $param['end_date']);
                }
                if (!empty($param['done_start_date'])) {//最小到帐时间
                    $query->where('deposits.done_at', '>=', $param['done_start_date']);
                }
                if (!empty($param['done_end_date'])) {//最大到帐时间
                    $query->where('deposits.done_at', '<=', $param['done_end_date']);
                }
                if (!empty($param['deal_start_date'])) {//最小到帐时间
                    $query->where('deposits.deal_at', '>=', $param['deal_start_date']);
                }
                if (!empty($param['deal_end_date'])) {//最大到帐时间
                    $query->where('deposits.deal_at', '<=', $param['deal_end_date']);
                }
                if (!empty($param['operate_type']) && $param['operate_type'] >= 0) {
                    $query->where('deposits.operate_type', $param['operate_type']);
                }
                if (!empty($param['payment_channel_id']) && is_array($param['payment_channel_id'])) {//支付通道
                    $query->whereIn('deposits.payment_channel_id', $param['payment_channel_id']); //通道编号
                }
                if (!empty($param['user_group_id'])) { //用户组
                    $query->where('users.user_group_id', $param['user_group_id']);
                }
            };

            $data['data'] = \Service\Models\Deposit::select(
                ['deposits.id',
                    'deposits.created_at',//申请时间
                    'deposits.status',//状态
                    'deposits.accountant_admin',//财务，就是点人工审核的用户
                    'deposits.cash_admin',//出纳，就是点击冲入游戏币的用户
                    'deposits.payment_channel_id',//支付通道ID
                    'deposits.remark',//备注
                    'payment_channel.name as payment_channel_name',//支付通道后台名称
                    'payment_category.name as payment_category_name',//支付渠道名称
                    'payment_category.id as payment_category_id',//支付渠道ID
                    'payment_category.ident as payment_category_ident',//支付渠道英文标识
                    'deposits.amount',//充值金额
                    'deposits.user_fee',//手续费
                    'deposits.manual_postscript',//人工输入附言
                    'users.username',//冲值用户
                    'user_profile.value as user_observe',//重点观察原因
                    'top_users.username as top_username',//总代
                    'deposits.deal_at',//人工审核时间
                    'deposits.done_at',//冲入游戏币时间
                    'user_group.name as user_group_name',//用户组
                    'payment_category.methods',
                    'deposits.third_amount',//虚拟币金额
                ]
            )
                ->leftJoin('users', 'users.id', 'deposits.user_id')
                ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
                ->leftJoin('user_profile', function ($join) {
                    $join->on('user_profile.user_id', '=', 'users.id')
                        ->where('user_profile.attribute', 'user_observe');
                })
                ->leftJoin('payment_channel', 'payment_channel.id', 'deposits.payment_channel_id')
                ->leftJoin('payment_category', 'payment_category.id', 'deposits.payment_category_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->where($where);

            if (empty($export)) {
                //计算过滤后总数
                $data['recordsTotal'] = $data['recordsFiltered'] = \Service\Models\Deposit
                    ::leftJoin('users', 'users.id', 'deposits.user_id')
                    ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
                    ->leftJoin('payment_channel', 'payment_channel.id', 'deposits.payment_channel_id')
                    ->leftJoin('payment_category', 'payment_category.id', 'deposits.payment_category_id')
                    ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                    ->where($where)->count();

                // 统计所有信息
                if ($param['calculate_total'] == '2' && $data['recordsTotal'] < 1000000) {
                    $sum_amount = \Service\Models\Deposit::select([
                        DB::RAW('sum(deposits.amount) as sum_money'),
                        DB::RAW('sum(deposits.user_fee) as sum_user_fee')])
                        ->leftJoin('users', 'users.id', 'deposits.user_id')
                        ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
                        ->leftJoin('payment_channel', 'payment_channel.id', 'deposits.payment_channel_id')
                        ->leftJoin('payment_category', 'payment_category.id', 'deposits.payment_category_id')
                        ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                        ->where($where)
                        ->first();

                    $data['sum_amount'] = ['sum_money' => 0, 'sum_user_fee' => 0];

                    if ($sum_amount) {
                        $data['sum_amount'] = $sum_amount;
                    }
                }

                $data['data'] = $data['data']->skip($start)->take($length)
                    ->orderBy('deposits.created_at', 'DESC')
                    ->get();
                $data['alert'] = false;
                if ($data['data']) {
                    $status_labels = ApiDeposit::getStatusLabels();
                    foreach ($data['data'] as &$deposit) {
                        $deposit->id_encode = id_encode($deposit->id);
                        $deposit->status_label = $status_labels[$deposit->status];
                        $methods = json_decode($deposit->methods, true) ?? [];
                        if (count(array_intersect(['transfer', 'qrcode_offline'], $methods)) > 0 && $deposit->status == 0) {
                            $data['alert'] = true;
                        }
                        if ($deposit->third_amount == 0) {
                            $deposit->third_amount = '';
                        }
                        $isVirtualCurrency = ApiDeposit::isVirtualCurrency($deposit->payment_category_ident);
                        //判断是否为虚拟货币银行管理页面之使用渠道
                        $getRate = $this->getRate($deposit->payment_category_ident);
                        if ($isVirtualCurrency) {
                            $deposit->rate = $this->setRate($deposit->amount, $deposit->third_amount);
                        } else if (!empty($getRate)) {
                            $deposit->rate = $getRate;
                        } else {
                            $deposit->rate = '';
                        }
                    }
                }
                return response()->json($data);
            } else {
                //导出数据
                $file_name = "充值记录.csv";
                $query = $data['data'];
                $response = new StreamedResponse(null, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
                ]);
                $response->setCallback(function () use ($query) {
                    $out = fopen('php://output', 'w');
                    fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                    $first = true;
                    $query->orderBy('deposits.created_at', 'DESC')->chunk(500, function ($results) use (&$first, $out) {
                        if ($first) {
                            //列名
                            $columnNames[] = '订单编号';
                            $columnNames[] = '状态';
                            $columnNames[] = '会员';
                            $columnNames[] = '总代';
                            $columnNames[] = '用户组';
                            $columnNames[] = '渠道';
                            $columnNames[] = '通道';
                            $columnNames[] = '金额';
                            $columnNames[] = '用户手续费';
                            $columnNames[] = '人工输入附言';
                            $columnNames[] = '备注';
                            $columnNames[] = '汇率';
                            $columnNames[] = '汇算后金额';
                            $columnNames[] = '申请时间';
                            $columnNames[] = '审核时间';
                            $columnNames[] = '到账时间';
                            $columnNames[] = '会计';
                            $columnNames[] = '出纳';
                            fputcsv($out, $columnNames);
                            $first = false;
                        }
                        foreach ($results as $item) {
                            $status_lable = Apideposit::getStatusLabels();
                            $currency = ApiDeposit::isVirtualCurrency($item->payment_category_ident) ? 'USDT' : 'CNY';
                            //判断是否为虚拟货币银行管理页面之使用渠道
                            $getRate = $this->getRate($item->payment_category_ident);
                            if ($currency == 'USDT') {
                                $rate = $this->setRate($item->amount, $item->third_amount);
                            } else if (!empty($getRate)) {
                                $rate = $getRate;
                            } else {
                                $rate = '';
                            }
                            if ($item->third_amount == 0) {
                                $item->third_amount = '';
                            }
                            $item->status_lable = $status_lable[$item->status];
                            fputcsv($out, [
                                id_encode($item->id),
                                $item->status_lable,
                                $item->username,
                                $item->top_username,
                                $item->user_group_name,
                                $item->payment_category_name,
                                $item->payment_channel_name,
                                $item->amount,
                                $item->user_fee,
                                $item->manual_postscript,
                                $item->remark,
                                $rate,
                                $item->third_amount,
                                $item->created_at,
                                $item->deal_at,
                                $item->done_at,
                                $item->accountant_admin,
                                $item->cash_admin,
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
     * 打开审核页面，更新 accountant_admin
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDeal(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = $this->_forDealCarry($id);
        if (in_array($row->status, [2, 3])) {
            return response()->json(array('status' => 1, 'msg' => '该笔充值已处理！'));
        } elseif(in_array($row->status,[1])){
            return response()->json(array('status' => 1, 'msg' => '该笔充值已经进入“充入游戏币”环节，无法再次“人工审核”'));
        }elseif (in_array($row->status, [0])) {
            //同一笔单子不允许多人审核
            if (!empty($row->accountant_admin) && $row->accountant_admin <> auth()->user()->username) {
                return response()->json(array('status' => 2, 'msg' => '该笔充值正在人工审核中！会计：' . $row->accountant_admin));
            }
            //进行人工审核操作，标识会计人员
            $row->accountant_admin = auth()->user()->username;
            if ($row->save()) {
                $data['status'] = 0;
                $row->extra = json_decode($row->extra, true);
                $data['data'] = (string)view('deposit.deal_acct', $row);
                return response()->json($data);
            }
        }else{
            return response()->json(array('status' => 1, 'msg' => '无效的状态-'.$row->status));
        }
    }

    /**
     * 处理审核操作
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putDeal(Request $request)
    {
        $id = (int)$request->get('id', 0);
        DB::beginTransaction();
        $row = \Service\Models\Deposit::select([
            'deposits.*',
            'payment_category.ident as payment_category_ident',//支付渠道英文标识
        ])
            ->leftJoin('payment_category', 'payment_category.id', 'deposits.payment_category_id')
            ->where('deposits.id', $id)->first();
        if (!$row) {
            DB::rollBack();
            return response()->json(array('status' => -1, 'msg' => '数据错误！[2]'));
        }
        if (in_array($row->status, [2, 3])) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '该笔充值已经处理完成'));
        } elseif ($row->status == 1) {
            return response()->json(array('status' => 1, 'msg' => '当前为“充入游戏币”环节，无法再次审核'));
        } elseif ($row->status == 0) {
            if ($row->accountant_admin <> auth()->user()->username) {
                DB::rollBack();
                return response()->json(array('status' => 2, 'msg' => '该笔充值已经被锁定！认领人：' . $row->cash_admin));
            }

            $manual_amount = (float)$request->get('amount', 0);
            $manual_postscript = (string)$request->get('postscript', '');
            $bank_order_no = (string)$request->get('bank_order_no', '');
            $manual_fee = (float)$request->get('fee', 0);

            //请认识填写表单
            if (empty($manual_amount) || empty($manual_postscript)) {
                DB::rollBack();
                return response()->json(array('status' => 2, 'msg' => '请正确填写表单[提交审核]'));
            }
            //判断是否为虚拟货币银行管理页面之使用渠道
            $getRate = $this->getRate($row->payment_category_ident);
            if (!empty($getRate)) {
                $row->third_amount = round($row->amount / $getRate, 4);
            }
            $row->status = 1;
            $row->manual_amount = $manual_amount;
            $row->manual_postscript = $manual_postscript;
            $row->bank_order_no = $bank_order_no;
            $row->manual_fee = $manual_fee;
            $row->accountant_admin = auth()->user()->username;
            $row->deal_at = date("Y-m-d H:i:s");
            if ($row->save()) {
                DB::commit();
                return response()->json(array('status' => 0, 'msg' => '该笔充值已提交给出纳！'));
            }
        } else {
            DB::rollBack();
            return response()->json(array('status' => 2, 'msg' => '无效的状态！[3]'));
        }
    }

    /**
     * 打开充入游戏币页面
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCarry(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = $this->_forDealCarry($id);
        if (in_array($row->status, [2, 3])) {
            return response()->json(array('status' => 1, 'msg' => '该笔充值已处理！'));
        } elseif(in_array($row->status,[1])){
            //同一笔单子不允许多个出纳操作
            if (!empty($row->cash_admin) && $row->cash_admin <> auth()->user()->username) {
                return response()->json(array('status' => 2, 'msg' => '该笔充值正在充入游戏币！出纳：' . $row->cash_admin));
            }
            //进行冲入游戏货操作，标识出纳人员
            $row->cash_admin = auth()->user()->username;
            if ($row->save()) {
                $data['status'] = 0;
                $row->extra = json_decode($row->extra, true);
                $data['data'] = (string)view('deposit.deal_cash', $row);
                return response()->json($data);
            }
        }elseif (in_array($row->status, [0])) {
            return response()->json(array('status' => 1, 'msg' => '该笔充值尚未审核，无法进入“充入游戏币”环节'));
        }else{
            return response()->json(array('status' => 1, 'msg' => '无效的状态-'.$row->status));
        }
    }

    /**
     * 处理充入游戏币操作
     * @return \Illuminate\Http\JsonResponse
     */
    public function putCarry(Request $request)
    {
        $id = (int)$request->get('id', 0);
        DB::beginTransaction();
        $row = \Service\Models\Deposit::select([
            'deposits.*',
            'payment_category.ident as payment_category_ident',//支付渠道英文标识
        ])
            ->leftJoin('payment_category', 'payment_category.id', 'deposits.payment_category_id')
            ->where('deposits.id', $id)->first();
        if (!$row) {
            DB::rollBack();
            return response()->json(array('status' => -1, 'msg' => '数据错误！[2]'));
        }
        if (in_array($row->status, [2, 3])) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '该笔充值已经处理完成'));
        } elseif ($row->status == 1) {
            if ($row->cash_admin <> auth()->user()->username) {
                DB::rollBack();
                return response()->json(array('status' => 2, 'msg' => '该笔充值已经被锁定！认领人：' . $row->cash_admin));
            }
            $errorType = (string)$request->get('refused_reason', '');
            $dealResult = (string)$request->get('deal_result', '');
            $remark = (string)$request->get('remark', '');
            if ((!in_array($dealResult, ['passed', 'refused'])) || empty($remark)) {
                DB::rollBack();
                return response()->json(array('status' => 2, 'msg' => '请正确填写表单'));
            }
            $deposit_api = new ApiDeposit();
            if ($dealResult == 'passed') {
                //充值成功
                if ($deposit_api->depositSuccess($row, 'ZXCZ', auth()->user())) {
                    DB::commit();
                    return response()->json(array('status' => 0, 'msg' => '操作成功！'));
                } else {
                    DB::rollBack();
                    return response()->json(array('status' => 1, 'msg' => $deposit_api->error_msg));
                }
            } else {
                if (empty($errorType)) {
                    DB::rollBack();
                    return response()->json(array('status' => 2, 'msg' => '请选择失败原因！'));
                }
                $row->error_type = $errorType;
                $row->admin_remark = $remark;
                if ($deposit_api->depositFail($row, auth()->user())) {
                    DB::commit();
                    return response()->json(array('status' => 0, 'msg' => '操作成功！你已经将当前充值纪录设置为【充值失败】！'));
                } else {
                    DB::rollBack();
                    return response()->json(array('status' => 1, 'msg' => $deposit_api->error_msg));
                }
            }
        } elseif ($row->status == 0) {
            DB::rollBack();
            return response()->json(array('status' => 2, 'msg' => '审核之后才能充入游戏币'));
        } else {
            DB::rollBack();
            return response()->json(array('status' => 2, 'msg' => '无效的状态-'.$row->status.'！[3]'));
        }
    }

    /**
     * 审核与充入游戏币共用
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|null|object|static
     */
    public function _forDealCarry($id)
    {
        if(!is_numeric($id) || empty($id)){
            return response()->json(array('status' => -1, 'msg' => '参数不能为空！[1]'));
        }
        $row = \Service\Models\Deposit::select([
            'deposits.*',
            'users.username',
            'topusers.username as topusername',
            'payment_channel.name as payment_channel_name',//支付通道后台名称
            'payment_channel.name as payment_channel_front_name',//支付通道前台名称
            'payment_category.name as payment_category_name',//支付渠道名称
            'payment_method.ident as payment_method_ident',
        ])
            ->leftJoin('users', 'users.id', 'deposits.user_id')
            ->leftJoin('users as topusers', 'topusers.id', 'users.top_id')
            ->leftJoin('payment_channel', 'payment_channel.id', 'deposits.payment_channel_id')
            ->leftJoin('payment_category', 'payment_category.id', 'deposits.payment_category_id')
            ->leftJoin('payment_method', 'payment_method.id', 'payment_channel.payment_method_id')
            ->where('deposits.id', $id)->first();
        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！[1]'));
        }
        if (in_array($row->status, array(2, 3))) {
            return response()->json(array('status' => 1, 'msg' => '该笔充值已处理！'));
        }
        return $row;
    }

    public function getDetail(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $api_detail = new ApiDeposit();
        if ($row = $api_detail->getDetail($id)) {

            $status_labels = ApiDeposit::getStatusLabels();
            $row->status_label = $status_labels[$row->status];

            $data['status'] = 0;
            $data['data'] = (string)view('deposit.detail', $row);
        } else {
            $data['status'] = 1;
            $data['msg'] = '无效的充值纪录ID[' . $id . ']';
        }
        return response()->json($data);
    }

    //计算数字货币汇率
    public function setRate($amount, $third_amount)
    {
        return ($amount > 0 && $third_amount > 0) ? round($amount / $third_amount, 4) : null;
    }

    //若渠道为虚拟货币银行管理页面之使用渠道则取配置时的汇率
    public function getRate($payment_category_ident)
    {
        $type = \Service\Models\BankVirtual::where('ident', 'USDT')->first();
        if (empty($type)) {
            return '';
        }
        $channelIdents = explode(",", $type->channel_idents);
        return in_array($payment_category_ident, $channelIdents) ? $type->rate : '';
    }
}
