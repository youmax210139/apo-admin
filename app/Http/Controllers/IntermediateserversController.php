<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\IntermediateServers;
use App\Http\Requests\IntermediateServersRequest;
use Service\API\Payment as ApiPayment;

class IntermediateserversController extends Controller
{
    protected $fields = [
        'name' => '',
        'ip'=>'',
        'domain' => '',
        'status' => true,
    ];

    protected $controller_name = 'intermediateservers';

    public function getIndex()
    {
        $data = [];
        $data['rows'] = \Service\Models\IntermediateServers::orderBy('id', 'asc')->get();
        foreach ($data['rows'] as $key => &$item) {
            $ip_array = explode('.', $item->ip);
            array_splice($ip_array, 0, 2, ['*', '*']);
            $item->ip = implode('.', $ip_array);
        }

        return view("{$this->controller_name}.index", $data);
    }

    public function getCreate()
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        return view("{$this->controller_name}.create", $data);
    }

    /**
     * 添加
     *
     * @param ConfigCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(IntermediateServersRequest $request)
    {
        $object = new IntermediateServers();

        foreach (array_keys($this->fields) as $field) {
            $object->$field = $request->get($field, $this->fields[$field]);
            if ($field === 'domain') {
                $object->$field = preg_replace('/[\/\s]{0,}$/', '', $object->$field);
            }
        }

        if ($object->save()) {
            //同步到服务器
            $ApiPayment = new ApiPayment();
            $sync_result = $ApiPayment->sync($object->id, 0);
            return redirect("/{$this->controller_name}/")->withSuccess('添加成功。'.$sync_result['msg']);
        } else {
            return redirect("/{$this->controller_name}/create/")->withErrors('添加失败');
        }
    }

    /**
     * 编辑页面
     *
     * @param Request $request
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');
        $row = IntermediateServers::find($id);
        if (!$row) {
            return redirect("/{$this->controller_name}/")->withErrors("找不到这个服务器");
        }

        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }

        return view("{$this->controller_name}.edit", $data);
    }

    /**
     * 保存编辑
     *
     * @param Request $request
     * @return unknown|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function putEdit(IntermediateServersRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $row = IntermediateServers::find($id);
        if (!$row) {
            return redirect("/{$this->controller_name}/")->withErrors("找不到这个服务器");
        }
        $is_sync = false;
        if ($request->get('ip') != $row->ip) {
            $is_sync = true;
        }

        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field, $this->fields[$field]);
            if ($field === 'domain') {
                $row->$field = preg_replace('/[\/\s]{0,}$/', '', $row->$field);
            }
        }

        if ($row->save()) {
            $msg = '';
            if ($is_sync) {
                $ApiPayment = new ApiPayment();
                $sync_result = $ApiPayment->sync($id, 0);
                $msg = $sync_result['msg'];
            }
            return redirect("/{$this->controller_name}/")->withSuccess('修改成功。'.$msg);
        } else {
            return redirect("/{$this->controller_name}/edit/?id={$id}")->withErrors('修改失败');
        }
    }

    /**
     * 设置状态
     *
     * @param Request $request
     * @return unknown|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function putSetStatus(Request $request)
    {
        $id = (int)$request->get('id');
        $status = (int)$request->get('status');

        $row = IntermediateServers::find($id);
        if (!$row) {
            return redirect("/{$this->controller_name}/")->withErrors("找不到这个服务器");
        }

        $row->status = $status;

        if ($row->save()) {
            $status_type = ($status == 1) ? '开启' : '关闭';
            return redirect("/{$this->controller_name}/")->withSuccess("【{$row->name}】设置为【{$status_type}】");
        } else {
            return redirect("/{$this->controller_name}/")->withErrors("设置失败");
        }
    }

    /**
     * 删除一个
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|unknown
     */
    public function deleteDelRecord(Request $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $id = (int)$request->get('id', 0);

        $row = IntermediateServers::find($id);

        if ($row && $row->delete()) {
            return redirect()->back()->withSuccess("删除成功");
        } else {
            return redirect()->back()->withErrors("删除失败");
        }
    }

    /**
     * 同步到服务器
     * @param $ids_array
     * @return bool
     */
    public function getRefreshServer(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $ApiPayment = new ApiPayment();
        $sync_result = $ApiPayment->sync($id, 0);
        if ($sync_result['result'] == true) {
            return redirect("/{$this->controller_name}/")->withSuccess($sync_result['msg']);
        } else {
            return redirect("/{$this->controller_name}/")->withErrors($sync_result['msg']);
        }
    }
}
