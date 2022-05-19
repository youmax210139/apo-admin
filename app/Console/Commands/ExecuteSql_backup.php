<?php

namespace App\Console\Commands;

class ExecuteSql_backup extends Command
{
    /*
     * 说明：此文件为ExecuteSql.php的备份文件，所以此文件里的方法不允许其他地方调用。
     * 不要修改此文件了
     */
    private function updateConfigDescription()
    {
        $type = get_config('dividend_type_ident');
        $config = Config::where('key', 'dividend_auto_send_regular')->first();
        if ($config) {
            $config->description = '0-需要上级前台审核后才能发放”，1-不需要审核，计算后自动发放，后台分红审核包括“上级审核”状态。默认『0』';
            $config->save();
            $this->info($type . '配置 ' . $config->key . ' 的描述更新成功');
        } else {
            $this->info($type . '不存在当前配置');
        }
    }

    private function update_dividens_users()
    {
        $type = get_config('dividend_type_ident');
        if (in_array($type, ['Feiyu', 'Ued'])) {
            $rows = ModelContractDividend::where('start_time', '<', '2020-08-01 00:00:00')
                ->whereIn('status', [4])
                ->update(['status' => 5]);
            $this->info($type . '更新完成,纪录数' . $rows);
        }
    }

    // 新增盈亏报表是否计算前期分红配置
    private function add_dividend_last_amount_to_report()
    {
        DB::beginTransaction();
        $type = get_config('dividend_type_ident');
        try {
            $last_amount_to_report = Config::where('key', 'dividend_last_amount_to_report')->first();

            if ($last_amount_to_report) {
                $this->warn($type . ' 是否计算前期分红配置已经存在！[ID ' . $last_amount_to_report->id . ']');
            } else {
                $dividend_config = Config::where('key', 'dividend_config')->first();
                Config::insert([
                    'parent_id' => $dividend_config->id,
                    'title' => '盈亏报表是否计算前期分红',
                    'key' => 'dividend_last_amount_to_report',
                    'value' => 0,
                    'description' => '0：关闭 1：启用'
                ]);
            }

            DB::commit();
            $this->info($type . ' 执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info($type . ' 执行失败' . PHP_EOL . $e->getMessage());
        }
    }

    // 备份菲娱3 hourly_wage
    private function hourly_wage_feiyu3_backup()
    {
        DB::beginTransaction();
        if (get_config('dividend_type_ident') == 'Feiyu3') {
            try {
                // 备份数据表
                $back_table_name = "hourly_wage_back_" . date('Ymd');
                DB::statement('DROP TABLE IF EXISTS  ' . $back_table_name . ';');
                DB::statement('CREATE TABLE  ' . $back_table_name . ' as TABLE hourly_wage;');
                DB::commit();
                $this->info('挂单工资表备份成功');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->info('挂单工资表备份失败');
                $this->info($e->getMessage());
            }
        } else {
            $this->info('备份失败');
        }
    }

    private function count_dividens_users()
    {
        $type = get_config('dividend_type_ident');
        if (in_array($type, ['Feiyu', 'Ued'])) {
            $users = ModelUserProfile::LeftJoin('users', 'users.id', '=', 'user_profile.user_id')
                ->where('user_profile.attribute', '=', 'dividend_lock')
                ->where('user_profile.value', '=', '1')
                ->where('users.user_group_id', '=', '1')//只统计注册用户
                ->select(['user_profile.user_id', 'users.username'])
                ->get();
            if ($users->isEmpty()) {
                $this->info($type . ' 没有用户被分红锁定');
            } else {
                $this->info($type . ' 分红锁定用户数' . $users->count() . '条');
                $usernames = [];
                foreach ($users as $user) {
                    $usernames[$user->user_id] = $user->username;
                }
                $this->info(implode(',', $usernames));
                $user_ids = array_keys($usernames);
                if (count($user_ids) > 0) {
                    $rows = ModelContractDividend::LeftJoin('users', 'users.id', 'contract_dividends.user_id')
                        ->where('contract_dividends.start_time', '<=', '2020-08-01 00:00:00')
                        ->whereIn('contract_dividends.status', [4])
                        ->whereIn('users.parent_id', $user_ids)
                        ->orderBy('contract_dividends.user_id', 'asc')->orderBy('contract_dividends.id', 'desc')
                        ->select([
                            'contract_dividends.id', 'users.username', 'contract_dividends.type',
                            'contract_dividends.start_time', 'contract_dividends.end_time', 'contract_dividends.amount',
                            'contract_dividends.send_type', 'contract_dividends.period', 'contract_dividends.status'
                        ])->get();
                    if ($rows->isEmpty()) {
                        $this->info('没有附合要求的分红纪录');
                    } else {
                        $this->info('附合要求的分红纪录共' . $rows->count() . '条');
                        foreach ($rows as $row) {
                            $this->info(var_export($row->toArray()));
                            $row->status = 5;
                            $row->save();
                            $this->info('更新完成');
                        }
                    }
                }

            }
        } else {
            $this->info('请在指定平台运行');
        }
    }

    // 日分红修正状态重新执行 光辉 (重新计算特定一笔记录)
    private function contract_dividends_change_status()
    {
        $type = get_config('dividend_type_ident');

        if (in_array($type, ['Guanghui'])) {
            // $datas = ContractDividend::where('status', 6)
            //             ->where('start_time', '2020-10-05 00:00:00')
            //             ->where('end_time', '2020-10-05 23:59:59')
            //             ->where('period', 1)
            //             ->update(['status' => 0]);
            $datas = ContractDividend::where('id', 1488)
                ->delete();

            $this->info($type . ' 修正分红状态共' . $datas . '条，更新成功');
        } else {
            $this->info($type . '更新失败');
        }
    }

    /**
     * 修改分红表 contract_dividends 唯一值，仅限菲娱 九城执行
     */
    private function contract_dividends_add_unique_feiyu_jiucheng()
    {
        DB::beginTransaction();
        try {
            // 备份数据表
            $back_table_name = "contract_dividends_back_" . date('Ymd');
            DB::statement('CREATE TABLE  ' . $back_table_name . ' as TABLE contract_dividends;');
            // 菲娱
            if (get_config('dividend_type_ident') == 'Feiyu') {
                Schema::table('contract_dividends', function (Blueprint $table) {
                    $table->dropUnique('contract_dividends_user_id_start_time_end_time_key');
                    $table->unique(['user_id', 'start_time', 'end_time', 'period']);
                });
            }
            // 九城
            if (get_config('dividend_type_ident') == 'Jiucheng') {
                Schema::table('contract_dividends', function (Blueprint $table) {
                    $table->dropUnique('report_contract_dividends_user_id_start_time_end_time_key');
                    $table->unique(['user_id', 'start_time', 'end_time', 'period']);
                });
            }

            DB::commit();
            $this->info(get_config('dividend_type_ident') . ' 分红表唯一值栏位调整成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('分红表唯一值栏位调整失败');
            $this->info($e->getMessage());
        }
    }

    /**
     * 修改分红表 contract_dividends 唯一值，全平台执行不包含菲娱 九城
     */
    private function contract_dividends_add_unique()
    {
        DB::beginTransaction();
        try {
            Schema::table('contract_dividends', function (Blueprint $table) {
                $table->dropUnique(['user_id', 'start_time', 'end_time']);
                $table->unique(['user_id', 'start_time', 'end_time', 'period']);
            });

            DB::commit();
            $this->info('分红表唯一值栏位调整成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('分红表唯一值栏位调整失败');
            $this->info($e->getMessage());
        }
    }

    /**
     * 添加私返
     */
    private function private_return_add_table()
    {
        DB::beginTransaction();
        try {
            $this->info('开始添加私返相关数据表');
            if (\Schema::hasTable('user_private_return_contract')) {
                $this->warn('表 user_private_return_contract 已存在');
            } else {
                (new \CreateTableUserPrivateReturnContract())->up();
                $this->info('表 user_private_return_contract 添加成功');
            }
            if (\Schema::hasTable('private_return')) {
                $this->warn('表 private_return 已存在');
            } else {
                (new \CreateTablePrivateReturn())->up();
                $this->info('表 private_return 添加成功');
            }
            if (\Schema::hasTable('private_return_jobs')) {
                $this->warn('表 private_return_jobs 已存在');
            } else {
                (new \CreateTablePrivateReturnJobs())->up();
                $this->info('表 private_return_jobs 添加成功');
            }
            //添加帐变类型
            $this->info('开始添加私返相关帐变类型');
            if ($order_type_sfff = DB::table('order_type')->where('ident', 'SFFF')->first()) {
                $this->warn('帐变类型 私返发放 SFFF 已经存在！[ID ' . $order_type_sfff->id . ' ]');
            } else {
                $order_type_id = DB::table('order_type')->insertGetId([
                    'name' => '私返发放',
                    'ident' => 'SFFF',
                    'display' => 1,
                    'operation' => 2,
                    'hold_operation' => 0,
                    'category' => 2,
                    'description' => '私返发放[-]',
                ]);
                $this->info('帐变类型 私返发放 SFFF 添加成功 id => ' . $order_type_id);
            }
            if ($order_type_sflq = DB::table('order_type')->where('ident', 'SFLQ')->first()) {
                $this->warn('帐变类型 私返领取 SFLQ 已经存在！[ID ' . $order_type_sflq->id . ' ]');
            } else {
                $order_type_id = DB::table('order_type')->insertGetId([
                    'name' => '私返领取',
                    'ident' => 'SFLQ',
                    'display' => 1,
                    'operation' => 1,
                    'hold_operation' => 0,
                    'category' => 2,
                    'description' => '私返领取[+]',
                ]);
                $this->info('帐变类型 私返领取 SFLQ 添加成功 id => ' . $order_type_id);
            }
            //添加相关配置
            if ($private_return_config = DB::table('config')->where('key', 'private_return')->first()) {
                $this->warn('私返配置已经存在！[ID ' . $private_return_config->id . ' ]');
            } else {
                //私返配置
                $private_return_config_parent_id = DB::table('config')->insertGetId([
                    'parent_id' => 0,
                    'title' => '私返配置',
                    'key' => 'private_return',
                    'value' => '#',
                    'description' => '',
                ]);
                DB::table('config')->insert([[
                    'parent_id' => $private_return_config_parent_id,
                    'title' => '是否开启',
                    'key' => 'private_return_enabled',
                    'value' => '0',
                    'description' => '私返功能开关，1：开启 0：关闭',
                ], [
                    'parent_id' => $private_return_config_parent_id,
                    'title' => '是否开启计算',
                    'key' => 'private_return_calculate',
                    'value' => '0',
                    'description' => '私返计算开关，1：开启 0：关闭',
                ], [
                    'parent_id' => $private_return_config_parent_id,
                    'title' => '是否开启发放',
                    'key' => 'private_return_send',
                    'value' => '0',
                    'description' => '私返发放开关，1：开启 0：关闭',
                ], [
                    'parent_id' => $private_return_config_parent_id,
                    'title' => '私返路径标识',
                    'key' => 'private_return_type_ident',
                    'value' => '',
                    'description' => '标记当私返使用的类所在目录，例如Haicai，代表日工资使用Service\PrivateReturn\Haicai这个目录的类',
                ], [
                    'parent_id' => $private_return_config_parent_id,
                    'title' => '私返比例最大值',
                    'key' => 'private_return_rate_max',
                    'value' => '0.05',
                    'description' => '填入数字，1代表1%，默认：0.05%。限制私返契约最大比例上限',
                ], [
                    'parent_id' => $private_return_config_parent_id,
                    'title' => '私返比例步长',
                    'key' => 'private_return_rate_step',
                    'value' => '0.01',
                    'description' => '填入数字，0.1代表契约比例可调整最小间距为0.1%，默认：0.01%',
                ], [
                    'parent_id' => $private_return_config_parent_id,
                    'title' => '私返基数取整单位',
                    'key' => 'private_return_rank_unit',
                    'value' => '10000',
                    'description' => '填入数字，10000代表万元取整，默认：10000。万元取整时，团队销量为19800时计算为10000',
                ], [
                    'parent_id' => $private_return_config_parent_id,
                    'title' => '私返是否扣减团队给上级的返点',
                    'key' => 'private_return_sub_uplevel_rebate',
                    'value' => '0',
                    'description' => '0:不扣除，1:扣除，默认：0。私返团队销量扣除自身及下级提供的返点',
                ], [
                    'parent_id' => $private_return_config_parent_id,
                    'title' => '前一日有效用户最低销售额',
                    'key' => 'private_return_active_sale_min',
                    'value' => '1000',
                    'description' => '至少达到所填数值的销量才能算1个有效人数。',
                ]]);
                //添加私返计划任务配置
                if ($crontab_config = DB::table('config')->where('key', 'crontab_config')->first()) {
                    DB::table('config')->insert([
                        [
                            'parent_id' => $crontab_config->id,
                            'title' => '日私返计算',
                            'key' => 'crontab_private_return_calculate_daily',
                            'value' => '0',
                            'description' => '1：计划任务启用 0：计划任务关闭',
                        ],
                        [
                            'parent_id' => $crontab_config->id,
                            'title' => '小时私返计算',
                            'key' => 'crontab_private_return_calculate_hourly',
                            'value' => '0',
                            'description' => '1：计划任务启用 0：计划任务关闭',
                        ],
                        [
                            'parent_id' => $crontab_config->id,
                            'title' => '私返发放',
                            'key' => 'crontab_private_return_send',
                            'value' => '0',
                            'description' => '1：计划任务启用 0：计划任务关闭',
                        ]
                    ]);
                }
                $this->info('私返配置添加完成');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('回滚，私返数据添加失败');
            $this->error($e->getMessage());
        }
    }

    private function private_return_add_detail()
    {
        $parent_id = DB::table('admin_role_permissions')->where('name', '报表管理')->where('parent_id', 0)->value('id');

        //后台菜单
        if ($row = DB::table('admin_role_permissions')
            ->where('rule', 'privatereturn/detail')
            ->first()) {
            $this->info('admin_role_permissions 表中 ' . $row->rule . ' 纪录已经存在，ID为' . $row->id);
        } else {
            $permission_id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => $parent_id,
                'rule' => 'privatereturn/detail',
                'name' => '私返明细',
            ]);
            $this->info('admin_role_permissions 表中 privatereturn/detail 纪录插入成功，ID为' . $permission_id);
        }
    }

    private function issue_wage_queue_add_unique()
    {
        DB::beginTransaction();
        try {
            Schema::table('issue_wage_queue', function (Blueprint $table) {
                $table->dropIndex(['type', 'lottery_id']);
                $table->unique(['type', 'lottery_id']);
            });
            Schema::table('issue_wage', function (Blueprint $table) {
                $table->index(['type', 'lottery_id', 'issue', 'source_user_id']);
            });

            DB::commit();
            $this->info('奖期工资队列索引调整成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('奖期工资队列索引调整失败');
            $this->info($e->getMessage());
        }
    }

    /**
     * 添加报表状态字段与生成报表需要的索引
     */
    private function add_column_index_issue_wage()
    {
        DB::beginTransaction();
        try {
            //如果工资线没有ID字段
            if (Schema::hasColumn('issue_wage', 'report_status')) {
                $this->info('奖期工资表 issue_wage 字段 report_status 已经存在，不需要重复添加');
                return '';
            }
            //工资线添加新字段与唯一键
            Schema::table('issue_wage', function (Blueprint $table) {
                $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
                $table->index(['status', 'report_status']);
            });
            DB::commit();
            $this->info('奖期工资表 issue_wage 字段 report_status 添加成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('奖期工资表字段与索引添加失败');
            $this->info($e->getMessage());
        }
    }

    /**
     * 添加奖期工资表
     */
    private function create_table_issue_wage()
    {
        DB::beginTransaction();
        try {
            //添加表 issue_wage
            if (\Schema::hasTable('issue_wage')) {
                $this->info('表 issue_wage 已存在');
            } else {
                (new \CreateTableIssueWage())->up();
            }
            //添加表 issue_wage
            if (\Schema::hasTable('issue_wage_queue')) {
                $this->info('表 issue_wage_queue 已存在');
            } else {
                (new \CreateTableIssueWageQueue())->up();
            }
            DB::commit();
            $this->info('奖期工资表 issue_wage , issue_wage_queue 添加成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('奖期工资表添加失败');
            $this->info($e->getMessage());
        }
    }

    private function find_q_user()
    {
        $dailywage_type_ident = get_config('dailywage_type_ident', '');
        $this->info('当前平台' . $dailywage_type_ident);
        $sql = 'select user_id ,count(*) from user_dailywage_contract where user_id > 20019 and status = 0 group by user_id ,status HAVING count(*) > 1 ';
        $result = DB::select($sql);
        if ($result) {
            $user_ids = [];
            $usernames = [];
            foreach ($result as $value) {
                $user_ids[] = $value->user_id;
            }
            if ($user_ids) {
                $this->error('发现重复契约用户 ' . count($user_ids) . ' 个');
                $sql = 'select id,username from users where id IN ( ' . implode(',', $user_ids) . ' )';
                $result = DB::select($sql);
                foreach ($result as $value) {
                    $usernames[] = $value->username;
                }
                $this->error('分别是 =>  ' . implode(',', $usernames));
            }
        } else {
            $this->info('没有发现重复生效契约用户');
        }
    }

    private function wage_line_multi()
    {
        DB::beginTransaction();
        try {
            //如果工资线没有ID字段
            if (Schema::hasColumn('user_dailywage_line', 'id')) {
                $this->info('总代契约线表 user_dailywage_line 字段 id 已经存在，不需要添加');
            } else {
                //工资线删除旧主键
                Schema::table('user_dailywage_line', function (Blueprint $table) {
                    $table->dropPrimary();
                });
                //工资线添加新字段与唯一键
                Schema::table('user_dailywage_line', function (Blueprint $table) {
                    $table->smallIncrements('id');
                    $table->unique(['top_user_id', 'type']);
                });
            }
            //如果用户契约表没有类型字段
            if (Schema::hasColumn('user_dailywage_contract', 'type')) {
                $this->info('用户契约表user_dailywage_contract字段type已经存在，不需要添加');
            } else {
                $this->info('用户契约表user_dailywage_contract字段type不存在，需要添加');
                //用户契约添加类型字段
                Schema::table('user_dailywage_contract', function (Blueprint $table) {
                    $table->unsignedTinyInteger('type')->default(0)->comment('工资类型');
                });
                //用户契约表添加新索引
                Schema::table('user_dailywage_contract', function (Blueprint $table) {
                    $table->index(['user_id', 'type', 'status']);
                });
            }

            //如果用户契约表没有类型字段
            if (Schema::hasColumn('user_dailywage_contract', 'type')) {
                $this->info('用户契约表user_dailywage_contract字段type已经存在，更新默认值');
                $count = $contracts = DB::table('user_dailywage_contract')->where('type', '>', 0)->count();
                if ($count > 0) {
                    $this->info('当前 user_dailywage_contract 用户契约表字段type已经更新过，不需要重复执行');
                } else {
                    //备份数据表
                    $back_table_name = "user_dailywage_contract_back_" . date('Ymd');
                    DB::statement('DROP TABLE IF EXISTS  ' . $back_table_name . ';');
                    DB::statement('CREATE TABLE  ' . $back_table_name . ' as TABLE user_dailywage_contract;');
                    //查询所有的工资线，然后赋值新字段数值
                    $lines = DB::table('user_dailywage_line')->get();
                    if ($lines->isNotEmpty()) {
                        foreach ($lines as $line) {
                            $top_user_id = $line->top_user_id;//工资线总代ID
                            $wage_type = $line->type;//工资线总代ID
                            $this->info('总代工资线 top_id => ' . $top_user_id . ' ,wage_type => ' . $wage_type);
                            //查询有工资线的，总代ID为$top_user_id的用户ID
                            $have_contract_users = DB::table('user_dailywage_contract')
                                ->leftJoin('users', 'users.id', 'user_dailywage_contract.user_id')
                                ->where('users.top_id', $top_user_id)
                                ->where('user_dailywage_contract.type', 0)
                                ->select(['user_dailywage_contract.user_id'])
                                ->get();
                            $this->info('需要更新契约类型的纪录条数为' . $have_contract_users->count());
                            $have_contract_user_ids = [];
                            foreach ($have_contract_users as $have_contract_user) {
                                if ($have_contract_user->user_id) {
                                    $have_contract_user_ids[] = $have_contract_user->user_id;
                                }
                            }
                            if (count($have_contract_user_ids) > 0) {
                                $af = DB::table('user_dailywage_contract')
                                    ->whereIn('user_id', $have_contract_user_ids)
                                    ->where('type', 0)
                                    ->update(['type' => $wage_type]);
                                $this->info('更新影响纪录数' . $af);
                            }
                        }
                    }
                }
            }

            DB::commit();
            $this->info('多线工资提交');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('多线工资回滚');
            $this->info($e->getMessage());
        }
    }

    /**
     * Execute the console command.
     * @return void
     * @throws \Exception
     */
    private function handle()
    {
        $method_name = $this->argument('method');
        if (method_exists($this, $method_name)) {
            $this->$method_name();
        } else {
            $this->info('找不到要执行的方法');
        }
    }


    /**
     * 添加投注服务费报表菜单
     */
    private function _admin_permissions_projects_fee_report()
    {
        $parent_id = DB::table('admin_role_permissions')->where('name', '报表管理')->where('parent_id', 0)->value('id');

        //后台菜单
        if ($row = DB::table('admin_role_permissions')
            ->where('rule', 'projectsfeereport/index')
            ->first()) {
            $this->info('admin_role_permissions 表中 ' . $row->rule . ' 纪录已经存在，ID为' . $row->id);
        } else {
            $permission_id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => $parent_id,
                'rule' => 'projectsfeereport/index',
                'name' => '服务费报表',
            ]);
            $this->info('admin_role_permissions 表中 projectsfeereport/index 纪录插入成功，ID为' . $permission_id);
        }
    }

    /**
     * 添加奖期工资进度列表菜单
     */
    private function _admin_permissions_issue_wage_queue()
    {
        $parent_id = DB::table('admin_role_permissions')->where('name', '报表管理')->where('parent_id', 0)->value('id');

        //后台菜单
        if ($row = DB::table('admin_role_permissions')->where('rule', 'issuewagequeue/index')->first()) {
            $this->info('admin_role_permissions 表中 ' . $row->rule . ' 纪录已经存在，ID为' . $row->id);
        } else {
            $permission_id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => $parent_id,
                'rule' => 'issuewagequeue/index',
                'name' => '奖期工资进度列表',
            ]);
            $this->info('admin_role_permissions 表中 issuewagequeue/index 纪录插入成功，ID为' . $permission_id);
        }
    }

    /**
     * 暂时不需要分红清理功能，注释掉
     * 清除2020-04-01 23:59:59前测试数据
     *
     * private function _jinxin_wage_delete()
     * {
     * $dividend_type_ident = get_config('dividend_type_ident');
     * if($dividend_type_ident <> 'Jinxin'){
     * $this->info('当前方法只允许 '.$dividend_type_ident.' 使用');
     * }else{
     * DB::table('daily_wage')->truncate();
     * DB::table('wage_jobs')->truncate();
     * $this->info('daily_wage wage_jobs 表 执行清除成功');
     * }
     * }*/

    /**
     * 删除必乐部分浮动工资数据
     */
    private function _bile_floatwages_delete()
    {
        $platform_type_ident = get_config('dailywage_type_ident');
        if ($platform_type_ident <> 'Bile') {
            $this->info('当前方法只允许 ' . $platform_type_ident . ' 使用');
        } else {
            DB::beginTransaction();
            try {
                $count = \Service\Models\FloatWages::leftJoin('users', 'users.id', 'float_wages.user_id')
                    ->where(DB::raw('jsonb_array_length(users.parent_tree)'), '=', 2)
                    ->where('float_wages.date', '>=', '2020-08-01')
                    ->delete();
                $result = \Service\Models\WageJobs::where('type', '=', 'FloatWage')->update(['last_calculate_time' => '2020-07-31 00:00:00']);
                DB::commit();
                $this->info('执行成功 删除的日亏损数据: ' . $count . '条, 更新工资任务表数据: ' . $result . '条');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->info("执行失败" . PHP_EOL . $e->getMessage());
            }
        }
    }

    /**
     * 优亿一键通过上级审核
     */
    private function _youyi_dividend_status()
    {
        $dividend_type_ident = get_config('dividend_type_ident');
        if ($dividend_type_ident <> 'Youyi') {
            $this->info('当前方法只允许 ' . $dividend_type_ident . ' 使用');
        } else {
            $where = function ($query) {
                $query->where('start_time', '=', '2020-05-15 00:00:00');
                $query->where('end_time', '=', '2020-05-31 23:59:59');
                $query->where('status', '=', '3');
            };
            $count = ContractDividend::where($where)->count();
            $this->info('待上级审核分红共' . $count . '条');
            if ($count == 120) {
                $update = ContractDividend::where($where)->update(['status' => 2]);
                $this->info('更新分红纪录共' . $update . '条');
            } else {
                $this->info('纪录数不正确，不是120条');
            }
        }
    }


    /**
     * 添加分红报表删除功能
     */
    private function _admin_permissions_dividendreport_delete()
    {
        //后台菜单
        if ($permission = DB::table('admin_role_permissions')
            ->where('rule', 'dividendreport/delete')
            ->value('id')) {
            $this->info('admin_role_permissions 表中 dividendreport/delete 纪录已经存在');
        } else {
            $parent_id = DB::table('admin_role_permissions')
                ->where('rule', 'report')
                ->where('parent_id', 0)
                ->value('id');
            $permission_id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => $parent_id,
                'rule' => 'dividendreport/delete',
                'name' => '删除分红报表',
            ]);
            $this->info('admin_role_permissions 表中 dividendreport/delete 纪录插入成功，ID为' . $permission_id);
        }
    }

    /**
     * 投注服务费表 projects_fee 添加user_id字段
     */
    private function _add_project_fee_user_id()
    {
        DB::beginTransaction();
        try {
            //服务费 projects_fee 添加字段 user_id
            if (\Schema::hasColumn('projects_fee', 'user_id')) {
                $this->info('表 projects_fee 字段 user_id 已存在');
            } else {
                DB::statement('ALTER TABLE public.projects_fee ADD COLUMN user_id integer NOT NULL DEFAULT 0');
                DB::statement('COMMENT ON COLUMN "public"."projects_fee"."user_id" IS \'用户ID\';');
                DB::statement('CREATE INDEX "projects_fee_user_id_index" ON "public"."projects_fee" USING btree ("user_id")');
                $this->info('表 projects_fee 字段 user_id 创建成功！');
            }
            $affected = DB::update('UPDATE config SET "key" = \'project_fee_rate\' where "key" like \'%project_fee_rate%\'');
            $this->info('更新纪录数' . $affected);

            $affected = DB::update('UPDATE projects_fee SET user_id = (SELECT projects.user_id FROM projects WHERE projects_fee.project_id = projects.id )');
            $this->info('更新纪录数' . $affected);
            //删除无用字段以及索引
            DB::statement('DROP INDEX IF EXISTS  projects_fee_task_id_index');
            $this->info('删除索引 projects_fee_task_id_index');
            DB::statement('ALTER TABLE public.projects_fee DROP COLUMN IF EXISTS task_id');
            $this->info('删除 projects_fee 表字段 task_id');
            DB::commit();
            $this->info('服务费相关操作完成');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('回滚');
            $this->info($e->getMessage());
            throw $e;
        }
    }

    //增加投注服务费表
    private function _create_table_project_fee()
    {
        DB::beginTransaction();
        try {
            //添加配置值
            $this->info('开始添加网站配置');
            if ($project_rate_config = DB::table('config')->where('key', 'project_fee_rate')->first()) {
                $this->info('配置 project_fee_rate 已经存在！');
            } else {
                if ($config_operation = DB::table('config')->where('key', 'operation')->first()) {
                    $project_rate_config_id = DB::table('config')->insertGetId([
                        'parent_id' => $config_operation->id,
                        'title' => '投注服务费比例',
                        'key' => 'project_fee_rate',
                        'value' => '0',
                        'description' => '取值必须在0至1之间的4位小数，0则关闭服务费',
                    ]);
                    $this->info('配置 project_fee_rate 插入成功，新增ID => ' . $project_rate_config_id);
                }
            }
            //添加帐变类型变
            $this->info('开始添加帐变类型');
            if ($order_type_tzfwf = DB::table('order_type')->where('ident', 'TZFWF')->first()) {
                $this->info('帐变类型 TZFWF 已经存在！');
            } else {
                $order_type_id = DB::table('order_type')->insertGetId([
                    'name' => '投注服务费',
                    'ident' => 'TZFWF',
                    'display' => 1,
                    'operation' => 2,
                    'hold_operation' => 0,
                    'category' => 1,
                    'description' => '投注服务费',
                ]);
                $this->info('帐变类型 TZFWF 添加成功 id => ' . $order_type_id);
            }
            if ($order_type_tzfwf = DB::table('order_type')->where('ident', 'FHTZFWF')->first()) {
                $this->info('帐变类型 FHTZFWF 已经存在！');
            } else {
                $order_type_id = DB::table('order_type')->insertGetId([
                    'name' => '返还投注服务费',
                    'ident' => 'FHTZFWF',
                    'display' => 1,
                    'operation' => 1,
                    'hold_operation' => 0,
                    'category' => 1,
                    'description' => '返还投注服务费',
                ]);
                $this->info('帐变类型 FHTZFWF 添加成功 id => ' . $order_type_id);
            }

            if ($order_type = DB::table('order_type')->where('ident', 'ZHFWF')->first()) {
                $this->info('帐变类型 ZHFWF 已经存在！');
            } else {
                $order_type_id = DB::table('order_type')->insertGetId([
                    'name' => '追号服务费',
                    'ident' => 'ZHFWF',
                    'display' => 1,
                    'operation' => 2,
                    'hold_operation' => 1,
                    'category' => 1,
                    'description' => '追号服务费',
                ]);
                $this->info('帐变类型 ZHFWF 添加成功 id => ' . $order_type_id);
            }

            if ($order_type = DB::table('order_type')->where('ident', 'DQZHFWFFK')->first()) {
                $this->info('帐变类型 DQZHFWFFK 已经存在！');
            } else {
                $order_type_id = DB::table('order_type')->insertGetId([
                    'name' => '当期追号服务费返款',
                    'ident' => 'DQZHFWFFK',
                    'display' => 1,
                    'operation' => 1,
                    'hold_operation' => 2,
                    'category' => 1,
                    'description' => '当期追号服务费返款',
                ]);
                $this->info('帐变类型 DQZHFWFFK 添加成功 id => ' . $order_type_id);
            }

            if ($order_type = DB::table('order_type')->where('ident', 'ZHFWFFK')->first()) {
                $this->info('帐变类型 ZHFWFFK 已经存在！');
            } else {
                $order_type_id = DB::table('order_type')->insertGetId([
                    'name' => '追号服务费返款',
                    'ident' => 'ZHFWFFK',
                    'display' => 1,
                    'operation' => 1,
                    'hold_operation' => 2,
                    'category' => 1,
                    'description' => '追号服务费返款',
                ]);
                $this->info('帐变类型 ZHFWFFK 添加成功 id => ' . $order_type_id);
            }


            //添加表
            if (\Schema::hasTable('projects_fee')) {
                $this->info('表 projects_fee 已存在');
            } else {
                (new \CreateTableProjectsFee())->up();
            }

            //追号添加字段
            if (\Schema::hasColumn('tasks', 'project_fee_rate')) {
                $this->info('表 tasks 字段project_fee_rate 已存在');
            } else {
                DB::statement('ALTER TABLE public.tasks ADD COLUMN project_fee_rate numeric(4,4) NULL DEFAULT 0');
                DB::statement('COMMENT ON COLUMN "public"."tasks"."project_fee_rate" IS \'服务费比例\';');
                $this->info('表 tasks project_fee_rate 字段创建成功！');
            }
            DB::commit();
            $this->info('服务费相关操作完成');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('回滚');
            $this->info($e->getMessage());
            throw $e;
        }
    }

    //增加配置 指定彩种服务费
    private function _add_config_project_fee_lottery_ident()
    {
        $operation = DB::table('config')->where('key', 'operation')->first();

        DB::beginTransaction();
        try {
            DB::table('config')->insert([
                'parent_id' => $operation->id,
                'title' => '投注服务费限制彩种',
                'key' => 'project_fee_lottery_ident',
                'value' => '',
                'description' => '彩种标识,若有多个彩种则使用英文,隔开。不填写则全彩种皆抽取服务费。默认:空',
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加配置 启用上下级转账通知
    private function _add_config_transfer_notification()
    {
        $operation = DB::table('config')->where('key', 'operation')->first();

        DB::beginTransaction();
        try {
            DB::table('config')->insert([
                'parent_id' => $operation->id,
                'title' => '启用上下级转账通知',
                'key' => 'transfer_notification',
                'value' => '0',
                'description' => '默认:0不开启，1开启',
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function _AlterTable()
    {
        $result = DB::select("
            SELECT column_name FROM information_schema.columns
            WHERE table_name='notices ' AND column_name = 'end_at'");

        if (empty($result)) {
            DB::statement("
                ALTER TABLE public.notices ADD end_at timestamp NULL;
            ");

            DB::statement("
                 COMMENT ON COLUMN public.notices.end_at IS '结束时间';
            ");
        }
    }
    

    private function _drawsourceData()
    {
        $idents_array = [
            'ah11x5',
            'ahk3',
            'bjkl8',
            'bjpk10',
            'bjssc',
            'cqkls',
            'cqssc',
            'fucai3d',
            'gd11x5',
            'gdkls',
            'hbk3',
            'jsk3',
            'jsscpk10',
            'jx11x5',
            'pcdd',
            'pl3pl5',
            'sd11x5',
            'sh11x5',
            'tjkls',
            'tjssc',
            'xglhc',
            'xjssc',
            'xyftpk10',
            'zj11x5'
        ];
        $source_dir = 'Apollo'; //首字母大写
        $source_name = 'Apollo奖源';
        $source_url = 'http://www.gg-apollo.com';
        $source_status = 't';
        $source_rank = 100;

        //$lottery_rows = \Service\Models\Lottery::select(['id', 'ident'])->whereIn('ident', $idents_array)->orderBy('id', 'asc')->get();
        $lottery_rows = \Service\Models\Lottery::select(['id', 'ident'])->where('special', '0')->orderBy(
            'id',
            'asc'
        )->get();
        if (!empty($lottery_rows)) {
            $lottery_id2row = [];
            $lottery_ids_array = [];
            foreach ($lottery_rows as $lottery) {
                $lottery_id2row[$lottery->id] = $lottery;
                $lottery_ids_array[] = $lottery->id;
            }
            unset($lottery_rows);
            if ($lottery_ids_array) {
                $drawsource_rows = \Service\Models\Drawsource::select(['lottery_id'])
                    ->where('ident', 'like', $source_dir . '%')
                    ->whereIn('lottery_id', $lottery_ids_array)
                    ->get();
                $drawsource_lottery_ids_array = [];
                foreach ($drawsource_rows as $row) {
                    $drawsource_lottery_ids_array[] = $row->lottery_id;
                }

                $inserts_data = [];
                foreach ($lottery_id2row as $lottery_id => $row) {
                    if (!in_array($lottery_id, $drawsource_lottery_ids_array)) {
                        $ident_ucfirst = ucfirst(strtolower($row->ident));
                        $inserts_data[] = [
                            'lottery_id' => $lottery_id,
                            'name' => $source_name,
                            //'ident' => $source_dir.'\\'.$ident_ucfirst,
                            'ident' => $source_dir . '\\Common',
                            'url' => $source_url,
                            'status' => $source_status,
                            'rank' => $source_rank,
                        ];
                    }
                }
                if ($inserts_data) {
                    \DB::table('drawsource')->insert($inserts_data);
                }
            }
        }
    }

    /**
     * 实时工资小时工资表
     * @throws \Exception
     */
    private function _realtime_wage()
    {
        DB::beginTransaction();

        try {
            // 创建实时表
            DB::statement("
                -- Drop table
                -- DROP TABLE public.realtime_wage;

                CREATE TABLE public.realtime_wage (
                    id serial NOT NULL,
                    project_id int4 NOT NULL, -- 注单ID
                    user_id int4 NOT NULL, -- 用户ID
                    amount numeric(15,4) NOT NULL DEFAULT '0'::numeric, -- 总计派发金额
                    send_date timestamp NULL, -- 发放时间
                    status int2 NOT NULL DEFAULT '0'::smallint, -- 0-待确认,1-待发放,2-已发放
                    report_status int2 NOT NULL DEFAULT '0'::smallint, -- 报表汇总状态：0. 未开始; 1. 进行中; 2. 完成
                    created_at timestamp(0) NOT NULL DEFAULT LOCALTIMESTAMP, -- 写入时间
                    remark varchar NOT NULL, -- 备注
                    PRIMARY KEY (id),
                    UNIQUE (user_id, project_id)
                );
            ");
            // -- Table Index
            DB::statement("
                CREATE INDEX realtime_wage_status_report_status_index ON public.realtime_wage USING btree (status, report_status);
            ");
            // -- Column comments
            DB::statement("COMMENT ON COLUMN public.realtime_wage.project_id IS '注单ID';");
            DB::statement("COMMENT ON COLUMN public.realtime_wage.user_id IS '用户ID';");
            DB::statement("COMMENT ON COLUMN public.realtime_wage.amount IS '总计派发金额';");
            DB::statement("COMMENT ON COLUMN public.realtime_wage.send_date IS '发放时间';");
            DB::statement("COMMENT ON COLUMN public.realtime_wage.status IS '0-待确认,1-待发放,2-已发放';");
            DB::statement("COMMENT ON COLUMN public.realtime_wage.report_status IS '报表汇总状态：0. 未开始; 1. 进行中; 2. 完成';");
            DB::statement("COMMENT ON COLUMN public.realtime_wage.created_at IS '写入时间';");
            DB::statement("COMMENT ON COLUMN public.realtime_wage.remark IS '备注';");
            $this->info('realtime_wage 表创建成功');

            DB::statement("ALTER TABLE public.user_dailywage_line ADD type smallint NULL DEFAULT 1;");
            DB::statement("COMMENT ON COLUMN public.user_dailywage_line.type IS '工资类型 1：日工资 2：实时工资 3:小时工资';");
            $this->info('日工资类型新增成功');


            // 创建小时工资表
            DB::statement("
                CREATE TABLE public.hourly_wage
                (
                    id serial NOT NULL,
                    user_id integer NOT NULL,
                    type smallint NOT NULL,
                    amount numeric(15,4) NOT NULL DEFAULT '0'::numeric,
                    start_date timestamp(0) without time zone NOT NULL,
                    end_date timestamp(0) without time zone NOT NULL,
                    status smallint NOT NULL DEFAULT '0'::smallint,
                    report_status smallint NOT NULL DEFAULT '0'::smallint,
                    created_at timestamp(0) without time zone NOT NULL DEFAULT LOCALTIMESTAMP,
                    deleted_at timestamp(0) without time zone,
                    remark jsonb NOT NULL DEFAULT '{}'::jsonb,
                    PRIMARY KEY (id),
                    UNIQUE (user_id, type, start_date, end_date)

                );
            ");

            DB::statement("COMMENT ON COLUMN public.hourly_wage.user_id IS '用户ID';");
            DB::statement("COMMENT ON COLUMN public.hourly_wage.type IS '工资类型：1：中单 2:挂单 3:挂单单挑';");
            DB::statement("COMMENT ON COLUMN public.hourly_wage.amount IS '用户派发金额';");
            DB::statement("COMMENT ON COLUMN public.hourly_wage.start_date IS '结算开始时间';");
            DB::statement("COMMENT ON COLUMN public.hourly_wage.end_date IS '结算结束时间';");
            DB::statement("COMMENT ON COLUMN public.hourly_wage.status IS '0-待确认,1-待发放,2-已发放';");
            DB::statement("COMMENT ON COLUMN public.hourly_wage.report_status IS '报表汇总状态：0. 未开始; 1. 进行中; 2. 完成';");
            DB::statement("COMMENT ON COLUMN public.hourly_wage.created_at IS '写入时间';");
            DB::statement("COMMENT ON COLUMN public.hourly_wage.deleted_at IS '软删除删除时间';");
            DB::statement("COMMENT ON COLUMN public.hourly_wage.remark IS '备注';");

            DB::statement("CREATE INDEX hourly_wage_deleted_at_index ON public.hourly_wage USING btree (deleted_at);");
            DB::statement("CREATE INDEX hourly_wage_status_report_status_index ON public.hourly_wage USING btree (status, report_status);");
            $this->info('hourly_wage 表创建成功！');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('日工资SQL执行失败！');
        }
    }

    /**
     * IP防火墙
     * @return string
     * @throws \Exception
     */
    private function _ip_firewall()
    {
        DB::beginTransaction();
        try {
            DB::statement("
                CREATE TABLE \"public\".\"ip_firewall\" (
                    \"id\" serial NOT NULL,
                    \"type\" varchar(16) NOT NULL,
                    \"ip\" inet NOT NULL,
                    \"remark\" varchar(64) DEFAULT '',
                    \"admin\" varchar(64) DEFAULT '',
                    \"created_at\" timestamp(0) DEFAULT LOCALTIMESTAMP NOT NULL,
                    \"updated_at\" timestamp(0) DEFAULT LOCALTIMESTAMP NOT NULL,
                    PRIMARY KEY (\"id\")
                );
            ");

            DB::statement("COMMENT ON COLUMN \"public\".\"ip_firewall\".\"type\" IS '类型：admin后台、user前台用户';");
            DB::statement("COMMENT ON COLUMN \"public\".\"ip_firewall\".\"ip\" IS 'IP';");
            DB::statement("COMMENT ON COLUMN \"public\".\"ip_firewall\".\"remark\" IS '备注';");
            DB::statement("COMMENT ON COLUMN \"public\".\"ip_firewall\".\"admin\" IS '操作管理员';");
            DB::statement("COMMENT ON COLUMN \"public\".\"ip_firewall\".\"created_at\" IS '创建时间';");
            DB::statement("COMMENT ON COLUMN \"public\".\"ip_firewall\".\"updated_at\" IS '修改时间';");
            DB::statement("CREATE INDEX \"ip_firewall_type_index\" ON \"public\".\"ip_firewall\" USING btree (\"type\");");

            //后台菜单
            $parent = DB::table('admin_role_permissions')->where('rule', 'permission')->first(['id']);
            if (empty($parent)) {
                $this->info('IP Firewall:admin_role_permissions 表没有 permission 菜单');
                DB::rollBack();
                return '';
            }
            $id = $parent->id;

            DB::table('admin_role_permissions')->insert([
                [
                    'parent_id' => $id,
                    'rule' => 'ipfirewall/index',
                    'name' => 'IP防火墙',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'ipfirewall/create',
                    'name' => '添加IP记录',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'ipfirewall/edit',
                    'name' => '编辑IP记录',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'ipfirewall/delete',
                    'name' => '删除IP记录',
                ],
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('IP Firewall SQL执行失败！');
        }
    }

    /**
     * 添加GG棋牌表
     * @throws \Exception
     */
    private function _ggqipai()
    {
        DB::beginTransaction();
        try {
            DB::statement('
                CREATE TABLE public.third_game_ggqipai_bet
                (
                    id serial NOT NULL,
                    third_game_ident character varying(32) COLLATE pg_catalog."default" NOT NULL DEFAULT \'\'::character varying,
                    user_id integer NOT NULL,
                    game character varying(32) COLLATE pg_catalog."default" NOT NULL,
                    game_date timestamp(0) without time zone,
                    game_type character varying(32) COLLATE pg_catalog."default" NOT NULL DEFAULT \'\'::character varying,
                    bet_id character varying(32) COLLATE pg_catalog."default" NOT NULL,
                    status character varying(32) COLLATE pg_catalog."default" NOT NULL DEFAULT \'0\'::character varying,
                    total_bets numeric(15,4) NOT NULL DEFAULT \'0\'::numeric,
                    total_wins numeric(15,4) NOT NULL DEFAULT \'0\'::numeric,
                    chou_shui numeric(15,4) NOT NULL DEFAULT \'0\'::numeric,
                    rebate_status smallint NOT NULL DEFAULT \'0\'::smallint,
                    remark character varying(250) COLLATE pg_catalog."default" NOT NULL
                );
            ');

            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.third_game_ident IS '游戏接口ident';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.user_id IS '用户ID';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.game IS '游戏名称或标识';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.game_date IS '游戏时间';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.game_type IS '游戏类型';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.bet_id IS '平台返回的投注记录ID';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.status IS '注单状态';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.total_bets IS '总共投注';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.total_wins IS '盈亏(+:玩家赢, -:玩家输)';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.chou_shui IS '抽水金额';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.rebate_status IS '0:还没有返水,1:已经返水,2:返水错误';");
            DB::statement("COMMENT ON COLUMN third_game_ggqipai_bet.remark IS '备注';");

            DB::statement('CREATE INDEX "third_game_ggqipai_bet_bet_id_index" ON "third_game_ggqipai_bet" USING btree ("bet_id" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST);');
            DB::statement('CREATE INDEX "third_game_ggqipai_bet_game_date_index" ON "third_game_ggqipai_bet" USING btree ("game_date" "pg_catalog"."timestamp_ops" ASC NULLS LAST);');
            DB::statement('CREATE INDEX "third_game_ggqipai_bet_user_id_index" ON "third_game_ggqipai_bet" USING btree ("user_id" "pg_catalog"."int4_ops" ASC NULLS LAST);');
            DB::statement('ALTER TABLE "third_game_ggqipai_bet" ADD CONSTRAINT "third_game_ggqipai_bet_user_id_bet_id_game_type_unique" UNIQUE ("user_id", "bet_id", "game_type");');
            DB::statement('ALTER TABLE "third_game_ggqipai_bet" ADD CONSTRAINT "third_game_ggqipai_bet_pkey" PRIMARY KEY ("id");');

            //order type
            DB::statement("select setval('order_type_id_seq',(select max(id) from order_type));");
            DB::statement("INSERT INTO \"order_type\"(\"ident\", \"name\", \"display\", \"operation\", \"hold_operation\", \"category\", \"description\") VALUES ('GGQIPAICR', 'GG棋牌存入', 1, 2, 0, 3, 'GG棋牌存入');");
            DB::statement("INSERT INTO \"order_type\"(\"ident\", \"name\", \"display\", \"operation\", \"hold_operation\", \"category\", \"description\") VALUES ('GGQIPAITQ', 'GG棋牌提取', 1, 1, 0, 3, 'GG棋牌提取');");
            DB::statement("INSERT INTO \"order_type\"(\"ident\", \"name\", \"display\", \"operation\", \"hold_operation\", \"category\", \"description\") VALUES ('GGQIPAIFS', 'GG棋牌返水', 1, 1, 0, 3, 'GG棋牌返水');");

            //platform
            DB::statement("select setval('third_game_platform_id_seq',(select max(id) from third_game_platform));");
            DB::statement("INSERT INTO \"third_game_platform\"(\"ident\", \"name\", \"sort\", \"status\", \"rebate_type\") VALUES ('GgQiPai', 'GG棋牌', 0, 1, 0);");

            //third_game
            DB::statement("select setval('third_game_id_seq',(select max(id) from third_game));");
            DB::statement("INSERT INTO \"third_game\"(\"third_game_platform_id\", \"ident\", \"name\", \"merchant\", \"merchant_key\", \"api_base\", \"merchant_test\", \"merchant_key_test\", \"api_base_test\", \"status\", \"login_status\", \"transfer_status\", \"transfer_type\", \"deny_user_group\", \"last_fetch_time\") VALUES ((select id from third_game_platform where ident = 'GgQiPai'), 'CiaGgQiPai', 'GG棋牌(cia)', '', '', 'http://api.wm-cia.com/api/', 'hpcvwbyomorxeunp', 'ed54eb1e52b68c2861b7935fc343a73d', 'http://uat.wmcia.net/api', 1, 0, 0, 0, '[\"3\"]', '2019-07-17 14:18:54');");


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('ggqipai sql执行失败！');
        }
    }

    //GG电游
    private function _ggdy()
    {
        DB::beginTransaction();
        try {
            //创建third_game_ggdy_bet表
            (new \CreateTableThirdGameGgDyBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('GGDYCR', 'GG电游存入', 1, 2, 0, 3, 'GG电游存入'),
                ('GGDYTQ', 'GG电游提取', 1, 1, 0, 3, 'GG电游提取'),
                ('GGDYFS', 'GG电游返水', 1, 1, 0, 3, 'GG电游返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('GgDy', 'GG电游', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'GgDy'),
                    'CiaGgDy',
                    'GG电游(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("gg电游SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    //利记体育
    private function _thirdGameSbo()
    {
        DB::beginTransaction();
        try {
            //创建third_game_sbo_bet表
            (new \CreateTableThirdGameSboBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('SBOCR', '利记体育存入', 1, 2, 0, 3, '利记体育存入'),
                ('SBOTQ', '利记体育提取', 1, 1, 0, 3, '利记体育提取'),
                ('SBOFS', '利记体育返水', 1, 1, 0, 3, '利记体育返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('Sbo', '利记体育', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'Sbo'),
                    'CiaSbo',
                    '利记体育(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('利记体育SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("利记体育SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    //CIA AIO沙巴体育
    private function _thirdGameAio()
    {
        DB::beginTransaction();
        try {
            //创建third_game_aio_bet表
            (new \CreateTableThirdGameAioBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('AIOCR', 'AIO沙巴体育存入', 1, 2, 0, 3, 'AIO沙巴体育存入'),
                ('AIOTQ', 'AIO沙巴体育提取', 1, 1, 0, 3, 'AIO沙巴体育提取'),
                ('AIOFS', 'AIO沙巴体育返水', 1, 1, 0, 3, 'AIO沙巴体育返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('Aio', 'AIO沙巴体育', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'Aio'),
                    'CiaAio',
                    'AIO沙巴体育(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('AIO沙巴体育 SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("AIO沙巴体育 SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    private function _avCloud()
    {
        DB::beginTransaction();
        try {
            $ident = 'AvCloud';
            $check = \Service\Models\ThirdGamePlatform::where('ident', $ident)->first();
            if (empty($check)) {
                $arr = [
                    'ident' => 'AvCloud',
                    'name' => 'av云',
                    'sort' => 0,
                    'status' => 0,
                    'rebate_type' => 0,
                ];
                $av_id = \Service\Models\ThirdGamePlatform::insertGetId($arr);

                $arr = [
                    'third_game_platform_id' => $av_id,
                    'ident' => 'CiaAvCloud',
                    'name' => 'av云',
                    'merchant' => '',
                    'merchant_key' => '',
                    'api_base' => '',
                    'merchant_test' => '',
                    'merchant_key_test' => '',
                    'api_base_test' => '',
                    'status' => 0,
                    'login_status' => 0,
                    'transfer_status' => 0,
                    'transfer_type' => 1,
                    'deny_user_group' => '["3"]',
                ];
                $gime_id = \Service\Models\ThirdGame::insertGetId($arr);
                $arr = [
                    [
                        'third_game_id' => $gime_id,
                        'ident' => 'api_white_list',
                        'name' => '调用本平台的第三方ip白名单',
                        'value' => '',
                    ],
                    [
                        'third_game_id' => $gime_id,
                        'ident' => 'member_level',
                        'name' => '会员等级配置',
                        'value' => '{"bet":888,"deposit":1,"level":3,"history_days":7}',
                    ],
                    [
                        'third_game_id' => $gime_id,
                        'ident' => 'member_limit_switch',
                        'name' => '限制条件开关(1开启)',
                        'value' => '1',
                    ],
                    [
                        'third_game_id' => $gime_id,
                        'ident' => 'member_white_list',
                        'name' => '会员白名单',
                        'value' => '',
                    ],
                ];

                \Service\Models\ThirdGameExtend::insert($arr);

                DB::commit();
                $this->info('av云添加成功！');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    //幸运棋牌
    private function _thirdGameLgQp()
    {
        DB::beginTransaction();
        try {
            //创建third_game_lgqp_bet表
            (new \CreateTableThirdGameLgQpBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('LGQPCR', '幸运棋牌存入', 1, 2, 0, 3, '幸运棋牌存入'),
                ('LGQPTQ', '幸运棋牌提取', 1, 1, 0, 3, '幸运棋牌提取'),
                ('LGQPFS', '幸运棋牌返水', 1, 1, 0, 3, '幸运棋牌返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('LgQp', '幸运棋牌', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'LgQp'),
                    'CiaLgQp',
                    '幸运棋牌(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('幸运棋牌SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("幸运棋牌SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    //龙城棋牌
    private function _thirdGameLcQp()
    {
        DB::beginTransaction();
        try {
            //创建third_game_lcqp_bet表
            (new \CreateTableThirdGameLcQpBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('LCQPCR', '龙城棋牌存入', 1, 2, 0, 3, '龙城棋牌存入'),
                ('LCQPTQ', '龙城棋牌提取', 1, 1, 0, 3, '龙城棋牌提取'),
                ('LCQPFS', '龙城棋牌返水', 1, 1, 0, 3, '龙城棋牌返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('LcQp', '龙城棋牌', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'LcQp'),
                    'CiaLcQp',
                    '龙城棋牌(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('龙城棋牌SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("龙城棋牌SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    //财神棋牌
    private function _thirdGameVgQp()
    {
        DB::beginTransaction();
        try {
            //创建third_game_vgqp_bet表
            (new \CreateTableThirdGameVgQpBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('VGQPCR', '财神棋牌存入', 1, 2, 0, 3, '财神棋牌存入'),
                ('VGQPTQ', '财神棋牌提取', 1, 1, 0, 3, '财神棋牌提取'),
                ('VGQPFS', '财神棋牌返水', 1, 1, 0, 3, '财神棋牌返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('VgQp', '财神棋牌', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'VgQp'),
                    'CiaVgQp',
                    '财神棋牌(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('财神棋牌SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("财神棋牌SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    //MG电游
    private function _mgdy()
    {
        DB::beginTransaction();
        try {
            //创建third_game_mgdy_bet表
            (new \CreateTableThirdGameMgDyBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('MGDYCR', 'MG电游存入', 1, 2, 0, 3, 'MG电游存入'),
                ('MGDYTQ', 'MG电游提取', 1, 1, 0, 3, 'MG电游提取'),
                ('MGDYFS', 'MG电游返水', 1, 1, 0, 3, 'MG电游返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('MgDy', 'MG电游', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'MgDy'),
                    'CiaMgDy',
                    'MG电游(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("mg电游SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    //MG2电游
    private function _mg2()
    {
        DB::beginTransaction();
        try {
            //创建third_game_mg2_bet表
            (new \CreateTableThirdGameMg2Bet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('MG2CR', 'MG2电游存入', 1, 2, 0, 3, 'MG2电游存入'),
                ('MG2TQ', 'MG2电游提取', 1, 1, 0, 3, 'MG2电游提取'),
                ('MG2FS', 'MG2电游返水', 1, 1, 0, 3, 'MG2电游返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('Mg2', 'MG2电游', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'Mg2'),
                    'CiaMg2',
                    'MG2电游(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("mg2电游SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    //BNG电游
    private function _bngdy()
    {
        DB::beginTransaction();
        try {
            //创建third_game_bngdy_bet表
            (new \CreateTableThirdGameBngDyBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('BNGDYCR', 'BNG电游存入', 1, 2, 0, 3, 'BNG电游存入'),
                ('BNGDYTQ', 'BNG电游提取', 1, 1, 0, 3, 'BNG电游提取'),
                ('BNGDYFS', 'BNG电游返水', 1, 1, 0, 3, 'BNG电游返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('BngDy', 'BNG电游', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'BngDy'),
                    'CiaBngDy',
                    'BNG电游(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("bng电游SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    // WML 完美真人
    private function _thirdGameWml()
    {
        DB::beginTransaction();
        try {
            // 创建third_game_wml_bet表
            (new \CreateTableThirdGameWmlBet())->up();

            // 创建 third_game 打赏表
            (new \CreateTableThirdGameDs())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('WMLCR', '完美真人存入', 1, 2, 0, 3, '完美真人存入'),
                ('WMLTQ', '完美真人提取', 1, 1, 0, 3, '完美真人提取'),
                ('WMLFS', '完美真人返水', 1, 1, 0, 3, '完美真人返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('Wml', '完美真人', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'Wml'),
                    'CiaWml',
                    '完美真人(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("完美真人SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    // LEG 乐游棋牌
    private function _thirdGameLeg()
    {
        DB::beginTransaction();
        try {
            // 创建third_game_leg_bet表
            (new \CreateTableThirdGameLegBet())->up();

            // order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('LEGCR', '乐游棋牌存入', 1, 2, 0, 3, '乐游棋牌存入'),
                ('LEGTQ', '乐游棋牌提取', 1, 1, 0, 3, '乐游棋牌提取'),
                ('LEGFS', '乐游棋牌返水', 1, 1, 0, 3, '乐游棋牌返水');"
            );

            // platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('Leg', '乐游棋牌', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'Leg'),
                    'CiaLeg',
                    '乐游棋牌(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("乐游棋牌SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    // AVIA 泛亚电竞
    private function _thirdGameAvia()
    {
        DB::beginTransaction();
        try {
            // 创建third_game_Avia_bet表
            (new \CreateTableThirdGameAviaBet())->up();

            // order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('AVIACR', '泛亚电竞存入', 1, 2, 0, 3, '泛亚电竞存入'),
                ('AVIATQ', '泛亚电竞提取', 1, 1, 0, 3, '泛亚电竞提取'),
                ('AVIAFS', '泛亚电竞返水', 1, 1, 0, 3, '泛亚电竞返水');"
            );

            // platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('Avia', '泛亚电竞', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'Avia'),
                    'CiaAvia',
                    '泛亚电竞(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("泛亚电竞SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    private function _thirdGameDaShang()
    {
        DB::beginTransaction();
        try {
            //third_game_order
            DB::statement('ALTER TABLE "third_game_order" ADD COLUMN "order_type_id" int4 NOT NULL DEFAULT 0');
            DB::statement('COMMENT ON COLUMN "third_game_order"."order_type_id" IS \'帐变类型ID\'');
            DB::statement('CREATE INDEX "third_game_order_order_type_id_from_to_index" ON "public"."third_game_order" USING btree ("order_type_id" "pg_catalog"."int4_ops" ASC NULLS LAST,"from" "pg_catalog"."int4_ops" ASC NULLS LAST,"to" "pg_catalog"."int4_ops" ASC NULLS LAST)');

            //order type
            DB::statement("select setval('order_type_id_seq',(select max(id) from order_type));");
            DB::statement("INSERT INTO \"order_type\"(\"ident\", \"name\", \"display\", \"operation\", \"hold_operation\", \"category\", \"description\") VALUES ('VRDSKK', '竞速打赏扣款', 1, 2, 0, 3, '竞速打赏扣款');");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('dashang SQL执行失败！');
        }
    }

    /**
     * 添加浮动工资
     * @throws \Exception
     */
    private function _float_wage()
    {
        DB::beginTransaction();
        try {
            DB::statement("
                CREATE TABLE public.float_wages
                (
                    id serial,
                    user_id integer NOT NULL,
                    date date NOT NULL,
                    total_price numeric(15,4) NOT NULL DEFAULT '0'::numeric,
                    total_rebate numeric(15,4) NOT NULL DEFAULT '0'::numeric,
                    activity smallint NOT NULL DEFAULT '0'::smallint,
                    rate numeric(15,4) NOT NULL DEFAULT '0'::numeric,
                    amount numeric(15,4) NOT NULL DEFAULT '0'::numeric,
                    total_amount numeric(15,4) NOT NULL DEFAULT '0'::numeric,
                    child_amount numeric(15,4) NOT NULL DEFAULT '0'::numeric,
                    status smallint NOT NULL DEFAULT '0'::smallint,
                    report_status smallint NOT NULL DEFAULT '0'::smallint,
                    created_at timestamp(0) without time zone NOT NULL DEFAULT LOCALTIMESTAMP,
                    send_at timestamp(0) without time zone,
                    CONSTRAINT float_wages_pkey PRIMARY KEY (id),
                    CONSTRAINT float_wages_user_id_date_unique UNIQUE (user_id, date)
                );
            ");

            DB::statement("COMMENT ON COLUMN public.float_wages.user_id IS '用户ID'");

            DB::statement("COMMENT ON COLUMN public.float_wages.date IS '哪一天的日工资'");

            DB::statement("COMMENT ON COLUMN public.float_wages.total_price IS '日销量'");

            DB::statement("COMMENT ON COLUMN public.float_wages.total_rebate IS '累计返点'");

            DB::statement("COMMENT ON COLUMN public.float_wages.activity IS '活跃人数'");

            DB::statement("COMMENT ON COLUMN public.float_wages.rate IS '分红比例'");

            DB::statement("COMMENT ON COLUMN public.float_wages.amount IS '工资金额'");

            DB::statement("COMMENT ON COLUMN public.float_wages.total_amount IS '总计工资金额'");

            DB::statement("COMMENT ON COLUMN public.float_wages.child_amount IS '下级工资金额'");

            DB::statement("COMMENT ON COLUMN public.float_wages.status IS '0-待确认,1-待发放,2-已发放'");

            DB::statement("COMMENT ON COLUMN public.float_wages.report_status IS '报表汇总状态：0. 未开始; 1. 进行中; 2. 完成'");

            DB::statement("COMMENT ON COLUMN public.float_wages.created_at IS '写入时间'");

            DB::statement("COMMENT ON COLUMN public.float_wages.send_at IS '派发时间'");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('浮动工资 SQL执行失败！');
        }
    }

    /**
     * 更新表字段类型
     */
    private function _alert_table_type()
    {
        DB::statement("ALTER TABLE realtime_wage ALTER created_at TYPE timestamp(0)");
        DB::statement("ALTER TABLE realtime_wage ALTER send_date TYPE timestamp(0)");
        DB::statement("ALTER TABLE notices ALTER end_at TYPE timestamp(0)");
    }

    /**
     * 添加工资任务表
     */
    private function _create_table_wage_jobs()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS public.wage_jobs
            (
                id serial,
                type character varying(255) COLLATE pg_catalog.\"default\" NOT NULL,
                last_calculate_time timestamp(0) without time zone NOT NULL DEFAULT LOCALTIMESTAMP,
                CONSTRAINT wage_jobs_pkey PRIMARY KEY (id),
                CONSTRAINT wage_jobs_type_unique UNIQUE (type)
            )");

        DB::statement("COMMENT ON COLUMN public.wage_jobs.type IS '工资类型'");
        DB::statement("COMMENT ON COLUMN public.wage_jobs.last_calculate_time IS '上次计算时间'");

        $this->info('SQL执行成功！');
    }

    /**
     * 添加前台菜单表
     * @throws \Exception
     */
    private function _create_table_front_menu()
    {

        if (\Schema::hasTable('front_menu')) {
            $this->info('front_menu 表已存在');
            return;
        }

        $date_time = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            DB::statement("
                CREATE TABLE public.front_menu
                (
                    \"id\" serial,
                    \"name\" varchar(32) COLLATE \"default\" NOT NULL,
                    \"ident\" varchar(32) COLLATE \"default\" NOT NULL,
                    \"category\" varchar(16) COLLATE \"default\" NOT NULL,
                    \"status\" bool DEFAULT false NOT NULL,
                    \"data\" jsonb DEFAULT '[]'::jsonb NOT NULL,
                    \"last_editor\" varchar(64) COLLATE \"default\" DEFAULT ''::character varying NOT NULL,
                    \"created_at\" timestamp(0),
                    \"updated_at\" timestamp(0),
                    CONSTRAINT front_menu_pkey PRIMARY KEY (\"id\"),
                    CONSTRAINT front_menu_ident_unique UNIQUE (\"ident\")
                );
            ");

            DB::statement("COMMENT ON COLUMN public.front_menu.name IS '菜单种类名称'");
            DB::statement("COMMENT ON COLUMN public.front_menu.ident IS '菜单种类英文标识'");
            DB::statement("COMMENT ON COLUMN public.front_menu.category IS '分类'");
            DB::statement("COMMENT ON COLUMN public.front_menu.status IS '状态，0 为禁用，1 为启用'");
            DB::statement("COMMENT ON COLUMN public.front_menu.data IS '菜单JSON数据'");
            DB::statement("COMMENT ON COLUMN public.front_menu.last_editor IS '最后修改的管理员'");
            DB::statement("CREATE INDEX front_menu_status_index ON public.front_menu USING btree (status);");

            DB::table('front_menu')->insert([
                [
                    'name' => '数字彩票PC',
                    'ident' => 'lottery_pc',
                    'category' => 'pc',
                    'data' => '[{"name": "时时彩", "path": "ssc", "sort": 0, "children": [{"name": "重庆时时彩", "path": "/lottery/cqssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "乐利时时彩", "path": "/live/leli/120", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "乐利1.5分彩", "path": "/live/leli/119", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "腾讯分分彩", "path": "/lottery/txffc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "奇趣腾讯分分彩", "path": "/lottery/qiqutxffssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "腾讯五分彩", "path": "/lottery/tx5fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "奇趣腾讯五分彩", "path": "/lottery/qiqutx5fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "阿里云分分彩", "path": "/lottery/qiqualyffssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "阿里云五分彩", "path": "/lottery/qiqualy5fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "阿里云十分彩", "path": "/lottery/qiqualy10fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "比特币分分彩", "path": "/lottery/btcffc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "吉利分分彩", "path": "/lottery/jlffc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "凤凰吉利时时彩", "path": "/lottery/fhjlssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "天津时时彩", "path": "/lottery/tjssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "新疆时时彩", "path": "/lottery/xjssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "香港时时彩", "path": "/lottery/5fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "澳门时时彩", "path": "/lottery/3fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "台湾时时彩", "path": "/lottery/2fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "重庆分分彩", "path": "/lottery/1fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极速秒秒彩", "path": "/lottery/jsmmc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR三分彩", "path": "/live/vr/11", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR1.5分彩", "path": "/live/vr/1", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "河内分分彩", "path": "/lottery/hne1fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "河内5分彩", "path": "/lottery/hne5fssc", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "11选5", "path": "11x5", "sort": 1, "children": [{"name": "山东11选5", "path": "/lottery/sd11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "广东11选5", "path": "/lottery/gd11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "江西11选5", "path": "/lottery/jx11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "上海11选5", "path": "/lottery/sh11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "浙江11选5", "path": "/lottery/zj11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "香港11选5", "path": "/lottery/5f11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "澳门11选5", "path": "/lottery/3f11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "台湾11选5", "path": "/lottery/2f11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "曼谷11选5", "path": "/lottery/1f11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "秒秒11选5", "path": "/lottery/jsmm11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "安徽11选5", "path": "/lottery/ah11x5", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "快三", "path": "k3", "sort": 2, "children": [{"name": "江苏快三", "path": "/lottery/jsk3", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "湖北快三", "path": "/lottery/hbk3", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "安徽快三", "path": "/lottery/ahk3", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "乐利快三", "path": "/live/leli/121", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "快乐10分", "path": "kls", "sort": 2, "children": [{"name": "湖南快乐10分", "path": "/lottery/hnkls", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "广东快乐10分", "path": "/lottery/gdkls", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "天津快乐10分", "path": "/lottery/tjkls", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "重庆快乐10分", "path": "/lottery/cqkls", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "快乐8", "path": "kl8", "sort": 2, "children": [{"name": "北京快乐8", "path": "/lottery/bjkl8", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "加拿大快乐8", "path": "/lottery/jndkl8", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "PC蛋蛋", "path": "pcdd", "sort": 3, "children": [{"name": "PC蛋蛋", "path": "/lottery/pcdd", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "加拿大PC28", "path": "/lottery/jndpcdd", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "PK10", "path": "pk10", "sort": 4, "children": [{"name": "北京PK10", "path": "/lottery/bjpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "幸运飞艇", "path": "/lottery/xyftpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "幸运赛艇", "path": "/lottery/jsxystpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "海南赛车PK拾", "path": "/lottery/5fpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极速赛车", "path": "/lottery/jsscpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "腾讯PK10", "path": "/lottery/tx5fpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极速赛马PK10", "path": "/lottery/xyftpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR木星赛车", "path": "/live/vr/35", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR北京赛车", "path": "/live/vr/13", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR赛车", "path": "/live/vr/2", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR赛马", "path": "/live/vr/36", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR游泳", "path": "/live/vr/37", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR自行车", "path": "/live/vr/38", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "联销彩", "path": "lxc", "sort": 5, "children": [{"name": "福彩3D", "path": "/lottery/fucai3d", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "香港3D", "path": "/lottery/5f3d", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "秒秒3D", "path": "/lottery/jsmm3d", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "排列三、五", "path": "/lottery/pl3pl5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "香港六合彩", "path": "/lottery/xglhc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "上海时时乐", "path": "/lottery/shssl3d", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR六合彩", "path": "/live/vr/16", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR百家乐", "path": "/live/vr/15", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "乐利六合彩", "path": "/live/leli/118", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => '数字彩票H5',
                    'ident' => 'lottery_h5',
                    'category' => 'h5',
                    'data' => '[{"name": "热门游戏", "path": "hot", "sort": 0, "children": [{"name": "重庆时时彩", "path": "/lottery/cqssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "比特币分分彩", "path": "/lottery/btcffc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "吉利分分彩", "path": "/lottery/jlffc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "凤凰吉利时时彩", "path": "/lottery/fhjlssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "腾讯PK10", "path": "/lottery/tx5fpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "山东11选5", "path": "/lottery/sd11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "香港六合彩", "path": "/lottery/xglhc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "江苏快三", "path": "/lottery/jsk3", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "北京快乐8", "path": "/lottery/bjkl8", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "湖南快乐10分", "path": "/lottery/hnkls", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "PC蛋蛋", "path": "/lottery/pcdd", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "腾讯分分彩", "path": "/lottery/txffc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "奇趣腾讯分分彩", "path": "/lottery/qiqutxffssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "腾讯五分彩", "path": "/lottery/tx5fssc", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "时时彩", "path": "ssc", "sort": 1, "children": [{"name": "重庆时时彩", "path": "/lottery/cqssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "腾讯分分彩", "path": "/lottery/txffc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "奇趣腾讯分分彩", "path": "/lottery/qiqutxffssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "腾讯五分彩", "path": "/lottery/tx5fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "奇趣腾讯五分彩", "path": "/lottery/qiqutx5fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "阿里云分分彩", "path": "/lottery/qiqualyffssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "阿里云五分彩", "path": "/lottery/qiqualy5fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "阿里云十分彩", "path": "/lottery/qiqualy10fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "比特币分分彩", "path": "/lottery/btcffc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "吉利分分彩", "path": "/lottery/jlffc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "凤凰吉利时时彩", "path": "/lottery/fhjlssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "天津时时彩", "path": "/lottery/tjssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "新疆时时彩", "path": "/lottery/xjssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "香港时时彩", "path": "/lottery/5fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "澳门时时彩", "path": "/lottery/3fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "台湾时时彩", "path": "/lottery/2fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "重庆分分彩", "path": "/lottery/1fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极速秒秒彩", "path": "/lottery/jsmmc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "河内分分彩", "path": "/lottery/hne1fssc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "河内5分彩", "path": "/lottery/hne5fssc", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "11选5", "path": "11x5", "sort": 2, "children": [{"name": "山东11选5", "path": "/lottery/sd11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "广东11选5", "path": "/lottery/gd11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "江西11选5", "path": "/lottery/jx11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "上海11选5", "path": "/lottery/sh11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "浙江11选5", "path": "/lottery/zj11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "香港11选5", "path": "/lottery/5f11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "澳门11选5", "path": "/lottery/3f11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "台湾11选5", "path": "/lottery/2f11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "曼谷11选5", "path": "/lottery/1f11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "秒秒11选5", "path": "/lottery/jsmm11x5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "安徽11选5", "path": "/lottery/ah11x5", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "快三", "path": "k3", "sort": 3, "children": [{"name": "江苏快三", "path": "/lottery/jsk3", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "湖北快三", "path": "/lottery/hbk3", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "安徽快三", "path": "/lottery/ahk3", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "快乐10分", "path": "kls", "sort": 4, "children": [{"name": "湖南快乐10分", "path": "/lottery/hnkls", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "广东快乐10分", "path": "/lottery/gdkls", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "天津快乐10分", "path": "/lottery/tjkls", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "重庆快乐10分", "path": "/lottery/cqkls", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "快乐8", "path": "kl8", "sort": 5, "children": [{"name": "北京快乐8", "path": "/lottery/bjkl8", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "加拿大快乐8", "path": "/lottery/jndkl8", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "PC蛋蛋", "path": "pcdd", "sort": 6, "children": [{"name": "PC蛋蛋", "path": "/lottery/pcdd", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "加拿大PC28", "path": "/lottery/jndpcdd", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "PK10", "path": "pk10", "sort": 7, "children": [{"name": "北京PK10", "path": "/lottery/bjpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "幸运飞艇", "path": "/lottery/xyftpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "幸运赛艇", "path": "/lottery/jsxystpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "海南赛车PK拾", "path": "/lottery/5fpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极速赛车", "path": "/lottery/jsscpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "腾讯PK10", "path": "/lottery/tx5fpk10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极速赛马PK10", "path": "/lottery/xyftpk10", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "联销彩", "path": "lxc", "sort": 8, "children": [{"name": "福彩3D", "path": "/lottery/fucai3d", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "香港3D", "path": "/lottery/5f3d", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "秒秒3D", "path": "/lottery/jsmm3d", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "排列三、五", "path": "/lottery/pl3pl5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "香港六合彩", "path": "/lottery/xglhc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "上海时时乐", "path": "/lottery/shssl3d", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => 'VR彩票',
                    'ident' => 'vr_lottery',
                    'category' => 'h5',
                    'data' => '[{"name": "热门游戏", "path": "hot", "sort": 0, "children": [{"name": "VR三分彩", "path": "/live/vr/11", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR1.5分彩", "path": "/live/vr/1", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR六合彩", "path": "/live/vr/16", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "时时彩", "path": "ssc", "sort": 1, "children": [{"name": "VR三分彩", "path": "/live/vr/11", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR1.5分彩", "path": "/live/vr/1", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "PK10", "path": "pk10", "sort": 2, "children": [{"name": "VR木星赛车", "path": "/live/vr/35", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR北京赛车", "path": "/live/vr/13", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR赛车", "path": "/live/vr/2", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR赛马", "path": "/live/vr/36", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR游泳", "path": "/live/vr/37", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR自行车", "path": "/live/vr/38", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "联销彩", "path": "lxc", "sort": 3, "children": [{"name": "VR六合彩", "path": "/live/vr/16", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR百家乐", "path": "/live/vr/15", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => '乐利彩票',
                    'ident' => 'lolly_lottery',
                    'category' => 'h5',
                    'data' => '[{"name": "热门游戏", "path": "hot", "sort": 0, "children": [{"name": "乐利时时彩", "path": "/live/leli/120", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "乐利1.5分彩", "path": "/live/leli/119", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "乐利六合彩", "path": "/live/leli/118", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "乐利快三", "path": "/live/leli/121", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "时时彩", "path": "ssc", "sort": 1, "children": [{"name": "乐利时时彩", "path": "/live/leli/120", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "乐利1.5分彩", "path": "/live/leli/119", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "快三", "path": "k3", "sort": 2, "children": [{"name": "乐利快三", "path": "/live/leli/121", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "联销彩", "path": "lxc", "sort": 3, "children": [{"name": "VR六合彩", "path": "/live/vr/16", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "VR百家乐", "path": "/live/vr/15", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "乐利六合彩", "path": "/live/leli/118", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => 'PT电游',
                    'ident' => 'pt_game',
                    'category' => 'common',
                    'data' => '[{"name": "上古文明 神秘力量", "path": "myth", "sort": 0, "children": [{"name": "神灵时代：命运姐妹", "path": "/game/play/impt/ftsis", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "神的时代：奥林匹斯之国王", "path": "/game/play/impt/zeus", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "神的时代：雷霆4神", "path": "/game/play/impt/furf", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "众神时代：风暴之神TM", "path": "/game/play/impt/aeolus", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "神的时代", "path": "/game/play/impt/aogs", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "超级法老王宝藏", "path": "/game/play/impt/phtd", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "法老王的秘密", "path": "/game/play/impt/pst", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "荣耀罗马", "path": "/game/play/impt/gtsrng", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "印加大奖", "path": "/game/play/impt/aztec", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "泰国神庙", "path": "/game/play/impt/thtk", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "亚特兰蒂斯女王", "path": "/game/play/impt/gtsatq", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "胆大戴夫和拉之眼", "path": "/game/play/impt/gtsdrdv", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "动物明星 魅力上场", "path": "animal", "sort": 1, "children": [{"name": "小猪与狼", "path": "/game/play/impt/paw", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "熊之舞", "path": "/game/play/impt/bob", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "狐狸的宝藏", "path": "/game/play/impt/fxf", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "金钱蛙", "path": "/game/play/impt/jqw", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "企鹅假期", "path": "/game/play/impt/pgv", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "豹月", "path": "/game/play/impt/pmn", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "猫赌神", "path": "/game/play/impt/ctiv", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "野牛闪电战", "path": "/game/play/impt/bfb", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "热带动物园", "path": "/game/play/impt/sfh", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "怀特王", "path": "/game/play/impt/whk", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "四象", "path": "/game/play/impt/sx", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "海豚礁", "path": "/game/play/impt/dnr", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "中国文化 好运连连", "path": "chinese", "sort": 2, "children": [{"name": "大明帝国", "path": "/game/play/impt/gtsgme", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "飞龙在天", "path": "/game/play/impt/gtsflzt", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "中国厨房", "path": "/game/play/impt/cm", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "云从龙", "path": "/game/play/impt/yclong", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "五虎将", "path": "/game/play/impt/ftg", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "五路财神", "path": "/game/play/impt/wlcsh", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "六福兽", "path": "/game/play/impt/kfp", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "吉祥8", "path": "/game/play/impt/gtsjxb", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "壮志凌云", "path": "/game/play/impt/topg", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "好运来", "path": "/game/play/impt/sol", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "招财进宝", "path": "/game/play/impt/zcjb", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "招财进宝彩池", "path": "/game/play/impt/zcjbjp", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "武则天", "path": "/game/play/impt/heavru", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "龙之战士", "path": "/game/play/impt/drgch", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "龙龙龙", "path": "/game/play/impt/longlong", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "舞龙", "path": "/game/play/impt/paw", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "奇幻故事 刺激好玩", "path": "fantasy", "sort": 3, "children": [{"name": "白雪公主", "path": "/game/play/impt/ashfta", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "宝石皇后", "path": "/game/play/impt/gemq", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "杰克与魔豆", "path": "/game/play/impt/ashbob", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "权杖女王", "path": "/game/play/impt/qnw", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "仙境冒险", "path": "/game/play/impt/ashadv", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "三剑客和女王的钻石", "path": "/game/play/impt/tmqd", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "弓箭手", "path": "/game/play/impt/arc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "神秘夏洛克", "path": "/game/play/impt/shmst", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "猿人传奇", "path": "/game/play/impt/epa", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "富有的唐吉可德", "path": "/game/play/impt/donq", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "青春之泉", "path": "/game/play/impt/foy", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "洛奇", "path": "/game/play/impt/rky", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "美女船长", "path": "/game/play/impt/ashcpl", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "狂野精灵", "path": "/game/play/impt/wis", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "狂躁的海盗", "path": "/game/play/impt/gts52", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "樱花之恋", "path": "/game/play/impt/chl", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "鬼屋", "path": "/game/play/impt/hh", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "神奇的栈", "path": "/game/play/impt/mgstk", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "边境之心", "path": "/game/play/impt/ashhof", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "招财进宝 赚最多", "path": "money", "sort": 4, "children": [{"name": "巨额财富", "path": "/game/play/impt/gtspor", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "万圣节财富2", "path": "/game/play/impt/hlf2", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "开心假期", "path": "/game/play/impt/er", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "开心假期加强版", "path": "/game/play/impt/vcstd", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "日日生财", "path": "/game/play/impt/ririshc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "返利先生", "path": "/game/play/impt/mcb", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "金土地", "path": "/game/play/impt/lndg", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "超级888", "path": "/game/play/impt/chao", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "幸运月", "path": "/game/play/impt/ashfmf", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "银弹", "path": "/game/play/impt/sib", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "黄金游戏", "path": "/game/play/impt/glg", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "热力宝石", "path": "/game/play/impt/gts50", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "疯狂7", "path": "/game/play/impt/c7", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "疯狂麻将", "path": "/game/play/impt/fkmj", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "纯金翅膀", "path": "/game/play/impt/gtswng", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "烈焰钻石", "path": "/game/play/impt/ght_a", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "奖金巨人", "path": "/game/play/impt/jpgt", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "野外宝藏", "path": "/game/play/impt/legwld", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "甜蜜派对", "path": "/game/play/impt/cnpr", "sort": 0, "ishot": 0, "isnew": 0}]}, {"name": "世界奇迹 疯狂畅玩", "path": "world", "sort": 5, "children": [{"name": "巴西森宝", "path": "/game/play/impt/gtssmbr", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "泰国天堂", "path": "/game/play/impt/tpd2", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "艺伎故事", "path": "/game/play/impt/ges", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "足球狂欢节", "path": "/game/play/impt/gtsfc", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "狂野亚马逊", "path": "/game/play/impt/ashamw", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极地冒险", "path": "/game/play/impt/gtsir", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "百慕达三角洲", "path": "/game/play/impt/bt", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "牛仔和外星人", "path": "/game/play/impt/gtscbl", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "沉默的武士", "path": "/game/play/impt/sis", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "武士元素", "path": "/game/play/impt/pisa", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "亚马逊的秘密", "path": "/game/play/impt/samz", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "埃斯梅拉达", "path": "/game/play/impt/esmk", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "冰穴", "path": "/game/play/impt/ashicv", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "湛蓝深海", "path": "/game/play/impt/bib", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "湛蓝深海彩池TM", "path": "/game/play/impt/grbjp", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "海滨嘉年华", "path": "/game/play/impt/bl", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "金刚-世界的第八大奇迹", "path": "/game/play/impt/kkg", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => 'GG电游',
                    'ident' => 'gg_game',
                    'category' => 'common',
                    'data' => '[{"name": "全部", "path": "all", "sort": 0, "children": [{"name": "甜心空姐", "path": "/game/play/GgDy/1001", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "阳光沙滩", "path": "/game/play/GgDy/1002", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "欢乐原始人", "path": "/game/play/GgDy/1003", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "幸运美人鱼", "path": "/game/play/GgDy/1004", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "SM", "path": "/game/play/GgDy/1005", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "夜上海", "path": "/game/play/GgDy/1006", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "青楼梦", "path": "/game/play/GgDy/1007", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "盗窃谜案", "path": "/game/play/GgDy/1008", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "中华厨娘", "path": "/game/play/GgDy/1009", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "比基尼拳赛", "path": "/game/play/GgDy/1010", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => '开元棋牌',
                    'ident' => 'ky_poker',
                    'category' => 'common',
                    'data' => '[{"name": "全部", "path": "all", "sort": 0, "children": [{"name": "德州扑克", "path": "/game/play/poker/620", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "二八杠", "path": "/game/play/poker/720", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢庄牛牛", "path": "/game/play/poker/830", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "三公", "path": "/game/play/poker/860", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "押庄龙虎", "path": "/game/play/poker/900", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "21点", "path": "/game/play/poker/600", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "通比牛牛", "path": "/game/play/poker/870", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极速炸金花", "path": "/game/play/poker/230", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢庄牌九", "path": "/game/play/poker/730", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "十三水", "path": "/game/play/poker/630", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "百家乐", "path": "/game/play/poker/910", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "森林舞会", "path": "/game/play/poker/920", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "百人牛牛", "path": "/game/play/poker/930", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "万人炸金花", "path": "/game/play/poker/1950", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "斗地主", "path": "/game/play/poker/610", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "炸金花", "path": "/game/play/poker/220", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => 'GG棋牌',
                    'ident' => 'gg_poker',
                    'category' => 'common',
                    'data' => '[{"name": "全部", "path": "all", "sort": 0, "children": [{"name": "大众麻将", "path": "/game/play/GgQiPai/1", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "牛牛", "path": "/game/play/GgQiPai/2", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "十三水", "path": "/game/play/GgQiPai/3", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "炸金花", "path": "/game/play/GgQiPai/4", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "三公", "path": "/game/play/GgQiPai/5", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => '幸运棋牌',
                    'ident' => 'lg_qp',
                    'category' => 'common',
                    'data' => '[{"name": "全部", "path": "all", "sort": 0, "children": [{"name": "炸金花", "path": "/game/play/LgQp/100001", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "看牌抢庄牛牛", "path": "/game/play/LgQp/100018", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "经典牛牛", "path": "/game/play/LgQp/100029", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢庄牌九", "path": "/game/play/LgQp/100028", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "通比牛牛", "path": "/game/play/LgQp/100006", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢红包", "path": "/game/play/LgQp/100020", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "龙虎斗", "path": "/game/play/LgQp/100010", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢庄牛牛", "path": "/game/play/LgQp/100002", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "二十一点", "path": "/game/play/LgQp/100005", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极速狂飙", "path": "/game/play/LgQp/100019", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "二八杠", "path": "/game/play/LgQp/100003", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "飞禽走兽", "path": "/game/play/LgQp/100009", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "百人牛牛", "path": "/game/play/LgQp/100012", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "骰宝", "path": "/game/play/LgQp/100022", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "极速炸金花", "path": "/game/play/LgQp/100007", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "红黑大战", "path": "/game/play/LgQp/100016", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "梭哈", "path": "/game/play/LgQp/100011", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "三公", "path": "/game/play/LgQp/100013", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "百家乐", "path": "/game/play/LgQp/100015", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => '龙城棋牌',
                    'ident' => 'lc_qp',
                    'category' => 'common',
                    'data' => '[{"name": "全部", "path": "all", "sort": 0, "children": [{"name": "德州扑克", "path": "/game/play/LcQp/620", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "二八杠", "path": "/game/play/LcQp/720", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢庄牛牛", "path": "/game/play/LcQp/830", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "炸金花", "path": "/game/play/LcQp/220", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "三公", "path": "/game/play/LcQp/860", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "龙虎斗", "path": "/game/play/LcQp/900", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "21点", "path": "/game/play/LcQp/600", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "通比牛牛", "path": "/game/play/LcQp/870", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢庄牌九", "path": "/game/play/LcQp/730", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "十三水", "path": "/game/play/LcQp/630", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "斗地主", "path": "/game/play/LcQp/610", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "看四张抢庄牛", "path": "/game/play/LcQp/890", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "百家乐", "path": "/game/play/LcQp/910", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "二人麻将", "path": "/game/play/LcQp/740", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "百人牛牛", "path": "/game/play/LcQp/930", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "跑得快", "path": "/game/play/LcQp/640", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "捕鱼", "path": "/game/play/LcQp/510", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "幸运水果机", "path": "/game/play/LcQp/960", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "红黑大战", "path": "/game/play/LcQp/950", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢庄五选三", "path": "/game/play/LcQp/990", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "随机庄百变牛", "path": "/game/play/LcQp/8100", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "百人骰宝", "path": "/game/play/LcQp/8200", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢庄21点", "path": "/game/play/LcQp/8300", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "看牌抢庄三公", "path": "/game/play/LcQp/8400", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "欢乐斗牛", "path": "/game/play/LcQp/8500", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
                [
                    'name' => '财神棋牌',
                    'ident' => 'vg_qp',
                    'category' => 'common',
                    'data' => '[{"name": "全部", "path": "all", "sort": 0, "children": [{"name": "斗地主", "path": "/game/play/VgQp/1", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "抢庄牛牛", "path": "/game/play/VgQp/3", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "百人牛牛", "path": "/game/play/VgQp/4", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "龙王捕鱼", "path": "/game/play/VgQp/5", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "竞咪楚汉德州", "path": "/game/play/VgQp/7", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "推筒子", "path": "/game/play/VgQp/8", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "加倍斗地主", "path": "/game/play/VgQp/9", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "保险楚汉德州", "path": "/game/play/VgQp/10", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "血战麻将", "path": "/game/play/VgQp/11", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "炸金花", "path": "/game/play/VgQp/12", "sort": 0, "ishot": 0, "isnew": 0}, {"name": "必下德州", "path": "/game/play/VgQp/13", "sort": 0, "ishot": 0, "isnew": 0}]}]',
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
            ]);

            //菜单
            DB::statement("DELETE FROM admin_role_has_permission WHERE permission_id IN (
	SELECT \"id\" FROM admin_role_permissions WHERE \"rule\" IN
	('frontmenu/index','frontmenu/create','frontmenu/edit','frontmenu/editdata','frontmenu/refresh','frontmenu/refresh', 'frontmenu/menudelete', 'frontmenu/output')
);");
            DB::statement("DELETE FROM admin_role_permissions WHERE \"rule\" IN
('frontmenu/index','frontmenu/create','frontmenu/edit','frontmenu/editdata','frontmenu/refresh','frontmenu/refresh', 'frontmenu/menudelete', 'frontmenu/output');");

            $parent_id = DB::table('admin_role_permissions')->where('rule', 'site')
                ->where('parent_id', 0)
                ->value('id');
            DB::table('admin_role_permissions')->insert([
                [
                    'parent_id' => $parent_id,
                    'rule' => 'frontmenu/index',
                    'name' => '前台菜单配置',
                ],
                [
                    'parent_id' => $parent_id,
                    'rule' => 'frontmenu/create',
                    'name' => '添加前台菜单种类',
                ],
                [
                    'parent_id' => $parent_id,
                    'rule' => 'frontmenu/edit',
                    'name' => '修改前台菜单种类',
                ],
                [
                    'parent_id' => $parent_id,
                    'rule' => 'frontmenu/editdata',
                    'name' => '编辑前台菜单内容',
                ],
                [
                    'parent_id' => $parent_id,
                    'rule' => 'frontmenu/refresh',
                    'name' => '刷新前台菜单缓存',
                ],
                [
                    'parent_id' => $parent_id,
                    'rule' => 'frontmenu/menudelete',
                    'name' => '删除前台菜单',
                ],
                [
                    'parent_id' => $parent_id,
                    'rule' => 'frontmenu/output',
                    'name' => '导出前台菜单数据',
                ],
            ]);

            DB::commit();
            $this->info('前台菜单 SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('前台菜单 SQL执行失败！');
        }
    }

    //添加MG电游菜单
    private function _mgdy_front_menu()
    {
        if (\Service\Models\FrontMenu::where('ident', 'mg_game')->count()) {
            $this->info('MG电游前台菜单已存在');
            return;
        }

        $date_time = date('Y-m-d H:i:s');
        $front_menu = new \CreateTableFrontMenu();

        DB::beginTransaction();
        try {
            DB::table('front_menu')->insert([
                [
                    'name' => 'MG电游',
                    'ident' => 'mg_game',
                    'category' => 'common',
                    'data' => json_encode($front_menu->_mgGameMenuInit(), JSON_UNESCAPED_UNICODE),
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
            ]);
            DB::commit();
            $this->info('添加MG电游前台菜单 SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('添加MG电游前台菜单 SQL执行失败！');
        }
    }

    //添加BNG电游菜单
    private function _bngdy_front_menu()
    {
        if (\Service\Models\FrontMenu::where('ident', 'bng_game')->count()) {
            $this->info('BNG电游前台菜单已存在');
            return;
        }

        $date_time = date('Y-m-d H:i:s');
        $front_menu = new \CreateTableFrontMenu();

        DB::beginTransaction();
        try {
            DB::table('front_menu')->insert([
                [
                    'name' => 'BNG电游',
                    'ident' => 'bng_game',
                    'category' => 'common',
                    'data' => json_encode($front_menu->_bngGameMenuInit(), JSON_UNESCAPED_UNICODE),
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
            ]);
            DB::commit();
            $this->info('添加BNG电游前台菜单 SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('添加BNG电游前台菜单 SQL执行失败！');
        }
    }

    //添加GG棋牌菜单
    private function _ggqp_front_menu()
    {
        $date_time = date('Y-m-d H:i:s');
        $front_menu = new \CreateTableFrontMenu();

        DB::beginTransaction();
        try {
            DB::table('front_menu')->updateOrInsert(
                [
                    'ident' => 'gg_poker'
                ],
                [
                    'name' => 'GG棋牌',
                    'ident' => 'gg_poker',
                    'category' => 'common',
                    'data' => json_encode($front_menu->_ggPokerMenuInit(), JSON_UNESCAPED_UNICODE),
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ]
            );
            DB::commit();
            $this->info('添加GG棋牌前台菜单 SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('添加GG棋牌前台菜单 SQL执行失败！');
        }
    }

    //添加GG电游菜单
    private function _ggdy_front_menu()
    {
        $date_time = date('Y-m-d H:i:s');
        $front_menu = new \CreateTableFrontMenu();

        DB::beginTransaction();
        try {
            DB::table('front_menu')->updateOrInsert(
                [
                    'ident' => 'gg_game'
                ],
                [
                    'name' => 'GG电游',
                    'ident' => 'gg_game',
                    'category' => 'common',
                    'data' => json_encode($front_menu->_ggGameMenuInit(), JSON_UNESCAPED_UNICODE),
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ]
            );
            DB::commit();
            $this->info('添加GG电游前台菜单 SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('添加GG电游前台菜单 SQL执行失败！');
        }
    }

    //添加MG电游菜单
    private function _mg2_front_menu()
    {
        if (\Service\Models\FrontMenu::where('ident', 'mg2_game')->count()) {
            $this->info('MG2电游前台菜单已存在');
            return;
        }

        $date_time = date('Y-m-d H:i:s');
        $front_menu = new \CreateTableFrontMenu();

        DB::beginTransaction();
        try {
            DB::table('front_menu')->insert([
                [
                    'name' => 'MG2电游',
                    'ident' => 'mg2_game',
                    'category' => 'common',
                    'data' => json_encode($front_menu->_mg2GameMenuInit(), JSON_UNESCAPED_UNICODE),
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
            ]);
            DB::commit();
            $this->info('添加MG2电游前台菜单 SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('添加MG2电游前台菜单 SQL执行失败！');
        }
    }

    /**
     * project表添加字段 requested_at
     */
    private function _project_request_at()
    {
        if (\Schema::hasColumn('projects', 'requested_at')) {
            $this->info('request_at 字段已存在');
            return;
        }

        DB::beginTransaction();

        DB::statement('ALTER TABLE "public"."projects"  ADD COLUMN "requested_at" timestamp(0)');
        DB::statement('COMMENT ON COLUMN "public"."projects"."requested_at" IS \'请求时间\';');

        DB::commit();
    }

    /**
     * 添加活动的界面参数字段
     */
    private function _activity_config_ui()
    {
        if (\Schema::hasColumn('activity', 'config_ui')) {
            $this->info('config_ui 字段已存在');
            return;
        }

        DB::beginTransaction();

        DB::statement('ALTER TABLE "public"."activity" ADD COLUMN "config_ui" jsonb NOT NULL DEFAULT \'{}\'::jsonb');
        DB::statement('COMMENT ON COLUMN "public"."activity"."config_ui" IS \'活动界面参数配置\';');

        DB::commit();
    }

    /**
     * 添加彩种计划任务字段
     * @throws \Exception
     */
    private function _lottery_cron_field()
    {
        if (\Schema::hasColumn('lottery', 'cron')) {
            $this->info('lottery.cron 字段已存在');
            return;
        }

        DB::beginTransaction();
        try {
            DB::statement("ALTER TABLE public.lottery ADD COLUMN IF NOT EXISTS cron VARCHAR(64) NOT NULL DEFAULT '* * * * *'");
            DB::statement("COMMENT ON COLUMN public.lottery.cron IS '计划任务'");
            //更新记录
            DB::statement("UPDATE public.lottery SET cron='* 0-3,7-23 * * *' WHERE ident='cqssc'");
            DB::statement("UPDATE public.lottery SET cron='* 0-2,10-23 * * *' WHERE ident='xjssc'");
            DB::statement("UPDATE public.lottery SET cron='* 9-23 * * *' WHERE ident='tjssc'");
            DB::statement("UPDATE public.lottery SET cron='* 0,9-23 * * *' WHERE ident='bjssc'");
            DB::statement("UPDATE public.lottery SET cron='* 20-22 * * *' WHERE ident='pl3pl5'");
            DB::statement("UPDATE public.lottery SET cron='* 0,9-23 * * *' WHERE ident='bjkl8'");
            DB::statement("UPDATE public.lottery SET cron='* 0,9-23 * * *' WHERE ident='pcdd'");
            DB::statement("UPDATE public.lottery SET cron='* 9-22 * * *' WHERE ident='jsk3'");
            DB::statement("UPDATE public.lottery SET cron='* 9-22 * * *' WHERE ident='hbk3'");
            DB::statement("UPDATE public.lottery SET cron='* 9-22 * * *' WHERE ident='ahk3'");
            DB::statement("UPDATE public.lottery SET cron='* 9-22 * * *' WHERE ident='js11x5'");
            DB::statement("UPDATE public.lottery SET cron='* 9-23 * * *' WHERE ident='gd11x5'");
            DB::statement("UPDATE public.lottery SET cron='* 9-23 * * *' WHERE ident='sd11x5'");
            DB::statement("UPDATE public.lottery SET cron='* 9-23 * * *' WHERE ident='jx11x5'");
            DB::statement("UPDATE public.lottery SET cron='* 9-22 * * *' WHERE ident='ah11x5'");
            DB::statement("UPDATE public.lottery SET cron='* 0,9-23 * * *' WHERE ident='sh11x5'");
            DB::statement("UPDATE public.lottery SET cron='* 9-23 * * *' WHERE ident='zj11x5'");
            DB::statement("UPDATE public.lottery SET cron='* 8-23 * * *' WHERE ident='hlj11x5'");
            DB::statement("UPDATE public.lottery SET cron='* 21-22 * * *' WHERE ident='fucai3d'");
            DB::statement("UPDATE public.lottery SET cron='* 10-22 * * *' WHERE ident='shssl3d'");
            DB::statement("UPDATE public.lottery SET cron='* 21-22 * * *' WHERE ident='xglhc'");
            DB::statement("UPDATE public.lottery SET cron='* 9-23 * * *' WHERE ident='hnkls'");
            DB::statement("UPDATE public.lottery SET cron='* 9-23 * * *' WHERE ident='tjkls'");
            DB::statement("UPDATE public.lottery SET cron='* 9-23 * * *' WHERE ident='gdkls'");
            DB::statement("UPDATE public.lottery SET cron='* 0-3,7-23 * * *' WHERE ident='cqkls'");
            DB::statement("UPDATE public.lottery SET cron='* 0,9-23 * * *' WHERE ident='bjpk10'");
            DB::statement("UPDATE public.lottery SET cron='* 0-5,13-23 * * *' WHERE ident='xyftpk10'");

            //后台菜单
            $parent_id = DB::table('admin_role_permissions')
                ->where('rule', 'lottery')
                ->where('parent_id', 0)
                ->value('id');

            DB::table('admin_role_permissions')->insert([
                [
                    'parent_id' => $parent_id,
                    'rule' => 'lottery/refreshcron',
                    'name' => '刷新彩种计划任务',
                ],
            ]);

            DB::commit();
            $this->info('增加 lottery.cron 字段失成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('增加 lottery.cron 字段失败');
        }
    }

    /**
     * 对话充值
     * @throws \Exception
     */
    private function _chat_deposit()
    {

        try {
            DB::beginTransaction();
            if (\Schema::hasTable('chat_deposit_payment')) {
                $this->info('chat_deposit_payment 表已存在，跳过');
            } else {
                (new \CreateTableChatDepositPayment())->up();
            }
            if (get_config('app_ident', '') != 'fy') {
                if (\Schema::hasColumn('chat_deposit_message', 'type')) {
                    $this->info('chat_deposit_message表的type 字段已存在，跳过字段添加');
                } else {
                    DB::statement("ALTER TABLE public.chat_deposit_message ADD \"type\" int2 NULL DEFAULT 0");
                    DB::statement("COMMENT ON COLUMN public.chat_deposit_message.type IS '类型'");
                }

                //后台权限
                $parent_id = DB::table('admin_role_permissions')
                    ->where('rule', 'withdrawal')
                    ->where('parent_id', 0)
                    ->value('id');
                $result = DB::select("SELECT * FROM public.admin_role_permissions WHERE rule='chatdeposit/payment'");
                if ($result) {
                    $this->info('admin_role_permissions表 chatdeposit/payment 已存在，跳过纪录插入');
                } else {
                    //chatdeposit/config在2019-11-03于3ca5c52ce8823ec8b7aca66f263267e3629de42d次提交被去掉，转换使用chatdeposit/payment                    ;
                    if (!$old_row = DB::table('admin_role_permissions')
                        ->where('name', '配置会话充值')
                        ->where('rule', 'chatdeposit/config')
                        ->first()) {
                        DB::table('admin_role_permissions')->insert([
                            [
                                'parent_id' => $parent_id,
                                'rule' => 'chatdeposit/payment',
                                'name' => '配置会话充值',
                            ],
                        ]);
                        $this->info('admin_role_permissions表 配置会话充值，chatdeposit/payment 插入');
                    } else {
                        DB::table('admin_role_permissions')
                            ->where('name', '配置会话充值')
                            ->where('rule', 'chatdeposit/config')
                            ->update([
                                'rule' => 'chatdeposit/payment',
                            ]);
                        $this->info('admin_role_permissions表 配置会话充值，chatdeposit/config 更新为 chatdeposit/payment');
                    }
                }
            }
            $result = DB::select("SELECT * FROM public.config WHERE key='chat_deposit_auto_reply' ");
            if ($result) {
                $this->info('config 表配置chat_deposit_auto_reply已存在，跳过纪录插入');
            } else {
                //后台配置
                $parent_config = Config::where('key', 'deposit')->first();
                $config = new Config();
                $config->parent_id = $parent_config->id;
                $config->title = "代理充值自动回复";
                $config->key = "chat_deposit_auto_reply";
                $config->value = '0';
                $config->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _dividendReportField()
    {
        try {
            if (\Schema::hasColumn('contract_dividends', 'send_type')) {
                $this->info('send_type 字段已存在');
                return;
            }
            if (\Schema::hasColumn('contract_dividends', 'period')) {
                $this->info('period 字段已存在');
                return;
            }
            DB::beginTransaction();
            DB::statement('ALTER TABLE "public"."contract_dividends" ADD COLUMN "send_type" int2 NOT NULL DEFAULT 0, ADD COLUMN "period" int2 NOT NULL DEFAULT 0');
            DB::statement("COMMENT ON COLUMN \"public\".\"contract_dividends\".\"send_type\" IS '发放方式 1-系统发放，2-上级发放'");
            DB::statement("COMMENT ON COLUMN \"public\".\"contract_dividends\".\"period\" IS '结算周期 1:日结 2:半月结 3:月结 4:周结'");
            DB::commit();
            $this->info('contract_dividends表send_type, period字段添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _dividendReportFieldValue()
    {
        try {
            if (!get_config('dividend_available', 0)) {
                $this->info('分红未开启');
                return;
            }
            if (!\Schema::hasColumn('contract_dividends', 'send_type')) {
                $this->info('send_type 字段不存在');
                return;
            }
            DB::beginTransaction();

            //更新系统派发层级
            $send_low_level = get_config('dividend_send_low_level', 0);
            $send_high_level = get_config('dividend_send_high_level');  // 系统最高派发代理层级
            for ($i = $send_high_level; $i <= $send_low_level; $i++) {
                $config_key = 'dividend_default_interval_' . $i;
                $period = get_config($config_key);
                if (!is_numeric($period) || !in_array($period, ['1', '2', '3', '4'], true)) {
                    $this->info("对不起，{$config_key} 配置错误！");
                    DB::rollBack();
                    return;
                }
                DB::statement("
                    UPDATE contract_dividends SET send_type = 1, period = {$period} WHERE type=2 AND user_id IN (
                        SELECT id FROM users WHERE jsonb_array_length(parent_tree::jsonb) = {$i}
                    )
                ");
            }

            //更新用户派发层级
            $period = get_config('dividend_default_interval_agency');
            if (!is_numeric($period) || !in_array($period, ['1', '2', '3', '4'], true)) {
                $this->info("对不起，dividend_default_interval_agency 配置错误！");
                DB::rollBack();
                return;
            }
            DB::statement("
                UPDATE contract_dividends SET send_type = 2, period = {$period} WHERE type=2 AND user_id IN (
                    SELECT id FROM users WHERE jsonb_array_length(parent_tree::jsonb) > {$send_low_level}
                )
            ");
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _z3budingwei()
    {
        DB::beginTransaction();
        DB::statement('INSERT INTO public.lottery_method (id,parent_id,lottery_method_category_id,ident,name,draw_rule,lock_table_name,lock_init_function,modes,prize_level,prize_level_name,layout,sort,status,max_bet_num) VALUES
(100801006,100801000,11,\'ssc_z3_budingwei_erma\',\'中三二码\',\'{"is_sum": 0, "tag_bonus": "n3_2mbudingwei", "tag_check": "n2_budingwei", "code_count": 3, "start_position": 2}\',\'\',\'\',\'[1,2,3,4,5,6,7,8]\',\'[37.03703]\',\'["中三二码"]\',\'
{
    "desc": "从0-9中任意选择2个以上号码。",
    "help": "从0-9中选择2个号码，每注由2个不同的号码组成，开奖号码的千位、百位、十位中同时包含所选的2个号码，即为中奖。",
    "example": "投注方案：1,2；开奖号码中三位：至少出现1和2各1个，即中中三二码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}\',0,true,0)
,(100801005,100801000,11,\'ssc_z3_budingwei_yima\',\'中三一码\',\'{"is_sum": 0, "tag_bonus": "n3_1mbudingwei", "tag_check": "n1_budingwei", "code_count": 3, "start_position": 2}\',\'\',\'initNumberTypeBudingWeiLock\',\'[1,2,3,4,5,6,7,8]\',\'[7.38007]\',\'["中三一码"]\',\'
{
    "desc": "从0-9中任意选择1个以上号码。",
    "help": "从0-9中选择1个号码，每注由1个号码组成，只要开奖号码的千位、百位、十位中包含所选号码，即为中奖。",
    "example": "投注方案：1；开奖号码中三位：至少出现1个1，即中中三一码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}\',0,true,0)
;');
        DB::commit();
    }

    //增加奖池活动表
    private function _create_table_prize_pool()
    {
        DB::beginTransaction();
        try {
            (new \CreateTablePrizePool())->up();
            DB::commit();
            $this->info('prize_pool 表创建成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _create_table_user_rebates_log()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableUserRebatesLog())->up();
            DB::commit();
            $this->info('user_rebates_log 表创建成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _create_table_report_daily_third_game()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableReportDailyThirdGame())->up();
            DB::commit();
            $this->info('report_daily_third_game 表创建成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _kaijiang()
    {
        DB::beginTransaction();
        try {
            DB::statement("
                UPDATE drawsource SET \"status\"='f' WHERE ident LIKE 'Kaijiang%' AND lottery_id NOT IN (SELECT \"id\" FROM lottery WHERE ident IN('xcqssc', 'xxjssc', 'xbjpk10', 'sxyftpk10'))
            ");
            DB::commit();
            $this->info('SQL执行成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _tj360backup()
    {
        DB::beginTransaction();
        try {
            $lottery_ident = 'cxg360ffc';
            $check = \Service\Models\Lottery::where('ident', $lottery_ident)->first();
            if (($check)) {
                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $check->id,
                        'name' => 'tj360奖源备用',
                        'ident' => 'Tj360backup\\Cxg360ffc',
                        'url' => 'http://5f6efc48fb12e1185559.304dg.cn/',
                        'status' => 't',
                        'rank' => 100,
                    ],
                ]);
            }

            $lottery_ident = 'cxg3605fc';
            $check = \Service\Models\Lottery::where('ident', $lottery_ident)->first();

            if (($check)) {
                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $check->id,
                        'name' => 'tj360奖源备用',
                        'ident' => 'Tj360backup\\Cxg3605fc',
                        'url' => 'http://5f6efc48fb12e1185559.304dg.cn/',
                        'status' => 't',
                        'rank' => 100,
                    ],
                ]);
            }

            DB::commit();
            $this->info('奖源添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    private function _update_lottery_vc()
    {
        DB::beginTransaction();
        try {
            $lottery_ident = 'jlk3';
            $rs = \Service\Models\Lottery::where('ident', $lottery_ident)->delete();
            if ($rs) {
                $lottery = [
                    'lottery_category_id' => 1,
                    'lottery_method_category_id' => 50,
                    'ident' => 'jlk3',
                    'name' => '吉林快三',
                    'official_url' => '',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "1200", "status": "1", "end_hour": "21", "end_sale": "180", "drop_time": "180", "end_minute": "40", "end_second": "00", "start_hour": "21", "start_minute": "40", "start_second": "00", "first_end_hour": "08", "input_code_time": "60", "first_end_minute": "40", "first_end_second": "00", "first_start_yesterday": "1"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0100',
                    'min_profit' => '0.0200',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n2]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                    'special' => '0',
                    'special_config' => '{}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 't',
                    'cron' => '* 8-20 * * *',
                ];
                $lottery_id = \Service\Models\Lottery::insertGetId($lottery);
                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $lottery_id,
                        'name' => '多彩奖源',
                        'ident' => 'Manycai\\Jlk3',
                        'url' => 'http://vip.manycai.com',
                        'status' => 't',
                        'rank' => 100,
                    ],
                ]);
            }
            DB::commit();
            $this->info('更新成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _new_lottery_vc()
    {
        DB::beginTransaction();
        try {
            $lottery_ident = 'hnk3';
            $check = \Service\Models\Lottery::where('ident', $lottery_ident)->first();
            if (empty($check)) {
                $lottery = [
                    'lottery_category_id' => 1,
                    'lottery_method_category_id' => 50,
                    'ident' => 'hnk3',
                    'name' => '河南快三',
                    'official_url' => '',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "1200", "status": "1", "end_hour": "22", "end_sale": "180", "drop_time": "180", "end_minute": "35", "end_second": "00", "start_hour": "22", "start_minute": "35", "start_second": "00", "first_end_hour": "08", "input_code_time": "60", "first_end_minute": "55", "first_end_second": "00", "first_start_yesterday": "1"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0100',
                    'min_profit' => '0.0200',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n2]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                    'special' => '0',
                    'special_config' => '{}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 't',
                    'cron' => '* 8-23 * * *',
                ];
                $lottery_id = \Service\Models\Lottery::insertGetId($lottery);
                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $lottery_id,
                        'name' => '多彩奖源',
                        'ident' => 'Manycai\\Hnk3',
                        'url' => 'http://vip.manycai.com',
                        'status' => 't',
                        'rank' => 100,
                    ],
                ]);
            }
            //*************************************

            $lottery_ident = 'jlk3';
            $check = \Service\Models\Lottery::where('ident', $lottery_ident)->first();
            if (empty($check)) {
                $lottery = [
                    'lottery_category_id' => 1,
                    'lottery_method_category_id' => 50,
                    'ident' => 'jlk3',
                    'name' => '吉林快三',
                    'official_url' => '',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "1200", "status": "1", "end_hour": "21", "end_sale": "180", "drop_time": "180", "end_minute": "40", "end_second": "00", "start_hour": "21", "start_minute": "40", "start_second": "00", "first_end_hour": "08", "input_code_time": "60", "first_end_minute": "40", "first_end_second": "00", "first_start_yesterday": "1"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0100',
                    'min_profit' => '0.0200',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n2]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                    'special' => '0',
                    'special_config' => '{}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 't',
                    'cron' => '* 8-20 * * *',
                ];
                $lottery_id = \Service\Models\Lottery::insertGetId($lottery);
                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $lottery_id,
                        'name' => '多彩奖源',
                        'ident' => 'Manycai\\Jlk3',
                        'url' => 'http://vip.manycai.com',
                        'status' => 't',
                        'rank' => 100,
                    ],
                ]);
            }

            //*********************
            $lottery_ident = 'gxk3';
            $check = \Service\Models\Lottery::where('ident', $lottery_ident)->first();
            if (empty($check)) {
                $lottery = [
                    'lottery_category_id' => 1,
                    'lottery_method_category_id' => 50,
                    'ident' => 'gxk3',
                    'name' => '广西快三',
                    'official_url' => '',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "1200", "status": "1", "end_hour": "22", "end_sale": "180", "drop_time": "180", "end_minute": "30", "end_second": "00", "start_hour": "22", "start_minute": "30", "start_second": "00", "first_end_hour": "09", "input_code_time": "60", "first_end_minute": "30", "first_end_second": "00", "first_start_yesterday": "1"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0100',
                    'min_profit' => '0.0200',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n2]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                    'special' => '0',
                    'special_config' => '{}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 't',
                    'cron' => '* 9-23 * * *',
                ];
                $lottery_id = \Service\Models\Lottery::insertGetId($lottery);
                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $lottery_id,
                        'name' => '多彩奖源',
                        'ident' => 'Manycai\\Gxk3',
                        'url' => 'http://vip.manycai.com',
                        'status' => 't',
                        'rank' => 100,
                    ],
                ]);
            }
            DB::commit();
            $this->info('彩种添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    private function _new_lottery()
    {
        DB::beginTransaction();
        try {
            $lottery_ident = 'sxyftpk10';
            $check = \Service\Models\Lottery::where('ident', $lottery_ident)->first();
            if (empty($check)) {
                $lottery = [
                    'lottery_category_id' => 2,
                    'lottery_method_category_id' => 70,
                    'ident' => 'sxyftpk10',
                    'name' => '官方幸运飞艇',
                    'official_url' => 'http://luckyflightship.com',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "04", "end_sale": "21", "drop_time": "21", "end_minute": "04", "end_second": "00", "start_hour": "04", "start_minute": "04", "start_second": "00", "first_end_hour": "13", "input_code_time": "4", "first_end_minute": "09", "first_end_second": "00", "first_start_yesterday": "0"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0200',
                    'min_profit' => '0.0100',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                    'special' => '0',
                    'special_config' => '{}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 't',
                    'cron' => '* 0-5,13-23 * * *',
                    'deny_user_group' => '[1, 3]',
                ];
                $lottery_id = \Service\Models\Lottery::insertGetId($lottery);
                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $lottery_id,
                        'name' => 'Apollo奖源',
                        'ident' => 'Apollo\\Common',
                        'url' => 'https://www.gg-apollo.com',
                        'status' => 't',
                        'rank' => 100,
                    ],
                    [
                        'lottery_id' => $lottery_id,
                        'name' => '聚合奖源',
                        'ident' => 'Kaijiang\\Sxyftpk10',
                        'url' => 'http://www.kaijiang.net',
                        'status' => 't',
                        'rank' => 100,
                    ],
                ]);

                //奖期
                $Generator = new \Service\API\Issue\Generator();
                $Generator->generate($lottery_id, '', strtotime(date('Y-m-d')), strtotime('+3 days'));
            }


            $drawsource_config_parent_id = \Service\Models\Config::where('key', 'drawsource_config')->value('id');
            $config_data = [
                [
                    'parent_id' => $drawsource_config_parent_id,
                    'title' => '聚合奖源预开号码接收推送开关',
                    'key' => 'kaijiang_codes_enabled',
                    'value' => '1',
                    'description' => '0|关闭,1|开启',
                ],
                [
                    'parent_id' => $drawsource_config_parent_id,
                    'title' => '聚合奖源预开号码代理',
                    'key' => 'kaijiang_codes_proxy_host',
                    'value' => '',
                    'description' => '中间站域名 http://xxpay.abc.com。如果不使用，请留空',
                ],
                [
                    'parent_id' => $drawsource_config_parent_id,
                    'title' => '聚合奖源预开号码密钥',
                    'key' => 'kaijiang_codes_key',
                    'value' => '',
                    'description' => '',
                ],
                [
                    'parent_id' => $drawsource_config_parent_id,
                    'title' => '聚合奖源预开号码推送IP白名单',
                    'key' => 'kaijiang_codes_ips',
                    'value' => '18.162.226.168',
                    'description' => '多个IP使用英文逗号分隔',
                ],
            ];
            if ($drawsource_config_parent_id) {
                $config_data_keys = [];
                foreach ($config_data as $row) {
                    $config_data_keys[] = $row['key'];
                }
                $check = \Service\Models\Config::select(['key'])->whereIn('key', $config_data_keys)->get();
                if ($check->isEmpty()) {
                    \Service\Models\Config::insert($config_data);
                } else {
                    $this->info('config keys已经存在' . print_r($check->toArray(), true));
                }
            } else {
                $this->info('config.drawsource_config 不存在');
            }

            DB::commit();
            $this->info('彩种添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // 新增彩种－幸运赛艇 -- Margin
    private function _new_lottery_xyst()
    {
        DB::beginTransaction();
        try {
            $lottery_ident = 'jsxystpk10';
            $check = \Service\Models\Lottery::where('ident', $lottery_ident)->first();
            if (empty($check)) {
                $lottery = [
                    'lottery_category_id' => 2,
                    'lottery_method_category_id' => 70,
                    'ident' => $lottery_ident,
                    'name' => '幸运赛艇',
                    'official_url' => 'https://lucky-188.com',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "1", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0200',
                    'min_profit' => '0.0100',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                    'special' => '0',
                    'special_config' => '{}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 't',
                    'cron' => '* * * * *',
                    'deny_user_group' => '[]',
                ];
                $lottery_id = \Service\Models\Lottery::insertGetId($lottery);

                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $lottery_id,
                        'name' => 'Apollo奖源',
                        'ident' => 'Apollo\\Common',
                        'url' => 'http://www.gg-apollo.com',
                        'status' => 't',
                        'rank' => 100,
                    ],
                    [
                        'lottery_id' => $lottery_id,
                        'name' => 'Lucky188',
                        'ident' => 'Lucky188\\Jsxystpk10',
                        'url' => 'https://www.lucky-188.com',
                        'status' => 't',
                        'rank' => 100,
                    ],
                    [
                        'lottery_id' => $lottery_id,
                        'name' => 'Cssc591',
                        'ident' => 'Cssc591\\Jsxystpk10',
                        'url' => 'http://api01.cssc591.com',
                        'status' => 't',
                        'rank' => 100,
                    ],
                ]);
            }

            DB::commit();
            $this->info('彩种添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //TCG体育
    private function _tcgbet()
    {
        DB::beginTransaction();
        try {
            //创建third_game_tcg_bet表
            (new \CreateTableThirdGameTcgBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('TCGCR', 'TCG体育存入', 1, 2, 0, 3, 'TCG体育存入'),
                ('TCGTQ', 'TCG体育提取', 1, 1, 0, 3, 'TCG体育提取'),
                ('TCGFS', 'TCG体育返水', 1, 1, 0, 3, 'TCG体育返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('Tcg', 'TCG体育', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'Tcg'),
                    'CiaTcg',
                    'TCG体育(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            //throw $e;
            $this->info("TCG体育SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    private function _create_table_report_daily_lottery_bonus()
    {
        DB::beginTransaction();
        try {
            //创建third_game_tcg_bet表
            (new \CreateTableReportDailyLotteryBonus())->up();
            DB::commit();
            $this->info('平台每日报表创建成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("平台每日报表创建失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function _k3_pk_method()
    {
        DB::beginTransaction();
        try {
            DB::statement("
            INSERT INTO \"public\".\"lottery_method\" (\"id\", \"parent_id\", \"lottery_method_category_id\", \"ident\", \"name\", \"draw_rule\", \"lock_table_name\", \"lock_init_function\", \"modes\", \"prize_level\", \"prize_level_name\", \"layout\", \"sort\", \"status\", \"max_bet_num\") VALUES
            ('152106000', '152100000', '52', 'k3_pk_hezhi_01', '和值', '[]', '', '', '[]', '[]', '[]', '{}', '0', 't', '0'),
            ('152106001', '152106000', '52', 'k3_pk_hezhi_daxiaodanshuang', '大小单双', '{}', '', '', '[9]', '[2]', '[\"大小单双\"]', '{}', '0', 't', '0');
");
            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    // 添加五分快三、三分快三两种彩种
    private function _add_tow_lottery()
    {
        DB::beginTransaction();
        try {
            $lottery_ident_arr = ['z5fk3', 'z3fk3', 'zffk3'];
            $lottery = [
                'z5fk3' => [
                    'lottery_category_id' => 1,
                    'lottery_method_category_id' => 50,
                    'ident' => 'z5fk3',
                    'name' => '五分快三',
                    'official_url' => '',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "00", "end_sale": "0", "drop_time": "0", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "1", "first_end_minute": "05", "first_end_second": "00", "first_start_yesterday": "0"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0200',
                    'min_profit' => '0.0100',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                    'special' => '1',
                    'special_config' => '{"flag": "0", "times": "5", "max_time": "5", "hand_coding": "0", "probability": "10", "request_time": "10", "sleep_seconds": "5", "default_method": ""}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 'f',
                    'cron' => '* * * * *',
                    'deny_user_group' => '[]',
                ],
                'z3fk3' => [
                    'lottery_category_id' => 1,
                    'lottery_method_category_id' => 50,
                    'ident' => 'z3fk3',
                    'name' => '三分快三',
                    'official_url' => '',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "180", "status": "1", "end_hour": "00", "end_sale": "0", "drop_time": "0", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "1", "first_end_minute": "03", "first_end_second": "00", "first_start_yesterday": "0"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0200',
                    'min_profit' => '0.0100',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                    'special' => '1',
                    'special_config' => '{"flag": "0", "times": "5", "max_time": "5", "hand_coding": "0", "probability": "10", "request_time": "10", "sleep_seconds": "5", "default_method": ""}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 'f',
                    'cron' => '* * * * *',
                    'deny_user_group' => '[]',
                ],
                'zffk3' => [
                    'lottery_category_id' => 1,
                    'lottery_method_category_id' => 50,
                    'ident' => 'zffk3',
                    'name' => '皇冠快三',
                    'official_url' => '',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "0", "drop_time": "0", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "1", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0200',
                    'min_profit' => '0.0100',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                    'special' => '1',
                    'special_config' => '{"flag": "0", "times": "5", "max_time": "5", "hand_coding": "0", "probability": "10", "request_time": "10", "sleep_seconds": "5", "default_method": ""}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 'f',
                    'cron' => '* * * * *',
                    'deny_user_group' => '[]',
                ]
            ];
            $check = \Service\Models\Lottery::whereIn('ident', $lottery_ident_arr)->get()->keyBy('ident');
            foreach ($lottery as $key => $val) {
                if (isset($check[$key])) {
                    $this->info($val['ident'] . '彩种已存在');
                    continue;
                }
                $lottery_id = \Service\Models\Lottery::insertGetId($lottery[$key]);
                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $lottery_id,
                        'name' => 'LocalApi',
                        'ident' => 'LocalApi',
                        'url' => 'http://www.localapi.cn',
                        'status' => 't',
                        'rank' => 100,
                    ],
                ]);
                $this->info($val['ident'] . '彩种插入成功');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _delete_lottery()
    {
        $app_ident = get_config('app_ident', '');
        if ($app_ident === 'fz') {
            $this->info('方舟不用删除');
            return '';
        }
        $lottery_idents = ['hnk3', 'jlk3', 'gxk3'];
        $rows = \Service\Models\Lottery::whereIn('ident', $lottery_idents)->get();
        $lottery_ids = [];
        foreach ($rows as $row) {
            $lottery_ids[] = $row->id;
        }
        DB::beginTransaction();
        try {
            \Service\Models\Drawsource::whereIn('lottery_id', $lottery_ids)->delete();
            \Service\Models\Lottery::whereIn('ident', $lottery_idents)->delete();
            DB::commit();
            $this->info('删除彩种成功');
        } catch (\Exception $e) {
            $this->info('删除彩种失败');
            DB::rollBack();
            throw $e;
        }
    }

    private function _gjtx_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'gjtx5fssc',
                'name' => '国际腾讯5分彩',
                'official_url' => 'http://www.qq.international/',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "00", "end_sale": "15", "drop_time": "15", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "05", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'gjtx10fssc',
                'name' => '国际腾讯10分彩',
                'official_url' => 'http://www.qq.international/',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "600", "status": "1", "end_hour": "00", "end_sale": "15", "drop_time": "15", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "10", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'gjtxpk10',
                'name' => '国际腾讯赛车PK10',
                'official_url' => 'http://www.qq.international/',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "15", "drop_time": "15", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "10", "sprepeat": "", "end_number": "10", "startrepeat": "", "spend_number": "", "start_number": "0 1", "spstart_number": ""}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* * * * *',
            ],
        ];
        $drawsource_datas = [
            'gjtx5fssc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => '国际腾讯',
                    'ident' => 'Idl01\\Gjtx5fssc',
                    'url' => 'http://www.qq.international',
                    'status' => 't',
                    'rank' => 100,
                ],
            ],
            'gjtx10fssc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => '国际腾讯',
                    'ident' => 'Idl01\\Gjtx10fssc',
                    'url' => 'http://www.qq.international',
                    'status' => 't',
                    'rank' => 100,
                ],
            ],
            'gjtxpk10' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => '国际腾讯',
                    'ident' => 'Idl01\\Gjtxpk10',
                    'url' => 'http://www.qq.international',
                    'status' => 't',
                    'rank' => 100,
                ],
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    private function _addPermissionClearTeamThirdRebate()
    {
        $row = DB::table('admin_role_permissions')->where('name', '用户管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/clearteamthirdrebate',
                'name' => '清理团队三方返水',
            ]]);
        $this->info('清理团队三方返水插入成功');
    }

    private function _jackpot()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableJackpotPeriod())->up();
            (new \CreateTableJackpotUserCode())->up();
            (new \CreateTableJackpotPeriodCodeData())->up();
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    private function _jackpotActivity()
    {
        DB::beginTransaction();
        try {
            $check = DB::table('activity')
                ->where('ident', 'jackpot')
                ->first();
            if (empty($check)) {
                DB::table('activity')->insert([
                    'ident' => 'jackpot',
                    'name' => '幸运大奖池',
                    'config' => '{"hide": "0", "config": [[[{"title": "奖池占销量总额比例(%)", "value": "0.2"},{"title": "继承上期奖池比例(%)", "value": "52"}]]]}',
                    'config_ui' => '{}',
                    'summary' => '投注抽大奖',
                    'description' => '',
                    'start_time' => date('Y-m-d 00:00:00'),
                    'end_time' => date('Y-m-d 23:59:59'),
                    'draw_method' => 1,
                    'status' => 0 //默认禁用
                ]);
            } else {
                $this->info('活动 jackpot 已存在');
            }

            $check = DB::table('admin_role_permissions')
                ->where('rule', 'activity/jackpot')
                ->first();
            if (empty($check)) {
                $parent_id = DB::table('admin_role_permissions')
                    ->where('rule', 'activity')
                    ->value('id');
                if ($parent_id) {
                    DB::table('admin_role_permissions')->insert([
                        'parent_id' => $parent_id,
                        'rule' => 'activity/jackpot',
                        'name' => '幸运大奖池管理',
                    ]);
                } else {
                    $this->info('菜单 activity 不存在');
                }
            } else {
                $this->info('菜单 activity/jackpot 已存在');
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    private function _coupon_edit_config()
    {
        $coupon_config_parent_id = \Service\Models\Config::where('key', 'coupon')->value('id');
        if (empty($coupon_config_parent_id)) {
            DB::rollBack();
            $this->info('失败：红包雨配置 coupon 不存在');
            return;
        }
        $configs = [
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '领取红包充值金额',
                'key' => 'coupon_limit_deposit',
                'value' => '0',
                'description' => '0为不限制',
            ],
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '领取红包充值金额是否统计昨天',
                'key' => 'coupon_limit_deposit_is_yesterday',
                'value' => '1',
                'description' => '1昨天，0今天',
            ],
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '领取红包投注金额（汇总）',
                'key' => 'coupon_limit_bet',
                'value' => '0',
                'description' => '0为不限制',
            ],
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '领取红包投注金额（汇总）是否统计昨天',
                'key' => 'coupon_limit_bet_is_yesterday',
                'value' => '1',
                'description' => '1昨天，0今天',
            ]
        ];
        DB::beginTransaction();
        try {
            $config_key2row = \Service\Models\Config::whereIn('key', ['coupon_limit_deposit', 'coupon_limit_deposit_is_yesterday', 'coupon_limit_bet', 'coupon_limit_bet_is_yesterday'])->get()->keyBy('key');
            foreach ($configs as $config) {
                if (isset($config_key2row[$config['key']])) {
                    \Service\Models\Config::where('key', $config['key'])->update([
                        'title' => $config['title']
                    ]);
                } else {
                    \Service\Models\Config::insert($config);
                }
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    private function _sxyftpk10_to_jsxyftpk10()
    {
        DB::beginTransaction();
        try {
            $row = \Service\Models\Lottery::where('ident', 'sxyftpk10')->first();
            if ($row) {
                $row->ident = 'jsxyftpk10';
                $row->official_url = 'https://www.lucky-188.com';
                $row->issue_set = '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "04", "end_sale": "30", "drop_time": "30", "end_minute": "04", "end_second": "00", "start_hour": "04", "start_minute": "04", "start_second": "00", "first_end_hour": "13", "input_code_time": "8", "first_end_minute": "09", "first_end_second": "00", "first_start_yesterday": "0"}]';
                $row->save();
                $source = \Service\Models\Drawsource::where('ident', 'Kaijiang\\Sxyftpk10')
                    ->where('lottery_id', $row->id)
                    ->first();
                if ($source) {
                    $source->name = 'Lucky188';
                    $source->ident = 'Lucky188\\Jsxyftpk10';
                    $source->url = 'https://www.lucky-188.com';
                    $source->save();
                }
                $this->info('sxyftpk10 to jsxyftpk10 成功');
            } else {
                $this->info('sxyftpk10 彩种不存在');
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    private function _add_coupon_config_and_create_table()
    {
        DB::beginTransaction();
        try {
            //添加表&&添加菜单项
            if (!Schema::hasTable('coupon')) {
                //执行建表&&添加菜单项逻辑
                (new \CreateTableCoupon())->up();
            }
            if (!Schema::hasTable('coupon_split')) {
                //执行建表逻辑
                (new \CreateTableCouponSplit())->up();
            }
            //添加配置项
            $coupon_config_parent_id = DB::table('config')->insertGetId(['key' => 'coupon', 'value' => '#', 'title' => '红包雨配置', 'parent_id' => '0', 'description' => '红包雨配置']);
            $configs = [
                [
                    'parent_id' => $coupon_config_parent_id,
                    'title' => '是否开启',
                    'key' => 'coupon_enabled',
                    'value' => '0',
                    'description' => '关闭这前台领不了红包',
                ],
                [
                    'parent_id' => $coupon_config_parent_id,
                    'title' => '是否开启小时定时红包',
                    'key' => 'hourly_coupon_enabled',
                    'value' => '0',
                    'description' => '1：计划任务启用 0：计划任务关闭',
                ],
                [
                    'parent_id' => $coupon_config_parent_id,
                    'title' => '领取红包充值金额',
                    'key' => 'coupon_limit_deposit',
                    'value' => '0',
                    'description' => '0为不限制',
                ],
                [
                    'parent_id' => $coupon_config_parent_id,
                    'title' => '领取红包充值金额是否统计昨天',
                    'key' => 'coupon_limit_deposit_is_yesterday',
                    'value' => '0',
                    'description' => '1昨天，0今天',
                ],
                [
                    'parent_id' => $coupon_config_parent_id,
                    'title' => '领取红包投注金额（汇总）',
                    'key' => 'coupon_limit_bet',
                    'value' => '0',
                    'description' => '0为不限制',
                ],
                [
                    'parent_id' => $coupon_config_parent_id,
                    'title' => '领取红包投注金额（汇总）是否统计昨天',
                    'key' => 'coupon_limit_bet_is_yesterday',
                    'value' => '0',
                    'description' => '1昨天，0今天',
                ]
            ];
            DB::table('config')->insert($configs);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    private function __add_transfer_to_parent_config()
    {
        //创建账变
        $order_types = [
            ['ident' => 'XSJZZ', 'name' => '向上级转账', 'display' => '1', 'operation' => '2', 'description' => '向上级转账'],
            ['ident' => 'CXJZR', 'name' => '从下级转入', 'display' => '1', 'operation' => '1', 'description' => '从下级转入'],
        ];
        //创建权限
        //后台菜单
        $parent = DB::table('admin_role_permissions')->where('rule', 'user')->first(['id']);
        if (empty($parent)) {
            $this->info('Transfer_to_parent:admin_role_permissions 表没有 user 菜单');
            DB::rollBack();
            return '';
        }
        $id = $parent->id;
        $permission = [
            [
                'parent_id' => $id,
                'rule' => 'user/allowtransfertoparent',
                'name' => '向上级转账',
            ]
        ];


        DB::beginTransaction();
        try {
            DB::table('order_type')->insert($order_types);
            DB::table('admin_role_permissions')->insert($permission);
            $this->info('执行成功');
            DB::commit();
        } catch (\Exception $e) {
            $this->info('执行失败' . $e->getMessage());
            DB::rollBack();
        }
    }

    private function _ssc_xlh()
    {
        $data = [
            //新龙虎 101400000,
            ['id' => '101400000', 'parent_id' => '0', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_01', 'name' => '新龙虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //新龙虎 101401000,
            ['id' => '101401000', 'parent_id' => '101400000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_02', 'name' => '新龙虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401001', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_wanqian', 'name' => '万千', 'draw_rule' => '{"is_sum": 0, "position": "0,1", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,1', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从万位、千位上选择一个形态组成一注。",
    "help": "根据万位、千位号码数值比大小，万位号码大于千位号码为龙，万位号码小于千位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于千位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401002', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_wanbai', 'name' => '万百', 'draw_rule' => '{"is_sum": 0, "position": "0,2", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,2', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从万位、百位上选择一个形态组成一注。",
    "help": "根据万位、百位号码数值比大小，万位号码大于百位号码为龙，万位号码小于百位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于百位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401003', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_wanshi', 'name' => '万十', 'draw_rule' => '{"is_sum": 0, "position": "0,3", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,3', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从万位、十位上选择一个形态组成一注。",
    "help": "根据万位、十位号码数值比大小，万位号码大于十位号码为龙，万位号码小于十位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于十位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401004', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_wange', 'name' => '万个', 'draw_rule' => '{"is_sum": 0, "position": "0,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,4', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从万位、个位上选择一个形态组成一注。",
    "help": "根据万位、个位号码数值比大小，万位号码大于个位号码为龙，万位号码小于个位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401005', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_qianbai', 'name' => '千百', 'draw_rule' => '{"is_sum": 0, "position": "1,2", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 1}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,5', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从千位、百位上选择一个形态组成一注。",
    "help": "根据千位、百位号码数值比大小，千位号码大于百位号码为龙，千位号码小于百位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码千位大于百位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401006', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_qianshi', 'name' => '千十', 'draw_rule' => '{"is_sum": 0, "position": "1,3", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 1}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,6', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从千位、十位上选择一个形态组成一注。",
    "help": "根据千位、十位号码数值比大小，千位号码大于十位号码为龙，千位号码小于十位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码千位大于十位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401007', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_qiange', 'name' => '千个', 'draw_rule' => '{"is_sum": 0, "position": "1,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 1}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,7', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从千位、个位上选择一个形态组成一注。",
    "help": "根据千位、个位号码数值比大小，千位号码大于个位号码为龙，千位号码小于个位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码千位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401008', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_baishi', 'name' => '百十', 'draw_rule' => '{"is_sum": 0, "position": "2,3", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 2}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,8', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从百位、十位上选择一个形态组成一注。",
    "help": "根据百位、十位号码数值比大小，百位号码大于十位号码为龙，百位号码小于十位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码百位大于十位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401009', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_baige', 'name' => '百个', 'draw_rule' => '{"is_sum": 0, "position": "2,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 2}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,9', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从百位、个位上选择一个形态组成一注。",
    "help": "根据百位、个位号码数值比大小，百位号码大于个位号码为龙，百位号码小于个位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码百位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '101401010', 'parent_id' => '101401000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_xlh_shige', 'name' => '十个', 'draw_rule' => '{"is_sum": 0, "position": "3,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 3}', 'lock_table_name' => 'lock_lhh', 'lock_init_function' => 'initNumberTypeLHH,10', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["新龙虎"]', 'layout' => '
{
    "desc": "从十位、个位上选择一个形态组成一注。",
    "help": "根据十位、个位号码数值比大小，十位号码大于个位号码为龙，十位号码小于个位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码十位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
        ];

        DB::beginTransaction();
        try {
            DB::table('lottery_method')->insert($data);
            $this->info('执行成功');
            DB::commit();
        } catch (\Exception $e) {
            $this->info('执行失败' . $e->getMessage());
            DB::rollBack();
        }
    }

    //增加新开用户是否默认开启下级充值权限配置
    private function _add_config_sub_recharge()
    {
        try {
            DB::beginTransaction();
            if ($sub_recharge_status = DB::table('config')->where('key', 'sub_recharge_status')->first()) {
                $this->info('配置 sub_recharge_status 已经存在！');
            } else {
                //添加配置值
                $this->info('开始添加新开用户是否开启下级充值权限配置');
                if ($config_operation = DB::table('config')->where('key', 'operation')->first()) {
                    $sub_recharge_status = DB::table('config')->insertGetId([
                        'parent_id' => $config_operation->id,
                        'title' => '新开用户是否默认开启下级充值权限',
                        'key' => 'sub_recharge_status',
                        'value' => '0',
                        'input_type' => '1',
                        'input_option' => '0|关闭,1|允许直属下级,2|允许所有下级',
                        'description' => '0-关闭,1-允许直属下级,2-允许所有下级',
                    ]);
                    $this->info('配置插入成功，新增ID => ' . $sub_recharge_status);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('回滚');
            $this->info($e->getMessage());
            throw $e;
        }
    }


    private function _hl30s_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'hl30s',
                'name' => '欢乐30秒',
                'official_url' => 'http://brssys.com/results.html',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "30", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "00", "first_end_second": "30", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{"flag": "0", "times": "5", "max_time": "5", "hand_coding": "0", "probability": "10", "request_time": "10", "sleep_seconds": "5", "default_method": ""}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'hl30s' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => '新加坡奖源',
                    'ident' => 'Singapore\\Hl30s',
                    'url' => 'http://infonetwork.cn',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => '新加坡奖源_备用',
                    'ident' => 'Singapore2\\Hl30s',
                    'url' => 'http://brssys.com/',
                    'status' => 't',
                    'rank' => 100,
                ],
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 奇趣腾讯三分彩
    private function _qiqutx3fssc_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'qiqutx3fssc',
                'name' => '奇趣腾讯三分彩',
                'official_url' => 'http://www.77tj.org/tencent',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "180", "status": "1", "end_hour": "00", "end_sale": "4", "drop_time": "4", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "03", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'qiqutx3fssc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => '奇趣官网',
                    'ident' => 'Qiqu\\Qiqutx3fssc',
                    'url' => 'http://77tj.org',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => '奇趣官网_备用',
                    'ident' => 'Qiqucom\\Qiqutx3fssc',
                    'url' => 'http://77tj.com',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => 'WIN奖源',
                    'ident' => 'Wincenter\\Qiqutx3fssc',
                    'url' => 'http://www.gg-win.com',
                    'status' => 't',
                    'rank' => 100,
                ],
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 福彩快乐8
    private function _fckl8_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Keno']->id,
                'lottery_method_category_id' => $method_categorys['kl8']->id,
                'ident' => 'fckl8',
                'name' => '福彩快乐8',
                'official_url' => 'http://www.fhlm.com',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "40000", "status": "1", "end_hour": "21", "end_sale": "90", "drop_time": "90", "end_minute": "30", "end_second": "00", "start_hour": "07", "start_minute": "00", "start_second": "00", "first_end_hour": "21", "input_code_time": "360", "first_end_minute": "30", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0010',
                'min_profit' => '0.0230',
                'issue_rule' => '{"day": "1", "rule": "Y[n3]", "year": "0", "month": "1"}',
                'number_rule' => '{"len": "20", "end_number": "80", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* 0,9-23 * * *',
            ]
        ];
        $drawsource_datas = [
            'fckl8' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 黑龙江时时彩
    private function _hljssc_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'hljssc',
                'name' => '黑龙江时时彩',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "1200", "status": "1", "end_hour": "22", "end_sale": "30", "drop_time": "30", "end_minute": "40", "end_second": "00", "start_hour": "22", "start_minute": "04", "start_second": "00", "first_end_hour": "09", "input_code_time": "30", "first_end_minute": "00", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "1", "rule": "[n7]", "year": "1", "month": "1"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* 0,9-23 * * *',
            ]
        ];
        $drawsource_datas = [
            'hljssc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 海南11选5
    private function _hn11x5_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['11x5']->id,
                'ident' => 'hn11x5',
                'name' => '海南11选5',
                'official_url' => 'https://hainancp.net',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "1200", "status": "1", "end_hour": "00", "end_sale": "120", "drop_time": "120", "end_minute": "10", "end_second": "00", "start_hour": "00", "start_minute": "10", "start_second": "00", "first_end_hour": "09", "input_code_time": "30", "first_end_minute": "30", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n2]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "sprepeat": "", "end_number": "11", "startrepeat": "", "spend_number": "", "start_number": "01", "spstart_number": ""}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* 0,9-23 * * *',
            ]
        ];
        $drawsource_datas = [
            'hn11x5' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 谷歌分分彩
    private function _ggffc_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'ggffc',
                'name' => '谷歌分分彩',
                'official_url' => 'https://googleauthenticator.org',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'ggffc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 谷歌二分彩
    private function _ggffc2f_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'ggffc2f',
                'name' => '谷歌二分彩',
                'official_url' => 'https://googleauthenticator.org',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "120", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "02", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'ggffc2f' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 谷歌三分彩
    private function _ggffc3f_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'ggffc3f',
                'name' => '谷歌三分彩',
                'official_url' => 'https://googleauthenticator.org',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "180", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "03", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'ggffc3f' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 谷歌五分彩
    private function _ggffc5f_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'ggffc5f',
                'name' => '谷歌五分彩',
                'official_url' => 'https://googleauthenticator.org',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "05", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'ggffc5f' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 谷歌十分彩
    private function _ggffc10f_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'ggffc10f',
                'name' => '谷歌十分彩',
                'official_url' => 'https://googleauthenticator.org',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "600", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "10", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'ggffc10f' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 阿里云分分彩
    private function _aliyunffc_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'aliyunffc',
                'name' => '阿里云分分彩',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'aliyunffc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // WIN 腾讯分分彩
    private function _tx1fc_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'tx1fc',
                'name' => '腾讯分分彩1',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'tx1fc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 极速时时彩
    private function _jsssc_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'jsssc',
                'name' => '极速时时彩',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'jsssc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 澳洲PK10
    private function _azpk10_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'azpk10',
                'name' => '澳洲PK10',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "00", "end_sale": "10", "drop_time": "10", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "05", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'azpk10' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 极速赛马
    private function _jssm_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'jssm',
                'name' => '极速赛马',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'jssm' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 极速飞艇
    private function _jsft_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'jsft',
                'name' => '极速飞艇',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'jsft' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 阿里云五分彩
    private function _aliyun5fc_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'aliyun5fc',
                'name' => '阿里云五分彩',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "0", "first_end_minute": "05", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'aliyun5fc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    private function addRemarkColumnForFloatWage()
    {
        Schema::table('float_wages', function (Blueprint $table) {
            $table->jsonb('remark')->default('{}')->comment('备注');
        });
    }

    //增加云挂机相关配置
    private function _add_config_cloud_plan()
    {
        try {
            DB::beginTransaction();

            if ($cloud_plan = DB::table('config')->where('key', 'cloud_plan')->first()) {
                $this->info('配置cloud_plan存在跳过！');
            } else {
                //添加配置值
                $this->info('开始添加云挂机软件url配置');
                if ($config_operation = DB::table('config')->where('key', 'client')->first()) {
                    $cloud_plan = DB::table('config')->insertGetId([
                        'parent_id' => $config_operation->id,
                        'title' => '云挂机软件登陆地址',
                        'key' => 'cloud_plan',
                        'value' => 'http://guaji.ruanjian.com/api/third-login',
                    ]);
                    $this->info('配置插入成功，新增ID => ' . $cloud_plan);
                }
            }

            if ($cloud_plan = DB::table('config')->where('key', 'cloud_plan_salt')->first()) {
                $this->info('配置cloud_plan_salt已经存在跳过！');
            } else {
                //添加配置值
                $this->info('开始添加云挂机软件加密salt配置');
                if ($config_operation = DB::table('config')->where('key', 'client')->first()) {
                    $cloud_plan = DB::table('config')->insertGetId([
                        'parent_id' => $config_operation->id,
                        'title' => '云挂机加密salt',
                        'key' => 'cloud_plan_salt',
                        'value' => 'ae125efkkf455ferfkny6oxi8',
                    ]);
                    $this->info('配置插入成功，新增ID => ' . $cloud_plan);
                }
            }

            if ($cloud_plan = DB::table('config')->where('key', 'cloud_plan_platform')->first()) {
                $this->info('配置cloud_plan_platform已经存在跳过！');
            } else {
                //添加配置值
                $this->info('开始添加云挂机cloud_plan_platform配置');
                if ($config_operation = DB::table('config')->where('key', 'client')->first()) {
                    $cloud_plan = DB::table('config')->insertGetId([
                        'parent_id' => $config_operation->id,
                        'title' => '云挂机商户平台名称',
                        'key' => 'cloud_plan_platform',
                        'value' => 'UED',
                    ]);
                    $this->info('配置插入成功，新增ID => ' . $cloud_plan);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('回滚');
            $this->info($e->getMessage());
            throw $e;
        }
    }

    //新增会员身分转为代理权限配置
    private function _add_change_to_agent()
    {
        try {
            //后台菜单
            DB::beginTransaction();
            $parent = DB::table('admin_role_permissions')->where('rule', 'user')->first(['id']);

            if (empty($parent)) {
                $this->info('Transfer_to_parent:admin_role_permissions 表没有 user 菜单');
                DB::rollBack();
                return '';
            }

            $permission = [
                [
                    'parent_id' => $parent->id,
                    'icon' => '',
                    'rule' => 'user/changetoagent',
                    'name' => '身份变更',
                ]
            ];

            DB::table('admin_role_permissions')->insert($permission);
            $this->info('执行成功');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('回滚');
            $this->info($e->getMessage());
            throw $e;
        }
    }

    //增加谷歌验证器参数配置
    private function _add_config_google()
    {
        try {
            DB::beginTransaction();
            $row = DB::table('config')->where('key', 'client')->first();
            DB::table('config')->insert([
                [
                    'parent_id' => $row->id,
                    'title' => '谷歌验证器 使用帮助',
                    'key' => 'google_help_url',
                    'value' => 'https://support.google.com/accounts/answer/1066447',
                    'description' => '',
                ],
                [
                    'parent_id' => $row->id,
                    'title' => '谷歌验证器 Android版',
                    'key' => 'google_android_url',
                    'value' => 'https://shouji.baidu.com/software/22417419.html',
                    'description' => '',
                ],
                [
                    'parent_id' => $row->id,
                    'title' => '谷歌验证器 IOS版',
                    'key' => 'google_ios_url',
                    'value' => 'https://itunes.apple.com/cn/app/google-authenticator/id388497605?mt=8',
                    'description' => '',
                ]
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function _add_wage_config_for_member_role()
    {
        try {
            DB::beginTransaction();
            $row = DB::table('config')->where('key', 'dailywage_config')->first();
            DB::table('config')->insert([
                [
                    'parent_id' => $row->id,
                    'title' => '用户角色会员是否禁止签订日工资',
                    'key' => 'forbid_member_wage_sign',
                    'value' => '0',
                    'description' => '0允许，1禁止',
                ],
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //新增同IP统计表页面
    private function _add_user_login_ip()
    {
        try {
            //后台菜单
            DB::beginTransaction();
            $parent = DB::table('admin_role_permissions')->where('rule', 'user')->first(['id']);

            if (empty($parent)) {
                $this->info('admin_role_permissions 表没有 user 菜单');
                DB::rollBack();
                return '';
            }

            $permission = [
                [
                    'parent_id' => $parent->id,
                    'icon' => '',
                    'rule' => 'userloginip/index',
                    'name' => '同ip统计表',
                ]
            ];

            DB::table('admin_role_permissions')->insert($permission);
            $this->info('执行成功');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('回滚');
            $this->info($e->getMessage());
            throw $e;
        }
    }

    //新增同IP统计表详情页
    private function _add_user_login_ip_detail()
    {
        try {
            //后台菜单
            DB::beginTransaction();
            $parent = DB::table('admin_role_permissions')->where('rule', 'user')->first(['id']);

            if (empty($parent)) {
                $this->info('admin_role_permissions 表没有 user 菜单');
                DB::rollBack();
                return '';
            }

            $permission = [
                [
                    'parent_id' => $parent->id,
                    'icon' => '',
                    'rule' => 'userloginip/detail',
                    'name' => '同ip统计表详情页',
                ]
            ];

            DB::table('admin_role_permissions')->insert($permission);
            $this->info('执行成功');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('回滚');
            $this->info($e->getMessage());
            throw $e;
        }
    }

    //添加团队日统计报表
    private function add_report_team_total_daily()
    {
        DB::beginTransaction();
        try {
            //创建report_team_total_daily表
            (new \CreateTableReportTeamTotalDaily())->up();
            DB::commit();
            $this->info('团队每日报表创建成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("团队每日报表创建失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function add_config_henei_kill_users()
    {
        $row = \Service\Models\Config::where('key', 'henei_codes_enabled')->first();
        if (empty($row)) {
            $this->info('henei_codes_enabled 配置不存在');
            return;
        }
        DB::beginTransaction();
        try {
            $data = [
                'parent_id' => $row->parent_id,
                'title' => '联合奖源彩种必杀用户名',
                'key' => 'henei_kill_users',
                'value' => '',
                'description' => '多个用户名使用英文逗号,分隔',
            ];
            \Service\Models\Config::insert($data);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function add_dongjiejine_ordertype()
    {
        DB::beginTransaction();
        try {
            $data = [
                [
                    'name' => '冻结金额增加',
                    'ident' => 'DJJEZJ',
                    'display' => 0,
                    'operation' => 0,
                    'hold_operation' => 1,
                    'category' => 2,
                    'description' => '冻结金额增加',
                ],
                [
                    'name' => '冻结金额扣除',
                    'ident' => 'DJJEKC',
                    'display' => 0,
                    'operation' => 0,
                    'hold_operation' => 2,
                    'category' => 2,
                    'description' => '冻结金额扣除',
                ],
            ];
            \Service\Models\OrderType::insert($data);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加异常盈利扣减账变类型
    private function add_ycylkj_ordertype()
    {
        $data = [
            [
                'name' => '异常盈利扣减',
                'ident' => 'YCYLKJ',
                'display' => 1,
                'operation' => 2,
                'hold_operation' => 0,
                'category' => 2,
                'description' => '异常盈利扣减',
            ],
        ];

        DB::beginTransaction();
        try {
            \Service\Models\OrderType::insert($data);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //添加审核管理特殊配置表
    private function add_table_risk_refused_reason()
    {
        DB::beginTransaction();
        try {
            //创建special表
            (new \CreateTableRiskRefusedReason())->up();
            DB::commit();
            $this->info('risk_refused_reason表创建成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("risk_refused_reason表创建失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加飞单系统
    private function add_fly_system()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableFlySystemConfig())->up();  //增加飞单配置表

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //删除飞单配置表的约束
    private function fly_system_config_unique()
    {
        DB::beginTransaction();
        try {
            DB::unprepared('alter table fly_system_config drop constraint fly_system_config_lottery_idents_unique;');

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //添加乐彩轩的充值送礼金活动
    private function add_activity_recharge10pct()
    {
        DB::beginTransaction();
        try {
            $data = [
                'ident' => 'recharge10pct',
                'name' => '充值送彩金活动',
                'config' => '{"hide": "0", "config": [[[{"title": "充值返百分比", "value": "10"}], [{"title": "最低礼金", "value": "100"}], [{"title":  "最高礼金","value": "8888"}],[{"title": "指定彩种英文标识用,号分割", "value": "cxg360ffc,cxg3605fc,hne1fssc,hne5fssc,jsxyftpk10"}],[{"title": "彩票流水倍数", "value": "8"}]]]}',
                'config_ui' => '{}',
                'summary' => '投注360分分彩、360五分彩、河内分分彩、河内五分彩、幸运飞艇彩种的用户，均可参与充值送彩金活动',
                'description' => '<p>活动规则介绍内容:</p>
                                <p>1.活动对象：本平台所有用户</p>
                                <p>2.活动派发条件：前一日有进行充值，消费(本金+彩金)的8倍流水方可满足领取条件</p>
                                <p>3.活动奖励：前一日充值总额的10%（奖金最低100元，最高8888元）</p>
                                <p>4.活动限制：活动只限对应彩种，不限玩法，用户投注注数不可大于该玩法的总注数的90%（比如：一星不超过9注，二星不超过90注，三星不超过900注，四星不超过9000注）</p>
                                <p>&nbsp;&nbsp;限制的彩种：360分分彩、360五分彩、河内分分彩、河内五分彩、幸运飞艇</p>
                                <p>5.领取方式：达成条件后，于活动页面点击领取</p>
                                <p>6.同一账户，同一IP，同一电脑，每日只允许参与一次</p>
                                <p>7.本活动只欢迎真实玩家，拒绝白菜党，若发现有用户运用此活动进行套利行为，风控部有权将其账号进行冻结（连同本金）</p>
                                <p>8.此活动最终解释权归乐彩轩运营部活动组所有</p>
                                ',
                'start_time' => (string)Carbon::today()->startOfDay(),
                'end_time' => (string)Carbon::today()->endOfDay(),
                'draw_method' => 0,
                'status' => 0
            ];
            \Service\Models\Activity::insert($data);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    /**
     * 乐彩轩2添加银行卡姓名字段
     */
    private function add_account_name_guest_book()
    {
        DB::beginTransaction();
        try {
            Schema::table('guestbook', function (Blueprint $table) {
                $table->string('account_name', 32)->default('');
            });
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加留言表
    private function add_table_guest_book()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableGuestbook())->up();  //增加留言表

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加 USDT的充值汇率 参数配置
    private function add_usdt_config()
    {
        $row = DB::table('config')->where('key', 'deposit')
            ->where('parent_id', 0)
            ->first();

        DB::beginTransaction();
        try {
            DB::table('config')->insert([
                [
                    'parent_id' => $row->id,
                    'title' => 'USDT的充值汇率',
                    'key' => 'usdt_deposit_exchange_rate',
                    'value' => '0',
                    'description' => '例：填入700，意思为1元人民币＝700U币',
                ]
            ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function _fix_yicai2_user_banks_id()
    {
        $yicai1_banks = [
            1 => 'ICBC', 2 => 'CCB', 3 => 'BOCOM', 4 => 'tenpay', 5 => 'CMB', 6 => 'CMBC',
            7 => 'ABC', 8 => 'BOC', 9 => 'SPDB', 10 => 'PAB', 11 => 'CIB', 12 => 'CNCB',
            13 => 'HXB', 14 => 'CEB', 15 => 'PSBC', 16 => 'EGB', 17 => 'CZBANK', 18 => 'CBHB',
            19 => 'HSB', 20 => 'SNCB', 21 => 'KEB', 22 => 'WOORI', 23 => 'SHB', 24 => 'IBK',
            25 => 'HNB', 26 => 'GDB', 34 => 'GRC',
        ];

        $yicai2_banks = \Service\Models\Bank::get();
        $bank_id_map = [];
        foreach ($yicai2_banks as $key => $value) {
            $bank_id_map[$value->ident] = $value->id;
        }

        $users_id = \Service\Models\User::where('username', 'like', '1c%')->get()->pluck('id')->toArray();
        DB::beginTransaction();

        try {
            DB::enableQueryLog();
            \Service\Models\UserBanks::whereIn('user_id', $users_id)->chunk(1000, function ($records) use ($bank_id_map, $yicai1_banks) {
                foreach ($records as $record) {

                    $this->info("用户ID：{$record->user_id} 用户银行卡ID修改前user_bank_id：{$record->bank_id}");

                    $record->bank_id = $bank_id_map[$yicai1_banks[$record->bank_id]] ?? 0;

                    $this->info("用户ID：{$record->user_id} 用户银行卡ID修改后user_bank_id：{$record->bank_id}");


                    if ($record->save()) {
                        $this->info("用户ID：{$record->user_id} 修改完成");
                    } else {
                        DB::rollback();
                        $this->info("用户银行卡ID：{$record->user_id} 修改失败");
                    }
                }
            });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加虚拟货币钱包
    private function add_bank_virtual()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableBankVirtual())->up();  //增加虚拟货币银行配置表

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //168幸运飞艇
    private function _xyft168_lottery()
    {
        DB::beginTransaction();
        try {
            $lottery_ident = 'xyft168';
            $check = \Service\Models\Lottery::where('ident', $lottery_ident)->first();
            if (empty($check)) {
                $lottery = [
                    'lottery_category_id' => 2,
                    'lottery_method_category_id' => 70,
                    'ident' => $lottery_ident,
                    'name' => '168幸运飞艇',
                    'official_url' => 'http://luckylottoz.com',
                    'deny_method_ident' => '[]',
                    'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "04", "end_sale": "21", "drop_time": "21", "end_minute": "04", "end_second": "00", "start_hour": "04", "start_minute": "04", "start_second": "00", "first_end_hour": "13", "input_code_time": "4", "first_end_minute": "09", "first_end_second": "00", "first_start_yesterday": "0"}]',
                    'week_cycle' => '127',
                    'min_spread' => '0.0200',
                    'min_profit' => '0.0100',
                    'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                    'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                    'special' => '0',
                    'special_config' => '{}',
                    'closed_time_start' => '2017-01-01 00:00:00',
                    'closed_time_end' => '2017-01-01 00:00:00',
                    'status' => 't',
                    'cron' => '* 0-5,13-23 * * *',
                    'deny_user_group' => '[]',
                ];
                $lottery_id = \Service\Models\Lottery::insertGetId($lottery);

                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $lottery_id,
                        'name' => '168开奖网',
                        'ident' => 'Kai168\\Xyftpk10',
                        'url' => 'http://168kai.com',
                        'status' => 'f',
                        'rank' => 100,
                    ],
                ]);
            }

            DB::commit();
            $this->info('彩种添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //168幸运飞艇增加apollo开奖中心
    private function _xyft168_lottery_apollo()
    {
        DB::beginTransaction();
        try {
            $lottery = \Service\Models\Lottery::where('ident', 'xyft168')->first();
            $draw_source = \Service\Models\Drawsource::where('lottery_id', $lottery->id)->where('ident', 'Apollo\\Common')->first();
            if (empty($draw_source)) {
                //奖源
                \Service\Models\Drawsource::insert([
                    [
                        'lottery_id' => $lottery->id,
                        'name' => 'Apollo奖源',
                        'ident' => 'Apollo\\Common',
                        'url' => 'http://www.gg-apollo.com',
                        'status' => 'f',
                        'rank' => 100,
                    ],
                ]);
            }

            DB::commit();
            $this->info('添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //360飞艇pk10
    private function _xyft360pk10_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'cxg360ffcpk10',
                'name' => '360分分飞艇',
                'official_url' => 'http://tj360.org/',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "1", "drop_time": "1", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "1", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "10", "sprepeat": "", "end_number": "10", "startrepeat": "", "spend_number": "", "start_number": "0 1", "spstart_number": ""}',
                'special' => '0',
                'special_config' => '{"flag": "0", "times": "5", "max_time": "5", "hand_coding": "0", "probability": "10", "request_time": "10", "sleep_seconds": "5", "default_method": ""}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'cxg3605fcpk10',
                'name' => '360五分飞艇',
                'official_url' => 'http://tj360.org/',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "04", "end_sale": "10", "drop_time": "10", "end_minute": "04", "end_second": "00", "start_hour": "04", "start_minute": "04", "start_second": "00", "first_end_hour": "13", "input_code_time": "2", "first_end_minute": "09", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "10", "sprepeat": "", "end_number": "10", "startrepeat": "", "spend_number": "", "start_number": "0 1", "spstart_number": ""}',
                'special' => '0',
                'special_config' => '{"flag": "0", "times": "5", "max_time": "5", "hand_coding": "0", "probability": "10", "request_time": "10", "sleep_seconds": "5", "default_method": ""}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* 0-5,13-23 * * *',
            ],
        ];


        $drawsource_datas = [
            'cxg360ffcpk10' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => 'tj360奖源',
                    'ident' => 'Tj360\\Cxg360ffcpk10',
                    'url' => 'http://tj360.org/',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => 'tj360奖源备用',
                    'ident' => 'Tj360backup\\Cxg360ffcpk10',
                    'url' => 'http://5f6efc48fb12e1185559.304dg.cn/',
                    'status' => 't',
                    'rank' => 100,
                ],
            ],
            'cxg3605fcpk10' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => 'tj360奖源',
                    'ident' => 'Tj360\\Cxg3605fcpk10',
                    'url' => 'http://tj360.org/',
                    'status' => 't',
                    'rank' => 100,
                ],
                [
                    'name' => 'tj360奖源备用',
                    'ident' => 'Tj360backup\\Cxg3605fcpk10',
                    'url' => 'http://5f6efc48fb12e1185559.304dg.cn/',
                    'status' => 't',
                    'rank' => 100,
                ],
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 查询全平台 contract_dividends 栏位 period 为 0 或 null，取得配置 dividend_to_report
    private function get_contract_dividends_period()
    {
        try {
            $count = \Service\Models\ContractDividend::where('period', 0)
                ->orWhereNull('period')
                ->count();
            $this->info('平台: ' . get_config('dividend_type_ident'));
            $this->info('period为0或null 数量: ' . $count);
            $this->info('dividend_to_report 配置值: ' . get_config('dividend_to_report', 0));
        } catch (Exception $e) {
            $this->info('执行失败');
        }
    }

    private function insert_ssc_pk_lhh()
    {
        $data = [
            //龙虎和-万千 102110000,
            ['id' => '102110000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanqian', 'name' => '龙虎万千', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102110001', 'parent_id' => '102110000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanqian_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102110002', 'parent_id' => '102110000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanqian_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102110003', 'parent_id' => '102110000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanqian_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //龙虎和-万百 102111000,
            ['id' => '102111000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanbai', 'name' => '龙虎万百', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102111001', 'parent_id' => '102111000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanbai_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102111002', 'parent_id' => '102111000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanbai_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102111003', 'parent_id' => '102111000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanbai_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //龙虎和-万十 102112000,
            ['id' => '102112000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanshi', 'name' => '龙虎万十', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102112001', 'parent_id' => '102112000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanshi_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102112002', 'parent_id' => '102112000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanshi_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102112003', 'parent_id' => '102112000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wanshi_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //龙虎和-万个 102113000,
            ['id' => '102113000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wange', 'name' => '龙虎万个', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102113001', 'parent_id' => '102113000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wange_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102113002', 'parent_id' => '102113000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wange_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102113003', 'parent_id' => '102113000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_wange_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //龙虎和-千百 102114000,
            ['id' => '102114000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qianbai', 'name' => '龙虎千百', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102114001', 'parent_id' => '102114000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qianbai_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102114002', 'parent_id' => '102114000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qianbai_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102114003', 'parent_id' => '102114000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qianbai_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //龙虎和-千十 102115000,
            ['id' => '102115000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qianshi', 'name' => '龙虎千十', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102115001', 'parent_id' => '102115000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qianshi_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102115002', 'parent_id' => '102115000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qianshi_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102115003', 'parent_id' => '102115000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qianshi_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //龙虎和-千个 102116000,
            ['id' => '102116000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qiange', 'name' => '龙虎千个', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102116001', 'parent_id' => '102116000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qiange_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102116002', 'parent_id' => '102116000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qiange_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102116003', 'parent_id' => '102116000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_qiange_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //龙虎和-百十 102117000,
            ['id' => '102117000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_baishi', 'name' => '龙虎百十', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102117001', 'parent_id' => '102117000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_baishi_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102117002', 'parent_id' => '102117000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_baishi_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102117003', 'parent_id' => '102117000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_baishi_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //龙虎和-百个 102118000,
            ['id' => '102118000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_baige', 'name' => '龙虎百个', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102118001', 'parent_id' => '102118000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_baige_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102118002', 'parent_id' => '102118000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_baige_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102118003', 'parent_id' => '102118000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_baige_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            //龙虎和-十个 102119000,
            ['id' => '102119000', 'parent_id' => '102000000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_shige', 'name' => '龙虎十个', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[]', 'prize_level' => '[]', 'prize_level_name' => '[]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102119001', 'parent_id' => '102119000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_shige_long', 'name' => '龙', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["龙"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102119002', 'parent_id' => '102119000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_shige_hu', 'name' => '虎', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[2.2222222]', 'prize_level_name' => '["虎"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '102119003', 'parent_id' => '102119000', 'lottery_method_category_id' => '12', 'ident' => 'ssc_pk_lhh_shige_he', 'name' => '和', 'draw_rule' => '[]', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[9]', 'prize_level' => '[10]', 'prize_level_name' => '["和"]', 'layout' => '
{}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],

        ];
        DB::beginTransaction();
        try {
            \Service\Models\LotteryMethod::insert($data);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    //添加禁用台湾ip数据
    private function add_taiwan_deny_ip()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableIpFirewall())->addTaiwanDenyIp();

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //检查后台白名单错误
    private function check_admin_ipfirewall()
    {
        $taboo = ['台湾', '臺灣', '台灣', 'TW', 'tw', 'taipei', '台北', '臺北'];

        echo "----开始检查白名单----\n";

        try {
            $rows = \Service\Models\IpFirewall::where('type', 'admin')->get();

            foreach ($rows as $item) {
                foreach ($taboo as $val) {
                    if (strpos($item->remark, $val) !== false) {
                        echo '包含敏感词:' . $item->ip . '    备注:' . $item->remark . "\n";
                    }
                }

                $check = \Service\Models\IpFirewall::where('type', 'admin_black')
                    ->where('ip', '>>=', DB::raw("inet '{$item->ip}'"))
                    ->first();
                if ($check) {
                    echo '包含台湾IP:' . $item->ip . '    备注:' . $item->remark . "\n";
                }
            }
        } catch (\Exception $e) {
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }

        echo "----结束检查白名单----\n";
    }

    //增加提款延迟补偿金活动
    private function add_activity_withdrawaldelay()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableActivityWithdrawalDelay())->up();      //增加表

            $parent_id = DB::table('admin_role_permissions')
                ->where('rule', 'activity')
                ->value('id');

            DB::table('admin_role_permissions')->insert([
                [
                    'parent_id' => $parent_id,
                    'rule' => 'activity/withdrawaldelay',
                    'name' => '提款补偿金列表',
                ],
                [
                    'parent_id' => $parent_id,
                    'rule' => 'activity/withdrawaldelayverify',
                    'name' => '提款补偿金审核',
                ],
            ]);

            $data = [
                'ident' => 'withdrawaldelay',
                'name' => '提款延迟补偿金活动',
                'config' => '{"hide": "0", "config": [[[{"title": "提款延迟(分钟)≥", "value": 30}, {"title": "礼金百分比", "value": 0.07}], [{"title": "提款延迟(分钟)≥", "value": 60}, {"title": "礼金百分比", "value": 0.08}], [{"title": "提款延迟(分钟)≥", "value": 90}, {"title": "礼金百分比", "value": 0.09}], [{"title": "提款延迟(分钟)≥", "value": 120}, {"title": "礼金百分比", "value": 1.00}]]]}',
                'config_ui' => '{}',
                'summary' => '任何一个玩家，提款延迟到账都有奖励',
                'description' => '<p>活动规则:</p>
                                <p>1.参加对象：任何一个玩家，提款延迟到账都有奖励</p>
                                <p>2.以半小时为阶梯，最高奖励提款金额的1%</p>
                                <p>3.奖励内容：</p>
                                <table width="100%">
                                <tr>
                                <th>提款延迟时间</th>
                                <th>提款奖励比例(%)</th>
                                </tr>
                                <tr>
                                <td>≥30分钟</td>
                                <td>0.07</td>
                                </tr>
                                <tr>
                                <td>≥60分钟</td>
                                <td>0.08</td>
                                </tr>
                                <tr>
                                <td>≥90分钟</td>
                                <td>0.09</td>
                                </tr>
                                <tr>
                                <td>≥120分钟</td>
                                <td>1.00</td>
                                </tr>
                                </table>
                                <p>4.此活动最终解释权归运营部活动组所有</p>
                                ',
                'start_time' => (string)Carbon::today()->startOfDay(),
                'end_time' => (string)Carbon::today()->endOfDay(),
                'draw_method' => 4,
                'status' => 0
            ];
            \Service\Models\Activity::insert($data);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //添加api_fetch字段
    private function add_api_fetch()
    {
        try {
            (new \CreateTableBankVirtual())->addApiFetch();
            $this->info('执行成功');
        } catch (\Exception $e) {
            $this->error("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加亿博专用活动:主管充值奖励活动
    private function add_activity_rechargemanage()
    {
        $app_ident = get_config('app_ident', '');
        if ($app_ident !== 'YB') {
            $this->info('这是亿博专用活动');
            return;
        }

        DB::beginTransaction();
        try {
            $data = [
                'ident' => 'yibo_rechargemanage',
                'name' => '亿博专用活动：主管充值奖励活动',
                'config' => '{"hide":"1","config":[[[{"title": "最后固定比例日", "value": "2020-09-29"}]],[[{"title":"当日团队亏损金额(万)","value":"0"},{"title":"当日团队销量(万)","value":"0"},{"title":"有效人数","value":"3"},{"title":"奖励充值总额比例(%)","value":"0.50"}],[{"title":"当日团队亏损金额(万)","value":"5"},{"title":"当日团队销量(万)","value":"100"},{"title":"有效人数","value":"10"},{"title":"奖励充值总额比例(%)","value":"0.80"}],[{"title":"当日团队亏损金额(万)","value":"10"},{"title":"当日团队销量(万)","value":"200"},{"title":"有效人数","value":"20"},{"title":"奖励充值总额比例(%)","value":"1.00"}]]]}',
                'config_ui' => '{}',
                'summary' => '主管充值奖励',
                'description' => '<p>奖励内容:</p>
                                    <table width="100%">
                                    <tr>
                                    <th>当日团队亏损金额(万)</th>
                                    <th>当日团队销量(万)</th>
                                    <th>有效人数</th>
                                    <th>奖励充值总额比例(%)</th>
                                    </tr>
                                    <tr>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>3</td>
                                    <td>0.50</td>
                                    </tr>
                                    <tr>
                                    <td>5</td>
                                    <td>100</td>
                                    <td>10</td>
                                    <td>0.80</td>
                                    </tr>
                                    <tr>
                                    <td>10</td>
                                    <td>200</td>
                                    <td>20</td>
                                    <td>1.00</td>
                                    </tr>
                                    </table>
                                <p>发放对象：1级代理</p>
                                ',
                'start_time' => (string)Carbon::today()->startOfDay(),
                'end_time' => (string)Carbon::today()->endOfDay(),
                'draw_method' => 2,
                'status' => 0
            ];
            \Service\Models\Activity::insert($data);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    // 亿博 奖期工资 补发
    private function yibo_issuewage_again()
    {
        if (get_config('dailywage_type_ident') == 'Yibo') {
            if (!get_config('dailywage_available')) {
                $this->info('请开启日工资功能');
                return;
            }

            (\Service\API\DailyWage\IssueWageA::factory())->run_again("2020-11-14 03:00:00", "2020-11-15 16:00:00");
        } else {
            $this->info('当前方法只允许 Yibo 使用');
        }
    }

    private function ssc_n5_dwd_new()
    {
        $data = [
            ['id' => '100701002', 'parent_id' => '100701000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_n5_dingweidan_wanwei', 'name' => '万位', 'draw_rule' => '{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name' => 'lock_dwd', 'lock_init_function' => 'initNumberTypeYiWeiLock,1-5', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[20]', 'prize_level_name' => '["定位胆"]', 'layout' => '
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码万位：1，即中定位胆万位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '100701003', 'parent_id' => '100701000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_n5_dingweidan_qianwei', 'name' => '千位', 'draw_rule' => '{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name' => 'lock_dwd', 'lock_init_function' => 'initNumberTypeYiWeiLock,1-5', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[20]', 'prize_level_name' => '["定位胆"]', 'layout' => '
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码千位：1，即中定位胆千位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '100701004', 'parent_id' => '100701000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_n5_dingweidan_baiwei', 'name' => '百位', 'draw_rule' => '{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name' => 'lock_dwd', 'lock_init_function' => 'initNumberTypeYiWeiLock,1-5', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[20]', 'prize_level_name' => '["定位胆"]', 'layout' => '
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码百位：1，即中定位胆百位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '100701005', 'parent_id' => '100701000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_n5_dingweidan_shiwei', 'name' => '十位', 'draw_rule' => '{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name' => 'lock_dwd', 'lock_init_function' => 'initNumberTypeYiWeiLock,1-5', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[20]', 'prize_level_name' => '["定位胆"]', 'layout' => '
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码十位：1，即中定位胆十位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '100701006', 'parent_id' => '100701000', 'lottery_method_category_id' => '11', 'ident' => 'ssc_n5_dingweidan_gewei', 'name' => '个位', 'draw_rule' => '{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name' => 'lock_dwd', 'lock_init_function' => 'initNumberTypeYiWeiLock,1-5', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[20]', 'prize_level_name' => '["定位胆"]', 'layout' => '
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码个位：1，即中定位胆个位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
        ];
        DB::beginTransaction();
        try {
            \Service\Models\LotteryMethod::insert($data);
            \Service\Models\LotteryMethod::whereIn('ident', ['ssc_n5_dingweidan_wanwei', 'ssc_n5_dingweidan_qianwei', 'ssc_n5_dingweidan_baiwei', 'ssc_n5_dingweidan_shiwei', 'ssc_n5_dingweidan_gewei'])
                ->update(['status' => 'f']);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //添加充值申请的界面参数third_amount字段
    private function add_deposits_third_amount()
    {
        DB::beginTransaction();
        try {
            Schema::table('deposits', function (Blueprint $table) {
                $table->decimal('third_amount', 15, 4)->default(0)->comment('虚拟币金额');
            });
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加上下级聊天软删除功能
    private function add_chatmessage_delete()
    {
        DB::beginTransaction();
        try {
            Schema::table('chat_message', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
            });

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function add_withdrawals_virtual()
    {
        try {
            if (\Schema::hasColumn('withdrawals', 'third_amount')) {
                $this->info('third_amount 字段已存在');
                return;
            }

            DB::beginTransaction();
            DB::statement('ALTER TABLE "public"."withdrawals" ADD COLUMN "third_amount" numeric(15,4) DEFAULT \'0\'::numeric NOT NULL');
            DB::statement("COMMENT ON COLUMN \"public\".\"withdrawals\".\"third_amount\" IS '第三方金额'");
            DB::commit();
            $this->info('withdrawals表third_amount字段添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //增加用户通道锁定功能
    private function add_user_channel_table()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableUserChannelLock())->up();

            Schema::table('payment_channel', function (Blueprint $table) {
                $table->smallInteger('invalid_times_lock')->default(0)->unsigned()->comment('用户10分钟无效申请最多次数');
            });

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //添加虚拟货币银行配置页面参数channel_idents字段
    private function add_bank_virtual_channel_idents()
    {
        DB::beginTransaction();
        try {
            Schema::table('bank_virtual', function (Blueprint $table) {
                $table->string('channel_idents', 256)->default('offlinerbcxpay,xinrbcxpay,rbcxpay')->comment('使用渠道,填写填渠道英文标示,如有多个用”,”隔开');
            });
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加推荐彩种功能
    private function add_lottery_recommend_table()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableLotteryRecommend())->up();
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //检查ip_firewall表约束
    private function check_ip_firewall_constraint()
    {
        $sql = "select 1 from information_schema.table_constraints where table_name='ip_firewall' and constraint_name='ip_firewall_type_ip_unique'";
        $result = DB::select($sql);
        if (!$result) {
            echo 'ip_firewall表缺少约束：ip_firewall_type_ip_unique' . PHP_EOL;
        }
    }

    //更改充值送彩金活动
    private function update_activity_recharge10pct()
    {
        DB::beginTransaction();
        try {
            \Service\Models\Activity::where("ident", "recharge10pct")->update([
                'config' => '{"hide": "0", "config": [[[{"title": "最高礼金", "value": "8888"}],[{"title": "指定彩种英文标识用,号分割", "value": "cxg360ffc,cxg3605fc,hne1fssc,hne5fssc,jsxyftpk10"}]],[[{"title": "充值金额(万)≥", "value": "0"},{"title": "流水倍数", "value": "3"},{"title": "奖励(%)", "value": "2"}],[{"title": "充值金额(万)≥", "value": "1"},{"title": "流水倍数", "value": "5"},{"title": "奖励(%)", "value": "3"}],[{"title": "充值金额(万)≥", "value": "5"},{"title": "流水倍数", "value": "8"},{"title": "奖励(%)", "value": "4"}],[{"title": "充值金额(万)≥", "value": "20"},{"title": "流水倍数", "value": "10"},{"title": "奖励(%)", "value": "5"}]]]}',
                'description' => '<p>活动规则介绍内容:</p>
                                <table width="100%">
                                <tr>
                                <th>充值金额</th>
                                <th>消费金额</th>
                                <th>奖励比例(%)</th>
                                </tr>
                                <tr>
                                <td>0-1万</td>
                                <td>消费（充值＋彩金）总金额3倍以上</td>
                                <td>2</td>
                                </tr>
                                <tr>
                                <td>1万-5万</td>
                                <td>消费（充值＋彩金）总金额5倍以上</td>
                                <td>3</td>
                                </tr>
                                <tr>
                                <td>5万-20万</td>
                                <td>消费（充值＋彩金）总金额8倍以上</td>
                                <td>4</td>
                                </tr>
                                <tr>
                                <td>20万-50万</td>
                                <td>消费（充值＋彩金）总金额10倍以上</td>
                                <td>5</td>
                                </tr>
                                </table>
                                <p>1.活动对象：本平台所有用户</p>
                                <p>2.活动派发条件：前一日有进行充值，并且前一日消费(本金+彩金)流水满足领取条件</p>
                                <p>3.活动奖励：奖金最高8888元</p>
                                <p>4.活动限制：活动只限对应彩种，不限玩法，用户投注注数不可大于该玩法的总注数的90%（比如：一星不超过9注，二星不超过90注，三星不超过900注，四星不超过9000注）</p>
                                <p>&nbsp;&nbsp;限制的彩种：360分分彩、360五分彩、河内分分彩、河内五分彩、幸运飞艇</p>
                                <p>5.领取方式：达成条件后，于活动页面点击领取</p>
                                <p>6.同一账户，同一IP，同一电脑，每日只允许参与一次</p>
                                <p>7.本活动只欢迎真实玩家，拒绝白菜党，若发现有用户运用此活动进行套利行为，风控部有权将其账号进行冻结（连同本金）</p>
                                <p>8.此活动最终解释权归运营部活动组所有</p>
                                ',
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加虚拟货币银行管理页面
    private function add_bank_virtual_amout()
    {
        try {
            if (\Schema::hasColumn('bank_virtual', 'amount_max')) {
                $this->info('amount_max 字段已存在');
                return;
            }
            if (\Schema::hasColumn('bank_virtual', 'amount_min')) {
                $this->info('amount_min 字段已存在');
                return;
            }
            if (\Schema::hasColumn('bank_virtual', 'start_time')) {
                $this->info('start_time 字段已存在');
                return;
            }
            if (\Schema::hasColumn('bank_virtual', 'end_time')) {
                $this->info('end_time 字段已存在');
                return;
            }
            DB::beginTransaction();
            DB::statement('ALTER TABLE "public"."bank_virtual" ADD COLUMN "amount_max" numeric(15,4) DEFAULT \'1000\'::numeric NOT NULL, ADD COLUMN  "amount_min" numeric(15,4) DEFAULT \'0\'::numeric NOT NULL,ADD COLUMN  "start_time"  TEXT  DEFAULT \'00:00\' NOT NULL,ADD COLUMN  "end_time" TEXT  DEFAULT \'23:59\' NOT NULL');
            DB::statement("COMMENT ON COLUMN \"public\".\"bank_virtual\".\"amount_max\" IS '提现上限'");
            DB::statement("COMMENT ON COLUMN \"public\".\"bank_virtual\".\"amount_min\" IS '提现下限'");
            DB::statement("COMMENT ON COLUMN \"public\".\"bank_virtual\".\"start_time\" IS '提现开始时间'");
            DB::statement("COMMENT ON COLUMN \"public\".\"bank_virtual\".\"end_time\" IS '提现结束时间'");
            DB::commit();
            $this->info('bank_virtual表amount_max,amount_min,start_time,end_time字段添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //新增广东省广州市南沙区
    private function guangzhou_add_nansha_area()
    {
        DB::beginTransaction();
        try {
            DB::table('regions')->insert([
                'id' => 3311,
                'name' => '南沙区',
                'parent_id' => 231,
                'level' => 3,
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //banks表增加银行标识唯一约束
    private function bank_ident_unique()
    {
        DB::beginTransaction();
        try {
            Schema::table('banks', function (Blueprint $table) {
                $table->unique('ident');
            });

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function win_codes_config()
    {
        if (get_config('app_ident') == 'fy') {
            $this->info('该平台不执行');
            return;
        }

        $drawsource_config_parent_id = Config::where('key', 'drawsource_config')->value('id');
        if (empty($drawsource_config_parent_id)) {
            $this->info('失败：drawsource_config_parent_id 不存在');
            return;
        }
        DB::beginTransaction();
        try {
            Config::insert([
                [
                    'parent_id' => $drawsource_config_parent_id,
                    'title' => 'UNAI奖源开关',
                    'key' => 'win_codes_enabled',
                    'value' => '1',
                    'description' => '0|关闭,1|开启',
                ],
                [
                    'parent_id' => $drawsource_config_parent_id,
                    'title' => 'UNAI奖源来源IP白名单',
                    'key' => 'win_codes_ip_whitelist',
                    'value' => '13.75.122.38',
                    'description' => '多个IP使用英文逗号,分割',
                ],
                [
                    'parent_id' => $drawsource_config_parent_id,
                    'title' => 'UNAI奖源发送代理地址',
                    'key' => 'win_codes_proxy_url',
                    'value' => '',
                    'description' => '留空不使用代理。示例 http://中间站域名/proxy/query',
                ],
                [
                    'parent_id' => $drawsource_config_parent_id,
                    'title' => 'UNAI奖源必杀用户名',
                    'key' => 'win_codes_kill_users',
                    'value' => '',
                    'description' => '多个用户名使用英文逗号,分隔',
                ],
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function add_db_trigger()
    {
        DB::beginTransaction();
        try {
            DB::statement('DROP TRIGGER IF EXISTS project_delete_trigger ON projects');
            DB::statement('DROP FUNCTION IF EXISTS project_delete_fun');

            DB::statement('DROP TRIGGER IF EXISTS orders_delete_trigger ON orders');
            DB::statement('DROP FUNCTION IF EXISTS orders_delete_fun');

            (new \CreateFunctionAndTrigger())->up();
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function shadow_user_fund()
    {
        DB::beginTransaction();
        try {
            Schema::create('shadow_user_fund', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('user_id')->comment('用户ID');
                $table->decimal('balance', 14, 4)->default(0)->comment('帐户余额');
                $table->decimal('hold_balance', 14, 4)->default(0)->comment('冻结金额');
                $table->integer('points')->default(0)->comment('用户积分');
                $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('时间');
                $table->index(['user_id', 'created_at']);
                $table->index(['created_at']);
            });

            DB::statement("
            CREATE OR REPLACE FUNCTION shadow_user_fund_add_fun() RETURNS trigger AS $$
                BEGIN
                    INSERT INTO shadow_user_fund (
                        user_id,
                        balance,
                        hold_balance,
                        points
                    ) VALUES (
                        NEW.user_id,
                        NEW.balance,
                        NEW.hold_balance,
                        NEW.points
                    );
                    RETURN NEW;
                END;
                $$
                language plpgsql;
            ");

            DB::statement("
            CREATE TRIGGER user_fund_update_trigger AFTER UPDATE ON user_fund
                FOR EACH ROW
                EXECUTE PROCEDURE shadow_user_fund_add_fun();
            ");

            DB::statement("
            CREATE TRIGGER shadow_user_fund_delete_trigger BEFORE DELETE ON shadow_user_fund
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除资金影子');
            ");
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function modify_shadow_user_fund()
    {
        DB::beginTransaction();
        try {
            Schema::table('shadow_user_fund', function (Blueprint $table) {
                $table->decimal('balance_diff', 14, 4)->default(0)->comment('帐户余额变化');
                $table->decimal('hold_balance_diff', 14, 4)->default(0)->comment('冻结金额变化');
                $table->integer('points_diff')->default(0)->comment('用户积分变化');
            });
            DB::statement("
            CREATE OR REPLACE FUNCTION shadow_user_fund_add_fun() RETURNS trigger AS $$
                DECLARE
                    balance_diff DECIMAL;
                    hold_balance_diff DECIMAL;
                    points_diff INT;
                BEGIN
                    balance_diff := NEW.balance - OLD.balance;
                    hold_balance_diff := NEW.hold_balance - OLD.hold_balance;
                    points_diff := NEW.points - OLD.points;

                    INSERT INTO shadow_user_fund (
                        user_id,
                        balance,
                        hold_balance,
                        points,
                        balance_diff,
                        hold_balance_diff,
                        points_diff
                    ) VALUES (
                        NEW.user_id,
                        NEW.balance,
                        NEW.hold_balance,
                        NEW.points,
                        balance_diff,
                        hold_balance_diff,
                        points_diff
                    );
                    RETURN NEW;
                END;
                $$
                language plpgsql;
            ");
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function replace_trigger()
    {
        DB::beginTransaction();
        try {
            DB::statement("
                ALTER TABLE user_behavior_log ALTER column \"action\"  type VARCHAR(32);
            ");

            DB::statement("
            CREATE OR REPLACE FUNCTION shadow_user_fund_update_fun() RETURNS trigger AS $$
                BEGIN
                    RETURN NULL;
                END;
                $$
                language plpgsql;
            ");

            DB::statement("DROP TRIGGER IF EXISTS shadow_user_fund_update_trigger ON shadow_user_fund");
            DB::statement("
            CREATE TRIGGER shadow_user_fund_update_trigger AFTER UPDATE ON shadow_user_fund
                FOR EACH ROW
                EXECUTE PROCEDURE shadow_user_fund_update_fun();
            ");

            DB::statement("DROP TRIGGER IF EXISTS orders_delete_trigger ON orders");
            DB::statement("
            CREATE TRIGGER orders_delete_trigger BEFORE DELETE ON orders
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除账变');
            ");

            DB::statement("DROP TRIGGER IF EXISTS projects_delete_trigger ON projects");
            DB::statement("
            CREATE TRIGGER projects_delete_trigger BEFORE DELETE ON projects
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除注单');
            ");

            DB::statement("DROP TRIGGER IF EXISTS withdrawals_delete_trigger ON withdrawals");
            DB::statement("
            CREATE TRIGGER withdrawals_delete_trigger BEFORE DELETE ON withdrawals
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除提款');
            ");

            DB::statement("DROP TRIGGER IF EXISTS deposits_delete_trigger ON deposits");
            DB::statement("
            CREATE TRIGGER deposits_delete_trigger BEFORE DELETE ON deposits
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除充值');
            ");

            //user_behavior_log
            DB::statement("DROP TRIGGER IF EXISTS user_behavior_log_delete_trigger ON user_behavior_log");
            DB::statement("
            CREATE TRIGGER user_behavior_log_delete_trigger BEFORE DELETE ON user_behavior_log
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除异常行为日志');
            ");


            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function recovery_trigger()
    {
        DB::beginTransaction();
        try {
            DB::statement("
            CREATE OR REPLACE FUNCTION refuse_delete_common_fun() RETURNS trigger AS $$
                DECLARE
                    log_user_id INT;
                    action_name VARCHAR;
                    query_sql TEXT;
                    allow_special_del_tables VARCHAR[];

                BEGIN
                    IF TG_ARGV[0] IS NULL THEN
                        action_name := '未定义';
                    ELSE
                        action_name := TG_ARGV[0];
                    END IF;

                    IF TG_TABLE_NAME = 'users' THEN
                        log_user_id := OLD.id;
                    ELSEIF TG_TABLE_NAME = 'orders' THEN
                        log_user_id := OLD.from_user_id;
                    ELSEIF OLD.user_id IS NOT NULL THEN
                         log_user_id := OLD.user_id;
                    ELSE
                         log_user_id := 0;
                    END IF;

                    query_sql := current_query();
                    allow_special_del_tables := array['report_lottery', 'report_lottery_compressed', 'report_lottery_total', 'report_lottery_total_compressed'];

                    IF query_sql ~ E'AnD  \'apl_special\'=\'apl_special\'' AND TG_TABLE_NAME=ANY(allow_special_del_tables) THEN
                        RETURN OLD;
                    ELSE
                        INSERT INTO user_behavior_log (
                            user_id,
                            db_user,
                            level,
                            action,
                            description
                        ) VALUES (
                            log_user_id,
                            user,
                            1,
                            action_name,
                            '尝试删除被拒绝，执行语句：' || current_query() || '  ，数据记录： ' || OLD
                        );
                        RETURN NULL;
                    END IF;
                END;
                $$
                language plpgsql;
            ");

            DB::statement("DROP TRIGGER IF EXISTS orders_delete_trigger ON orders");
            DB::statement("
            CREATE TRIGGER orders_delete_trigger BEFORE DELETE ON orders
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除账变');
            ");

            DB::statement("DROP TRIGGER IF EXISTS projects_delete_trigger ON projects");
            DB::statement("
            CREATE TRIGGER projects_delete_trigger BEFORE DELETE ON projects
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除注单');
            ");

            DB::statement("DROP TRIGGER IF EXISTS withdrawals_delete_trigger ON withdrawals");
            DB::statement("
            CREATE TRIGGER withdrawals_delete_trigger BEFORE DELETE ON withdrawals
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除提款');
            ");

            DB::statement("DROP TRIGGER IF EXISTS deposits_delete_trigger ON deposits");
            DB::statement("
            CREATE TRIGGER deposits_delete_trigger BEFORE DELETE ON deposits
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除充值');
            ");

            DB::statement("DROP TRIGGER IF EXISTS user_behavior_log_delete_trigger ON user_behavior_log");
            DB::statement("
            CREATE TRIGGER user_behavior_log_delete_trigger BEFORE DELETE ON user_behavior_log
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '30 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除异常行为日志');
            ");

            DB::statement("DROP TRIGGER IF EXISTS shadow_user_fund_delete_trigger ON shadow_user_fund");
            DB::statement("
            CREATE TRIGGER shadow_user_fund_delete_trigger BEFORE DELETE ON shadow_user_fund
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('自动账变');
            ");


            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function recovery2_trigger()
    {
        DB::beginTransaction();
        try {
            DB::statement("
            CREATE OR REPLACE FUNCTION refuse_delete_common_fun() RETURNS trigger AS $$
                DECLARE
                    log_user_id INT;
                    action_name VARCHAR;
                    query_sql TEXT;
                    allow_special_del_tables VARCHAR[];

                BEGIN
                    IF TG_ARGV[0] IS NULL THEN
                        action_name := '未定义';
                    ELSE
                        action_name := TG_ARGV[0];
                    END IF;

                    IF TG_TABLE_NAME = 'users' THEN
                        log_user_id := OLD.id;
                    ELSEIF TG_TABLE_NAME = 'orders' THEN
                        log_user_id := OLD.from_user_id;
                    ELSEIF OLD.user_id IS NOT NULL THEN
                         log_user_id := OLD.user_id;
                    ELSE
                         log_user_id := 0;
                    END IF;

                    query_sql := current_query();
                    allow_special_del_tables := array['report_lottery', 'report_lottery_compressed', 'report_lottery_total', 'report_lottery_total_compressed'];

                    IF query_sql ~ E'AnD  \'apl_special\'=\'apl_special\'' AND TG_TABLE_NAME=ANY(allow_special_del_tables) THEN
                        RETURN OLD;
                    ELSIF TG_TABLE_NAME=ANY(allow_special_del_tables) AND OLD.created_at IS NOT NULL AND OLD.created_at < LOCALTIMESTAMP - interval '60 days' THEN
                        RETURN OLD;
                    ELSE
                        INSERT INTO user_behavior_log (
                            user_id,
                            db_user,
                            level,
                            action,
                            description
                        ) VALUES (
                            log_user_id,
                            user,
                            1,
                            action_name,
                            '尝试删除被拒绝，执行语句：' || current_query() || '  ，数据记录： ' || OLD
                        );
                        RETURN NULL;
                    END IF;
                END;
                $$
                language plpgsql;
            ");

            //report_lottery 60天 在fun判断
            DB::statement("DROP TRIGGER IF EXISTS report_lottery_delete_trigger ON report_lottery");
            DB::statement("
            CREATE TRIGGER report_lottery_delete_trigger BEFORE DELETE ON report_lottery
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除报表rl');
            ");

            //report_lottery_compressed 60天 在fun判断
            DB::statement("DROP TRIGGER IF EXISTS report_lottery_compressed_delete_trigger ON report_lottery_compressed");
            DB::statement("
            CREATE TRIGGER report_lottery_compressed_delete_trigger BEFORE DELETE ON report_lottery_compressed
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除报表rlc');
            ");

            //report_lottery_total 60天 在fun判断
            DB::statement("DROP TRIGGER IF EXISTS report_lottery_total_delete_trigger ON report_lottery_total");
            DB::statement("
            CREATE TRIGGER report_lottery_total_delete_trigger BEFORE DELETE ON report_lottery_total
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除报表rlt');
            ");

            //report_lottery_total_compressed 60天 在fun判断
            DB::statement("DROP TRIGGER IF EXISTS report_lottery_total_compressed_delete_trigger ON report_lottery_total_compressed");
            DB::statement("
            CREATE TRIGGER report_lottery_total_compressed_delete_trigger BEFORE DELETE ON report_lottery_total_compressed
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除报表rltc');
            ");

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function recovery_db_fun()
    {
        DB::beginTransaction();
        try {
            DB::statement("
          CREATE OR REPLACE FUNCTION refuse_delete_common_fun() RETURNS trigger AS $$
                DECLARE
                     log_user_id INT;
                     action_name VARCHAR;
                     query_sql TEXT;
                     allow_special_del_tables VARCHAR[] := array['report_lottery', 'report_lottery_compressed', 'report_lottery_total', 'report_lottery_total_compressed'];
                     no_user_id_tables VARCHAR[] :=  array['lottery', 'user_request_log', 'user_bet_request_log', 'admin_request_log'];
                     no_created_at_tables VARCHAR[] :=  array['user_fund'];
                     forbid_delete_tables VARCHAR[] := array['users', 'user_fund', 'lottery'];

                BEGIN
                     IF TG_ARGV[0] IS NULL THEN
                          action_name := '未定义';
                     ELSE
                          action_name := TG_ARGV[0];
                     END IF;

                     IF TG_TABLE_NAME = 'users' THEN
                          log_user_id := OLD.id;
                     ELSEIF TG_TABLE_NAME = 'orders' THEN
                          log_user_id := OLD.from_user_id;
                     ELSEIF TG_TABLE_NAME=ANY(no_user_id_tables) THEN
                          log_user_id := 0;
                     ELSEIF OLD.user_id IS NOT NULL THEN
                          log_user_id := OLD.user_id;
                     ELSE
                          log_user_id := 0;
                     END IF;

                     query_sql := current_query();

                     IF query_sql ~ E'AnD  \'apl_special\'=\'apl_special\'' AND TG_TABLE_NAME=ANY(allow_special_del_tables) THEN
                          RETURN OLD;
                     ELSEIF TG_TABLE_NAME=ANY(forbid_delete_tables) THEN
                          INSERT INTO user_behavior_log (
                                user_id,
                                db_user,
                                level,
                                action,
                                description
                          ) VALUES (
                                log_user_id,
                                user,
                                1,
                                action_name,
                                '尝试删除被拒绝，执行语句：' || current_query() || '  ，数据记录： ' || OLD
                          );
                          RETURN NULL;
                     ELSIF TG_TABLE_NAME=ANY(allow_special_del_tables) AND OLD.created_at IS NOT NULL AND OLD.created_at < LOCALTIMESTAMP - interval '60 days' THEN
                          RETURN OLD;
                     ELSE
                          INSERT INTO user_behavior_log (
                                user_id,
                                db_user,
                                level,
                                action,
                                description
                          ) VALUES (
                                log_user_id,
                                user,
                                1,
                                action_name,
                                '尝试删除被拒绝，执行语句：' || current_query() || '  ，数据记录： ' || OLD
                          );
                          RETURN NULL;
                     END IF;
                END;
                $$
                language plpgsql;
          ");

            DB::statement("
          CREATE OR REPLACE FUNCTION refuse_update_created_common_fun() RETURNS trigger AS $$
                DECLARE
                     log_user_id INT;
                     action_name VARCHAR;
                     no_user_id_tables VARCHAR[] :=  array['lottery', 'user_request_log', 'user_bet_request_log', 'admin_request_log'];

                BEGIN
                     IF TG_ARGV[0] IS NULL THEN
                          action_name := '未定义';
                     ELSE
                          action_name := TG_ARGV[0];
                     END IF;

                     IF TG_TABLE_NAME = 'users' THEN
                          log_user_id := OLD.id;
                     ELSEIF TG_TABLE_NAME = 'orders' THEN
                          log_user_id := OLD.from_user_id;
                     ELSEIF TG_TABLE_NAME = ANY(no_user_id_tables) THEN
                          log_user_id := 0;
                     ELSEIF OLD.user_id IS NOT NULL THEN
                          log_user_id := OLD.user_id;
                     ELSE
                          log_user_id := 0;
                     END IF;

                     IF NEW.created_at = OLD.created_at THEN
                          RETURN NEW;
                     ELSE
                          INSERT INTO user_behavior_log (
                                user_id,
                                db_user,
                                level,
                                action,
                                description
                          ) VALUES (
                                log_user_id,
                                user,
                                1,
                                action_name,
                                '修改创建时间被拒绝，执行语句：' || current_query() || '  ，原数据： ' || OLD
                          );
                          RETURN NULL;
                     END IF;
                END;
                $$
                language plpgsql;
          ");
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function update_created_triggler()
    {
        DB::beginTransaction();
        try {

            DB::statement("
        CREATE OR REPLACE FUNCTION refuse_update_created_common_fun() RETURNS trigger AS $$
            DECLARE
                log_user_id INT;
                action_name VARCHAR;

            BEGIN
                IF TG_ARGV[0] IS NULL THEN
                    action_name := '未定义';
                ELSE
                    action_name := TG_ARGV[0];
                END IF;

                IF TG_TABLE_NAME = 'users' THEN
                    log_user_id := OLD.id;
                ELSEIF TG_TABLE_NAME = 'orders' THEN
                    log_user_id := OLD.from_user_id;
                ELSEIF OLD.user_id IS NOT NULL THEN
                     log_user_id := OLD.user_id;
                ELSE
                     log_user_id := 0;
                END IF;

                IF OLD.created_at IS NULL THEN
                    RETURN NEW;
                ELSEIF NEW.created_at = OLD.created_at THEN
                    RETURN NEW;
                ELSE
                    INSERT INTO user_behavior_log (
                        user_id,
                        db_user,
                        level,
                        action,
                        description
                    ) VALUES (
                        log_user_id,
                        user,
                        1,
                        action_name,
                        '修改创建时间被拒绝，执行语句：' || current_query() || '  ，原数据： ' || OLD
                    );
                    RETURN NULL;
                END IF;
            END;
            $$
            language plpgsql;
        ");

            //orders
            DB::statement("
        CREATE TRIGGER orders_update_created_trigger BEFORE UPDATE ON orders
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改账变创建时间');
        ");

            //projects
            DB::statement("
        CREATE TRIGGER projects_update_created_trigger BEFORE UPDATE ON projects
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改注单创建时间');
        ");

            //withdrawals
            DB::statement("
        CREATE TRIGGER withdrawals_update_created_trigger BEFORE UPDATE ON withdrawals
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改提款创建时间');
        ");

            //deposits
            DB::statement("
        CREATE TRIGGER deposits_update_created_trigger BEFORE UPDATE ON deposits
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改充值创建时间');
        ");

            //user_bet_request_log
            DB::statement("
        CREATE TRIGGER user_bet_request_log_update_created_trigger BEFORE UPDATE ON user_bet_request_log
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改用户投注日志时间');
        ");

            //user_login_log
            DB::statement("
        CREATE TRIGGER user_login_log_update_created_trigger BEFORE UPDATE ON user_login_log
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改用户登录日志时间');
        ");

            //user_request_log
            DB::statement("
        CREATE TRIGGER user_request_log_update_created_trigger BEFORE UPDATE ON user_request_log
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改用户非投注日志时间');
        ");

            //admin_request_log
            DB::statement("
        CREATE TRIGGER admin_request_log_update_created_trigger BEFORE UPDATE ON admin_request_log
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改管理员日志时间');
        ");

            //user_behavior_log
            DB::statement("
        CREATE TRIGGER user_behavior_log_update_created_trigger BEFORE UPDATE ON user_behavior_log
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改异常行为日志时间');
        ");

            //report_lottery
            DB::statement("
        CREATE TRIGGER report_lottery_update_created_trigger BEFORE UPDATE ON report_lottery
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改报表rl时间');
        ");

            //report_lottery_compressed
            DB::statement("
        CREATE TRIGGER report_lottery_compressed_update_created_trigger BEFORE UPDATE ON report_lottery_compressed
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改报表rlc时间');
        ");

            //report_lottery_total
            DB::statement("
        CREATE TRIGGER report_lottery_total_update_created_trigger BEFORE UPDATE ON report_lottery_total
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改报表rlt时间');
        ");

            //report_lottery_total_compressed
            DB::statement("
        CREATE TRIGGER report_lottery_total_compressed_update_created_trigger BEFORE UPDATE ON report_lottery_total_compressed
            FOR EACH ROW
            EXECUTE PROCEDURE refuse_update_created_common_fun('修改报表rltc时间');
        ");

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function add_shadow_fund()
    {
        DB::beginTransaction();
        try {
            DB::statement("DROP TRIGGER IF EXISTS shadow_user_fund_update_trigger ON shadow_user_fund");
            DB::statement("
            CREATE TRIGGER shadow_user_fund_update_trigger BEFORE UPDATE ON shadow_user_fund
                FOR EACH ROW
                EXECUTE PROCEDURE shadow_user_fund_update_fun();
            ");

            $row = DB::table('admin_role_permissions')->where('name', '账变管理')->where('parent_id', 0)->first();
            if (empty($row)) {
                $this->info('账变管理 菜单不存在');
                return;
            }
            DB::table('admin_role_permissions')->insert([
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'shadowfund/index',
                'name' => '自动账变',
            ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function drop_fun_message_deletes()
    {
        DB::beginTransaction();
        try {
            DB::statement('DROP FUNCTION IF EXISTS message_deletes');
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //检查函数
    private function _check_function()
    {
        $sql = 'select pg_get_functiondef(oid) as s from pg_proc
                where pronamespace in (select oid from pg_namespace where nspname not in (\'information_schema\',\'pg_catalog\'))';
        $result = DB::select($sql);
        echo '-----------------------------begin---------------------------------' . PHP_EOL;
        foreach ($result as $value) {
            echo $value->s;
        }
        echo '-----------------------------end---------------------------------' . PHP_EOL;
    }

    //检查id连续
    private function _check_id()
    {
        $tables = ['admin_request_log', 'report_lottery', 'report_lottery_compressed', 'report_lottery_total', 'report_lottery_total_compressed',
            'user_behavior_log', 'user_bet_request_log', 'projects', 'orders', 'deposits', 'withdrawals'];
        foreach ($tables as $table) {
            $this->_check_id_sub($table);
        }
    }

    private function _check_id_sub($table)
    {
        $begin_time = Carbon::today();
        $sql = "select id,created_at from {$table} where created_at>='{$begin_time}' order by id asc;";
        $result = DB::select($sql);
        if ($result) {
            $out = '';
            $pre_id = $result[0]->id;
            $pre_time = $result[0]->created_at;
            foreach ($result as $value) {
                $diff = $value->id - $pre_id - 1;
                if ($diff > 0) {
                    $again_num = $this->_check_id_again($pre_id, $value->id, $table);
                    if ($again_num < $diff) {
                        $out .= "pre_id:{$pre_id}    created_at:{$pre_time}-----------" .
                            "next_id:{$value->id}    created_at:{$value->created_at}--------" .
                            "diff_num:{$diff}       check_again:{$again_num}\n";
                    }
                }
                $pre_id = $value->id;
                $pre_time = $value->created_at;
            }
            if ($out) {
                echo "{$table} begin---------------------------------------------------------------\n{$out}";
            }
        }
    }

    private function _check_id_again($min_id, $max_id, $table)
    {
        $sql = "select count(1) as count from {$table} where id>{$min_id} and id<{$max_id}";
        $result = DB::select($sql);
        return $result[0]->count;
    }

    //用户列表增加收回三方余额功能
    private function _add_permission_third_balance()
    {
        $row = DB::table('admin_role_permissions')->where('name', '用户管理')
            ->where('parent_id', 0)
            ->first();

        DB::beginTransaction();
        try {
            DB::table('admin_role_permissions')->insert([
                [
                    'parent_id' => $row->id,
                    'icon' => '',
                    'rule' => 'user/recythirdbalance',
                    'name' => '收回三方余额',
                ]]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    /**
     * 查询异常帐号
     */
    private function _get_bad_user()
    {
        $this->line('time => ' . date('Y-m-d H:i:s'));
        $this->line('platform => ' . get_config('app_ident', ''));
        $query = DB::table('users')->where('parent_tree', '[]')->where(function ($query) {
            $query->whereRaw('top_id <> id ')->orWhere('parent_id', '<>', 0);
        });
        $sql = $query->toSql();
        $rows = $query->get();
        //$this->line('$sql => '.$sql) ;
        if ($rows->isEmpty()) {
            $this->line('暂无非法修改用户');
        } else {
            $this->line('共' . $rows->count() . '条被篡改用户');
            foreach ($rows as $row) {
                $this->line(implode(' ', [$row->id, $row->username]));
            }
        }

    }

    //推荐彩种增加栏位
    private function add_lottery_recommend_column()
    {
        DB::beginTransaction();
        try {
            Schema::table('lottery_recommend', function (Blueprint $table) {
                $table->jsonb('data')->default('[]')->comment('推荐彩种数据');
            });

            $sql = "select * from lottery_recommend";
            $result = DB::select($sql);
            foreach ($result as $row) {
                $s = [];
                for ($i = 1; $i <= 4; $i++) {
                    $val = $row->{'lottery_ident' . $i};
                    if ($val) {
                        $s[] = $val;
                    }
                }
                \Service\Models\LotteryRecommend::where('id', $row->id)->update(['data' => json_encode($s)]);
            }

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //推荐彩种删除栏位
    private function delete_lottery_recommend_column()
    {
        DB::beginTransaction();
        try {
            Schema::table('lottery_recommend', function (Blueprint $table) {
                $table->dropColumn(['lottery_ident1', 'lottery_ident2', 'lottery_ident3', 'lottery_ident4']);
            });
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }


    /**
     * 69    QYFHFF    契约分红发放
     * 70    QYFHLQ    契约分红领取
     * 17    LPCZ    理赔充值
     * 33    GLYJK    管理员减扣
     */
    private function yb_fh_fuck()
    {
        //确认是亿博平台
        $platform_type_ident = get_config('dividend_type_ident');
        if ($platform_type_ident <> 'Yibo') {
            $this->info('当前方法只允许 Yibo 使用');
        } else {
            $fh_fail_send_types = [];
            $fh_lq_success_id = [];
            $fh_lq_fail_id = [];
            $fh_lq_zero_id = [];
            $fh_ff_success_id = [];
            $fh_ff_fail_id = [];
            $fh_ff_zero_id = [];

            $jk_amount_total = 0;
            $total_jksb_users = [];
            $total_jksb_amount = 0;
            $total_jkcg_users = [];
            $total_jkcg_amount = 0;

            $total_jkzq_users = [];
            $total_jkzq_amount = 0;

            $lp_amount_total = 0;
            $total_lpsb_amount = 0;
            $total_lpsb_users = [];
            $total_lpcg_amount = 0;
            $total_lpcg_users = [];
            $total_lpzq_amount = 0;
            $total_lpzq_users = [];


            $sql = 'select cd.*,u.username as username,u.parent_id from contract_dividends cd left join users u on u.id = cd.user_id where cd.start_time = \'2020-11-19 00:00:00\' and cd.end_time = \'2020-11-19 23:59:59\' and cd.status = 1';
            $fh_rows = DB::select($sql);
            if ($fh_rows) {
                foreach ($fh_rows as $fh_row) {
                    $this->info('分红ID ' . $fh_row->id . ' 用户名 ' . $fh_row->username . ' 用户ID ' . $fh_row->user_id . ' ，上级ID ' . $fh_row->parent_id . ' 分红金额 ' . $fh_row->amount . ' 性质 ' . $fh_row->send_type);
                    if ($fh_row->send_type == 1) {
                        $to_user_id = 0;
                    } elseif ($fh_row->send_type == 2) {
                        $to_user_id = $fh_row->parent_id;
                    } else {
                        $fh_fail_send_types[] = $fh_row->id;
                        $this->info('无效发送类型' . $fh_row->send_type);
                        continue;                        //跳出
                    }
                    //查询分红领取纪录 70	QYFHLQ	契约分红领取
                    $sql = 'select * from orders o where from_user_id = ' . $fh_row->user_id . '  and to_user_id = ' . $to_user_id . ' and order_type_id = 70 and amount = \'' . $fh_row->amount . '\' and created_at >= \'2020-11-20 02:00:00\' ';
                    $lq_order_rows = DB::select($sql);
                    if ($lq_order_rows) {
                        //如果存在分红领取纪录
                        if (count($lq_order_rows) > 1) {
                            //且分红领取纪录大于1条
                            $jk_amount = $fh_row->amount * (count($lq_order_rows) - 1);//管理员扣减
                            $jk_amount_total += $jk_amount;
                            $fh_lq_fail_id[] = $fh_row->id;
                            $this->info('领取帐变错误，存在多条分红领取纪录，管理员扣减' . $jk_amount);

                            //判断这个用户是否有管理员扣减纪录 33	GLYJK	管理员减扣
                            $sql = 'select * from orders o where from_user_id = ' . $fh_row->user_id . '  and order_type_id = 33 and amount >= \'' . ($jk_amount - 1) . '\'  and amount <= \'' . ($jk_amount + 1) . '\' and created_at >= \'2020-11-20 06:00:00\' ';
                            $jk_order_rows = DB::select($sql);
                            if ($jk_order_rows) {
                                $total_jkzq_users[] = $fh_row->user_id;
                                $_tmp_jkzq_amount = 0;
                                foreach ($jk_order_rows as $jk_order_row) {
                                    $_tmp_jkzq_amount += $jk_order_row->amount;
                                }
                                $total_jkzq_amount += $_tmp_jkzq_amount;
                                $this->info('已经存在管理员扣减帐变' . count($jk_order_rows) . '笔，合计' . $_tmp_jkzq_amount);
                            } else {
                                //如果不存在
                                DB::beginTransaction();
                                try {
                                    //GLYJK	管理员减扣
                                    $order = new Orders();
                                    $order->from_user_id = $fh_row->user_id;
                                    $order->amount = $jk_amount;
                                    $order->comment = '分红单ID: ' . $fh_row->id;
                                    if (!UserFund::modifyFund($order, 'GLYJK', true)) {
                                        DB::rollBack();
                                        $total_jksb_amount += $jk_amount;
                                        $total_jksb_users[] = $fh_row->user_id;
                                        $this->info($fh_row->user_id . '扣款' . $jk_amount . '失败' . UserFund::$error_msg);
                                        continue;
                                    }
                                    $total_jkcg_amount += $jk_amount;
                                    $total_jkcg_users[] = $fh_row->user_id;
                                    $this->info($fh_row->user_id . '扣除成功，金额  ' . $jk_amount);
                                    DB::commit();
                                } catch (\Exception $e) {
                                    DB::rollBack();
                                    $total_jksb_amount += $jk_amount;
                                    $total_jksb_users[] = $fh_row->user_id;
                                    $this->info($fh_row->user_id . '扣款' . $jk_amount . '失败' . UserFund::$error_msg);
                                    $this->info("执行失败" . PHP_EOL . $e->getMessage());
                                }
                            }

                        } else {
                            $this->info('领取帐变正常');
                            $fh_lq_success_id[] = $fh_row->id;
                        }
                    } else {
                        $fh_lq_zero_id[] = $fh_row->id;
                        $this->info('领取帐变丢失，没有对应的分红领取帐变纪录');
                    }
                    //查询分红发放纪录  69	QYFHFF	契约分红发放
                    if ($to_user_id > 0) {
                        $sql = 'select * from orders o where from_user_id = ' . $to_user_id . '  and to_user_id = ' . $fh_row->user_id . ' and order_type_id = 69 and amount = \'' . $fh_row->amount . '\' and created_at >= \'2020-11-20 02:00:00\' ';
                        $ff_order_rows = DB::select($sql);
                        if ($ff_order_rows) {
                            if (count($ff_order_rows) > 1) {
                                $lp_amount = $fh_row->amount * (count($ff_order_rows) - 1);//理赔充值金额
                                $lp_amount_total += $lp_amount;
                                $fh_ff_fail_id[] = $fh_row->id;
                                $this->info('发放错误，存在多条分红领取纪录,理赔充值 ' . $lp_amount);

                                //判断这个用户是否有理赔充值纪录 17	LPCZ	理赔充值
                                $sql = 'select * from orders o where from_user_id = ' . $to_user_id . '  and order_type_id = 17 and amount >= \'' . ($lp_amount - 1) . '\'   and amount <= \'' . ($lp_amount + 1) . '\'  and created_at >= \'2020-11-20 06:00:00\' ';
                                $lp_order_rows = DB::select($sql);
                                if ($lp_order_rows) {
                                    $total_lpzq_users[] = $to_user_id;
                                    $_lpzq_amount = 0;
                                    foreach ($lp_order_rows as $lp_order_row) {
                                        $_lpzq_amount += $lp_order_row->amount;
                                    }
                                    $total_lpzq_amount += $_lpzq_amount;
                                    $this->info('已经存在管理员扣减帐变' . count($lp_order_rows) . '笔，合计' . $_lpzq_amount);
                                } else {
                                    DB::beginTransaction();
                                    try {
                                        //LPCZ	理赔充值
                                        $order = new Orders();
                                        $order->from_user_id = $to_user_id;
                                        $order->amount = $lp_amount;
                                        $order->comment = '多发:' . $fh_row->username . ',分红单ID: ' . $fh_row->id;

                                        if (!UserFund::modifyFund($order, 'LPCZ', true)) {
                                            DB::rollBack();
                                            $total_lpsb_amount += $lp_amount;
                                            $total_lpsb_users[] = $to_user_id;
                                            $this->info($to_user_id . '理赔' . $lp_amount . '失败' . UserFund::$error_msg);
                                            continue;
                                        }
                                        $total_lpcg_amount += $lp_amount;
                                        $total_lpcg_users[] = $fh_row->user_id;
                                        $this->info($to_user_id . '理赔成功，金额  ' . $lp_amount . ' user_id ');
                                        DB::commit();
                                    } catch (\Exception $e) {
                                        DB::rollBack();
                                        $total_lpsb_amount += $lp_amount;
                                        $total_lpsb_users[] = $to_user_id;
                                        $this->info($to_user_id . '理赔' . $lp_amount . '失败' . UserFund::$error_msg);
                                        $this->info("执行失败" . PHP_EOL . $e->getMessage());
                                    }
                                }
                            } else {
                                $this->info('发放正常');
                                $fh_ff_success_id[] = $fh_row->id;
                            }
                        } else {
                            $fh_ff_zero_id[] = $fh_row->id;
                            $this->info('发放丢失，没有对应的分红发放帐变纪录');
                        }
                    }
                }

                $this->info('打印汇总');

                $this->info('分红发放类型错误' . count($fh_fail_send_types) . '条 ');

                $this->info('分红领取帐变正常的分红共纪录' . count($fh_lq_success_id) . '条 ');
                $this->info('分红领取帐变不正常的分红纪录' . count($fh_lq_fail_id) . '条，多取 ' . $jk_amount_total);


                $this->info('分红领取帐变扣减成功的用户数' . count($total_jkcg_users) . '条，管理员扣减成功 ' . $total_jkcg_amount);
                $this->info('分红领取帐变扣减失败的用户数' . count($total_jksb_users) . '条，管理员扣减失败' . $total_jksb_amount);
                $this->info('分红领取帐变扣减成功的用户数' . count($total_jkzq_users) . '条，管理员扣减已存' . $total_jkzq_amount);

                $this->info('分红领取帐变丢失的分红纪录' . count($fh_lq_zero_id) . '条');

                $this->info('分红发放帐变正常，涉及分红纪录' . count($fh_ff_success_id) . '条');
                $this->info('分红发放帐变不正常的分红纪录' . count($fh_ff_fail_id) . '条，多扣' . $lp_amount_total);

                $this->info('分红发放帐变扣减成功的用户数' . count($total_lpcg_users) . '条，理赔充值成功 ' . $total_lpcg_amount);
                $this->info('分红发放帐变扣减失败的用户数' . count($total_lpsb_users) . '条，理赔充值失败' . $total_lpsb_amount);
                $this->info('分红发放帐变扣减之前的用户数' . count($total_lpzq_users) . '条，理赔充值已存' . $total_lpzq_amount);
                $this->info('分红发放帐变不存在，涉及分红纪录' . count($fh_ff_zero_id) . '条');
            } else {
                $this->info('没有分红数据');
            }
        }
    }

    private function bl_wage_fuck()
    {
        //确认是必乐平台
        $platform_type_ident = get_config('dailywage_type_ident');
        if ($platform_type_ident <> 'Bile') {
            $this->info('当前方法只允许 ' . $platform_type_ident . ' 使用');
        } else {
            //相同用户与金额的工资，查询出来
            $sql = "select from_user_id,amount,count(id) as total from orders o where created_at >= '2020-11-20 00:00:00' and order_type_id=27 group by from_user_id,amount";
            $rows = DB::select($sql);
            $total_amount = 0;//发放工资
            $total_df_amount = 0;//多发工资
            $total_kk_amount = 0;//运营扣款工资
            $total_zckk_amount = 0;//需要再次扣款的工资
            $total_kksb_amount = 0;//扣款成功
            $total_kkcg_amount = 0;//扣款失败
            $total_users = [];
            $total_kksb_users = [];//扣款失败用户
            $total_kkcg_users = [];//扣款成功用户
            foreach ($rows as $row) {
                $total_amount += $row->amount * $row->total;
                $total_users[] = $row->from_user_id;
                //相同金额如果多发
                if ($row->total > 1) {
                    $total_df_amount += $row->amount * ($row->total - 1);//合计多发金额
                    //查询九点后运营是否有管理员扣款纪录
                    $sql = 'select from_user_id,amount from orders where from_user_id= ' . $row->from_user_id . ' and order_type_id = 33 and created_at >= \'2020-11-20 09:00:00\' ';
                    $kk_rows = DB::select($sql);
                    if ($kk_rows) {
                        //如果存在，就跳过
                        foreach ($kk_rows as $kk_row) {
                            $this->info('已经存在扣除 用户 ' . $kk_row->from_user_id . '，金额 ' . $kk_row->amount);
                            $total_kk_amount += $kk_row->amount;
                        }
                    } else {
                        //如果不存在，就统计要再次扣款金额
                        $amount = ($row->total - 1) * $row->amount;
                        $this->info('需扣除 用户 ' . $row->from_user_id . '，金额 ' . $row->amount . '，总金额 ' . ($row->total * $row->amount) . ' ，多发次数' . ($row->total - 1) . '，多发金额' . $amount);
                        $total_zckk_amount += $amount;
                        DB::beginTransaction();
                        try {
                            //投注方案帐变
                            $order = new Orders();
                            $order->from_user_id = $row->from_user_id;
                            $order->amount = $amount;
                            if (!UserFund::modifyFund($order, 'GLYJK', true)) {
                                DB::rollBack();
                                $total_kksb_amount += $amount;
                                $total_kksb_users[] = $row->from_user_id;
                                $this->info($row->from_user_id . '扣款失败' . $amount . UserFund::$error_msg);
                                continue;
                            }
                            $total_kkcg_amount += $amount;
                            $total_kkcg_users[] = $row->from_user_id;
                            $this->info('扣除成功，金额  ' . $total_amount . ' user_id ' . $row->from_user_id);
                            DB::commit();

                        } catch (\Exception $e) {
                            DB::rollBack();
                            $this->info("执行失败" . PHP_EOL . $e->getMessage());
                        }
                    }
                }
            }
            $this->info('已经存在扣除 用户重复发放次数 ' . count($total_users) . '， 涉及用户数 ' . count(array_unique($total_users)) . '  ，金额 ' . $total_amount);
            $this->info('发放金额 ' . $total_amount . ' ，多发金额 ' . $total_df_amount . ' ，运营扣款金额 ' . $total_kk_amount . '，仍需扣款金额' . $total_zckk_amount);

            $this->info('扣除失败用户 ' . implode(',', $total_kksb_users) . ' ，扣款失败金额 ' . $total_kksb_amount . ' ，');
            $this->info('扣除成功用户 ' . implode(',', $total_kkcg_users) . ' ，扣款成功金额 ' . $total_kkcg_amount . ' ，');
        }
    }

    //新增三方游戏异常营利扣除表及帐变类型
    private function add_third_game_deduct()
    {
        DB::beginTransaction();
        try {
            DB::table('order_type')->insertGetId([
                'name' => '三方异常盈利扣减',
                'ident' => 'SFYCYLKJ',
                'display' => 1,
                'operation' => 2,
                'hold_operation' => 0,
                'category' => 2,
                'description' => '三方异常盈利扣减',
            ]);

            (new \CreateTableThirdGameDeduct())->up();

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //去掉旧的玩法分析表
    private function backup_method_analyse_data()
    {
        DB::beginTransaction();
        try {
            DB::statement("delete from admin_role_permissions where rule='methodanalysereport/index'");
            DB::statement('alter table report_method_analyse rename to report_method_analyse_20201217');
            DB::statement('alter table report_method_analyse_total rename to report_method_analyse_total_20201217');
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //合并彩票玩法分析数据
    private function _merge_method_analyse_data()
    {
        $sql = "
            insert into method_analyse(belong_date,lottery_id,lottery_method_id,user_id,bet_count,win_count,price,bonus,rebate)
            select belong_date,lottery_id,lottery_method_id,user_id,bet_count,win_count,price,bonus,rebate
            from report_method_analyse
            where belong_date<'2020-12-12';
            insert into method_analyse_total(belong_date,lottery_id,lottery_method_id,bet_user,lottery_bet_user,bet_count,win_count,price,bonus,rebate)
            select belong_date,lottery_id,lottery_method_id,bet_user,lottery_bet_user,bet_count,win_count,price,bonus,rebate
            from report_method_analyse_total
            where belong_date<'2020-12-12';
            ";
        DB::beginTransaction();
        try {
            DB::unprepared($sql);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //168幸运飞艇,168幸运赛艇
    private function _add_168_xyft_xyst_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'xyft168',
                'name' => '168幸运飞艇',
                'official_url' => 'https://kaicai.net',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "04", "end_sale": "30", "drop_time": "30", "end_minute": "04", "end_second": "00", "start_hour": "04", "start_minute": "04", "start_second": "00", "first_end_hour": "13", "input_code_time": "8", "first_end_minute": "09", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* 0-5,13-23 * * *',
            ],
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'xyst168',
                'name' => '168幸运赛艇',
                'official_url' => 'https://kaicai.net',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "10", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "1", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'xyft168' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ],
            ],
            'xyst168' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ],
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    //幸运大奖池开奖状态修改
    private function replace_code_status()
    {
        if (get_config('app_ident') !== 'gh') {
            $this->info('该平台不执行');
            return;
        }
        DB::beginTransaction();
        try {
            \Service\Models\JackpotPeriod::where('period', '=', '2020-03')->update(['code_status' => '0']);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    // 增加提现自动风控配置
    private function auto_risk_config()
    {
        if (Config::where('key', 'auto_risk')->first()) {
            $this->info('执行失败：auto_risk 自动风控配置已存在');
            return;
        }

        DB::beginTransaction();
        try {
            $risk_id = Config::insertGetId([
                'parent_id' => 0,
                'title' => '自动风控配置',
                'key' => 'auto_risk',
                'value' => '1',
                'description' => '用户提现自动风控审核',
            ]);

            Config::insert([
                // 进入自动风控流程条件：
                [
                    'parent_id' => $risk_id,
                    'title' => '最大自动风控审核金额',
                    'key' => 'risk_money_max',
                    'value' => '0',
                    'description' => '最大自动风控审核金额（默认：0 不进入）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '最小自动风控审核金额',
                    'key' => 'risk_money_min',
                    'value' => '0',
                    'description' => '最小自动风控审核金额（默认：0 不进入）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => 'USDT最大自动风控审核金额',
                    'key' => 'risk_usdt_max',
                    'value' => '0',
                    'description' => 'USDT最大自动风控审核金额（默认：0 不进入）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => 'USDT最小自动风控审核金额',
                    'key' => 'risk_usdt_min',
                    'value' => '0',
                    'description' => 'USDT最小自动风控审核金额（默认：0 不进入）',
                ],
                // 退出自动风控流程条件：
                [
                    'parent_id' => $risk_id,
                    'title' => '投注比充值最大倍数门槛',
                    'key' => 'risk_ctb_max',
                    'value' => '10',
                    'description' => '投注比充值最大倍数门槛：（默认：10）－充投比需包含上级充值。审核时间：上次提现到本次期间',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '盈利比充值多的最大倍数',
                    'key' => 'risk_cyb_max',
                    'value' => '10',
                    'description' => '盈利比充值多的最大倍数（默认：10）。审核时间：上次提现到本次期间',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '异地登录检查天数',
                    'key' => 'risk_login_day',
                    'value' => '10',
                    'description' => '异地登录检查天数（默认：10天）－含当天',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '异地登录退回人工',
                    'key' => 'risk_login_check',
                    'value' => '0',
                    'description' => '异地登录退回人工 0-否，1-是（默认：否）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '首次提款退回人工',
                    'key' => 'risk_first_withdrawal',
                    'value' => '1',
                    'description' => '首次提款退回人工 0-否，1-是（默认：是）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '存在解绑银行卡的用户退回人工',
                    'key' => 'risk_unbound',
                    'value' => '1',
                    'description' => '存在解绑银行卡的用户退回人工 0-否，1-是（默认：是）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '最近曾变更资金密码退回人工',
                    'key' => 'risk_security_password_check',
                    'value' => '1',
                    'description' => '最近曾变更资金密码退回人工 0-否，1-是（默认：是）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '变更资金密码检查天数',
                    'key' => 'risk_security_password_day',
                    'value' => '10',
                    'description' => '变更资金密码检查天数（默认：10）－建议限制最大天数，避免运营无限上纲',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '单笔提现金额过高检查天数',
                    'key' => 'risk_high_money_day',
                    'value' => '1',
                    'description' => '单笔提现金额过高检查天数（默认：1）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '单笔提现金额过高门槛金额',
                    'key' => 'risk_high_money_value',
                    'value' => '30000',
                    'description' => '单笔提现金额过高门槛金额（默认：30000）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '当日高于盈利上限金额',
                    'key' => 'risk_profit_max',
                    'value' => '30000',
                    'description' => '当日高于盈利上限金额（默认：30000）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '个人盈亏报表连续盈利天数',
                    'key' => 'risk_profit_day',
                    'value' => '7',
                    'description' => '个人盈亏报表连续盈利天数（默认：7）－建议限制最大天数，避免运营无限上纲',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '累计提现金额过高检查天数',
                    'key' => 'risk_withdrawal_money_day',
                    'value' => '1',
                    'description' => '累计提现金额过高检查天数（默认：1）',
                ],
                [
                    'parent_id' => $risk_id,
                    'title' => '累计提现金额过高门槛金额',
                    'key' => 'risk_withdrawal_money_max',
                    'value' => '30000',
                    'description' => '累计提现金额过高门槛金额（默认：30000）',
                ]
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function update_auto_risk_config()
    {
        DB::beginTransaction();
        try {
            Config::where('key', 'risk_ctb_max')
                ->update(['description' => '投注比充值最大倍数门槛：（默认：10）审核时间：上次提现到本次期间']);

            Config::where('key', 'risk_cyb_max')
                ->update(['description' => '盈利比充值多的最大倍数（默认：10）。审核时间：上次提现到本次期间']);

            Config::where('key', 'risk_login_day')
                ->update(['description' => '异地登录检查天数（默认：10天）']);

            Config::where('key', 'risk_unbound')
                ->update(['description' => '存在解绑银行卡的用户退回人工 0-否，1-是（默认：是）']);

            Config::where('key', 'risk_security_password_day')
                ->update(['description' => '变更资金密码检查天数（默认：10）']);

            Config::where('key', 'risk_profit_day')
                ->update(['description' => '个人盈亏报表连续盈利天数（默认：7）']);

            Config::where('key', 'risk_withdrawal_money_day')
                ->update([
                    'value' => '1',
                    'description' => '累计提现金额过高检查天数（默认：1）'
                ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //修改菜单名称
    private function _change_menu_name()
    {
        DB::beginTransaction();
        try {
            DB::statement("update admin_role_permissions set name='彩种分类' where name='彩种分类管理'");
            DB::statement("update admin_role_permissions set name='玩法分类' where name='玩法分类管理'");
            DB::statement("update admin_role_permissions set name='推荐彩种' where name='推荐彩种管理'");

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    // 新增 WithdrawalRisk 自动风控审核备注栏位
    private function add_withdrawal_risk_remark_column()
    {
        try {
            if (\Schema::hasColumn('withdrawal_risks', 'auto_risk_remark')) {
                $this->info('auto_risk_remark 字段已存在');
                return;
            }
            DB::beginTransaction();
            DB::statement("ALTER TABLE public.withdrawal_risks ADD COLUMN IF NOT EXISTS auto_risk_remark VARCHAR(200) DEFAULT '' NOT NULL");
            DB::statement("COMMENT ON COLUMN \"public\".\"withdrawal_risks\".\"auto_risk_remark\" IS '自动风控备注'");
            DB::commit();
            $this->info('withdrawal_risks 表 auto_risk_remark 字段添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //增加彩种介绍
    private function _add_lottery_introduce_table()
    {
        DB::beginTransaction();
        try {
            Schema::table('lottery', function (Blueprint $table) {
                $table->tinyInteger('introduce_status')->default(0)->comment('彩种介绍状态：0 未设置，1 已设置，2 被禁用');
            });

            (new \CreateTableLotteryIntroduce())->up();
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    /*
     * 说明：此文件为ExecuteSql.php的备份文件，所以此文件里的方法不允许其他地方调用。
     * 不要修改此文件了
     */
}
