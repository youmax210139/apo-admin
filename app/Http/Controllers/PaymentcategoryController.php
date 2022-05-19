<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\PaymentCategory;
use App\Http\Requests\PaymentCategoryRequest;

class PaymentcategoryController extends Controller
{
    protected $fields = [
        'ident' => '',
        'name' => '',
        'methods' => [],
        'status' => true,
    ];

    public function getIndex()
    {
        $paymentcate = new PaymentCategory();

        $data['categories'] = $paymentcate::select(['id', 'ident', 'name', 'methods', 'status'])
            ->orderBy('id', 'asc')
            ->get();
        $methods = \Service\Models\PaymentMethod::select(['id', 'ident', 'name'])->get();
        $method_ident2name = [];
        foreach ($methods as $method) {
            $method_ident2name[$method->ident] = $method->name;
        }
        foreach ($data['categories'] as &$cate) {
            $methods_array = json_decode($cate->methods);
            $methods_name_array = [];
            foreach ($methods_array as $method_ident) {
                if (isset($method_ident2name[$method_ident])) {
                    $methods_name_array[] = $method_ident2name[$method_ident];
                } else {
                    $methods_name_array[] = $method_ident;
                }
            }
            $cate->methods = implode(',', $methods_name_array);
        }

        return view('paymentcategory.index', $data);
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
        $methods = \Service\Models\PaymentMethod::select(['id', 'ident', 'name'])->where('status', true)->orderBy('id', 'asc')->get();
        foreach ($methods as $key => $method) {
            $data['methods'][$key] = ['ident' => $method->ident, 'name' => $method->name, 'checked' => false];
        }

        return view('paymentcategory.create', $data);
    }

    /**
     * 添加渠道记录
     *
     * @param ConfigCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(PaymentCategoryRequest $request)
    {
        $paymentcate = new PaymentCategory();

        foreach (array_keys($this->fields) as $field) {
            $paymentcate->$field = $request->get($field, $this->fields[$field]);
        }
        $paymentcate->methods = json_encode($paymentcate->methods);

        $check_ident = PaymentCategory::where('ident', $paymentcate->ident)->first();
        if ($check_ident) {
            return redirect('/paymentcategory/create')->withErrors("英文标识已经存在");
        }

        $paymentcate->save();

        return redirect('/paymentcategory/')->withSuccess('添加成功');
    }

    /**
     * 编辑渠道页面
     *
     * @param Request $request
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $payment = PaymentCategory::find($id);
        if (!$payment) {
            return redirect('/paymentcategory/')->withErrors("找不到这个支付渠道");
        }

        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $payment->$field);
        }
        $methods_array = json_decode($data['methods']);
        $methods = \Service\Models\PaymentMethod::select(['id', 'ident', 'name'])->where('status', true)->orderBy('id', 'asc')->get();
        $data['methods'] = [];
        foreach ($methods as $key => $method) {
            if (in_array($method['ident'], $methods_array)) {
                $data['methods'][] = ['ident' => $method->ident, 'name' => $method->name, 'checked' => true];
            } else {
                $data['methods'][] = ['ident' => $method->ident, 'name' => $method->name, 'checked' => false];
            }
        }

        return view('paymentcategory.edit', $data);
    }

    /**
     * 保存编辑渠道
     *
     * @param Request $request
     * @return unknown|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function putEdit(PaymentCategoryRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $payment = PaymentCategory::find($id);

        foreach (array_keys($this->fields) as $field) {
            $payment->$field = $request->get($field, $this->fields[$field]);
        }
        $payment->methods = json_encode($payment->methods);

        $check_ident = PaymentCategory::where([['ident', $payment->ident], ['id', '!=', $id]])->first();
        if ($check_ident) {
            return redirect('/paymentcategory/edit/?id=' . $id)->withErrors("英文标识已经存在");
        }

        if ($payment->save()) {
            return redirect('/paymentcategory/')->withSuccess('修改支付渠道成功');
        } else {
            return redirect('/paymentcategory/edit/?id=' . $id)->withErrors('修改支付渠道失败');
        }
    }
}
