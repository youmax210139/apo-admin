<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Service\Models\ChatDeposit;
use Service\Models\ChatDepositMessage;
use Service\Models\ChatDepositPayment;
use Service\Models\PaymentChannel;
use Service\Models\UserGroup;
use Carbon\Carbon;
use Service\Models\User;
use Illuminate\Support\Facades\DB;

class ChatdepositController extends Controller
{
    public function getIndex(Request $request)
    {
        $parent_id = (int)$request->get('parent_id', 0);

        $data['parent_id'] = $parent_id;
        $data['user_group'] = UserGroup::all();
        $data['start_date'] = Carbon::today();
        $data['end_date'] = Carbon::tomorrow();
        $data['zongdai_list'] = User::select(['id', 'username'])->where('parent_id', 0)->get();
        $data['pic_url'] = get_config('remote_pic_url');

        return view('chat_deposit.index', $data);
    }

    public function postIndex(Request $request)
    {
        $data = [];
        $data['draw'] = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $param = [];
        $param['calculate_total'] = $request->get('calculate_total');

        $username = $request->get('username', '');
        $status = $request->get('status');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $kefu = $request->get('kefu');

        $where = [];
        if (!empty($username)) {
            $where[] = ['users.username', '=', $username];
        }
        if (isset($status) && $status >= 0) {
            $where[] = ['chat_deposit.connect_status', '=', $status];
        }
        if (!empty($start_date)) {
            $where[] = ['chat_deposit.created_at', '>=', $start_date];
        }
        if (!empty($end_date)) {
            $where[] = ['chat_deposit.created_at', '<=', $end_date];
        }
        if (!empty($kefu)) {
            $where[] = ['kefu_no', '=', $kefu];
        }

        // 计算过滤后总数
        $data['recordsTotal'] = $data['recordsFiltered'] = ChatDeposit::leftJoin('users', 'users.id', 'chat_deposit.user_id')
            ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
            ->where($where)
            ->count();

        /**
         * 10分钟内无新消息会话则设置会断开
         */
        $data['data'] = ChatDeposit::select([
            'chat_deposit.id',
            'chat_deposit.user_id',
            'chat_deposit.deposit_id',
            'chat_deposit.connect_status',
            'chat_deposit.last_at',
            'chat_deposit.read_status',
            'chat_deposit.kefu_no',
            'deposits.amount',
            'deposits.status',
            'users.username',
            'chat_deposit_message.message',
            'chat_deposit_message.img',
            'payment_channel.name as channel_name',
            'payment_channel_attribute.value as kefu'
        ])
            ->leftJoin('deposits', 'deposits.id', 'chat_deposit.deposit_id')
            ->leftJoin('users', 'users.id', 'chat_deposit.user_id')
            ->leftJoin('chat_deposit_message', 'chat_deposit_message.id', 'chat_deposit.last_msg_id')
            ->leftJoin('payment_channel', 'payment_channel.id', 'deposits.payment_channel_id')
            ->join('payment_channel_attribute', function ($join) {
                $join->on('payment_channel_attribute.payment_channel_id', '=', 'deposits.payment_channel_id')
                    ->where('payment_channel_attribute.type', '=', 'agent_account');
            })
            ->where($where)
            ->skip($start)
            ->take($length)
            ->orderBy('chat_deposit.last_at', 'desc')
            ->get();
        foreach ($data['data'] as &$row) {
            $row->deposit_id = id_encode($row->deposit_id);
            $row->message = htmlentities($row->message);
            $row->kefu = json_decode($row->kefu);
        }
        return response()->json($data);
    }

