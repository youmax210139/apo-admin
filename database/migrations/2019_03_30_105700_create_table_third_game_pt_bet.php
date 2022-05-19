<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGamePtBet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_pt_bet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('third_game_ident', 32)->default('')->comment('游戏接口ident');
            $table->integer('user_id')->comment('用户ID');
            $table->string('game', 64)->comment('游戏名称或标识');
            $table->timestamp('game_date')->nullable()->comment('游戏时间');
            $table->string('game_type', 32)->comment('游戏类型');
            $table->string('bet_id', 32)->comment('平台返回的投注记录ID');
            $table->decimal('total_bets', 15, 4)->comment('总共投注');
            $table->decimal('total_wins', 15, 4)->comment('盈亏(+:玩家赢, -:玩家输)');
            $table->decimal('win', 15, 4)->default(0)->comment('奖金[Win]');
            $table->decimal('balance', 15, 4)->default(0)->comment('结余[Balance]');
            $table->string('game_code', 20)->default('')->comment('游戏代码');
            $table->tinyInteger('rebate_status')->default(0)->comment('0:还没有返水,1:已经返水,2:返水错误');
            $table->string('remark', 250)->comment('备注');
            $table->unique(['user_id', 'bet_id', 'game_type']);
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
            Schema::dropIfExists('third_game_pt_bet');
        }
    }
}
