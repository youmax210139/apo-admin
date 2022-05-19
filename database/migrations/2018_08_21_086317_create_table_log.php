<?php

use Illuminate\Database\Migrations\Migration;

class CreateTableLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->data();
    }


    /***
     * 插入路由数据
     */
    private function data()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon' => 'fa-terminal',
            'rule' => 'log',
            'name' => '行为日志管理',
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'behaviorlog/index',
                'name' => '异常行为监控',
            ],
            [
                'parent_id' => $id,
                'rule' => 'behaviorlog/detail',
                'name' => '查看异常行为详情',
            ],
            [
                'parent_id' => $id,
                'rule' => 'loginlog/index',
                'name' => '登录日志查询',
            ],
            [
                'parent_id' => $id,
                'rule' => 'requestlog/index',
                'name' => '请求日志查询',
            ],
            [
                'parent_id' => $id,
                'rule' => 'requestlog/detail',
                'name' => '查看请求日志详情',
            ],
            [
                'parent_id' => $id,
                'rule' => 'syslog/index',
                'name' => '系统日志',
            ],
            [
                'parent_id' => $id,
                'rule' => 'syslog/download',
                'name' => '下载系统日志',
            ],
            [
                'parent_id' => $id,
                'rule' => 'rebateslog/index',
                'name' => '用户返点修改记录查询',
            ]
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
