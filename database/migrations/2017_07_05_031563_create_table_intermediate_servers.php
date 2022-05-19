<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIntermediateServers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intermediate_servers', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 40)->comment('服务器名称');
            $table->ipAddress('ip')->comment('服务器IP');
            $table->string('domain', 128)->comment('同步接口');
            $table->boolean('status')->default(0)->comment('是否启用');
        });

        $this->data();
    }

    public function data()
    {
        $server_id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon'      => 'fa-server',
            'rule'      => 'intermediateservers',
            'name'      => '中间站管理',
        ]);

        //支付服务器菜单
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $server_id,
                'rule'      => 'intermediateservers/index',
                'name'      => '支付服务器管理',
            ],
            [
                'parent_id' => $server_id,
                'rule'      => 'intermediateservers/create',
                'name'      => '添加支付服务器',
            ],
            [
                'parent_id' => $server_id,
                'rule'      => 'intermediateservers/edit',
                'name'      => '编辑支付服务器',
            ],
            [
                'parent_id' => $server_id,
                'rule'      => 'intermediateservers/setstatus',
                'name'      => '设置支付服务器状态',
            ],
            [
                'parent_id' => $server_id,
                'rule'      => 'intermediateservers/delrecord',
                'name'      => '删除支付服务器',
            ],
            [
                'parent_id' => $server_id,
                'rule'      => 'intermediateservers/refreshserver',
                'name'      => '同步通道到服务器',
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
        if (app()->environment() != 'production') {
            Schema::dropIfExists('intermediate_servers');
        }
    }
}
