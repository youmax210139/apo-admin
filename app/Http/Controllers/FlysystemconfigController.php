<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\FlySystemConfigCreateRequest;
use App\Http\Requests\FlySystemConfigUpdateRequest;
use Service\Models\FlySystemConfig;

class FlysystemconfigController extends Controller
{
    protected $fields = [
        'ident' => '',
        'name' => '',
        'lottery_idents' => '',
        'domain' => '',
        'config' => '{}',
        'status' => 0,
    ];

    public function getIndex()
    {
        return view('fly-system-config.index');
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

            $data['recordsTotal'] = FlySystemConfig::all()->count();

            $data['recordsFiltered'] = $data['recordsTotal'];
            $data['data'] = FlySystemConfig::select()
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->skip($start)->take($length)
                ->get();

            return response()->json($data);
        }
    }

    public function getCreate()
    {
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        return view('fly-system-config.create', $data);
    }

    public function postCreate(FlySystemConfigCreateRequest $request)
    {
        $row = new FlySystemConfig();

        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }

        if (!self::isJsonFormat($row->config)) {
            return redirect()->back()->withErrors('参数配置不是json格式');
        }

        $row->ident = strtolower($row->ident);
        $row->domain = rtrim($row->domain, '/');

        $row->save();
        return redirect('/flysystemconfig\/')->withSuccess('添加成功');
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $fly_config = FlySystemConfig::find($id);
        if (!$fly_config) {
            return redirect('/flysystemconfig\/')->withErrors("找不到该配置");
        }

        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $fly_config->$field);
        }

        return view('fly-system-config.edit', $data);
    }

    public function putEdit(FlySystemConfigUpdateRequest $request)
    {
        $row = FlySystemConfig::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/flysystemconfig\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }

        if (!self::isJsonFormat($row->config)) {
            return redirect()->back()->withErrors('参数配置不是json格式');
        }

        $row->ident = strtolower($row->ident);
        $row->domain = rtrim($row->domain, '/');

        $row->save();

        Cache::store('redis')->forget('fly_system_config_' . $row->ident);   //清空缓存

        return redirect('/flysystemconfig\/')->withSuccess('修改成功');
    }

    //判断是否为json
    private static function isJsonFormat($json)
    {
        @json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }

    //禁用或启用
    public function putStatus(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $fly_config = FlySystemConfig::find($id);
        $fly_config->status = $fly_config->status == 0;
        $tips = ($fly_config->status == 1) ? '启用' : '禁用';

        if ($fly_config->save()) {
            Cache::store('redis')->forget('fly_system_config_' . $fly_config->ident);   //清空缓存
            return redirect('/flysystemconfig\/')->withSuccess("修改配置【{$fly_config->name}】为【{$tips}】");
        } else {
            return redirect('/flysystemconfig\/')->withErrors("更新配置失败");
        }
    }
}