    public function getNewMessage(Request $request)
    {
        $id = (int)$request->get('id');
        $last_id = (int)$request->get('last_id');

        $where = [
            ['chat_deposit_message.chat_deposit_id', '=', $id],
            ['chat_deposit_message.id', '>', $last_id]
        ];

        $message_list = ChatDepositMessage::select([
            "chat_deposit_message.id",
            "chat_deposit_message.chat_deposit_id",
            "chat_deposit_message.created_at",
            "chat_deposit_message.from_user_id",
            "from_user.username as from_username",
            "chat_deposit_message.to_user_id",
            "to_user.username as to_username",
            "chat_deposit_message.message",
            "chat_deposit_message.status",
            "chat_deposit_message.updated_at",
            "chat_deposit_message.img",
        ])
            ->leftJoin('users as from_user', 'from_user.id', 'chat_deposit_message.from_user_id')
            ->leftJoin('users as to_user', 'to_user.id', 'chat_deposit_message.to_user_id')
            ->where($where)
            ->orderBy('id', 'asc')
            ->get();
        if (!$message_list->isEmpty()) {
            $message_list = $message_list->toArray();

            // 标记数据为已读
            ChatDepositMessage::where([
                ['id', '<=', max(array_values(array_column($message_list, 'id')))],
                ['chat_deposit_message.chat_deposit_id', '=', $id],
                ['chat_deposit_message.id', '>', $last_id],
                ['chat_deposit_message.to_user_id', '=', '0']
            ])
                ->update(['status' => '1']);
            $row = ChatDeposit::find($id);
            $row->read_status = 1;
            $row->save();
            foreach ($message_list as &$item) {
                $item['message'] = htmlentities($item['message']);
            }
            return response()->json([
                'status' => '0',
                'data' => $message_list,
                'msg' => 'success',
            ]);
        } else {
            return response()->json([
                'status' => '0',
                'data' => [],
                'msg' => 'success',
            ]);
        }
    }

    public function postSendmsg(Request $request)
    {
        $deposit_id = $request->get('deposit_id', '');
        $message = $request->get('message');
        $deposit_id = id_decode($deposit_id);
        if (empty($deposit_id)) {
            return $this->_response('对不起，ID错误！', [], 1);
        }
        $row = ChatDeposit::select(['id', 'user_id'])->where('deposit_id', $deposit_id)->first();
        if (empty($row)) {
            return $this->_response('对不起，ID错误！', [], 1);
        }

        $chat_depost_msg = new ChatDepositMessage();
        $chat_depost_msg->chat_deposit_id = $row->id;
        $chat_depost_msg->from_user_id = 0;
        $chat_depost_msg->to_user_id = $row->user_id;
        $chat_depost_msg->message = $message;

        if ($chat_depost_msg->save()) {
            return response()->json([
                'status' => '0',
                'data' => ['id' => $chat_depost_msg->id],
                'msg' => '消息发送成功！',
            ]);
        } else {
            return response()->json([
                'status' => '1',
                'data' => [],
                'msg' => '对不起，消息发送失败，请联系客服！',
            ]);
        }
    }

    public function getPayment(Request $request)
    {
        $act = $request->get('act', '');
        if ($act == 'add') {
            $payment_channels = PaymentChannel::select(['payment_channel.name', 'payment_channel.id', 'front_name'])->leftJoin('payment_method', 'payment_channel.payment_method_id', 'payment_method.id')
                ->where('payment_method.ident', 'agent_chat')->get();
            return view('chat_deposit.add', [
                'payment_channels' => $payment_channels,
                'types' => [
                    'bank' => '银行卡',
                    'alipay' => '支付宝',
                    'wechat' => '微信',
                    'USDT' => 'USDT',
                ]
            ]);
        } elseif ($act == 'edit') {
            $id = $request->get('id', 0);
            $data['payment'] = ChatDepositPayment::find($id);
            if (!$data['payment']) {
                abort('记录不存在');
            }
            $data['payment_channels'] = PaymentChannel::select(['payment_channel.name', 'payment_channel.id', 'front_name'])->leftJoin('payment_method', 'payment_channel.payment_method_id', 'payment_method.id')
                ->where('payment_method.ident', 'agent_chat')->get();

            return view('chat_deposit.edit', $data);
        }
        $data['payments'] = ChatDepositPayment::select([
            'chat_deposit_payment.*',
            'payment_channel.name as channel_name'
        ])
            ->leftJoin('payment_channel', 'payment_channel.id', 'chat_deposit_payment.channel')
            ->where(function ($query) {
                $kf = (int)request()->get('kefu', 0);
                if (request()->get('kefu', 0)) {
                    $query->where('kefu', $kf);
                }
            })->get();
        return view('chat_deposit.config', $data);
    }

