<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserGroupCreateRequest;
use App\Http\Requests\UserGroupUpdateRequest;
use Service\Models\UserGroup;

class UsergroupController extends Controller
{

    protected $fields = [
        'name' => '',
    ];

    public function getIndex()
    {
        return view('user-group.index');
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
            $data['recordsTotal'] = UserGroup::count();
            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = UserGroup::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%');
                })->count();

                $data['data'] = UserGroup::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%');
                })->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = UserGroup::skip($start)->take($length)
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
        return view('user-group.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserGroupCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(UserGroupCreateRequest $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $row = new UserGroup();
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->save();
        return redirect('/usergroup\/')->withSuccess('添加成功');
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
        $row = UserGroup::find($id);
        if (!$row) {
            return redirect('/usergroup\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['id'] = (int)$id;
        return view('user-group.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserGroupUpdateRequest|Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEdit(UserGroupUpdateRequest $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $row = UserGroup::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/usergroup\/')->withErrors("找不到该用户组");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->save();
        return redirect('/usergroup\/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $row = UserGroup::find((int)$request->get('id', 0));

        if ($row) {
            $row->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
