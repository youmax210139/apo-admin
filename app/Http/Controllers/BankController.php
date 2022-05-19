<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BankController extends Controller
{
    protected $fields = [
        'name' => '',
        'ident' => '',
        'withdraw' => false,
        'disabled' => false
    ];

    public function getIndex()
    {
        $data = array();
        $data['banks'] = \Service\Models\Bank::orderBy('id', 'asc')->get();
        return view('bank.index', $data);
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

        return view('bank.create', $data);
    }

    public function putCreate(\App\Http\Requests\BankCreateRequest $request)
    {
        $row = new \Service\Models\Bank();

        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->save();
        return redirect('/bank\/')->withSuccess('添加成功');
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = \Service\Models\Bank::find($id);
        if (!$row) {
            return redirect('/bank\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['id'] = $id;
        return view('bank.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\BankUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(\App\Http\Requests\BankUpdateRequest $request)
    {
        $row = \Service\Models\Bank::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/bank\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }

        $row->save();

        return redirect('/bank\/')->withSuccess('修改成功');
    }

    public function putDisabled(Request $request)
    {
        $row = \Service\Models\Bank::find((int)$request->get('id', 0));
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
        $row = \Service\Models\Bank::find((int)$request->get('id', 0));
        if ($row) {
            $row->delete();
            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
