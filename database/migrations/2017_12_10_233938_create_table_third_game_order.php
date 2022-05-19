<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_num', 32)->comment('订单号');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('order_type_id')->default(0)->comment('帐变类型ID');
            $table->decimal('amount', 15, 4)->comment('订单金额');
            $table->integer('from')->unsigned()->comment('来源平台接口');
            $table->integer('to')->unsigned()->comment('目标平台接口');
            $table->integer('from_platform')->default(0)->unsigned()->comment('来源平台');
            $table->integer('to_platform')->default(0)->unsigned()->comment('目标平台');
            $table->tinyInteger('status')->default(0)->comment('第三方平台互转状态 0-订单创建初始化 1-目标平台入帐成功 2-来源平台扣款成功 3-来源平台扣款网络异常 4-来源平台扣款失败 5-目标平台入帐网络异常 6-目标平台入帐失败');
            $table->tinyInteger('refund_status')->default(0)->comment('退款状态 0-订单初始值 1-退款成功 2-退款网络异常 3-退款失败');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('更新时间');
            $table->ipAddress('created_ip', 32)->nullable()->comment('订单插入IP');
            $table->string('remark', 128)->comment('备注');

            $table->index(['user_id', 'from', 'to', 'created_at']);
            $table->index(['order_num', 'from', 'to']);
            $table->index(['order_type_id', 'from', 'to']);
        });
        $this->data();
    }

    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '第三方游戏')->where('parent_id', 0)->first();

        if (empty($row)) {
            return;
        }

        $id = $row->id;

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'thirdgameuser/index',
                'name' => '注册用户',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgameuser/refresh',
                'name' => '刷新余额',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgameuser/lock',
                'name' => '锁定/解锁',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgameorder/index',
                'name' => '转帐管理',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgamebet/index',
                'name' => '投注纪录',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgameprofitloss/index',
                'name' => '盈亏报表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgameuserprofit/index',
                'name' => '个人三方日报表',
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
            Schema::dropIfExists('third_game_order');
        }
    }
}
