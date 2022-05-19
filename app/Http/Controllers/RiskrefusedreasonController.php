<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\RiskRefusedReason;

class RiskrefusedreasonController extends Controller
{
    protected $fields = [
        'text' => '',
    ];

    public function getIndex()
    {
        return view('risk-refused-reason.index');
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
            $data['recordsTotal'] = RiskRefusedReason::count();
            if (!empty($search['value'])) {
                $data['recordsFiltered'] = RiskRefusedReason::where(function ($query) use ($search) {
                    $query
                        ->where('text', 'LIKE', '%' . $search['value'] . '%');
                })
                    ->count();
                $data['data'] = RiskRefusedReason::where(function ($query) use ($search) {
                    $query
                        ->where('text', 'LIKE', '%' . $search['value'] . '%');
                })
                    ->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = RiskRefusedReason::orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->skip($start)->take($length)
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
        return view('risk-refused-reason.create', $data);
    }

    /**
     * 新建后台配置项.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(Request $request)
    {
        $config = new RiskRefusedReason();
        foreach (array_keys($this->fields) as $field) {
            $config->$field = $request->get($field, $this->fields[$field]);
        }

        $value = $config->text;
        $config->text = is_array($value) ? implode(',', $value) : $value;
        if ($config->save()) {
            return redirect('/riskrefusedreason')->withSuccess('添加成功');
        } else {
            return redirect('/riskrefusedreason')->withErrors('添加失败');
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
        $config = RiskRefusedReason::find($id);
        if (!$config) {
            return redirect('/riskrefusedreason')->withErrors("找不到该配置项");
        }
        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $config->$field);
        }
        return view('risk-refused-reason.edit', $data);
    }

    /**
     * 更新后台配置项
     *
     * @param Request $request
     * @return mixed
     */
    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $config = RiskRefusedReason::find($id);

        foreach (array_keys($this->fields) as $field) {
            $config->$field = $request->get($field, $this->fields[$field]);
        }

        $value = $config->text;
        $config->text = is_array($value) ? implode(',', $value) : $value;

        if ($config->save()) {
            return redirect('/riskrefusedreason')->withSuccess('修改成功');
        } else {
            return redirect('/riskrefusedreason')->withErrors('修改失败');
        }
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
        $config = RiskRefusedReason::find($id);

        if ($config) {
            $config->delete();
            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
