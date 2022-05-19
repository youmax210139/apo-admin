<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePumpRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pump_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户 ID');
            $table->jsonb('content')->default('[]')->comment('备注');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态，0-生效中,1-已失效');
            $table->tinyInteger('stage')->unsigned()->nullable()->comment('操作位置：1-前台，2-后台');
            $table->string('created_username', 20)->comment('创建者');
            $table->string('updated_username', 20)->nullable()->comment('更新人');
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
        $this->_permissions();
    }

    private function _permissions()
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
                'rule' => 'pumprule/index',
                'name' => '抽返水规则列表',
            ], [
                'parent_id' => $row->id,
                'rule' => 'pumprule/detail',
                'name' => '规则明细',
            ], [
                'parent_id' => $row->id,
                'rule' => 'pumprule/history',
                'name' => '规则历史',
            ], [
                'parent_id' => $row->id,
                'rule' => 'pumprules/create',
                'name' => '创建规则',
            ], [
                'parent_id' => $row->id,
                'rule' => 'pumprules/edit',
                'name' => '编辑规则',
            ], [
                'parent_id' => $row->id,
                'rule' => 'pumprule/delete',
                'name' => '删除规则',
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
            Schema::dropIfExists('pump_rules');
        }
    }
}
