<?php

use Illuminate\Database\Migrations\Migration;

class CreateTableReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE OR REPLACE FUNCTION count_estimate(query text) RETURNS INTEGER AS
            $function$
            DECLARE
              rec   record;
              ROWS  INTEGER;
            BEGIN
              FOR rec IN EXECUTE \'EXPLAIN \' || query LOOP
                ROWS := SUBSTRING(rec."QUERY PLAN" FROM \' rows=([[:digit:]]+)\');
                EXIT WHEN ROWS IS NOT NULL;
              END LOOP;

              RETURN ROWS;
            END
            $function$
            LANGUAGE plpgsql;
        ');

        $this->data();
    }


    /***
     * 插入路由数据
     */
    private function data()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon' => 'fa-area-chart',
            'rule' => 'report',
            'name' => '报表管理',
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'report/index',
                'name' => '平台报表概览',
            ],
            [
                'parent_id' => $id,
                'rule' => 'lotteryreport/index',
                'name' => '游戏报表总览',
            ],
            [
                'parent_id' => $id,
                'rule' => 'lotterysinglesale/index',
                'name' => '单期盈亏报表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'lotteryprofit/index',
                'name' => '彩票盈亏报表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'lotteryprofit/detail',
                'name' => '游戏详情',
            ],
            [
                'parent_id' => $id,
                'rule' => 'lotterybonus/index',
                'name' => '彩票分红报表(汇总版)',
            ],
            [
                'parent_id' => $id,
                'rule' => 'lotterybonusrealtime/index',
                'name' => '彩票分红报表(实时版)',
            ],
            [
                'parent_id' => $id,
                'rule' => 'rechargereport/index',
                'name' => '充提报表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'balancereport/index',
                'name' => '团队余额',
            ],
            [
                'parent_id' => $id,
                'rule' => 'userreport/index',
                'name' => '个人盈亏排行',
            ],
            [
                'parent_id' => $id,
                'rule' => 'userprofit/index',
                'name' => '个人盈亏报表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'reportdateuser/index',
                'name' => '每日用户报表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'reportsingle/index',
                'name' => '单项查询报表',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
