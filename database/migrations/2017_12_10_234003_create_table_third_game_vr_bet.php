<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameVrBet extends Migration
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
        Schema::create('third_game_vr_bet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('third_game_ident', 32)->default('')->comment('游戏接口ident');

            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->string('player_name', 30)->comment('三方玩家名称');
            $table->decimal('merchant_prize', 15, 4)->unsigned()->default(0)->comment('商户中奖金额');
            $table->decimal('player_prize', 15, 4)->unsigned()->default(0)->comment('玩家中奖金额');
            $table->decimal('loss_prize', 15, 4)->unsigned()->default(0)->comment('重新颁奖损失');
            $table->tinyInteger('state')->unsigned()->default(0)->comment('投注状态 0:未颁奖, 1:撤单, 2:未中奖, 3:中奖');
            $table->tinyInteger('sub_state')->unsigned()->default(0)->comment('次级状态, 1:玩家撤单, 2:管理员撤单, 4:整期撤单, 8:重新颁奖');
            $table->decimal('unit', 15, 4)->comment('投注单位');
            $table->integer('multiple')->unsigned()->default(0)->comment('投注倍数');
            $table->integer('count')->unsigned()->default(0)->comment('投注注数');
            $table->decimal('cost', 15, 4)->unsigned()->default(0)->comment('投注金额');
            $table->string('odds', 100)->comment('赔率');
            $table->mediumText('number')->comment('投注数字');
            $table->string('position', 128)->comment('投注位置');
            $table->integer('channel_id')->unsigned()->comment('频道ID');
            $table->string('channel_name', 200)->comment('频道名称');
            $table->string('bet_type_name', 256)->comment('投注种类名称');
            $table->string('issue_number', 128)->comment('期号');
            $table->string('winning_number', 128)->comment('开奖数字');
            $table->string('note', 256)->comment('三方备注');
            $table->text('prize_detail')->comment('中奖明细{[awardName 中奖名称],[number 中奖数字], [count 中奖注数]}');
            $table->string('game', 32)->comment('游戏名称或标识');
            $table->string('game_type', 32)->comment('游戏类型');
            $table->timestamp('game_date')->comment('游戏时间');
            $table->string('bet_id', 32)->comment('平台返回的投注记录ID');
            $table->string('status', 32)->comment('注单状态');
            $table->decimal('total_bets', 15, 4)->comment('总共投注');
            $table->decimal('total_wins', 15, 4)->comment('盈亏(+:玩家赢, -:玩家输)');
            //$table->decimal('total_rebate', 15, 4)->default(0)->comment('返水总额');
            $table->tinyInteger('rebate_status')->default(0)->comment('0:还没有返水,1:已经返水,2:返水错误');
            $table->tinyInteger('rebate_extra_status')->default(0)->comment('0:还没有返水,1:已经返水,2:返水错误');
            $table->string('remark', 250)->comment('备注');

            $table->unique(['user_id', 'bet_id', 'game_type']);
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
            Schema::dropIfExists('third_game_vr_bet');
        }
    }
}
