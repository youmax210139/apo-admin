<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalChannelCreateRequest;
use App\Http\Requests\WithdrawalChannelUpdateRequest;
use Illuminate\Http\Request;
use Service\Models\WithdrawalCategory;
use Service\Models\WithdrawalChannel;
use Illuminate\Support\Facades\DB;

class WithdrawalchannelController extends Controller
{
    private $fields = [
        'name' => '',
        'withdrawal_category_ident' => '',
        'status' => 1,
        'merchant_id' => '',
        'key1' => '',
        'key2' => '',
        'key3' => '',
        'domain_id' => '',
        'amount_min' => 0,
        'amount_max' => 0,
        'user_fee_status' => 0,
        'user_fee_step' => 0,
        'user_fee_operation' => 0,
        'user_fee_down_type' => 0,
        'user_fee_down_value' => 0,
        'user_fee_up_type' => 0,
        'user_fee_up_value' => 0,
        'platform_fee_status' => 0,
        'platform_fee_step' => 0,
        'platform_fee_down_type' => 0,
        'platform_fee_down_value' => 0,
        'platform_fee_up_type' => 0,
        'platform_fee_up_value' => 0,
    ];

    public function getIndex(Request $request)
    {
        $withdrawal_channel = new WithdrawalChannel();
        $data['status'] = (int)$request->get('status', 1);
        $data['channels'] = $withdrawal_channel::select([
            'withdrawal_channel.id',
            'withdrawal_channel.merchant_id',
            'withdrawal_channel.extra',
            DB::raw('"withdrawal_channel"."name" AS channel_name'),
            DB::raw('"withdrawal_category"."name" AS category_name'),
            'withdrawal_channel.status',
        ])
            ->leftJoin('withdrawal_category', 'withdrawal_category.ident', 'withdrawal_channel.withdrawal_category_ident')
            ->where('withdrawal_channel.status', $data['status'])
            ->orderBy('id', 'asc')
            ->get();

        foreach ($data['channels'] as $num => $aRow) {
            $extra = json_decode(ssl_decrypt($aRow['extra'], env('ENCRYPT_KEY')), true);
            foreach ($this->fields as $key => $value) {
                if (in_array($key, ['key1', 'key2', 'key3', 'domain_id'])) {
                    if (!empty($extra[$key])) {
                        if ($key != 'domain_id') {
                            $data['channels'][$num][$key] = '******' . substr($extra[$key], -3, 3);
                        } else {
                            $data['channels'][$num][$key] = $extra[$key];
                        }
                    } else {
                        $data['channels'][$num][$key] = $value;
                    }
                } else {
                    $data['channels'][$num][$key] = old($key, $data['channels'][$num][$key]);
                }
            }
        }
        //中间站域名
        $data['domains'] = \Service\Models\PaymentDomain::select(['payment_domain.*', 'payment_category.name AS payment_category_name', 'intermediate_servers.name AS intermediate_servers_name'])
            ->leftJoin('payment_category', 'payment_category.id', 'payment_domain.payment_category_id')
            ->leftJoin('intermediate_servers', 'intermediate_servers.id', 'payment_domain.intermediate_servers_id')
            ->where('payment_domain.status', true)
            ->orderBy('payment_domain.id', 'asc')
            ->get();

        return view('withdrawalchannel.index', $data);
    }

    public function getCreate()
    {
        $data = [];

        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        $data['categorys'] = WithdrawalCategory::select(['id', 'ident', 'name'])->where('status', '1')->get();
        //中间站域名
        $data['domains'] = \Service\Models\PaymentDomain::select(['payment_domain.*', 'payment_category.name AS payment_category_name', 'intermediate_servers.name AS intermediate_servers_name'])
            ->leftJoin('payment_category', 'payment_category.id', 'payment_domain.payment_category_id')
            ->leftJoin('intermediate_servers', 'intermediate_servers.id', 'payment_domain.intermediate_servers_id')
            ->where('payment_domain.status', true)
            ->orderBy('payment_domain.id', 'asc')
            ->get();

        return view('withdrawalchannel.create', $data);
    }

