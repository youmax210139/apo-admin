<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameRebate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_rebate', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->integer('third_game_id')->unsigned()->comment('所属平台接口ID');
            $table->integer('third_game_platform_id')->default(0)->unsigned()->comment('所属平台ID');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('bet_record_id')->comment('投注记录表对应id');
            $table->smallInteger('order_type_id')->comment('帐变类型id');
            $table->decimal('amount', 14, 4)->comment('本条账变所产生的资金变化量');
            $table->timestamp('game_date')->comment('游戏投注时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('账变时间');
            $table->string('comment', 256)->default('')->comment('备注');

            $table->unique(['third_game_id', 'user_id', 'bet_record_id']);
            $table->index(['third_game_id', 'order_type_id', 'game_date']);
            $table->index(['user_id', 'game_date']);
            $table->index(['game_date']);
        });

        $this->data();
    }

    private function data()
    {
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('third_game_rebate');
        }
    }
}
