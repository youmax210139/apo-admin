<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableChatDeposit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_deposit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('deposit_id')->comment('充值订单ID');
            $table->tinyInteger('connect_status')->default(1)->comment('状态：0：断开 1：连接');
            $table->tinyInteger('read_status')->default(0)->comment('状态：0：未读 1：已读');
            $table->timestamp('last_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('查看时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('修改时间');
            $table->integer('last_msg_id')->default(0)->comment('最新用户消息');
            $table->integer('kefu_no')->default(1)->comment('客服编号');

            $table->unique(['user_id', 'deposit_id']);

            $table->index('last_at');
            $table->index(['deposit_id', 'user_id', 'kefu_no']);
        });
        $this->_data();
    }

    private function _data()
    {
        $row = DB::table('admin_role_permissions')->select(['id'])->where('name', '充提管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'chatdeposit/index',
                'name' => '充值会话管理',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'chatdeposit/newmessage',
                'name' => '接收新消息',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'chatdeposit/sendmsg',
                'name' => '发送新消息',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'chatdeposit/payment',
                'name' => '代理充值配置',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'chatdeposit/autokeyword',
                'name' => '自动回复语句',
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
            Schema::dropIfExists('chat_deposit');
        }
    }
}
