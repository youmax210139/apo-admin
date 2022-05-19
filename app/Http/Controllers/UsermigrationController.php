<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\API\DailyWage\UserDailyWageContract;
use Service\Models\User;
use Service\Models\UserDailywageLine;
use Service\Models\UserRebates;
use Service\Models\UserPrizeLevel;
use Service\Models\UserMigrationRecords;

class UsermigrationController extends Controller
{
    public function getIndex()
    {
        return view('user-migration.index');
    }

    public function postMigrate(Request $request)
    {
        $username = trim($request->get('username'));
        $new_parent = trim($request->get('new_parent'));
        if (empty($username)) {
            return redirect("/usermigration/")->withErrors('转移用户名 不能为空');
        }
        if (empty($new_parent)) {
            return redirect("/usermigration/")->withErrors('新父级用户名 不能为空');
        }
        if ($username == $new_parent) {
            return redirect("/usermigration/")->withErrors('转移用户 和 新父级用户 不能相同');
        }

        $userinfo = User::select(['id', 'top_id', 'parent_id', 'parent_tree', 'user_type_id', 'user_group_id'])->where('username', $username)->first();
        if (empty($userinfo)) {
            return redirect("/usermigration/")->withErrors($username . ' 转移用户 不存在');
        }
        //用户是否测试
        if ($userinfo->user_group_id == 2) {
            return redirect("/usermigration/")->withErrors($username . ' 是测试用户，禁止转移操作');
        }

        //用户是否试玩
        if ($userinfo->user_group_id == 3) {
            return redirect("/usermigration/")->withErrors($username . ' 是试玩用户，禁止转移操作');
        }

        //用户是否总代
        $userinfo_parent_tree_array = json_decode($userinfo->parent_tree, true);
        if ($userinfo->user_type_id == 1 || $userinfo->top_id == $userinfo->id || $userinfo->parent_id == 0 || empty($userinfo_parent_tree_array)) {
            return redirect("/usermigration/")->withErrors($username . ' 是总代，禁止转移操作');
        }

        $new_parentinfo = User::select(['id', 'top_id', 'parent_id', 'parent_tree', 'user_type_id', 'user_group_id'])->where('username', $new_parent)->first();
        if (empty($new_parentinfo)) {
            return redirect("/usermigration/")->withErrors($new_parent . ' 新父级用户 不存在');
        }
        //新父级是否测试
        if ($new_parentinfo->user_group_id == 2) {
            return redirect("/usermigration/")->withErrors($new_parent . ' 是测试用户，禁止转移操作');
        }

        //新父级是否试玩
        if ($new_parentinfo->user_group_id == 3) {
            return redirect("/usermigration/")->withErrors($new_parent . ' 是试玩用户，禁止转移操作');
        }

        //新父级是否会员
        if ($new_parentinfo->user_type_id == 3) {
            return redirect("/usermigration/")->withErrors($new_parent . ' 是会员，不能拥有下级，禁止转移操作');
        }

        if ($userinfo->parent_id == $new_parentinfo->id) {
            return redirect("/usermigration/")->withErrors($username . '已经是 ' . $new_parent . ' 的直属下级，禁止转移操作');
        }
        if (in_array($userinfo->id, json_decode($new_parentinfo->parent_tree, true))) {
            return redirect("/usermigration/")->withErrors($username . '是 ' . $new_parent . ' 的上级，禁止转移操作');
        }

        //日奖契约
        if (get_config('dailywage_available') == 1) {
            $wage_lines = UserDailywageLine::where('top_user_id', $userinfo->top_id)->orderBy('created_at', 'asc')->get();
            if ($wage_lines->isNotEmpty()) {
                foreach ($wage_lines as $wage_line) {
                    $user_dailywage_contrat = UserDailyWageContract::getUserContract($userinfo->id, '', $wage_line->type);
                    if (isset($user_dailywage_contrat->content)) {
                        //获取新上级的工资契约
                        if ($new_parent_dailywage_contrat = UserDailyWageContract::getUserContract($new_parentinfo->id, '', $wage_line->type)) {
                            if (isset($new_parent_dailywage_contrat->content)) {
                                //判断下级工资契约与上级工资契约是否存在冲突
                                if (in_array($new_parent_dailywage_contrat->type, [1, 2, 3, 4, 5, 6])) {
                                    $contract_data = ['user_id' => $userinfo->id];
                                    //判断哪些字段是存在的
                                    $show_conditions = [
                                        'bet' => 0, 'active' => 0, 'profit' => 0, 'rate' => 0, 'win_rate' => 0, 'loss_rate' => 0,
                                    ];
                                    foreach ($user_dailywage_contrat->content as $content_condition) {
                                        foreach ($show_conditions as $condition_key => $show_enable) {
                                            if (array_has($content_condition, $condition_key)) {
                                                $show_conditions[$condition_key] = 1;
                                            }
                                        }
                                    }
                                    //对应的赋值过去
                                    foreach ($show_conditions as $condition_key => $show_enable) {
                                        if ($show_enable == 1) {
                                            $contract_data[$condition_key] = array_pluck($user_dailywage_contrat->content, $condition_key);
                                        }
                                    }
                                } else {
                                    return redirect("/usermigration/")->withErrors('未知类型 ' . $new_parent_dailywage_contrat->type . ' 的工资契约');
                                }
                                $contract_check_result = UserDailyWageContract::check($contract_data, 2, $new_parentinfo->id);
                                if (!$contract_check_result['status']) {
                                    return redirect("/usermigration/")->withErrors('禁止转移操作。日奖检查失败,' . $contract_check_result['msg']);
                                }
                            } else {
                                return redirect("/usermigration/")->withErrors($new_parent . '没有设定日奖契约，禁止转移操作');
                            }
                        } else {
                            return redirect("/usermigration/")->withErrors($username . '存在类型的工资契约，' . $new_parent . '不存在' . __('wage.line_type_' . $wage_line->type) . '类型的工资契约，无法转移');
                        }
                    }
                }
            }
        }

        //彩票基础奖金
        $userinfo_prizelevel = UserPrizeLevel::select(['level'])->where('user_id', $userinfo->top_id)->first();
        $new_parentinfo_prizelevel = UserPrizeLevel::select(['level'])->where('user_id', $new_parentinfo->top_id)->first();
        if (empty($userinfo_prizelevel)) {
            return redirect("/usermigration/")->withErrors($username . ' 不存在彩票基础奖金，禁止转移操作');
        }
        if (empty($new_parentinfo_prizelevel)) {
            return redirect("/usermigration/")->withErrors($new_parent . ' 不存在彩票基础奖金，禁止转移操作');
        }
        //不同基础奖金的彩票返点差
        $lottery_rebate_diff = 0.0;
        if ($userinfo_prizelevel != $new_parentinfo_prizelevel) {
            $lottery_rebate_diff = ($userinfo_prizelevel->level - $new_parentinfo_prizelevel->level) / 2000;
        }

        //返点比较
        $user_rebates = UserRebates::select(['type', 'value'])->where('user_id', $userinfo->id)->get();
        $new_parent_rebates = UserRebates::select(['type', 'value'])->where('user_id', $new_parentinfo->id)->get();
        $user_rebates_type2value = [];
        foreach ($user_rebates as $row) {
            if ($row->type == 'lottery') {
                $user_rebates_type2value[$row->type] = $row->value + $lottery_rebate_diff;
            } else {
                $user_rebates_type2value[$row->type] = $row->value;
            }
        }
        unset($user_rebates);
        $new_parent_rebates_type2value = [];
        foreach ($new_parent_rebates as $row) {
            $new_parent_rebates_type2value[$row->type] = $row->value;
        }
        unset($new_parent_rebates);

        $rebate_tips = [];
        foreach ($user_rebates_type2value as $type => $value) {
            $value2 = isset($new_parent_rebates_type2value[$type]) ? $new_parent_rebates_type2value[$type] : 0;
            if ($value > $value2) {
                $rebate_tips[] = "{$type}:{$value} > {$value2}";
            }
        }
        if ($rebate_tips) {
            return redirect("/usermigration/")->withErrors($username . ' 的' . implode('，', $rebate_tips) . ' 返点设置比 ' . $new_parent . ' 的大，禁止转移操作');
        }

        $up_parent_tree_pre_array = json_decode($new_parentinfo->parent_tree, true);
        array_push($up_parent_tree_pre_array, $new_parentinfo->id);
        $up_parent_tree_pre_json = json_encode($up_parent_tree_pre_array);
        $up_top_id = $new_parentinfo->top_id;
        $up_parent_id = $new_parentinfo->id;

        $old_parentinfo = User::select(['username'])->where('id', $userinfo->parent_id)->first();

        $delete_zero_string = '';
        foreach ($userinfo_parent_tree_array as $key => $row) {
            $delete_zero_string .= ' - 0';
        }
        $user_n_children_sql = "SELECT \"id\" FROM users WHERE parent_tree @> '{$userinfo->id}' OR \"id\"='{$userinfo->id}' ";

        //设置脚本超时时间
        set_time_limit(300);

        //开始更新
        \DB::beginTransaction();
        $result = \DB::update("UPDATE users SET parent_tree=parent_tree::jsonb {$delete_zero_string} WHERE \"id\" IN ({$user_n_children_sql})");
        if (empty($result)) {
            \DB::rollBack();
            return redirect("/usermigration/")->withErrors('转移失败，请联系技术人员。code=1');
        }
        $result = \DB::update("UPDATE users SET parent_tree='{$up_parent_tree_pre_json}'::jsonb || parent_tree::jsonb,top_id='{$up_top_id}' WHERE \"id\" IN ({$user_n_children_sql})");
        if (empty($result)) {
            \DB::rollBack();
            return redirect("/usermigration/")->withErrors('转移失败，请联系技术人员。code=2');
        }
        $result = \DB::update("UPDATE users SET parent_id='{$up_parent_id}' WHERE \"id\" = '{$userinfo->id}'");
        if (empty($result)) {
            \DB::rollBack();
            return redirect("/usermigration/")->withErrors('转移失败，请联系技术人员。code=3');
        }
        if ($lottery_rebate_diff != 0) {
            $result = \DB::update("UPDATE user_rebates SET \"value\"=\"value\" + {$lottery_rebate_diff} WHERE user_id IN ({$user_n_children_sql}) AND \"type\"='lottery'");
            if (empty($result)) {
                \DB::rollBack();
                return redirect("/usermigration/")->withErrors('转移失败，请联系技术人员。code=4');
            }
            //把彩票返点为负数的改为0
            \DB::update("UPDATE user_rebates SET \"value\"=0 WHERE user_id IN ({$user_n_children_sql}) AND \"type\"='lottery' AND \"value\" < 0 ");
        }

        //写记录
        $result = UserMigrationRecords::create([
            'username' => $username,
            'old_parent_username' => $old_parentinfo->username,
            'new_parent_username' => $new_parent,
            'admin_username' => auth()->user()->username,
        ]);
        if (empty($result)) {
            \DB::rollBack();
            return redirect("/usermigration/")->withErrors('转移失败，请联系技术人员。code=6');
        }
        \DB::commit();

        return redirect("/usermigration/records")->withSuccess('用户转移成功');
    }

    public function getRecords()
    {
        $data = [];
        $data['rows'] = UserMigrationRecords::orderBy('created_at', 'desc')->get();
        return view('user-migration.records', $data);
    }
}
