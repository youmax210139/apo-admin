<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use Service\Models\Admin\AdminRole;
use Service\Models\Admin\AdminRolePermission;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected $fields = [
        'name' => '',
        'description' => '',
        'permissions' => [],
    ];

    public function getIndex()
    {
        return view('role.index');
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
            $data['recordsTotal'] = AdminRole::count();
            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = AdminRole::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%')
                        ->orWhere('description', 'LIKE', '%' . $search['value'] . '%');
                })->count();

                $data['data'] = AdminRole::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%')
                        ->orWhere('description', 'LIKE', '%' . $search['value'] . '%');
                })->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = AdminRole::skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            }

            return response()->json($data);
        }
        if ($request->get('export')) {
            $id = (int)$request->get('id', 0);
            $role = AdminRole::find($id);
            $permissions = [];
            $file_name = "{$role->name}.csv";
            if (!$role) {
                return redirect('/role\/')->withErrors("找不到该角色");
            }
            if ($role->permissions) {
                foreach ($role->permissions as $v) {
                    $permissions[] = $v;
                }
            }
            $response = new StreamedResponse(null, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ]);
            $response->setCallback(function () use ($permissions) {
                $out = fopen('php://output', 'w');
                fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                $columnNames[] = 'role';
                $columnNames[] = 'name';
                $datas = [];
                fputcsv($out, $columnNames);
                foreach ($permissions as $item) {
                    $datas[] = [
                        $item->rule,
                        $item->name
                    ];
                }
                foreach ($datas as $item) {
                    fputcsv($out, $item);
                }
            });
            $response->send();
        }
        if ($request->get('import')) {
            $id = (int)$request->get('id', 0);
            $role = AdminRole::find($id);
            if (!$role) {
                return redirect('/role\/')->withErrors("找不到该角色");
            }
            //上传文件
            $file = $request->file('import_file');
            if (!empty($file)) {
                $validator = Validator::make($request->all(), [
                    'import_file' => 'required|mimes:csv,txt'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors("失败：上传文件不正确，不是CSV格式");
                }
                $file_contents = File::get($file);
                if (mb_detect_encoding($file_contents) != "UTF-8") {
                    return redirect()->back()->withErrors("只能上传UTF-8格式文件");
                }
                $file_name = $request->file('import_file')->getClientOriginalName();
                if (!strstr($file_name, $role->name)) {
                    return redirect()->back()->withErrors("失败：上传文件档名不正确，请上传相对应的正确档名 ，如:平台管理员 则上传 平台管理员.csv");
                }
                if (!empty($file_contents)) {
                    $rows = explode("\n", $file_contents);
                    foreach ($rows as $line) {
                        $_v = array_filter(explode(',', $line), 'trim');
                        if (!empty($_v)) {
                            $rule[] = $_v[0];
                        }
                    }
                    $data = AdminRolePermission::whereIn('rule', $rule)->orderBy('id', 'asc')->get();
                    $permission = $data->pluck('id')->toArray();
                    if (!empty($permission)) {
                        $role->save();
                        $role->permissions()->sync($permission);

                        return redirect('/role\/')->withSuccess('修改成功');
                    } else {
                        return redirect()->back()->withErrors("失败：请检查上传文件或内容有错误");
                    }
                }
            }
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

        $data['permission_all'] = $this->_getPermissions();

        return view('role.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(RoleCreateRequest $request)
    {
        $role = new AdminRole();
        foreach (array_keys($this->fields) as $field) {
            $role->$field = $request->get($field);
        }
        unset($role->permissions);
        $role->save();

        $role->permissions()->sync($request->get('permissions', []));

        return redirect('/role\/')->withSuccess('添加成功');
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
        $role = AdminRole::find($id);
        if (!$role) {
            return redirect('/role\/')->withErrors("找不到该角色");
        }
        $permissions = [];
        if ($role->permissions) {
            foreach ($role->permissions as $v) {
                $permissions[] = $v->id;
            }
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $role->$field);
        }

        $data['permissions'] = $permissions;
        $data['permission_all'] = $this->_getPermissions();
        $data['id'] = (int)$id;
        return view('role.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RoleUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(RoleUpdateRequest $request)
    {
        $role = AdminRole::find((int)$request->get('id', 0));
        if (!$role) {
            return redirect('/role\/')->withErrors("找不到该角色");
        }
        foreach (array_keys($this->fields) as $field) {
            $role->$field = $request->get($field);
        }
        unset($role->permissions);
        $role->save();

        $role->permissions()->sync($request->get('permissions', []));
        return redirect('/role\/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $role = AdminRole::find((int)$request->get('id', 0));

        if ($role) {
            $role->users()->detach();

            $role->permissions()->detach();

            $role->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }

    private function _getPermissions()
    {
        $data = array();
        $arr = AdminRolePermission::all();
        foreach ($arr as $v) {
            if ($v->parent_id == 0) {
                $data['root'][] = $v;
            } else {
                $rule_arr = explode("/", $v->rule);
                $controller = $rule_arr[0];
                $action = isset($rule_arr[1]) ? $rule_arr[1] : '';
                if ($action == 'index') {
                    $v->controller = $controller;
                    $data['second'][$v->parent_id][] = $v;
                } else {
                    $data['third'][$controller][] = $v;
                }
            }
        }

        return $data;
    }
}
