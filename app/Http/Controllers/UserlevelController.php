<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLevelRequest;
use Illuminate\Http\Request;
use Service\Models\UserLevel;

class UserlevelController extends Controller
{
    protected $fields = [
        'name' => '',
        'register_start_time' => '',
        'register_end_time' => '',
        'deposit_times' => '',
        'deposit_count_amount' => '',
        'deposit_max_amount' => '',
        'withdrawal_times' => '',
        'withdrawal_count_amount' => '',
        'expense_count_amount' => '',
        'remark' => '',
        'status' => 1,
    ];

    public function getIndex()
    {
        $data = [];
        $data['rows'] = UserLevel::select([
            'id',
            'name',
            'register_start_time',
            'register_end_time',
            'deposit_times',
            'deposit_count_amount',
            'deposit_max_amount',
            'withdrawal_times',
            'withdrawal_count_amount',
            'expense_count_amount',
            'remark',
            'status',
        ])
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
        return view('userlevel.index', $data);
    }

    public function getCreate()
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        return view("userlevel.create", $data);
    }

    public function postCreate(UserLevelRequest $request)
    {
        $user_level = new UserLevel();
        foreach ($this->fields as $key => $val) {
            $user_level->$key = $request->get($key, $val);
        }

        if ($user_level->save()) {
            return redirect("/userlevel/")->withSuccess('添加成功');
        } else {
            return redirect() - back()->withErrors('添加失败');
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $user_level = UserLevel::find($id);

        if (empty($user_level)) {
            return redirect("/userlevel/")->withErrors("找不到这个分层");
        }

        return view("userlevel.edit", $user_level);
    }

    public function putEdit(UserLevelRequest $request)
    {
        $id = (int)$request->get('id');

        $user_level = UserLevel::find($id);

        if (empty($user_level)) {
            return redirect("/userlevel/")->withErrors("找不到这个分层");
        }

        foreach ($this->fields as $key => $val) {
            $user_level->$key = $request->get($key, $val);
        }

        if ($user_level->save()) {
            return redirect("/userlevel/")->withSuccess("更新成功");
        } else {
            return redirect("/userlevel/")->withErrors("对不起，更新失败");
        }
    }
}
