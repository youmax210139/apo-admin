<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportThirdGameUserProfit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_third_game_user_profit', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('数据日期');
            $table->integer('platform_id')->comment('所属游戏平台ID');
            $table->integer('user_id')->comment('用戶ID');
            $table->jsonb('data')->comment('数据详情');
            $table->timestamp('updated_at')->comment('更新时间');

            $table->unique(['date', 'platform_id', 'user_id']);
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
            Schema::dropIfExists('report_third_game_user_profit');
        }
    }
}