    public function postPayment(Request $request)
    {
        $id = $request->get('id', 0);
        $act = $request->get('act', '');
        if ($act == 'add') {
            $type = $request->get('type', '');
            $payment = new ChatDepositPayment();
            $payment->kefu = (int)$request->get('kefu', 1);
            $payment->type = $type;
            $payment->enabled = (int)$request->get('enabled', 0);
            $payment->channel = (int)$request->get('channel', 0);

            if ($type == 'bank') {
                $payment->name = $request->get('bank_username', '');
                $payment->account = $request->get('bank_account', '');
                $payment->bank_name = $request->get('bank_name', '');
                $payment->bank_branch = $request->get('bank_branch', '');
                if (empty($payment->name) || empty($payment->account)) {
                    return response()->json([
                        'status' => 1,
                        'data' => [],
                        'msg' => '请填写完整信息！',
                    ]);
                }
                $payment->save();
            } elseif ($type == 'alipay') {
                $payment->name = $request->get('alipay_name', '');
                $payment->account = $request->get('alipay_account', '');
                $payment->qrcode = $request->get('alipay_qrcode', '');
                $payment->last_name = $request->get('last_name', '');
                $payment->first_name = $request->get('first_name', '');
                if (empty($payment->name) || empty($payment->account)) {
                    return response()->json([
                        'status' => 1,
                        'data' => [],
                        'msg' => '请填写完整信息！',
                    ]);
                }
                $payment->save();
            } elseif ($type == 'wechat') {
                $payment->qrcode = $request->get('wechat_qrcode', '');
                $payment->name = $request->get('wechat_name', '');
                $payment->account = $request->get('wechat_account', '');
                $payment->last_name = $request->get('last_name', '');
                $payment->first_name = $request->get('first_name', '');
                $payment->save();
            } elseif ($type == 'USDT') {
                $payment->qrcode = $request->get('USDT_qrcode', '');
                if (empty($payment->qrcode)) {
                    return response()->json([
                        'status' => 1,
                        'data' => [],
                        'msg' => '请填写完整信息！',
                    ]);
                }
                $payment->save();
            }
            return response()->json([
                'status' => 0,
                'data' => [],
                'msg' => '添加成功！',
            ]);
        } elseif ($act == 'del') {
            $payment = ChatDepositPayment::find($id);
            $payment->delete();
            return response()->json([
                'status' => 0,
                'msg' => '操作成功！',
            ]);
        }

        $payment = ChatDepositPayment::find($id);
        if (!$payment) {
            return response()->json([
                'status' => 1,
                'msg' => '记录不存在！',
            ]);
        }
        if ($act == 'enabled') {
            $enabled = $request->get('enabled', 0);
            $payment->enabled = $enabled;
            if ($request->get('multiple', 0)) {
                ChatDepositPayment::where('account', $payment->account)->update(['enabled' => $enabled]);
            }
        } else {
            $payment->name = $request->get('name', '');
            $payment->account = $request->get('account', '');
            $payment->bank_name = $request->get('bank_name', '');
            $payment->bank_branch = $request->get('bank_branch', '');
            $payment->qrcode = $request->get('qrcode', '');
            $payment->kefu = (int)$request->get('kefu', '');
            $payment->last_name = $request->get('last_name', '');
            $payment->first_name = $request->get('first_name', '');
            $payment->enabled = $request->get('enabled', 0);
        }
        $payment->save();
        return response()->json([
            'status' => 0,
            'msg' => '操作成功！',
        ]);
    }

    public function getAutokeyword()
    {
        $data['keywords'] = json_decode(Cache::store('redis')->get('auto_keywords'), true);
        $data['keywords'] = $data['keywords'] ? $data['keywords'] : [];
        return view('chat_deposit.autokeyword', $data);
    }

    public function postAutokeyword(Request $request)
    {
        $keys = $request->get('keyword', []);
        $words = $request->get('word', []);
        $keywords = [];
        foreach ($keys as $k => $key) {
            if (isset($words[$k])) {
                $keywords[] = ['keyword' => $key, 'type' => 0, 'msg' => isset($words[$k]) ? $words[$k] : ''];
            }
        }
        Cache::store('redis')->forever('auto_keywords', json_encode($keywords));
        return response()->json([
            'status' => 0,
            'msg' => '操作成功！',
        ]);
    }
}
