<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLotteryMethodPrizeLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_method_prize_level', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lottery_id')->defualt(0)->comment("彩种ID");
            $table->integer('lottery_method_id')->defualt(0)->comment("玩法ID");
            $table->json('prize_level')->default('[]')->comment('奖金');
            $table->unique(['lottery_id','lottery_method_id']);
        });

        $this->data();
    }

    private function data()
    {
        $inserts_data = [];

        //幸运飞艇
        $lottery = DB::table('lottery')->where('ident', 'xyftpk10')->first(['id']);
        if (!empty($lottery)) {
            $lottery_id = $lottery->id;
            $inserts_data[] = [
                'lottery_id'=>$lottery_id,
                'lottery_method_id'=> 172101001,//盘口模式 冠亚军和 - 大
                'prize_level'=>json_encode([2.2882]),
            ];
            $inserts_data[] = [
                'lottery_id'=>$lottery_id,
                'lottery_method_id'=> 172101002, //盘口模式 冠亚军和 - 小
                'prize_level'=>json_encode([1.7395]),
            ];
            $inserts_data[] = [
                'lottery_id'=>$lottery_id,
                'lottery_method_id'=> 172101003, //盘口模式 冠亚军和 - 单
                'prize_level'=>json_encode([1.7395]),
            ];
            $inserts_data[] = [
                'lottery_id'=>$lottery_id,
                'lottery_method_id'=> 172101004, //盘口模式 冠亚军和 - 双
                'prize_level'=>json_encode([2.2882]),
            ];
        }

        //写入数据
        if ($inserts_data) {
            DB::table('lottery_method_prize_level')->insert($inserts_data);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('lottery_method_prize_level');
        }
    }
}
