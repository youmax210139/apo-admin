<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\PaymentMethod;
use App\Http\Requests\PaymentMethodRequest;

class PaymentmethodController extends Controller
{
    protected $fields = [
        'ident' => '',
        'name' => '',
        'sync' => true,
        'status' => true,
    ];

    protected $controller_name = 'paymentmethod';

    public function getIndex()
    {
        $data = [];
        $data['rows'] = \Service\Models\PaymentMethod::orderBy('id', 'asc')->get();

        return view("{$this->controller_name}.index", $data);
    }

    /**
     * display create page
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getCreate(Request $request)
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        return view("{$this->controller_name}.create", $data);
    }

    /**
     * 添加支付类型
     *
     * @param ConfigCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(PaymentMethodRequest $request)
    {
        $object = new PaymentMethod();

        foreach (array_keys($this->fields) as $field) {
            $object->$field = $request->get($field, $this->fields[$field]);
        }
        $object->ident = strtolower($object->ident);

        $object->save();

        return redirect("/{$this->controller_name}/")->withSuccess('添加支付类型成功');
    }

    /**
     * 编辑支付类型页面
     *
     * @param Request $request
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $row = PaymentMethod::find($id);
        if (!$row) {
            return redirect("/{$this->controller_name}/")->withErrors("找不到这个支付类型");
        }

        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }

        return view("{$this->controller_name}.edit", $data);
    }

    /**
     * 保存编辑支付类型
     *
     * @param Request $request
     * @return unknown|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function putEdit(PaymentMethodRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $row = PaymentMethod::find($id);

        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field, $this->fields[$field]);
        }
        $row->ident = strtolower($row->ident);
        $check_ident = PaymentMethod::where([['ident', $row->ident], ['id', '!=', $id]])->first();
        if ($check_ident) {
            return redirect("/{$this->controller_name}/edit/?id={$id}")->withErrors("英文标识已经存在");
        }

        if ($row->save()) {
            return redirect("/{$this->controller_name}/")->withSuccess('修改成功');
        } else {
            return redirect("/{$this->controller_name}/edit/?id={$id}")->withErrors('修改失败');
        }
    }
}
