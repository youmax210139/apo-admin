<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDrawClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draw_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->default('')->comment('客户端名称');
            $table->string('ident', 32)->unique()->comment('客户端标识');
            $table->boolean('request_status')->default(false)->comment('请求状态 1：启用 0:禁用');
            $table->string('request_key', 32)->default('')->comment('请求秘钥:32位');
            $table->string('request_ips', 256)->default('')->comment('请求IP地址，多个用,分隔。unlimited无限的');
            $table->boolean('push_status')->default(false)->comment('推送状态 1：启用 0:禁用');
            $table->string('push_key', 32)->default('')->comment('推送秘钥:32位');
            $table->string('push_url', 256)->default('')->comment('推送地址');
            $table->timestamp('created_at')->comment('创建时间');
            $table->timestamp('updated_at')->comment('修改时间');

            $table->index('request_status');
            $table->index('push_status');
        });

        //$this->data();
    }

    private function data()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon' => 'fa-truck',
            'rule' => 'drawclients',
            'name' => '号源客户',
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'drawclients/index',
                'name' => '客户列表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'drawclients/create',
                'name' => '添加客户',
            ],
            [
                'parent_id' => $id,
                'rule' => 'drawclients/edit',
                'name' => '修改客户',
            ],
            [
                'parent_id' => $id,
                'rule' => 'drawclients/delete',
                'name' => '删除客户',
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
            Schema::dropIfExists('draw_clients');
        }
    }
}
