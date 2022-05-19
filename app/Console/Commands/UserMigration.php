<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\DailyWage\UserDailyWageContract;
use Service\Models\User;
use Service\Models\UserDailywageLine;
use Service\Models\UserMigrationRecords;
use Service\Models\UserPrizeLevel;
use Service\Models\UserRebates;
use Illuminate\Support\Facades\DB;

class UserMigration extends Command
{
    protected $signature = 'UserMigration:run {method} {need_mirage_agent} {new_parent}';

    protected $description = '转移用户到其它总代名下';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $method_name = $this->argument('method');
        if (method_exists($this, $method_name)) {
            $this->$method_name();
        } else {
            $this->info('找不到要执行的方法');
        }
    }

    /*
    需要倒下来以下几个数据表：
    users,user_rebates,
    user_prize_level,
    user_dailywage_line,
    user_dailywage_contract,
    user_dividend_contract,
    user_migration_records,

    UED平台转移用户：
    1，请运维先备份UED平台 users, user_rebates 两张数据表
    2，在UED平台分别执行以下命令（把内容输到文件中发我一份）：
    php artisan UserMigration:run exec 2bj16888 uedjf88
    php artisan UserMigration:run exec bj16888 uedjf88
    php artisan UserMigration:run exec kslm888 uedjf88
    */

    public function exec()
    {
        //获取需要转移的代理和新上级参数
        $need_mirage_agent = $this->argument('need_mirage_agent');
        $new_parent = $this->argument('new_parent');
        //获取到需要转移代理的所有直属下级
        $need_mirage_user_id = User::where('username', $need_mirage_agent)->first(['id']);
        if (!$need_mirage_user_id) {
            $this->info('需要转移的代理不存在！，username:' . $need_mirage_agent);
            return;
        }
        $need_mirage_users = User::where('parent_id', $need_mirage_user_id->id)->get();
        $this->info("开始进行用户转移操作:");
        if ($need_mirage_users) {
            //将需要转移代理的所有直属下级转移给新上级
            $this->info('需要转移的代理：' . $need_mirage_agent . ', new_parent:' . $new_parent);
            foreach ($need_mirage_users as $value) {
                if (!empty($value->username)) {
                    $this->user_migrate($value->username, $new_parent);
                }
            }
        }

        $this->info('转操作结束。');
        return;
    }

    public function user_migrate($username, $new_parent)
    {
        if (empty($username)) {
            $this->info('转移用户名 不能为空');
            return;
        }
        if (empty($new_parent)) {
            $this->info('新父级用户名 不能为空');
            return;
        }
        if ($username == $new_parent) {
            $this->info('转移用户 和 新父级用户 不能相同');
            return;
        }
        $userinfo = User::select(['id', 'top_id', 'parent_id', 'parent_tree', 'user_type_id', 'user_group_id'])->where('username', $username)->first();
        if (empty($userinfo)) {
            $this->info($username . ' 转移用户 不存在');
            return;
        }
        //用户是否测试
        if ($userinfo->user_group_id == 2) {
            $this->info($username . ' 是测试用户，禁止转移操作');
            return;
        }
        //用户是否试玩
        if ($userinfo->user_group_id == 3) {
            $this->info($username . ' 是试玩用户，禁止转移操作');
            return;
        }
        //用户是否总代
        $userinfo_parent_tree_array = json_decode($userinfo->parent_tree, true);
        if ($userinfo->user_type_id == 1 || $userinfo->top_id == $userinfo->id || $userinfo->parent_id == 0 || empty($userinfo_parent_tree_array)) {
            $this->info($username . ' 是总代，禁止转移操作');
            return;
        }
        $new_parentinfo = User::select(['id', 'top_id', 'parent_id', 'parent_tree', 'user_type_id', 'user_group_id'])->where('username', $new_parent)->first();
        if (empty($new_parentinfo)) {
            $this->info($new_parent . ' 新父级用户 不存在');
            return;
        }
        //新父级是否测试
        if ($new_parentinfo->user_group_id == 2) {
            $this->info($new_parent . ' 新父级是测试用户，禁止转移操作');
            return;
        }
        //新父级是否试玩
        if ($new_parentinfo->user_group_id == 3) {
            $this->info($new_parent . ' 新父级是试玩用户，禁止转移操作');
            return;
        }
        //新父级是否会员
        if ($new_parentinfo->user_type_id == 3) {
            $this->info($new_parent . ' 是会员，不能拥有下级，禁止转移操作');
            return;
        }
        if ($userinfo->parent_id == $new_parentinfo->id) {
            $this->info($username . '已经是 ' . $new_parent . ' 的直属下级，禁止转移操作');
            return;
        }
        if (in_array($userinfo->id, json_decode($new_parentinfo->parent_tree, true))) {
            $this->info($username . '是 ' . $new_parent . ' 的上级，禁止转移操作');
            return;
        }
        //日奖契约
        if (get_config('dailywage_available') == 1) {
            $this->info("进行日奖契约检查。");
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
                                    $this->info('未知类型 ' . $new_parent_dailywage_contrat->type . ' 的工资契约');
                                    return;
                                }
                                $contract_check_result = UserDailyWageContract::check($contract_data, 2, $new_parentinfo->id);
                                if (!$contract_check_result['status']) {
                                    $this->info('禁止转移操作。日奖检查失败,' . $contract_check_result['msg']);
                                    return;
                                }
                            } else {
                                $this->info($new_parent . '没有设定日奖契约，禁止转移操作');
                                return;
                            }
                        } else {
                            $this->info($username . '存在类型的工资契约，' . $new_parent . '不存在' . __('wage.line_type_' . $wage_line->type) . '类型的工资契约，无法转移');
                            return;
                        }
                    }
                }
            }
        }
        //彩票基础奖金
        $userinfo_prizelevel = UserPrizeLevel::select(['level'])->where('user_id', $userinfo->top_id)->first();
        $new_parentinfo_prizelevel = UserPrizeLevel::select(['level'])->where('user_id', $new_parentinfo->top_id)->first();
        if (empty($userinfo_prizelevel)) {
            $this->info($username . ' 不存在彩票基础奖金，禁止转移操作');
            return;
        }
        if (empty($new_parentinfo_prizelevel)) {
            $this->info($new_parent . ' 不存在彩票基础奖金，禁止转移操作');
            return;
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
            $this->info($username . ' 的' . implode('，', $rebate_tips) . ' 返点设置比 ' . $new_parent . ' 的大，禁止转移操作');
            return;
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

        //开始更新
        DB::beginTransaction();
        try {
            $result = DB::update("UPDATE users SET parent_tree=parent_tree::jsonb {$delete_zero_string} WHERE \"id\" IN ({$user_n_children_sql})");
            if (empty($result)) {
                DB::rollBack();
                $this->info('转移失败。code=1, username:' . $username);
                return;
            }
            $result = DB::update("UPDATE users SET parent_tree='{$up_parent_tree_pre_json}'::jsonb || parent_tree::jsonb,top_id='{$up_top_id}' WHERE \"id\" IN ({$user_n_children_sql})");
            if (empty($result)) {
                DB::rollBack();
                $this->info('转移失败。code=2, username:' . $username);
                return;
            }
            $result = DB::update("UPDATE users SET parent_id='{$up_parent_id}' WHERE \"id\" = '{$userinfo->id}'");
            if (empty($result)) {
                DB::rollBack();
                $this->info('转移失败。code=3, username:' . $username);
                return;
            }
            if ($lottery_rebate_diff != 0) {
                $result = DB::update("UPDATE user_rebates SET \"value\"=\"value\" + {$lottery_rebate_diff} WHERE user_id IN ({$user_n_children_sql}) AND \"type\"='lottery'");
                if (empty($result)) {
                    DB::rollBack();
                    $this->info('转移失败。code=4,username:' . $username);
                    return;
                }
                //把彩票返点为负数的改为0
                DB::update("UPDATE user_rebates SET \"value\"=0 WHERE user_id IN ({$user_n_children_sql}) AND \"type\"='lottery' AND \"value\" < 0 ");
            }

            //写记录
            $result = UserMigrationRecords::create([
                'username' => $username,
                'old_parent_username' => $old_parentinfo->username,
                'new_parent_username' => $new_parent,
                'admin_username' => 'system',
            ]);

            if (empty($result)) {
                DB::rollBack();
                $this->info('转移失败。code=6, username:' . $username);
                return;
            }
            DB::commit();
            $this->info('用户转移成功， username:' . $username);
            return;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info($e->getMessage());
            return;
        }
    }
}
