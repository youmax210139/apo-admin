<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifychangepwdController extends Controller
{
    public function getIndex()
    {
        return view('secondverify.changepass');
    }

    public function postIndex(Request $request)
    {
        $data = array();
        $data['draw'] = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $columns = $request->get('columns');
        $order = $request->get('order');
        $search = $request->get('search');

        $param = array();
        $param['status'] = (int)$request->get('status', 0); //审核状态
        $param['created_admin'] = $request->get('created_admin'); //充值员
        $param['created_start_date'] = $request->get('created_start_date'); //开始时间
        $param['created_end_date'] = $request->get('created_end_date'); //结束时间
        $param['username'] = $request->get('username'); //充值会员
        $param['verify_admin'] = $request->get('verify_admin'); //审核员
        $param['verify_start_date'] = $request->get('verify_start_date'); //审核开始时间
        $param['verify_end_date'] = $request->get('verify_end_date'); //审核结束时间
        $param['page'] = $request->get('page'); //当前页

        $count_query = \Service\Models\SecondVerifyList::query();

        if (!empty($param['created_admin'])) {
            $count_query->leftJoin('admin_users as created_admin', 'created_admin.id', 'second_verify_list.created_admin_id');
        }
        if (!empty($param['username'])) {
            $count_query->leftJoin('users', 'users.id', 'second_verify_list.user_id');
        }

        if (!empty($param['verify_admin'])) {
            $count_query->leftJoin('admin_users as verify_admin', 'verify_admin.id', 'second_verify_list.verify_admin_id');
        }

        //查询条件
        $where = function ($query) use ($param) {
            //审核状态
            $query->where('status', $param['status']);
            $query->where('verify_type', 'changepass');

            if (!empty($param['created_admin'])) {
                $query->where('created_admin.username', $param['created_admin']);
            }
            //时间对比
            if (!empty($param['created_start_date'])) {
                $query->where('second_verify_list.created_at', '>=', $param['created_start_date']);
            }
            if (!empty($param['created_end_date'])) {
                $query->where('second_verify_list.created_at', '<=', $param['created_end_date']);
            }

            //用户
            if (!empty($param['username'])) {
                $query->where('users.username', $param['username']);
            }
            //审核员
            if (!empty($param['verify_admin'])) {
                $query->where('verify_admin.username', $param['verify_admin']);
            }
            //时间对比
            if (!empty($param['verify_start_date'])) {
                $query->where('second_verify_list.updated_at', '>=', $param['verify_start_date']);
            }
            if (!empty($param['verify_end_date'])) {
                $query->where('second_verify_list.updated_at', '<=', $param['verify_end_date']);
            }
        };

        //计算过滤后总数
        $data['recordsTotal'] =
        $data['recordsFiltered'] = $count_query->where($where)->count();

        $data['data'] = \Service\Models\SecondVerifyList::select([
            'second_verify_list.*',
            'users.username as username',
            'top.username as topname',
            'created_admin.username as created_admin_name',
            'verify_admin.username as verify_admin_name'
        ])
            ->leftJoin('admin_users as created_admin', 'created_admin.id', 'second_verify_list.created_admin_id')
            ->leftJoin('admin_users as verify_admin', 'verify_admin.id', 'second_verify_list.verify_admin_id')
            ->leftJoin('users', 'users.id', 'second_verify_list.user_id')
            ->leftJoin('users as top', 'users.top_id', 'top.id')
            ->where($where)
            ->skip($start)
            ->take($length)
            ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
            ->get();
        if (!$data['data']->isEmpty()) {
            foreach ($data['data'] as $k => $v) {
                $v['data'] = json_decode($v['data']);
                $data['data'][$k]['ordertype'] = $v['data']->flag == 'loginpass' ? '重设登陆密码' : '重设资金密码';
            }
        }
        return response()->json($data);
    }

    public function putVerify(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $status = (int)$request->get('status', 0);
        $verify = \Service\Models\SecondVerifyList::find($id);

        if (empty($verify)) {
            return redirect('/Verifychangepwd')->withErrors("找不到该纪录");
        }
        if ($verify->status != 0) {
            return redirect('/Verifychangepwd')->withErrors("该记录已经审核！");
        }
        $user = \Service\Models\User::find($verify->user_id);
        if (empty($user)) {
            $verify->delete();
            return redirect('/Verifychangepwd')->withErrors("用户不存在！");
        }
        if ($status == 1) {
            $postdata = json_decode($verify->data);
            if ($postdata->flag == 'loginpass') {
                $user->password = bcrypt($postdata->password);
            } else {
                $user->security_password = bcrypt($postdata->security_password);
            }
            // 判断修改密码是否强制登出
            if (get_config('op_change_password_to_force_logout') == 1) {
                $user->last_session = '';
            }
            $user->save();
            $verify->status = 1;
        } else {
            $verify->status = 2;
        }
        $verify->verify_admin_id = auth()->id();
        $verify->verify_at = date('Y-m-d H:i:s');
        if (!$verify->save()) {
            return redirect('/Verifychangepwd')->withErrors("审核失败！");
        }

        // 写入资金密码异动时间
        $data = json_decode($verify->data);
        if ($data->flag === 'securitypass' && $verify->status === 1) {
            \Service\API\User::setProfile($verify->user_id, 'change_security_password', Carbon::now());
        }

        return redirect('/Verifychangepwd\/')->withSuccess('审核成功');
    }
}
