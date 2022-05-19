<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('message_id')->default(0)->comment('关联的消息ID');
            $table->integer('sender_id')->default(0)->comment('发送者ID');
            $table->integer('receiver_id')->default(0)->comment('接收者ID');
            $table->tinyInteger('message_type')->default(0)->comment('消息类型 0 普通消息 1 中奖 2 充值,3 提现,4 上级消息,5 下级消息');
            $table->tinyInteger('sender_type')->default(0)->comment('发送用户类型 0 用户 1 管理员');
            $table->tinyInteger('sender_status')->default(0)->comment('发件箱中的状态：0--普通；1--删除');
            $table->tinyInteger('receiver_status')->default(0)->comment('收件箱状态：0--普通；1--删除');
            $table->timestamp('read_at')->nullable()->comment('阅读时间');

            $table->index(['receiver_id', 'receiver_status', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('user_messages');
        }
    }
}
