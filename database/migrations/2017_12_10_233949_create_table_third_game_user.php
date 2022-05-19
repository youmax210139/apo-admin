<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 注册了第三方游戏的玩家
         */
        Schema::create('third_game_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->integer('third_game_id')->unsigned()->comment('所属平台接口ID');
            $table->string('user_name_third', 32)->comment('平台用户名');
            $table->string('password', 256)->comment('平台登入密码');
            $table->decimal('balance', 15, 4)->unsigned()->default(0)->comment('游戏账户余额');
            //$table->decimal('deposit_sum', 15, 4)->unsigned()->default(0)->comment('累计转入金额');
            //$table->decimal('withdraw_sum', 15, 4)->unsigned()->default(0)->comment('累计转出金额');
            //$table->integer('deposit_times')->unsigned()->default(0)->comment('累计转入次数');
            //$table->integer('withdraw_times')->unsigned()->default(0)->comment('累计转出次数');
            //$table->decimal('deposit_max', 15, 4)->unsigned()->default(0)->comment('单次最大转入金额');
            //$table->decimal('withdraw_max', 15, 4)->unsigned()->default(0)->comment('单次转出最大金额');
            //$table->tinyInteger('status')->unsigned()->default(0)->comment('状态 0没有玩过，1正常玩家');
            //$table->integer('login_count')->unsigned()->default(0)->comment('用户登录游戏次数');
            $table->ipAddress('created_ip')->nullable()->comment('注册IP');
            $table->ipAddress('last_login_ip')->comment('用户最后登录游戏IP');
            $table->timestamp('last_login_time')->comment('用户最后登录游戏时间');
            $table->tinyInteger('is_lock')->unsigned()->default(0)->comment('是否锁定0否1是');
            $table->smallInteger('client_type')->comment('客户端类型');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('更新时间');


            $table->unique(['user_id', 'third_game_id'], 'user_id');
            $table->index(['user_name_third', 'third_game_id'], 'user_name_third');
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
            Schema::dropIfExists('third_game_user');
        }
    }
}
