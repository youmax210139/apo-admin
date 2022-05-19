<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\PaymentDomain;
use App\Http\Requests\PaymentDomainRequest;
use Service\API\Deposit as ApiDeposit;

class PaymentdomainController extends Controller
{
    protected $fields = [
        'domain' => '',
        'payment_category_id' => 0,
        'intermediate_servers_id' => 0,
        'status' => true,
        'remark' => '',
    ];

    protected $controller_name = 'paymentdomain';

    public function getIndex()
    {
        $data = [];
        $data['rows'] = \Service\Models\PaymentDomain::select(['payment_domain.*', 'payment_category.name AS payment_category_name', 'intermediate_servers.name AS intermediate_servers_name'])
            ->leftJoin('payment_category', 'payment_category.id', 'payment_domain.payment_category_id')
            ->leftJoin('intermediate_servers', 'intermediate_servers.id', 'payment_domain.intermediate_servers_id')
            ->orderBy('payment_domain.id', 'asc')
            ->get();

        return view("{$this->controller_name}.index", $data);
    }

    /**
     * display create page
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getCreate()
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        //服务器
        $data['categories'] = \Service\Models\PaymentCategory::where('status', true)->orderBy('id', 'asc')->get();
        //服务器
        $data['servers'] = \Service\Models\IntermediateServers::where('status', true)->orderBy('id', 'asc')->get();

        return view("{$this->controller_name}.create", $data);
    }

    /**
     * 添加
     *
     * @param ConfigCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(PaymentDomainRequest $request)
    {
        $object = new PaymentDomain();

        foreach (array_keys($this->fields) as $field) {
            $object->$field = $request->get($field, $this->fields[$field]);
        }

        //检查域名、如果不通则无法添加
        $apiDeposit = new ApiDeposit();
        $ping_result = $apiDeposit->ping($object->domain);

        if (!$ping_result) {
            return redirect("/{$this->controller_name}/")->withErrors("该域名不通，请检查服务器是否正常！");
        }

        $object->save();

        return redirect("/{$this->controller_name}/")->withSuccess('添加成功');
    }

    /**
     * 编辑页面
     *
     * @param Request $request
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $row = PaymentDomain::find($id);
        if (!$row) {
            return redirect("/{$this->controller_name}/")->withErrors("找不到这个域名");
        }

        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        //服务器
        $data['categories'] = \Service\Models\PaymentCategory::where('status', true)->orderBy('id', 'asc')->get();
        //服务器
        $data['servers'] = \Service\Models\IntermediateServers::where('status', true)->orderBy('id', 'asc')->get();

        return view("{$this->controller_name}.edit", $data);
    }

    /**
     * 保存编辑
     *
     * @param Request $request
     * @return unknown|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function putEdit(PaymentDomainRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $row = PaymentDomain::find($id);

        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field, $this->fields[$field]);
        }

        if ($row->save()) {
            return redirect("/{$this->controller_name}/")->withSuccess('修改成功');
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

        $row = PaymentDomain::find($id);

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

        $row = PaymentDomain::find($id);

        if ($row && $row->delete()) {
            return redirect()->back()->withSuccess("删除成功");
        } else {
            return redirect()->back()->withErrors("删除失败");
        }
    }
}
