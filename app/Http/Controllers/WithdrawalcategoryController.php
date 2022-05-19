<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalCategoryCreateRequest;
use App\Http\Requests\WithdrawalCategoryUpdateRequest;
use Illuminate\Http\Request;
use Service\Models\WithdrawalCategory;

class WithdrawalcategoryController extends Controller
{
    private $fields = [
        'name' => '',
        'ident' => '',
        'request_url' => '',
        'verify_url' => '',
        'notify_url' => '',
        'banks' => [],
        'status' => true,
    ];

    public function getIndex()
    {
        $withdrawal_cate = new WithdrawalCategory();

        $data['categories'] = $withdrawal_cate::select(['id', 'name', 'ident', 'status'])
            ->orderBy('id', 'asc')
            ->get();


        return view('withdrawalcategory.index', $data);
    }

    public function getCreate()
    {
        $data = [];

        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        $data['bank_list'] = \Service\Models\Bank::where(['disabled' => false, 'withdraw' => true])->get();

        return view('withdrawalcategory.create', $data);
    }

    public function postCreate(WithdrawalCategoryCreateRequest $request)
    {
        $withdrawal_cate = new WithdrawalCategory();

        foreach (array_keys($this->fields) as $field) {
            $withdrawal_cate->$field = $request->get($field, $this->fields[$field]);
        }
        $withdrawal_cate->banks = implode(",", $withdrawal_cate->banks);

        $check_ident = WithdrawalCategory::where('ident', $withdrawal_cate->ident)->first();
        if ($check_ident) {
            return redirect('/withdrawalcategory/create')->withErrors("英文标识已经存在");
        }

        $withdrawal_cate->save();

        return redirect('/withdrawalcategory/')->withSuccess('添加成功');
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $withdrawal = WithdrawalCategory::find($id);

        if (empty($withdrawal)) {
            return redirect('/withdrawalcategory/')->withErrors("找不到这个提现渠道");
        }

        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $withdrawal->$field);
        }
        $data['banks'] = !empty($data['banks']) ? explode(',', $data['banks']) : [];

        $data['bank_list'] = \Service\Models\Bank::where(['disabled' => false, 'withdraw' => true])->get();

        return view('withdrawalcategory.edit', $data);
    }

    public function putEdit(WithdrawalCategoryUpdateRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $withdrawal = WithdrawalCategory::find($id);

        if (empty($withdrawal)) {
            return redirect('/withdrawalcategory/')->withErrors("找不到这个提现渠道");
        }

        foreach (array_keys($this->fields) as $field) {
            $withdrawal->$field = $request->get($field, $this->fields[$field]);
        }
        $withdrawal->banks = implode(',', $withdrawal->banks);

        $check_ident = WithdrawalCategory::where([['ident', $withdrawal->ident], ['id', '!=', $id]])->first();
        if ($check_ident) {
            return redirect('/withdrawalcategory/edit/?id=' . $id)->withErrors("英文标识已经存在");
        }

        if ($withdrawal->save()) {
            return redirect('/withdrawalcategory/')->withSuccess('修改提现渠道成功');
        } else {
            return redirect('/withdrawalcategory/edit/?id=' . $id)->withErrors('修改提现渠道失败');
        }
    }

    /**
     * 删除一个支付渠道
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|unknown
     */
    public function deleteIndex(Request $request)
    {
        $id = (int)$request->get('id', 0);

        $row = WithdrawalCategory::find($id);
        if ($row && $row->delete()) {
            return redirect()->back()->withSuccess("删除成功");
        } else {
            return redirect()->back()->withErrors("删除失败");
        }
    }
}
