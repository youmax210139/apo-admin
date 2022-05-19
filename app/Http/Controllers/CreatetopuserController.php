<?php

namespace App\Http\Controllers;

use Service\Models\User;
use Service\Models\UserPrizeLevel;
use App\Http\Requests\CreatetopuserRequest;
use Illuminate\Support\Facades\DB;
use Service\Models\UserRebates;

class CreatetopuserController extends Controller
{
    protected $fields = [];

    public function getIndex()
    {
        $user_group = \Service\Models\UserGroup::all();
        return view('user.createtopuser', [
            'user_group' => $user_group,
            'third_game_rebate_limit' => get_config('third_game_rebate_limit', 0.012),
            'rebates' => \Service\Models\UserRebates::getRebateConfig(),
        ]);
    }

    public function putIndex(CreatetopuserRequest $request)
    {
        $username = strtolower($request->get('username', ''));
        $password = $request->get('password', '');
        $usernick = $request->get('usernick', '');
        $user_group_id = (int)$request->get('user_group', 0);
        $user_prize_level = (int)$request->get('user_prize_level', 0);
        $rebates = $request->get('rebates', array());
        if (!$username || !$password || !$usernick || !$user_group_id || !$user_prize_level) {
            return redirect()->back()->withErrors("请填写完整数据！");
        }
        if (User::where('username', $username)->first()) {
            return redirect()->back()->withErrors("用户名已存在！");
        }
        DB::beginTransaction();
        $sub_recharge_status = (int)get_config('sub_recharge_status', 0);// 新开用户是否默认开启下级充值权限

        $user = new User;
        $user->username = $username;
        $user->password = bcrypt($password);
        $user->usernick = $usernick;
        $user->user_group_id = $user_group_id;
        $user->user_type_id = 1;

        if (in_array($sub_recharge_status, [1, 2])) {
            $user->sub_recharge_status = $sub_recharge_status;//新开用户是否默认开启下级充值权限
        }

        if ($user->save()) {
            //总代默认总代本身
            $user->top_id = $user->id;
            $user->save();
            //账户
            $user_fund = new \Service\Models\UserFund;
            $user_fund->user_id = $user->id;
            $user_fund->save();

            $data = [];
            foreach ($rebates as $k => $v) {
                $data[$k]['user_id'] = $user->id;
                $data[$k]['type'] = $k;
                $data[$k]['value'] = $v / 100;
            }

            $res = UserRebates::insert($data);
            if (!$res) {
                DB::rollBack();
                return redirect()->back()->withErrors("注册失败,写入第三方游戏返点失败！");
            }

            //奖级
            $prizelevel = new UserPrizeLevel;
            $prizelevel->user_id = $user->id;
            $prizelevel->level = $user_prize_level;
            $prizelevel->save();
            DB::commit();
            return redirect()->back()->withSuccess("总代【{$username}】开户成功");
        }
        DB::rollBack();
        return redirect()->back()->withErrors("开户失败！");
    }
}
