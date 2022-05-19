<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableUserPrivateReturnContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_private_return_contract', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->tinyInteger('time_type')->default(0)->comment('时间类型: 1-日, 2-小时, 3-奖期, 4-实时');
            $table->tinyInteger('condition_type')->default(0)->comment('私返类型: 1-销量, 2-活跃, 3-销量+活跃, 4-盈亏, 5-销量+盈亏, 6-活跃+盈亏, 7-全部');
            $table->tinyInteger('cardinal_type')->default(0)->comment('私返基数: 1-销量, 4-盈亏');
            $table->decimal('top_rate', 15, 4)->default(0)->comment('最高比例');
            $table->jsonb('content')->default('[]')->comment('契约');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态: 0-生效中, 1-已失效');
            $table->tinyInteger('stage')->unsigned()->nullable()->comment('操作位置: 1-前台, 2-后台');
            $table->string('deleted_username', 20)->nullable()->comment('终结者');
            $table->string('created_username', 20)->comment('创建者');
            $table->timestamp('deleted_at')->nullable()->comment('失效时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->index(['user_id', 'status']);
        });

        $this->__permissions();
    }


    private function __permissions()
    {
        $row = DB::table('admin_role_permissions')->where('name', '用户管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }
        $user_private_return_row = DB::table('admin_role_permissions')->where('rule','user/userprivatereturn')->first();
        if($user_private_return_row){
            return;
        }
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'user/userprivatereturn',
                'name' => '私返设置',
            ],[
                'parent_id' => $row->id,
                'rule' => 'userprivatereturn/index',
                'name' => '私返契约列表',
            ],[
                'parent_id' => $row->id,
                'rule' => 'userprivatereturn/record',
                'name' => '私返契约历史',
            ],[
                'parent_id' => $row->id,
                'rule' => 'userprivatereturn/edit',
                'name' => '私返契约编辑',
            ],[
                'parent_id' => $row->id,
                'rule' => 'userprivatereturn/delete',
                'name' => '私返契约删除',
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
            Schema::dropIfExists('user_private_return_contract');
        }
    }
}