    public function postCreate(WithdrawalChannelCreateRequest $request)
    {
        $withdrawal_channel = new WithdrawalChannel();

        $encrypt_data = [];
        foreach ($this->fields as $key => $value) {
            if (in_array($key, ['key1', 'key2', 'key3', 'domain_id'])) {
                $encrypt_data[$key] = $request->get($key, $value);
            } else {
                $withdrawal_channel->$key = $request->get($key, $value);
            }
        }
        $withdrawal_channel->extra = ssl_encrypt(json_encode($encrypt_data), env('ENCRYPT_KEY'));

        if ($withdrawal_channel->save()) {
            return redirect('/withdrawalchannel/')->withSuccess('添加成功');
        }

        return redirect('/withdrawalchannel/')->withErrors('添加失败');
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $withdrawal = WithdrawalChannel::find($id);

        if (empty($withdrawal)) {
            return redirect('/withdrawalchannel/')->withErrors("找不到这个提现通道");
        }

        $data = ['id' => $id];
        $extra = json_decode(ssl_decrypt($withdrawal->extra, env('ENCRYPT_KEY')), true);
        foreach ($this->fields as $key => $value) {
            if (in_array($key, ['key1', 'key2', 'key3', 'domain_id'])) {
                if (!empty($extra[$key])) {
                    if ($key != 'domain_id') {
                        $data[$key] = '******' . substr($extra[$key], -3, 3);
                    } else {
                        $data[$key] = $extra[$key];
                    }
                } else {
                    $data[$key] = $value;
                }
            } else {
                $data[$key] = old($key, $withdrawal->$key);
            }
        }
        $data['categorys'] = WithdrawalCategory::select(['id', 'ident', 'name'])->where('status', '1')->get();

        //中间站域名
        $data['domains'] = \Service\Models\PaymentDomain::select(['payment_domain.*', 'payment_category.name AS payment_category_name', 'intermediate_servers.name AS intermediate_servers_name'])
            ->leftJoin('payment_category', 'payment_category.id', 'payment_domain.payment_category_id')
            ->leftJoin('intermediate_servers', 'intermediate_servers.id', 'payment_domain.intermediate_servers_id')
            ->where('payment_domain.status', true)
            ->orderBy('payment_domain.id', 'asc')
            ->get();

        return view('withdrawalchannel.edit', $data);
    }

    public function putEdit(WithdrawalChannelUpdateRequest $request)
    {
        $id = (int)$request->get('id', 0);


        $withdrawal = WithdrawalChannel::find($id);


        if ($request->get('set_status', '') == '1') {
            $withdrawal->status = (int)$request->get('status', 0);
            $withdrawal->save();
            $status_txt = $withdrawal->status == 1 ? '启用' : '禁用';
            return response()->json(['status' => 0, 'msg' => "{$status_txt} {$withdrawal->name} 成功"]);
        }
        //原本的密钥值
        $extra = json_decode(ssl_decrypt($withdrawal->extra, env('ENCRYPT_KEY')), true);

        $encrypt_data = [];
        foreach ($this->fields as $key => $value) {
            if (in_array($key, ['key1', 'key2', 'key3', 'domain_id'])) {
                $_key = $request->get($key, $value);
                if (strpos($_key, '******') === 0) {
                    $encrypt_data[$key] = $extra[$key];
                } else {
                    $encrypt_data[$key] = $request->get($key, $value);
                }
            } else {
                $withdrawal->$key = $request->get($key, $this->fields[$key]);
            }
        }
        $withdrawal->extra = ssl_encrypt(json_encode($encrypt_data), env('ENCRYPT_KEY'));

        if ($withdrawal->save()) {
            return redirect('/withdrawalchannel/')->withSuccess('修改提现通道成功');
        }

        return redirect('/withdrawalchannel/edit/?id=' . $id)->withErrors('修改提现通道失败');
    }

    /**
     * 删除一个支付通道
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|unknown
     */
    public function deleteIndex(Request $request)
    {
        $id = (int)$request->get('id', 0);

        $row = WithdrawalChannel::find($id);
        if ($row && $row->delete()) {
            return redirect()->back()->withSuccess("删除成功");
        } else {
            return redirect()->back()->withErrors("删除失败");
        }
    }
}
