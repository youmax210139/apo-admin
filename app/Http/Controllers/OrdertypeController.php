<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderTypeCreateRequest;
use App\Http\Requests\OrderTypeUpdateRequest;
use Illuminate\Http\Request;
use Service\Models\OrderType;

class OrdertypeController extends Controller
{
    protected $fields = [
        'id' => '',
        'ident' => '',
        'name' => '',
        'display' => 0,
        'operation' => 0,
        'hold_operation' => 0,
        'category' => 0,
        'description' => '',
    ];

    public function getIndex()
    {
        return view('order-type.index');
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
            $data['recordsTotal'] = OrderType::count();
            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = OrderType::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%')
                        ->orWhere('description', 'LIKE', '%' . $search['value'] . '%');
                })->count();

                $data['data'] = OrderType::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%')
                        ->orWhere('description', 'LIKE', '%' . $search['value'] . '%');
                })->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = OrderType::skip($start)->take($length)
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

        return view('order-type.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderTypeCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(OrderTypeCreateRequest $request)
    {
        $role = new OrderType();
        foreach (array_keys($this->fields) as $field) {
            $role->$field = $request->get($field);
        }
        unset($role->permissions);
        $role->save();

        return redirect('/orderType\/')->withSuccess('添加成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $role = OrderType::find($id);
        if (!$role) {
            return redirect('/orderType\/')->withErrors("找不到该记录");
        }

        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $role->$field);
        }
        $data['id'] = (int)$id;
        return view('order-type.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OrderTypeUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(OrderTypeUpdateRequest $request)
    {
        $role = OrderType::find((int)$request->get('id', 0));
        if (!$role) {
            return redirect('/role\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $role->$field = $request->get($field);
        }
        $role->save();
        return redirect('/orderType\/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $role = OrderType::find((int)$request->get('id', 0));

        if ($role) {
            $role->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
