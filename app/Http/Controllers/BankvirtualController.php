<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BankvirtualController extends Controller
{
    protected $fields = [
        'name' => '',
        'ident' => '',
        'currency' => '',
        'rate' => '',
        'withdraw' => false,
        'disabled' => false,
        'api_fetch' => false,
        'url' => '',
        'channel_idents' => '',
        'start_time' => '',
        'end_time' => '',
        'amount_min' => '',
        'amount_max' => '',
    ];

    public function getIndex()
    {
        $data = array();
        $data['bank_virtual'] = \Service\Models\BankVirtual::orderBy('id', 'asc')->get();
        return view('bank-vitual.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate()
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        return view('bank-vitual.create', $data);
    }

    public function putCreate(\App\Http\Requests\BankVirtualCreateRequest $request)
    {
        if ($request->get('api_fetch') == true) {
            $ident = strtolower($request->get('ident') . $request->get('currency'));
            $client = new \GuzzleHttp\Client();
            try {
                $res = $client->request('GET', $request->get('url'));
                $res = json_decode($res->getBody()->getContents(), true);
                $request->merge(['rate' => $res[$ident]]);
            } catch (\Exception $e) {
                return back()->withInput()->withErrors($e->getMessage());
            }
        }
        $row = new \Service\Models\BankVirtual();

        foreach (array_keys($this->fields) as $field) {
            if ($request->get($field) != null) {
                $row->$field = $request->get($field);
            }
        }
        $row->save();
        return redirect('/Bankvirtual\/')->withSuccess('添加成功');
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = \Service\Models\BankVirtual::find($id);
        if (!$row) {
            return redirect('/Bankvirtual\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }

        $data['start_time'] = substr($data['start_time'], 0, 5);
        $data['end_time'] = substr($data['end_time'], 0, 5);

        $data['id'] = $id;
        return view('bank-vitual.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BankUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(\App\Http\Requests\BankVirtualUpdateRequest $request)
    {
        $row = \Service\Models\BankVirtual::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/Bankvirtual\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            if ($request->get($field) != null) {
                $row->$field = $request->get($field);
            }
        }
        //判断使用渠道支付类型只能为数字货币、虚拟货币扫码
        $channelIdents = explode(",", $row->channel_idents);
        if (!empty($channelIdents)) {
            foreach ($channelIdents as $channelIdent) {
                $payment_category = \Service\Models\PaymentCategory::where('ident', $channelIdent)->first();
                if (empty($payment_category)) {
                    return redirect()->back()->withErrors("输入使用渠道错误");
                }
                $methods = json_decode($payment_category->methods, true);
                if (!in_array('dc_scan', $methods) && !in_array('digital_currency', $methods)) {
                    return redirect()->back()
                        ->withErrors("输入使用渠道时支付类型只能为数字货币(digital_currency)、虚拟货币扫码(dc_scan)");
                }
            }
        }
        $row->save();

        return redirect('/Bankvirtual\/')->withSuccess('修改成功');
    }

    public function putDisabled(Request $request)
    {
        $row = \Service\Models\BankVirtual::find((int)$request->get('id', 0));
        $disabled = (int)$request->get('disabled');
        if ($row) {
            $row->disabled = $disabled;
            if ($row->save()) {
                return redirect()->back()->withSuccess(($disabled ? "禁用" : '启用') . "成功");
            }
        }

        return redirect()->back()->withErrors("操作失败");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $row = \Service\Models\BankVirtual::find((int)$request->get('id', 0));
        if ($row) {
            $row->delete();
            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
