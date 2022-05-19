<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Service\Models\Notice;

class NoticeController extends Controller
{
    protected $fields = [
        'subject' => '',
        'published_at' => '',
        'end_at' => '',
        'content' => '',
        'sort' => 0,
        'is_top' => 0
    ];

    public function getIndex()
    {
        //如果失效或者记录删除更新弹出
        $alert_notice = json_decode(Cache::store('redis')->get('alert_notice'));
        if ($alert_notice) {
            $notice = Notice::find($alert_notice->id);
            if (!$notice || !$notice->is_alert || !$notice->is_show) {
                Cache::store('redis')->forget('alert_notice');
            }
        }
        return view('notice.index');
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
            $data['recordsTotal'] = \Service\Models\Notice::count();
            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = \Service\Models\Notice::where(function ($query) use ($search) {
                    $query->where('subject', 'LIKE', '%' . $search['value'] . '%');
                })->count();

                $data['data'] = \Service\Models\Notice::where(function ($query) use ($search) {
                    $query->where('subject', 'LIKE', '%' . $search['value'] . '%');
                })->leftJoin('admin_users', 'notices.created_admin_user_id', 'admin_users.id')
                    ->leftJoin('admin_users AS admin_users1', 'notices.verified_admin_user_id', 'admin_users1.id')->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->orderBy('id', 'DESC')
                    ->skip($start)->take($length)
                    ->get(['notices.*',
                        'admin_users.username AS created_admin',
                        'admin_users1.username AS verified_admin'
                    ]);
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = \Service\Models\Notice::leftJoin('admin_users', 'notices.created_admin_user_id', 'admin_users.id')
                    ->leftJoin('admin_users AS admin_users1', 'notices.verified_admin_user_id', 'admin_users1.id')
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->orderBy('id', 'DESC')
                    ->skip($start)->take($length)
                    ->get(['notices.*',
                        'admin_users.username AS created_admin',
                        'admin_users1.username AS verified_admin'
                    ]);
            }

            foreach ($data['data'] as &$notice) {
                if (mb_strlen($notice->subject) > 12) {
                    $notice->subject = mb_substr($notice->subject, 0, 100) . '...';
                }
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

        $data['published_at'] = now();
        return view('notice.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(\App\Http\Requests\NoticeCreateRequest $request)
    {
        $notice = new \Service\Models\Notice();
        foreach (array_keys($this->fields) as $field) {
            $notice->$field = $request->get($field);
        }

        $notice->created_admin_user_id = Auth()->id();
        $notice->save();

        return redirect('/notice\/')->withSuccess('添加成功');
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
        $notice = \Service\Models\Notice::find($id);
        if (!$notice) {
            return redirect('/notice\/')->withErrors("找不到该公告");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $notice->$field);
        }
        $data['id'] = (int)$id;
        return view('notice.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PermissionUpdateRequest|Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEdit(\App\Http\Requests\NoticeUpdateRequest $request)
    {
        $notice = \Service\Models\Notice::find((int)$request->get('id', 0));
        if (!$notice) {
            return redirect('/notice\/')->withErrors("找不到该公告");
        }
        foreach (array_keys($this->fields) as $field) {
            $notice->$field = $request->get($field);
        }
        $notice->verified_admin_user_id = 0;
        $notice->is_show = 0;
        $notice->is_alert = 0;
        $notice->save();

        return redirect('/notice\/')->withSuccess('修改成功');
    }

    /**
     * 审核
     *
     * @param Request $request
     * @return mixed
     */
    public function putVerify(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $notice = \Service\Models\Notice::find($id);
        if (!$notice) {
            return redirect('/notice\/')->withErrors("找不到该公告");
        }

        if ($notice->verified_admin_user_id) {
            $notice->verified_admin_user_id = 0;
            $notice->is_show = 0;
            $tips = "未审核";
        } else {
            $notice->verified_admin_user_id = Auth()->id();
            $notice->is_show = 1;
            $notice->verified_at = now();
            $tips = "已审核";
        }


        if ($notice->save()) {
            return redirect('/notice\/')->withSuccess("公告设置为【{$tips}】");
        } else {
            return redirect('/notice\/')->withErrors("设置失败");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putShow(Request $request)
    {
        $notice = \Service\Models\Notice::find((int)$request->get('id', 0));

        if ($notice) {
            if ($notice->verified_admin_user_id == 0 && $notice->is_show == 0) {
                return redirect()->back()->withErrors("无法显示未审核的公告");
            }

            $notice->is_show = $notice->is_show == 0;
            $tips = ($notice->is_show == 1) ? '开启' : '关闭';
            if ($notice->is_show == 0) {
                $notice->is_alert = 0;
            }

            $notice->save();

            return redirect()->back()->withSuccess("{$tips}成功");
        }

        return redirect()->back()->withErrors("找不到该公告");
    }

    /**
     * 弹出公告
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putAlert(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $notice = \Service\Models\Notice::find($id);

        if ($notice) {
            if ($notice->verified_admin_user_id == 0 || $notice->is_show == 0) {
                return redirect()->back()->withErrors("无法设置未审核或隐藏状态的公告");
            }

            $notice->is_alert = !$notice->is_alert;
            $tips = ($notice->is_alert) ? '开启弹出' : '关闭弹出';
            $notice->save();
            //写入REDIS
            $notice = Notice::where('is_alert', true)->where('verified_admin_user_id', '>', 0)->where('id', $id)->first();

            if ($notice) {
                event(new \Service\Events\Notice($notice->id, $notice->subject, $notice->content, 0, 1));
                Cache::store('redis')
                    ->forever(
                        'alert_notice',
                        json_encode([
                            'id' => $notice->id,
                            'subject' => $notice->subject,
                            'content' => $notice->content])
                    );
            }
            return redirect()->back()->withSuccess("{$tips}成功");
        }

        return redirect()->back()->withErrors("找不到该公告");
    }

    /**
     * 删除公告
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function putDel(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $notice = \Service\Models\Notice::find($id);

        if ($notice) {
            \Service\Models\Notice::destroy($id);
            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("找不到该公告");
    }
}
