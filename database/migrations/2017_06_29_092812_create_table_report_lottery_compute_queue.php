<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportLotteryComputeQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_lottery_total_compute_queue', function (Blueprint $table) {
            $table->integer('user_id')->comment('用户 ID');
            $table->smallInteger('lottery_id')->comment('彩种 ID');
            $table->string('issue', 32)->comment('奖期');

            $table->unique(['user_id', 'lottery_id', 'issue']);
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
            Schema::dropIfExists('report_lottery_total_compute_queue');
        }
    }
}
