<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AdminCreateRequest;
use App\Http\Requests\AdminUpdateRequest;
use App\Http\Requests\AdminIndexRequest;
use Service\Models\Admin\AdminRole;
use Service\Models\Admin\AdminUser;

class AdminController extends Controller
{
    protected $fields = [
        'usernick' => '',
        'username' => '',
        'roles' => [],
    ];

    public function getIndex()
    {
        $data = array();
        $data['role_list'] = \Service\Models\Admin\AdminRole::all(['id', 'name']);
        return view('admin.index', $data);
    }

    public function postIndex(AdminIndexRequest $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $search = $request->get('search');

            $param = array();
            $param['ip'] = $request->get('ip');//用户IP
            $param['username'] = trim($request->get('username'));
            $param['role_id'] = $request->get('role_id');//角色ID

            //查询条件
            $where = function ($query) use ($param) {
                if (!empty($param['username'])) {
                    $query->where('admin_users.username', $param['username']);
                }
                //用户IP
                if (!empty($param['ip'])) {
                    $query->where('admin_users.last_ip', '<<=', "{$param['ip']}/24");
                }
                //角色类型
                if ($param['role_id'] > 0) {
                    $query->where('admin_roles.id', $param['role_id']);
                }
            };

            //计算过滤后总数
            $data['recordsTotal'] = $data['recordsFiltered'] = AdminUser
                ::leftJoin('admin_user_has_role', 'admin_users.id', 'admin_user_has_role.user_id')
                ->leftJoin('admin_roles', 'admin_user_has_role.role_id', 'admin_roles.id')
                ->where($where)->count();

            $data['data'] = AdminUser::select([
                'admin_users.id',
                'admin_users.usernick',
                'admin_users.username',
                'admin_users.is_locked',
                'admin_users.created_at',
                'admin_users.last_time',
                'admin_users.last_ip',
                'admin_users.google_key',
                DB::raw('(CASE admin_users.id
                        WHEN 1 THEN \'超级管理员\'
                        ELSE string_agg(admin_roles.name, \'、\')
                        END) as role_name')
            ])
                ->leftJoin('admin_user_has_role', 'admin_users.id', 'admin_user_has_role.user_id')
                ->leftJoin('admin_roles', 'admin_user_has_role.role_id', 'admin_roles.id')
                ->where($where)
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->groupBy('admin_users.id')
                ->get();

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
        $data['all_roles'] = AdminRole::all();

        return view('admin.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdminCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(AdminCreateRequest $request)
    {
        $user = new AdminUser();
        foreach (array_keys($this->fields) as $field) {
            $user->$field = $request->get($field);
        }
        if (preg_match('/^sys/i', $user->username)) {
            return redirect()->back()->withErrors('禁止使用sys开头的字符作为用户名');
        }
        $user->password = bcrypt($request->get('password'));
        unset($user->roles);

        try {
            $user->save();
        } catch (\Exception $e) {
            return redirect('/admin\/')->withErrors('添加失败，该管理员已存在' . $e->getMessage());
        }

        if (is_array($request->get('roles'))) {
            $user->giveRoleTo($request->get('roles'));
        }
        return redirect('/admin\/')->withSuccess('添加成功');
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
        $user = AdminUser::find($id);
        if (!$user) {
            return redirect('/admin\/')->withErrors("找不到该用户");
        }

        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $user->$field);
        }

        $data['all_roles'] = AdminRole::all();
        $data['id'] = (int)$id;

        return view('admin.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AdminUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(AdminUpdateRequest $request)
    {
        $user = AdminUser::find((int)$request->get('id', 0));
        foreach (array_keys($this->fields) as $field) {
            $user->$field = $request->get($field);
        }
        if (preg_match('/^sys/i', $user->username)) {
            return redirect()->back()->withErrors('禁止使用sys开头的字符作为用户名');
        }
        unset($user->roles);
        if ($request->get('password') != '') {
            $user->password = bcrypt($request->get('password'));
        }
        try {
            $user->save();
            $user->giveRoleTo($request->get('roles', []));
        } catch (\Exception $e) {
            redirect('/admin\/')->withErrors('操作失败！' . $e->getMessage());
        }
        return redirect('/admin\/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $user = AdminUser::find((int)$request->get('id', 0));

        if ($user->id == 1) {
            return redirect()->back()->withErrors("无法删除超级管理员");
        }

        if ($user) {
            $user->roles()->detach();

            $user->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }

    public function putGooglekey(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $user = AdminUser::find($id);
        if (!$user) {
            return redirect('/admin\/')->withErrors("找不到该用户");
        }
        $user->google_key = '';
        $user->save();
        return redirect('/admin\/')->withSuccess("管理员【" . $user->username . '】解绑登录器成功！');
    }

    public function putLock(Request $request)
    {
        $id = (int)$request->get('id', 0);
        if ($id == 1) {
            return redirect()->back()
                ->withErrors("无法操作超级管理员");
        }
        $user = AdminUser::find($id);
        if (!$user) {
            return redirect('/admin\/')->withErrors("找不到该用户");
        }
        $user->is_locked = $user->is_locked ? false : true;
        $user->save();
        return redirect('/admin\/')->withSuccess("管理员【" . $user->username . '】' . ($user->is_locked ? ' 解冻成功' : ' 冻结成功'));
    }
}
