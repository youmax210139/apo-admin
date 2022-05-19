<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFlySystemConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fly_system_config', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('ident', 64)->unique()->comment('唯一英文标识');
            $table->string('name', 64)->unique()->comment('名称');
            $table->string('lottery_idents', 256)->comment('彩种标识');
            $table->string('domain', 128)->comment('推送域名');
            $table->jsonb('config')->default('{}')->comment("参数配置");
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态:0禁用,1启用');
        });
        $this->data();
    }

    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '游戏管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            return;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'flysystemconfig/index',
                'name' => '飞单配置',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'flysystemconfig/create',
                'name' => '创建飞单配置',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'flysystemconfig/edit',
                'name' => '编辑飞单配置',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'flysystemconfig/status',
                'name' => '启用或禁用飞单配置',
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
        if (app()->environment() != 'production') {
            Schema::dropIfExists('fly_system_config');
        }
    }
}
