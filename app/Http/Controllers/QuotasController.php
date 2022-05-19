<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuotasController extends Controller
{
    protected $fields = [
        'low' => 0,
        'high' => 0,
    ];

    public function getIndex()
    {
        $quotas = \Service\Models\Quotas::get();
        return view('quotas.index', ['quotas' => $quotas]);
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
        return view('quotas.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(Request $request)
    {
        $row = new \Service\Models\Quotas();
        if ($request->get('low') > $request->get('high')) {
            return redirect()->back()->withErrors("上限不能低于下限");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->save();
        return redirect('/quotas\/')->withSuccess('添加成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $row = \Service\Models\Quotas::find((int)$request->get('id', 0));
        if ($row) {
            $row->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
