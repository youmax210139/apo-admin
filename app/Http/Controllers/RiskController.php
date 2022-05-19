<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Service\Facades\Message;
use Service\API\Withdrawal as ApiWithdrawal;
use Service\API\UserFund as ApiUserFund;
use Service\Models\RiskRefusedReason;
use Service\Models\WithdrawalRisk as ModelWithdrawalRisk;
use Service\Models\Withdrawal as ModelWithdrawal;

class RiskController extends Controller
{
    public function getIndex()
    {
        $data = array();
        $data['start_date'] = date('Y-m-d 00:00:00', strtotime("-2 days"));
        $data['status_labels'] = ApiWithdrawal::getRiskStatusLabels();
        $data['adminuser'] = auth()->user()->username;
        return view('risk.index', $data);
    }

    /**
     * 账变列表数据
     *
     * @param OrderIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $param = array();
            $param['username'] = $request->get('username', ''); //会员名
            $param['verifier_username'] = $request->get('verifier_username', ''); //风控审核用户名
            $param['start_date'] = $request->get('start_date'); //开始时间
            $param['end_date'] = $request->get('end_date'); //结束时间
            $param['status'] = (int)$request->get('status', 0); //状态
            if (!strtotime($param['start_date'])) {
                $param['start_date'] = (string)Carbon::today();
            }
            if (!strtotime($param['end_date'])) {
                $param['end_date'] = (string)Carbon::now();
            }
            //查询条件
            $where = function ($query) use ($param) {
                if (!empty($param['username'])) {
                    $query->where('users.username', $param['username']);
                }
                if (!empty($param['verifier_username'])) {
                    $query->where('withdrawal_risks.verifier_username', $param['verifier_username']);
                }
                if ($param['status'] >= 0) {
                    if ($param['status'] === 0) {
                        $query->whereIn('withdrawal_risks.status', [0, 3]);
                    } else {
                        $query->where('withdrawal_risks.status', $param['status']);
                    }
                }

                if (!empty($param['start_date'])) {
                    $query->where('withdrawals.created_at', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where('withdrawals.created_at', '<', $param['end_date']);
                }
            };

            //计算过滤后总数
            $data['recordsTotal'] = $data['recordsFiltered'] = ModelWithdrawalRisk::leftJoin('withdrawals', 'withdrawals.id', 'withdrawal_risks.withdrawal_id')
                ->leftJoin('users', 'users.id', 'withdrawals.user_id')
                ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
                ->where($where)->count();

            $data['data'] = ModelWithdrawalRisk::select(
                [
                    'withdrawal_risks.*',
                    'withdrawals.created_at',
                    'withdrawals.amount',
                    'users.username',
                    'users.user_group_id',
                    'user_profile.value as user_observe',
                    'top_users.username as top_username',
                ]
            )
                ->leftJoin('withdrawals', 'withdrawals.id', 'withdrawal_risks.withdrawal_id')
                ->leftJoin('users', 'users.id', 'withdrawals.user_id')
                ->leftJoin('user_profile', function ($join) {
                    $join->on('user_profile.user_id', '=', 'users.id')
                        ->where('user_profile.attribute', 'user_observe');
                })
                ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
                ->where($where)
                ->skip($start)->take($length)
                ->orderBy("withdrawal_risks.id", 'DESC')
                ->get();
            return response()->json($data);
        }
    }

    public function getDeal(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = ModelWithdrawalRisk::select([
            'withdrawal_risks.*',
            'withdrawals.created_at',
            'withdrawals.ip as client_ip',
            'withdrawals.amount',
            'users.username',
            'users.user_group_id',
            'top_users.username as top_username',
            'profile_remark.value as user_remark',
            'profile_observe.value as user_observe',
        ])
            ->leftJoin('withdrawals', 'withdrawals.id', 'withdrawal_risks.withdrawal_id')
            ->leftJoin('users', 'users.id', 'withdrawals.user_id')
            ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
            ->leftJoin('user_profile as profile_remark', function ($query) {
                $query->on('withdrawals.user_id', 'profile_remark.user_id');
                $query->where('profile_remark.attribute', 'remark'); //用户备注
            })
            ->leftJoin('user_profile as profile_observe', function ($query) {
                $query->on('withdrawals.user_id', 'profile_observe.user_id');
                $query->where('profile_observe.attribute', 'user_observe'); //重点观察
            })
            ->where('withdrawal_risks.id', $id)
            ->first();
        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }
        if (in_array($row->status, [1, 2])) {
            return response()->json(array('status' => 1, 'msg' => '该笔提现审核已处理！'));
        }
        if ($row->status == 3 && $row->verifier_username <> auth()->user()->username) {
            return response()->json(array('status' => 2, 'msg' => '该笔提现审核已经被认领！认领风控员：' . $row->verifier_username));
        }
        $row->verifier_username = auth()->user()->username;
        $row->verifier_at = date('Y-m-d H:i:s');
        $row->verifier_ip = request()->ip();
        $row->status = 3;
        if (!$row->save()) {
            return response()->json(array('status' => 1, 'msg' => '认领失败！'));
        }
        $row['refused_reason'] = RiskRefusedReason::select('text')->orderBy('id', 'asc')->get();
        $data['status'] = 0;
        $data['data'] = (string)view('risk.deal', $row);
        return response()->json($data);
    }

    public function putDeal(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $withdrawal_risk = ModelWithdrawalRisk::find($id);
        if (!$withdrawal_risk) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }
        if (in_array($withdrawal_risk->status, [1, 2])) {
            return response()->json(array('status' => 2, 'msg' => '该笔提现审核还未认领或者已经审核！'));
        }
        if ($withdrawal_risk->status == 3 && $withdrawal_risk->verifier_username <> auth()->user()->username) {
            return response()->json(array('status' => 2, 'msg' => '该笔提现已经被认领！认领风控员：' . $withdrawal_risk->verifier_username));
        }

        $withdrawal = ModelWithdrawal::find($withdrawal_risk->withdrawal_id);
        if (!$withdrawal) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }

        $status = $request->get('status', '');
        $refused_reason = $request->get('refused_reason', '');
        $remark = $request->get('remark', '');

        if (!in_array($status, array('passed', 'refused'))) {
            return response()->json(array('status' => 1, 'msg' => '请选择审核结果！'));
        }
        if ($status == 'refused' && empty($refused_reason)) {
            return response()->json(array('status' => 1, 'msg' => '请填写拒绝原因！'));
        }
        $withdrawal_risk->done_at = date("Y-m-d H:i:s");
        $withdrawal_risk->verifier_ip = request()->ip();
        $withdrawal_risk->refused_msg = $refused_reason;
        $withdrawal_risk->risk_remark = $remark;
        //通过审核
        if ($status == 'passed') {
            $withdrawal_risk->status = 1;
            $withdrawal_risk->save();
            return response()->json(array('status' => 0, 'msg' => '操作成功！'));
        } else {
            DB::beginTransaction();
            //认领锁定记录防止其他管理员操作
            $withdrawal_risk->status = 2;
            if ($withdrawal_risk->save()) {
                //拒绝提现时把提现状态改为 2出款失败
                $withdrawal->status = 2;
                if (!$withdrawal->save()) {
                    DB::rollBack();
                    return response()->json(array('status' => 1, 'msg' => '操作失败！'));
                }

                $order = new \Service\Models\Orders();
                $order->from_user_id = $withdrawal->user_id;
                $order->admin_user_id = auth()->id();
                $order->amount = $withdrawal->amount;
                $order->comment = '提现订单号：' . $withdrawal->id;
                $order->ip = request()->ip();

                if (!ApiUserFund::modifyFund($order, 'TKSB')) {
                    DB::rollBack();
                    return response()->json(array('status' => 1, 'msg' => '操作失败！' . ApiUserFund::$error_msg));
                }

                //推送消息
                $msg = (object)[];
                $msg->sender_id = auth()->id();
                $msg->sender_name = auth()->user()->username;
                $msg->receiver_id = $withdrawal->user_id;
                $msg->subject = '提现申请失败';

                $msg->content = '您于[' . $withdrawal->created_at . ']发起的提款金额:' . $withdrawal->amount . '元。提现申请失败。原因:' . $refused_reason;

                $msg->sender_type = 1;
                $msg->send_type = 0;
                $msg->message_type = 3;
                $msg->send_at = date('Y-m-d H:i:s');
                Message::adminSend($msg);
                DB::commit();
                return response()->json(array('status' => 0, 'msg' => '操作成功！'));
            }
        }
    }

    public function getDetail(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $withdrawal_risk = ModelWithdrawalRisk::select(
            [
                'withdrawal_risks.*',
                'withdrawals.created_at',
                'withdrawals.ip as client_ip',
                'withdrawals.user_fee',
                'withdrawals.amount',
                'users.username',
                'top_users.username as top_username',
                'banks.name as user_bank_name',
                'user_banks.account_name',
                'user_banks.account',
                'banks.id as bank_id',
                'regions.name as province',
                'reg.name as city',
            ]
        )
            ->leftJoin('withdrawals', 'withdrawals.id', 'withdrawal_risks.withdrawal_id')
            ->leftJoin('users', 'users.id', 'withdrawals.user_id')
            ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
            ->leftJoin('user_banks', 'user_banks.id', 'withdrawals.user_bank_id')
            ->leftJoin('banks', 'banks.id', 'user_banks.bank_id')
            ->leftJoin('regions', 'regions.id', 'user_banks.province_id')
            ->leftJoin('regions as reg', 'reg.id', 'user_banks.city_id')
            ->where('withdrawal_risks.id', $id)
            ->first();
        $status_labels = ApiWithdrawal::getRiskStatusLabels();
        $withdrawal_risk->status_label = $status_labels[$withdrawal_risk->status];
        $data['status'] = 0;
        $data['data'] = (string)view('risk.detail', $withdrawal_risk);
        return response()->json($data);
    }
}
