<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\ConfigCreateRequest;
use App\Http\Requests\ConfigUpdateRequest;
use App\Http\Requests\ConfigSetRequest;
use Exception;
use Service\Models\Config;

class ConfigController extends Controller
{
    protected $fields = [
        'parent_id' => 0,
        'title' => '',
        'key' => '',
        'value' => '',
        'description' => '',
        'is_disabled' => 0,
        'input_type' => 0,
        'value_type' => 0,
        'input_option' => '',
    ];

    public function getIndex(Request $request)
    {
        $parent_id = (int)$request->get('parent_id', 0);

        $data['parent_id'] = $parent_id;
        if ($parent_id > 0) {
            $data['parent_name'] = Config::where('id', $parent_id)->value("title");
        }

        $data['refresh_at'] = Cache::store('redis')->get('ConfigLastRefreshAt', '');
        return view('config.index', $data);
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
            $parent_id = (int)$request->get('parent_id', 0);
            $data['recordsTotal'] = Config::where('parent_id', $parent_id)->count();
            if (!empty($search['value'])) {
                $data['recordsFiltered'] = Config::where(function ($query) use ($search) {
                    $query
                        ->where('key', 'LIKE', '%' . $search['value'] . '%')
                        ->orWhere('title', 'LIKE', '%' . $search['value'] . '%');
                })
                    ->count();
                $data['data'] = Config::where(function ($query) use ($search) {
                    $query
                        ->where('key', 'LIKE', '%' . $search['value'] . '%')
                        ->orWhere('title', 'LIKE', '%' . $search['value'] . '%');
                })
                    ->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = Config::where('parent_id', $parent_id)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->skip($start)->take($length)
                    ->get();
            }
            foreach ($data['data'] as $k => $v) {
                if ($v->input_option && $v->input_type == 1) {
                    $options = explode(",", $v->input_option);
                    foreach ($options as $option) {
                        $option = explode("|", $option);
                        if ($v->value == $option[0] && isset($option[1])) {
                            $data['data'][$k]->value = $option[1];
                        }
                    }
                }
            }
            return response()->json($data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate(Request $request)
    {
        $parent_id = (int)$request->get('parent_id', 0);
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        $parent_config = Config::where('parent_id', 0)->get();
        $data['parent_id'] = $parent_id;
        $data['parent_config'] = $parent_config;
        return view('config.create', $data);
    }

    /**
     * 新建后台配置项.
     *
     * @param ConfigCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(ConfigCreateRequest $request)
    {
        $parent_id = (int)$request->get('parent_id', 0);
        $config = new Config();
        foreach (array_keys($this->fields) as $field) {
            $config->$field = $request->get($field, $this->fields[$field]);
        }
        $config->parent_id = $parent_id;

        $value = $config->value;
        $config->value = is_array($value) ? implode(',', $value) : $value;

        $check_data = $this->_checkData($config->input_type, $config->value_type, $config->value, $config->input_option);
        if ($check_data !== true) {
            return redirect()->back()->withErrors($check_data['msg']);
        }

        if ($config->save()) {
            return redirect('/config\/?parent_id=' . $config->parent_id)->withSuccess('添加成功');
        } else {
            return redirect('/config\/?parent_id=' . $config->parent_id)->withErrors('添加失败');
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
        $config = Config::find($id);
        if (!$config) {
            return redirect('/config\/')->withErrors("找不到该配置项");
        }
        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $config->$field);
        }
        $parent_config = Config::where('parent_id', 0)->get();
        $data['parent_config'] = $parent_config;
        return view('config.edit', $data);
    }

    /**
     * 更新后台配置项
     *
     * @param ConfigUpdateRequest $request
     * @return mixed
     */
    public function putEdit(ConfigUpdateRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $config = Config::find($id);

        foreach (array_keys($this->fields) as $field) {
            $config->$field = $request->get($field, $this->fields[$field]);
        }

        $value = $config->value;
        $config->value = is_array($value) ? implode(',', $value) : $value;

        $check_data = $this->_checkData($config->input_type, $config->value_type, $config->value, $config->input_option);
        if ($check_data !== true) {
            return redirect()->back()->withErrors($check_data['msg']);
        }

        if ($config->save()) {
            return redirect('/config\/?parent_id=' . $config->parent_id)->withSuccess('修改成功');
        } else {
            return redirect('/config\/?parent_id=' . $config->parent_id)->withErrors('修改失败');
        }
    }

    /**
     * 配置设置为启用或者禁用
     *
     * @param Request $request
     * @return mixed
     */
    public function putDisable(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $config = Config::find($id);
        $config->is_disabled = $config->is_disabled == 0;
        $tips = ($config->is_disabled == 1) ? '禁用' : '启用';

        if ($config->save()) {
            return redirect('/config\/?parent_id=' . $config->parent_id)->withSuccess("【{$config->title}】设置为【{$tips}】");
        } else {
            return redirect('/config\/?parent_id=' . $config->parent_id)->withErrors("设置失败");
        }
    }

    /**
     * 配置项写入缓存redis
     *
     * @param Request $request
     * @return string
     */
    public function getRefresh(Request $request)
    {
        Redis::del('sysConfig');
        Redis::pipeline(function ($pipe) {
            $cache_config = Config::where('parent_id', "!=", 0)->where('is_disabled', 0)->get();
            foreach ($cache_config as $v) {
                $pipe->hset('sysConfig', $v->key, $v->value);
            }
        });
        Cache::store('redis')->forever('ConfigLastRefreshAt', auth()->user()->username . "|" . date('Y-m-d H:i:s'));
        return redirect('/config\/')->withSuccess("刷新缓存成功");
    }

    /**
     * 删除后台配置项
     *
     * @param Request $request
     * @return $this
     */
    public function deleteIndex(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $child = Config::where('parent_id', $id)->first();

        if ($child) {
            return redirect()->back()->withErrors("请先删除该配置项的子项");
        }

        $config = Config::find($id);

        if ($config) {
            $config->delete();
            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }

    /**
     * 设置配置
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getSet(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $config = Config::find($id);
        if (!$config) {
            return redirect('/config\/')->withErrors("找不到该配置项");
        }
        if ($config->parent_id == 0) {
            return redirect('/config\/')->withErrors("抱歉，分组无法设置");
        }
        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $config->$field);
        }
        $parent_config = Config::where('id', $config->parent_id)->first();
        $data['parent_config'] = $parent_config;
        return view('config.set', $data);
    }

    public function putSet(ConfigSetRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $value = $request->get('value', '');
        $config = Config::find($id);
        if (!$config) {
            return response()->json([
                'status' => 1,
                'msg' => '配置项不存在'
            ]);
        }
        if ($config->parent_id == 0) {
            return response()->json([
                'status' => 1,
                'msg' => '抱歉，分组无法设置'
            ]);
        }

        $config->value = is_array($value) ? implode(',', $value) : $value;

        $check_data = $this->_checkData($config->input_type, $config->value_type, $config->value, $config->input_option);
        if ($check_data !== true) {
            return redirect()->back()->withErrors($check_data['msg']);
            return response()->json([
                'status' => 1,
                'msg' => $check_data['msg']
            ]);
        }

        try {
            $config->save();
            return response()->json([
                'status' => 0,
                'msg' => '设置成功，刷新配置后生效'
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json([
                'status' => 1,
                'msg' => '操作失败'
            ]);
        }
    }

    //数据保存前的校验
    private function _checkData($input_type, $value_type, $value, $input_option)
    {
        if ($input_type == 0) {
            if ($value_type == 1 && !is_numeric($value)) {
                return ['msg' => '配置值应为数字'];
            }
            if ($value_type == 2 && (!is_numeric($value) || is_numeric($value) && $value <= 0)) {
                return ['msg' => '配置值应为正数'];
            }
        }
        if ($input_type == 1 || $input_type == 2) {
            if ($input_option == '') {
                return ['msg' => '输入选项不能为空'];
            }
            $input_option = explode(",", $input_option);
            if (count($input_option) < 2) {
                return ['msg' => '输入选项设置错误'];
            }
            $temp = [];
            foreach ($input_option as $option) {
                $option = explode("|", $option);
                if (count($option) < 2 || $option[0] == '' || $option[1] == "") {
                    return ['msg' => '输入选项设置错误'];
                }
                $temp[] = $option[0];
            }
            $value = explode(',', $value);
            if ($input_type == 1 && count($value) > 1) {
                return ['msg' => '配置值设置错误'];
            }
            foreach ($value as $item) {
                if (!in_array($item, $temp, true)) {
                    return ['msg' => '配置值设置错误'];
                }
            }
        }
        return true;
    }

    public function getRefreshapp()
    {
        event(new \Service\Events\Notice(0, '', '', 9));
        return redirect('/config\/')->withSuccess("广播发送成功");
    }
}
