<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportLotteryTotalCompressed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_lottery_total_compressed', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->integer('user_id')->comment('用户 ID');
            $table->smallInteger('lottery_id')->comment('彩种 ID');
            $table->string('issue', 32)->comment('奖期');
            $table->bigInteger('package_id')->default(0)->comment('Package ID');
            $table->decimal('price', 14, 4)->comment('投注额');
            $table->decimal('bonus', 14, 4)->comment('奖金');
            $table->decimal('rebate', 14, 4)->comment('返点');
            $table->timestamp('created_at')->comment('写入时间');

            $table->unique(['user_id', 'lottery_id', 'issue', 'package_id']);
            $table->index(['user_id', 'created_at', 'price', 'bonus', 'rebate']);
            $table->index(['created_at', 'lottery_id']);
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
            Schema::dropIfExists('report_lottery_total_compressed');
        }
    }
}
