<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserLotteryTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_lottery_total', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户 ID')->unsigned();
            $table->integer('bet_times')->default(0)->comment('累计投注次数')->unsigned();
            $table->integer('win_times')->default(0)->comment('累计中奖次数')->unsigned();
            $table->integer('share_times')->default(0)->comment('累计推单次数')->unsigned();
            $table->integer('share_win_times')->default(0)->comment('累计推单中奖次数')->unsigned();
            $table->decimal('price', 14, 4)->default(0)->comment('累计投注金额')->unsigned();
            $table->decimal('bonus', 14, 4)->default(0)->comment('累计中奖金额')->unsigned();
            $table->decimal('rebate', 14, 4)->default(0)->comment('累计返点金额')->unsigned();
            $table->decimal('share_price', 14, 4)->default(0)->comment('累计推单投注金额')->unsigned();
            $table->decimal('share_bonus', 14, 4)->default(0)->comment('累计推单中奖金额')->unsigned();
            $table->integer('user_level_id')->nullable()->comment('用户层级ID');
            $table->unique('user_id');
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
            Schema::dropIfExists('user_lottery_total');
        }
    }
}
