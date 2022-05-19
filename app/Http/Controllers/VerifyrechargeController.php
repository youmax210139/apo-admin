<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Service\Models\Deposit as ModelDeposit;
use Service\Models\OrderType as ModelOrderType;
use Service\Models\UserDepositTotal;

class VerifyrechargeController extends Controller
{
    public function getIndex()
    {
        //用户人工充值选项
        $recharge_options = get_user_hand_options('recharge');
        $order_types = ModelOrderType::whereIn('ident', $recharge_options)
            ->where(function ($query) {
                $query->where('operation', '=', 1)
                    ->orWhere('hold_operation', '=', 1);
            })
            ->get(['ident','name']);
        if ($order_types->isEmpty()) {
            return redirect()->back()->withErrors("参数配置 deposit_user_recharge_options 不能为空或是输入错误");
        }
        return view('secondverify.recharge',[
            'order_types' => $order_types,
        ]);
    }

    public function postIndex(Request $request)
    {
        $data = array();
        $data['draw'] = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $columns = $request->get('columns');
        $order = $request->get('order');

        $param = array();
        $param['status'] = (int)$request->get('status', 0); //审核状态
        $param['created_admin'] = $request->get('created_admin'); //充值员
        $param['created_start_date'] = $request->get('created_start_date'); //开始时间
        $param['created_end_date'] = $request->get('created_end_date'); //结束时间
        $param['username'] = $request->get('username'); //充值会员
        $param['verify_admin'] = $request->get('verify_admin'); //审核员
        $param['verify_start_date'] = $request->get('verify_start_date'); //审核开始时间
        $param['verify_end_date'] = $request->get('verify_end_date'); //审核结束时间
        $param['order_type'] = $request->get('order_type'); //帐变类型
        $param['amount_min'] = $request->get('amount_min'); //最大金额
        $param['amount_max'] = $request->get('amount_max'); //最小金额
        $param['page'] = $request->get('page'); //当前页

        $count_query = \Service\Models\SecondVerifyList::query();

        if (!empty($param['created_admin'])) {
            $count_query->leftJoin('admin_users as created_admin', 'created_admin.id', 'second_verify_list.created_admin_id');
        }
        if (!empty($param['username'])) {
            $count_query->leftJoin('users', 'users.id', 'second_verify_list.user_id');
        }

        if (!empty($param['verify_admin'])) {
            $count_query->leftJoin('admin_users as verify_admin', 'verify_admin.id', 'second_verify_list.verify_admin_id');
        }

        //查询条件
        $where = function ($query) use ($param) {
            //审核状态
            $query->where('status', $param['status']);
            $query->where('verify_type', 'recharge');

            //用户IP
            if (!empty($param['created_admin'])) {
                $query->where('created_admin.username', $param['created_admin']);
            }
            //时间对比
            if (!empty($param['created_start_date'])) {
                $query->where('second_verify_list.created_at', '>=', $param['created_start_date']);
            }
            if (!empty($param['created_end_date'])) {
                $query->where('second_verify_list.created_at', '<=', $param['created_end_date']);
            }

            //用户
            if (!empty($param['username'])) {
                $query->where('users.username', $param['username']);
            }
            //审核员
            if (!empty($param['verify_admin'])) {
                $query->where('verify_admin.username', $param['verify_admin']);
            }
            //时间对比
            if (!empty($param['verify_start_date'])) {
                $query->where('second_verify_list.updated_at', '>=', $param['verify_start_date']);
            }
            if (!empty($param['verify_end_date'])) {
                $query->where('second_verify_list.updated_at', '<=', $param['verify_end_date']);
            }
            //帐变类型
            if (!empty($param['order_type'])) {
                $query->whereRaw('"second_verify_list"."data"::json->>\'ordertype\' = ?', $param['order_type']);
            }
            //金额
            if (!empty($param['amount_min'])) {
                $query->whereRaw('("second_verify_list"."data"::json->>\'money\')::numeric >= ?',  $param['amount_min']);
            }
            if (!empty($param['amount_max'])) {
                $query->whereRaw('("second_verify_list"."data"::json->>\'money\'))::numeric <= ?', $param['amount_max']);
            }
        };

        //计算过滤后总数
        $data['recordsTotal'] =
        $data['recordsFiltered'] = $count_query->where($where)->count();

        $data['data'] = \Service\Models\SecondVerifyList::select([
            'second_verify_list.*',
            'users.username as username',
            'top.username as topname',
            'created_admin.username as created_admin_name',
            'verify_admin.username as verify_admin_name'
        ])
            ->leftJoin('admin_users as created_admin', 'created_admin.id', 'second_verify_list.created_admin_id')
            ->leftJoin('admin_users as verify_admin', 'verify_admin.id', 'second_verify_list.verify_admin_id')
            ->leftJoin('users', 'users.id', 'second_verify_list.user_id')
            ->leftJoin('users as top', 'users.top_id', 'top.id')
            ->where($where)
            ->skip($start)
            ->take($length)
            ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
            ->get();

        if (!$data['data']->isEmpty()) {
            foreach ($data['data'] as $k => $v) {
                $v['data'] = json_decode($v['data']);
                $data['data'][$k]['money'] = $v['data']->money;
                $data['data'][$k]['ordertype'] = $v['data']->ordertypetext;
                $data['data'][$k]['description'] = $v['data']->description;
            }
        }
        return response()->json($data);
    }

