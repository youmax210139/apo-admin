<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameSunbetBet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_sunbet_bet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('third_game_ident', 32)->default('')->comment('游戏接口ident');
            
            $table->integer('user_id')->comment('用户ID');
            $table->string('game', 32)->comment('游戏名称或标识');
            $table->timestamp('game_date')->nullable()->comment('游戏时间');
            $table->string('game_type', 32)->comment('gameprovidercode 游戏提供者编码');
            $table->string('bet_id', 50)->comment('平台返回的投注记录ID');
            $table->string('status', 32)->default(0)->comment('roundstatus 报告回合时的状态，[New=0，Open=1，Closed=2]');
            $table->decimal('total_bets', 15, 4)->comment('winloss 投注净总金额(winamt_riskamt)');
            $table->decimal('total_wins', 15, 4)->comment('winamt 投注赢的金额');

            $table->string('tx_id', 50)->default('')->comment('游戏提供商的交易或投注识别码');
            $table->timestamp('bet_closed_on')->nullable()->comment('投注关闭时间，包括时区偏移');
            $table->timestamp('bet_updated_on')->nullable()->comment('投注更新的时间，包括时区偏移');
            $table->string('round_id', 50)->default(0)->comment('游戏交易执行回合(round)时游戏提供商的辨识码');
            $table->decimal('before_bal', 15, 4)->default(0)->comment('投注交易前玩家的余额');
            $table->string('game_id', 10)->default(0)->comment('游戏ID');


            //$table->decimal('total_rebate', 15, 4)->default(0)->comment('返水总额');
            $table->tinyInteger('rebate_status')->default(0)->comment('0:还没有返水,1:已经返水,2:返水错误');
            $table->string('remark', 250)->comment('备注');
            $table->unique(['user_id', 'bet_id','game_type']);
            $table->index(['user_id']);
            $table->index(['bet_id']);
            $table->index(['game_date']);
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
            Schema::dropIfExists('third_game_sunbet_bet');
        }
    }
}
