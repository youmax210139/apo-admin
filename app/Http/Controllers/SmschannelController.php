<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LotteryCategoryCreateRequest;
use App\Http\Requests\LotteryCategoryUpdateRequest;
use Service\API\Sms;
use Service\Models\SmsCategory;
use Service\Models\SmsChannel;

class SmschannelController extends Controller
{
    protected $fields = [
        'name' => '',
        'category_id' => 0,
        'account' => '',
        'key' => '',
        'key2' => '',
        'signature' => '',
        'enabled' => true,
    ];

    public function getIndex()
    {
        return view('sms-channel.index');
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $search = $request->get('search');
            $data['recordsTotal'] = SmsChannel::count();
            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = SmsChannel::select(['sms_channel.*', 'sms_category.name as cate_name'])->
                leftJoin('sms_category', 'sms_category.id', 'sms_channel.category_id')
                    ->where(function ($query) use ($search) {
                        $query->where('sms_channel.name', 'LIKE', '%' . $search['value'] . '%');
                    })->count();

                $data['data'] = SmsChannel::select(['sms_channel.*', 'sms_category.name as cate_name'])->
                leftJoin('sms_category', 'sms_category.id', 'sms_channel.category_id')
                    ->where(function ($query) use ($search) {
                        $query->where('sms_channel.name', 'LIKE', '%' . $search['value'] . '%');
                    })->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = SmsChannel::select(['sms_channel.*', 'sms_category.name as cate_name'])->
                leftJoin('sms_category', 'sms_category.id', 'sms_channel.category_id')->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            }
            return response()->json($data);
        }
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
        $data['categories'] = SmsCategory::all();
        return view('sms-channel.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LotteryCategoryCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(Request $request)
    {

        $row = new SmsChannel();
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->key = empty($row->key) ? '' : ssl_encrypt($row->key, env('PAYMENT_ENCRYPT_KEY'));
        $row->key2 = empty($row->key2) ? '' : ssl_encrypt($row->key2, env('PAYMENT_ENCRYPT_KEY'));

        $result = $row->save();
        if ($result) {
            Sms::Synchronize(0, $row->id);
            return redirect('/smschannel\/')->withSuccess('添加成功');
        } else {
            return redirect('/smschannel\/')->withErrors('添加失败');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = SmsChannel::find($id);
        if (!$row) {
            return redirect('/smschannel\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['key'] = empty($data['key']) ? $data['key'] : ssl_decrypt($data['key'], env('PAYMENT_ENCRYPT_KEY'));
        $data['key2'] = empty($data['key2']) ? $data['key2'] : ssl_decrypt($data['key2'], env('PAYMENT_ENCRYPT_KEY'));

        $data['id'] = (int)$id;
        $data['categories'] = SmsCategory::all();
        return view('sms-channel.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LotteryCategoryUpdateRequest|Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEdit(Request $request)
    {
        $row = SmsChannel::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/smschannel\/')->withErrors("找不到该通道");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->key = empty($row->key) ? '' : ssl_encrypt($row->key, env('PAYMENT_ENCRYPT_KEY'));
        $row->key2 = empty($row->key2) ? '' : ssl_encrypt($row->key2, env('PAYMENT_ENCRYPT_KEY'));

        $result = $row->save();
        if ($result) {
            Sms::Synchronize(0, $row->id);
            return redirect('/smschannel\/')->withSuccess('修改成功');
        } else {
            return redirect('/smschannel\/')->withErrors('修改失败');
        }
    }

    /**
     * 发送短信
     * @param Request $request
     */
    public function getSend(Request $request)
    {

        $data['id'] = (int)$request->get('id', 0);
        $data['channel'] = SmsChannel::select(['sms_channel.*', 'sms_category.name as cate_name'])->
        leftJoin('sms_category', 'sms_category.id', 'sms_channel.category_id')
            ->first();
        if (!$data['channel']) {
            return redirect('/smschannel\/')->withErrors("找不到该通道");
        }
        return view('sms-channel.send', $data);
    }

    /**
     *
     * @param Request $request
     */
    public function putSend(Request $request)
    {
        $channel_id = (int)$request->get('id', 0);
        $phone = $request->get('phone', '');
        if(!preg_match('/^1[3456789]\d{9}$/', $phone)) {
            return redirect('/smschannel\/')->withErrors("手机号码格式错误");
        }
        $message = $request->get('message', '');
        $channel = SmsChannel::select(['sms_channel.*', 'sms_category.name as cate_name'])->
        leftJoin('sms_category', 'sms_category.id', 'sms_channel.category_id')
            ->where('sms_channel.id', $channel_id)
            ->first();
        if (!$channel) {
            return redirect('/smschannel\/')->withErrors("找不到该通道");
        }
        $send_status = Sms::sendMessage(0, $phone, $message, auth()->user()->username);
        if ($send_status) {
            return redirect('/smschannel\/')->withSuccess("短信发送成功");
        }
        return redirect('/smschannel\/')->withErrors('短信发送失败' . Sms::$error_msg);
    }

    public function getRefreshserver()
    {
        $status = Sms::Synchronize();
        if ($status) {
            return redirect('/smschannel\/')->withSuccess('同步成功');
        } else {
            return redirect('/smschannel\/')->withErrors('同步失败' . Sms::$error_msg);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $row = smschannel::find((int)$request->get('id', 0));

        if ($row) {
            $row->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
