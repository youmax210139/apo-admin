<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSmsMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_message', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->integer('channel_id')->comment('渠道ID');
            $table->string('phone', 11)->comment('手机号码');
            $table->string('message', 255)->default('')->comment('短信内容');
            $table->integer('server_id')->default(0)->comment('发送的服务器id');
            $table->string('admin_name', 60)->default('')->comment('管理员');
            $table->smallInteger('status')->default(0)->comment('是否验证过。0未发送成功,1发送到中间站');
            $table->timestamps();
            $table->index('user_id');
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
            Schema::dropIfExists('sms_message');
        }
    }
}
