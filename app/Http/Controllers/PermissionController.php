<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PermissionCreateRequest;
use App\Http\Requests\PermissionUpdateRequest;
use Service\Models\Admin\AdminRolePermission;

class PermissionController extends Controller
{
    protected $fields = [
        'parent_id' => 0,
        'icon' => '',
        'rule' => '',
        'name' => '',
        'description' => '',
    ];

    public function getIndex(Request $request)
    {
        $parent_id = (int)$request->get('parent_id', 0);

        $data['parent_id'] = $parent_id;
        return view('permission.index', $data);
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
            $parent_id = (int)$request->get('parent_id', 0);
            $data['recordsTotal'] = AdminRolePermission::where('parent_id', $parent_id)->count();
            if (!empty($search['value'])) {
                $data['recordsFiltered'] = AdminRolePermission::where('parent_id', $parent_id)
                    ->where(function ($query) use ($search) {
                        $query
                            ->where('rule', 'LIKE', '%' . $search['value'] . '%')
                            ->orWhere('name', 'LIKE', '%' . $search['value'] . '%')
                            ->orWhere('description', 'LIKE', '%' . $search['value'] . '%');
                    })
                    ->count();
                $data['data'] = AdminRolePermission::where('parent_id', $parent_id)
                    ->where(function ($query) use ($search) {
                        $query
                            ->where('rule', 'LIKE', '%' . $search['value'] . '%')
                            ->orWhere('name', 'LIKE', '%' . $search['value'] . '%')
                            ->orWhere('description', 'LIKE', '%' . $search['value'] . '%');
                    })
                    ->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = AdminRolePermission::where('parent_id', $parent_id)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
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
    public function getCreate(Request $request)
    {
        $parent_id = (int)$request->get('parent_id', 0);
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        $data['parent_id'] = $parent_id;

        return view('permission.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PremissionCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(PermissionCreateRequest $request)
    {
        $permission = new AdminRolePermission();
        foreach (array_keys($this->fields) as $field) {
            $permission->$field = $request->get($field, $this->fields[$field]);
        }

        $permission->save();

        return redirect('/permission\/?parent_id=' . $permission->parent_id)->withSuccess('添加成功');
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
        $permission = AdminRolePermission::find($id);
        if (!$permission) {
            return redirect('/permission\/')->withErrors("找不到该权限");
        }
        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $permission->$field);
        }

        return view('permission.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PermissionUpdateRequest|Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEdit(PermissionUpdateRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $permission = AdminRolePermission::find((int)$id);
        foreach (array_keys($this->fields) as $field) {
            $permission->$field = $request->get($field, $this->fields[$field]);
        }
        $permission->save();
        return redirect('/permission\/?parent_id=' . $permission->parent_id)->withSuccess('修改成功');
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

        $id = (int)$request->get('id', 0);
        $child = AdminRolePermission::where('parent_id', $id)->first();

        if ($child) {
            return redirect()->back()->withErrors("请先将该权限的子权限删除后再做删除操作");
        }

        $permission = AdminRolePermission::find((int)$id);

        if ($permission) {
            $permission->roles()->detach();

            $permission->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
