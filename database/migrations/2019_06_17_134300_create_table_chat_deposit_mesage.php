<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableChatDepositMesage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('chat_deposit_message', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chat_deposit_id')->comment('会话记录ID');
            $table->integer('from_user_id')->comment('发送人，0：系统');
            $table->integer('to_user_id')->comment('接收人，0：系统');
            $table->string('message', 255)->comment('消息内容');
            $table->string('img', 255)->default('')->comment('图片地址');
            $table->tinyInteger('status')->default(0)->comment('状态：0：未读 1：已读');
            $table->tinyInteger('type')->default(0)->comment('类型：0：正常 ');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('修改时间');

            $table->index(['chat_deposit_id','from_user_id','to_user_id','status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        if (app()->environment() != 'production') {
            Schema::dropIfExists('chat_deposit_message');
        }
    }
}
