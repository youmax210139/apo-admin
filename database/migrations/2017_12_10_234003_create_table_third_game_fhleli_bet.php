<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameFhLeliBet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * VR投注纪录
         */
        Schema::create('third_game_fhleli_bet', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->string('third_game_ident', 32)->default('')->comment('游戏接口ident');


            $table->string('user_account', 30)->comment('三方玩家名称');
            $table->string('issue_no', 30)->comment('期号');
            $table->integer('nums')->unsigned()->default(0)->comment('投注注数');
            $table->integer('count')->unsigned()->default(0)->comment('投注倍数');
            $table->decimal('confirm_amount', 15, 4)->unsigned()->default(0)->comment('有效投注额');
            $table->string('win_number', 100)->default('')->comment('中奖号码');
            $table->integer('win_count')->unsigned()->default(0)->comment('中奖注数');
            $table->decimal('win_amount', 15, 4)->unsigned()->default(0)->comment('中奖金额');
            $table->string('lottery_number', 100)->default('')->comment('开奖号码');
            $table->decimal('unit', 15, 4)->default(0)->comment('投注单位，1.0000 元模式，0.1000 角模式，0.0100 分模式，0.0010 厘模式');
            $table->integer('odds')->default(0)->comment('奖金模式');
            $table->text('content')->default('')->comment('投注内容');
            $table->integer('cancel_status')->comment('撤单状态 0 未撤单1 用户撤单成功 2用户撤单失败 3 支付失败 4 系统撤单成功 5 系统撤单失败');
            $table->string('lottery_code', 32)->comment('彩票code');


            $table->string('game', 32)->comment('游戏名称或标识');
            $table->string('game_type', 32)->comment('游戏类型');
            $table->timestamp('game_date')->comment('游戏时间');
            $table->string('bet_id', 32)->comment('平台返回的投注记录ID');
            $table->string('status', 32)->comment('注单状态 0 未开奖1 未中奖 2 已中奖');
            $table->decimal('total_bets', 15, 4)->comment('总共投注');
            $table->decimal('total_wins', 15, 4)->comment('盈亏(+:玩家赢, -:玩家输)');
            $table->tinyInteger('rebate_status')->default(0)->comment('0:还没有返水,1:已经返水,2:返水错误');
            $table->string('remark', 250)->comment('备注');

            $table->unique(['user_id', 'bet_id', 'game_type']);
            $table->index(['user_id', 'status', 'cancel_status']);
            $table->index('bet_id');
            $table->index('user_id');
            $table->index('game_date');
            $table->index('total_wins');
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
            Schema::dropIfExists('third_game_fhleli_bet');
        }
    }
}
