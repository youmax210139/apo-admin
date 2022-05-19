<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSmsCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 32)->unique()->comment('中文名称');
            $table->boolean('enabled')->default(true)->comment('状态');
            $table->timestamps();
        });
        $this->_data();
    }

    private function _data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '短信接口管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon' => 'fa-envelope',
                'rule' => 'sms',
                'name' => '短信接口管理',
            ]);
        } else {
            $id = $row->id;
        }
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'smscategory/index',
                'name' => '短信渠道管理',
            ],
            [
                'parent_id' => $id,
                'rule' => 'smscategory/create',
                'name' => '添加短信渠道',
            ],
            [
                'parent_id' => $id,
                'rule' => 'smscategory/edit',
                'name' => '编辑短信渠道',
            ],
            [
                'parent_id' => $id,
                'rule' => 'smscategory/delete',
                'name' => '删除短信渠道',
            ],

            [
                'parent_id' => $id,
                'rule' => 'smschannel/index',
                'name' => '短信通道管理',
            ],
            [
                'parent_id' => $id,
                'rule' => 'smschannel/create',
                'name' => '添加短信通道',
            ],
            [
                'parent_id' => $id,
                'rule' => 'smschannel/edit',
                'name' => '编辑短信通道',
            ],
            [
                'parent_id' => $id,
                'rule' => 'smschannel/delete',
                'name' => '删除短信通道',
            ],
            [
                'parent_id' => $id,
                'rule' => 'smschannel/send',
                'name' => '发送短信',
            ],
            [
                'parent_id' => $id,
                'rule' => 'smschannel/refreshserver',
                'name' => '同步短信通道到服务器',
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
            Schema::dropIfExists('sms_category');
        }
    }
}
