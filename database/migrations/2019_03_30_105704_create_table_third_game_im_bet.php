<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameImBet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_im_bet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('third_game_ident', 32)->default('')->comment('游戏接口ident');
            
            $table->integer('user_id')->comment('用户ID');
            $table->string('game', 64)->comment('游戏名称或标识');
            $table->timestamp('game_date')->nullable()->comment('游戏时间');
            $table->string('game_type', 32)->comment('游戏类型');
            $table->string('bet_id', 32)->comment('平台返回的投注记录ID');
            $table->string('status', 32)->default(0)->comment('注单状态');
            $table->decimal('total_bets', 15, 4)->comment('总共投注');
            $table->decimal('total_wins', 15, 4)->comment('盈亏(+:玩家赢, -:玩家输)');


            $table->string('player_id', 30)->default(0)->comment('三方玩家名称');
            $table->string('platform', 10)->default('')->comment('web mobile');
            $table->tinyInteger('is_cancelled')->default(0)->comment('0未撤单，1撤单');
            $table->tinyInteger('is_settled')->default(0)->comment('0未结算 1已经结算');
            $table->string('detail_items', 1000)->default('')->comment('详情');
            $table->string('odds_type', 10)->default('')->comment('赔率类别，香港，欧洲，马来，印尼盘');
            $table->string('currency', 10)->default('')->comment('货币类型');
            $table->string('wager_type', 10)->default('')->comment('注单类型，single单一，parlayall混合过关');
            $table->string('game_id', 20)->default(0)->comment('IM内部游戏ID');
            $table->string('provider', 20)->default('')->comment('游戏类别');


            //$table->decimal('total_rebate', 15, 4)->default(0)->comment('返水总额');
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
            Schema::dropIfExists('third_game_im_bet');
        }
    }
}
