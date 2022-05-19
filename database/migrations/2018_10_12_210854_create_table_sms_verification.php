<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSmsVerification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_verification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->integer('channel_id')->comment('渠道ID');
            $table->integer('server_id')->default(0)->comment('发送的服务器id');
            $table->string('phone', 11)->comment('手机号码');
            $table->string('code', 11)->comment('验证码');
            $table->string('type', 20)->comment('验证类型');
            $table->smallInteger('status')->default(0)->comment('是否验证过。0未发送成功,1发送到中间站,2用户已验证');
            $table->timestamps();
            $table->index(['user_id','phone','type','status']);
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
            Schema::dropIfExists('sms_verification');
        }
    }
}