    public function putVerify(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $status = (int)$request->get('status', 0);
        $verify = \Service\Models\SecondVerifyList::find($id);

        if (empty($verify)) {
            return redirect('/Verifyrecharge')->withErrors("找不到该纪录");
        }
        if ($verify->status != 0) {
            return redirect('/Verifyrecharge')->withErrors("该充值已经审核！");
        }

        if ($verify->created_admin_id == auth()->user()->id) {
            return redirect('/Verifyrecharge')->withErrors("对不起，充值管理员和审核管理员不能为同一人！");
        }

        if ($status == 1) {
            $postdata = json_decode($verify->data);
            $order = new \Service\Models\Orders();
            $order->from_user_id = $verify->user_id;
            $order->admin_user_id = auth()->id();
            $order->amount = $postdata->money;
            $order->comment = $postdata->description;
            $order->ip = request()->ip();
            if (!\Service\API\UserFund::modifyFund($order, $postdata->ordertype)) {
                DB::rollBack();
                return redirect('/Verifyrecharge')->withErrors("充值审核失败！");
            }
            $verify->status = 1;

            if (in_array($postdata->ordertype,['SFRGCZ','RGCZ'])) {
                //人工充值计入用户充值统计数据
                //更新user_deposit_total.times+1,amount+充值金额,once_max为最大值
                $user_deposit_total = UserDepositTotal::where('user_id', $verify->user_id)->first();
                if ($user_deposit_total) {
                    $user_deposit_total->times = $user_deposit_total->times + 1;
                    $user_deposit_total->amount = $user_deposit_total->amount + $order->amount;
                    if (round($order->amount, 4) > round($user_deposit_total->once_max, 4)) {
                        //纪录单笔最大值
                        $user_deposit_total->once_max = round($order->amount, 4);
                    }
                    if (round($order->amount, 4) < round($user_deposit_total->once_min, 4)) {
                        //纪录单笔最小值
                        $user_deposit_total->once_min = round($order->amount, 4);
                    }
                } else {
                    $user_deposit_total = new UserDepositTotal();
                    $user_deposit_total->user_id = $verify->user_id;
                    $user_deposit_total->times = 1;
                    $user_deposit_total->amount = $order->amount;
                    $user_deposit_total->once_max = $order->amount;
                    $user_deposit_total->once_min = $order->amount;
                }
                if (!$user_deposit_total->save()) {
                    DB::rollBack();
                    return redirect('/Verifyrecharge')->withErrors("用户充值统计[" . $user_deposit_total->user_id . "]状态更新失败!");
                }
                $deposit = new ModelDeposit();
                $deposit->user_id = $verify->user_id;
                $deposit->amount = $postdata->money;
                $deposit->payment_channel_id = 0;
                $deposit->payment_category_id = 0;
                $deposit->status = 2;
                $deposit->remark = '人工充值';
                if($postdata->ordertype == 'SFRGCZ'){
                    $deposit->remark = '三方人工充值';
                    $deposit->payment_channel_id = intval($postdata->payment_channel_id);
                    $deposit->payment_category_id = intval($postdata->payment_category_id);
                }
                $deposit->done_at = (string)Carbon::now();
                if (!$deposit->save()) {
                    DB::rollBack();
                    return redirect('/Verifyrecharge')->withErrors("充值审核失败:充值记录添加失败!");
                }
            } elseif ($postdata->ordertype == 'CXCZ') {
                //促销充值计入活动
                $activity_record = new \Service\Models\ActivityRecord();
                $activity_record->activity_id = $postdata->activity_id;
                $activity_record->user_id = $verify->user_id;
                $activity_record->extral = '促销充值';
                $activity_record->draw_money = $postdata->money;
                $activity_record->record_time = (string)\Carbon\Carbon::now();
                $activity_record->status = 1;
                if (!$activity_record->save()) {
                    DB::rollBack();
                    return redirect('/Verifyrecharge')->withErrors("充值审核失败:促销记录添加失败!");
                }
            }
        } else {
            $verify->status = 2;
        }
        $verify->verify_admin_id = auth()->id();
        $verify->verify_at = date('Y-m-d H:i:s');
        if (!$verify->save()) {
            DB::rollBack();
            return redirect('/Verifyrecharge')->withErrors("充值审核失败！");
        }
        DB::commit();
        return redirect('/Verifyrecharge\/')->withSuccess('审核成功');
    }
}
