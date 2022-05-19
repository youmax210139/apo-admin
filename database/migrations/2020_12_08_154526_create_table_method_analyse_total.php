<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMethodAnalyseTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('method_analyse_total', function (Blueprint $table) {
            $table->date('belong_date')->comment('所属日期');
            $table->smallInteger('lottery_id')->comment('彩种 ID');
            $table->integer('lottery_method_id')->comment('玩法 ID');
            $table->integer('bet_user')->default(0)->comment('投注人数');
            $table->integer('lottery_bet_user')->default(0)->comment('按彩种核算的投注人数');
            $table->integer('bet_count')->comment('投注单数');
            $table->integer('win_count')->comment('中奖单数');
            $table->decimal('price', 14, 4)->comment('投注额');
            $table->decimal('bonus', 14, 4)->comment('奖金');
            $table->decimal('rebate', 14, 4)->comment('返点');

            $table->unique(['belong_date', 'lottery_id', 'lottery_method_id']);
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
            Schema::dropIfExists('method_analyse_total');
        }
    }
}
