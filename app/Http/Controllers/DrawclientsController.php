<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\DrawClients as DrawClientsModel;

class DrawclientsController extends Controller
{
    protected $fields = [
        'name' => '',
        'ident' => '',
        'request_status' => 'f',
        'request_key' => '',
        'request_ips' => '',
        'push_status' => 'f',
        'push_key' => '',
        'push_url' => ''
    ];

    public function getIndex()
    {
        $data = [];
        $data['openapi_switch'] = get_config('openapi_switch', 0);
        $data['pushservice_switch'] = get_config('pushservice_switch', 0);
        return view('drawclients.index', $data);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $search = $request->get('search');
            $data['recordsTotal'] = DrawClientsModel::count();
            $data['recordsFiltered'] = $data['recordsTotal'];
            $select_array = ['id', 'name', 'ident', 'request_status', 'request_ips', 'push_status', 'push_url'];
            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = DrawClientsModel::where('name', 'LIKE', '%' . $search['value'] . '%')
                    ->count();
                $data['data'] = DrawClientsModel::select($select_array)->where('name', 'LIKE', '%' . $search['value'] . '%')
                    ->skip($start)
                    ->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = DrawClientsModel::select($select_array)->skip($start)
                    ->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
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
        return view('drawclients.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DrawsourceCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(Request $request)
    {
        $row = new DrawClientsModel();
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        if (empty($row->ident)) {
            return redirect()->back()->withErrors("英文标识不能为空");
        }
        $row->save();
        return redirect('/drawclients/')->withSuccess('添加成功');
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
        $row = DrawClientsModel::find($id);
        if (!$row) {
            return redirect('/drawclients/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['id'] = (int)$id;
        return view('drawclients.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $op_type = $request->get('op_type', '');
        $op_status = (int)$request->get('op_status', 0);
        $row = DrawClientsModel::find($id);
        if (in_array($op_type, ['request', 'push'])) {
            if (!$row) {
                return response()->json(['status' => 1, 'msg' => '找不到该记录']);
            }
            if ($op_type == 'request') {
                $row->request_status = $op_status;
            } elseif ($op_type == 'push') {
                $row->push_status = $op_status;
            } else {
                return response()->json(['status' => 1, 'msg' => '未知操作']);
            }
            if ($row->save()) {
                return response()->json(['status' => 0, 'msg' => '修改成功']);
            } else {
                return response()->json(['status' => 1, 'msg' => '保存失败']);
            }
        }
        if (!$row) {
            return redirect('/drawclients/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->input($field);
        }
        if (empty($row->ident)) {
            return redirect()->back()->withErrors("英文标识不能为空");
        }
        $row->save();
        return redirect('/drawclients/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $row = DrawClientsModel::find((int)$request->get('id', 0));

        if ($row) {
            $row->delete();
            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
