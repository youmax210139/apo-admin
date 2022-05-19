<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableContractDividends extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_dividends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->tinyInteger('type')->comment('分红类型：1-a线（分红金额固定）  2-b线（比例固定） 默认b线');
            $table->tinyInteger('mode')->nullable()->comment('分红模式：0:不累计 1:累计 (是上半月盈利 要不要抵消下半月的亏损)');
            $table->timestamp('start_time')->comment('开始时间');
            $table->timestamp('end_time')->comment('结束时间');
            $table->decimal('total_price', 15, 4)->default(0)->comment('总销量');
            $table->decimal('total_bonus', 15, 4)->default(0)->comment('总奖金');
            $table->decimal('total_rebate', 15, 4)->default(0)->comment('总返点');
            $table->decimal('total_deposit_fee', 15, 4)->default(0)->comment('充值平台手续费总额');
            $table->decimal('total_withdrawal_fee', 15, 4)->default(0)->comment('提现平台手续费总额');
            $table->decimal('total_wage', 15, 4)->default(0)->comment('总日工资');
            $table->decimal('total_activity', 15, 4)->default(0)->comment('活动金');

            $table->decimal('vr_price', 15, 4)->default(0)->comment('vr总销量');
            $table->decimal('vr_bonus', 15, 4)->default(0)->comment('vr总奖金');
            $table->decimal('vr_rebate', 15, 4)->default(0)->comment('vr总返点');

            $table->decimal('total_profit', 15, 4)->default(0)->comment('盈亏=总销量-总奖金-总返点-总充值手续费-总提现手续费-总日工资-活动金');
            $table->integer('total_daus')->default(0)->comment('总活跃人数');
            $table->integer('lottery_daus')->default(0)->comment('彩票活跃人数');
            $table->integer('vr_daus')->default(0)->comment('VR活跃人数');

            $table->decimal('last_rate', 15, 4)->default(0)->comment('上次分红比例');
            $table->decimal('last_amount', 15, 4)->default(0)->comment('上次分红金额');
            $table->decimal('rate', 15, 4)->default(0)->comment('分红比例');
            $table->decimal('amount', 15, 4)->default(0)->comment('实际分红金额');
            $table->jsonb('extra')->nullable()->comment('扩展字段，存放用户上级佣金');
            $table->tinyInteger('status')->default(0)->comment('状态 1:已发放 2:发放中 3:上级审核 4:后台审核 5:已取消 6:不符合条件 7:非结算日');

            $table->tinyInteger('send_type')->default(0)->comment('发放方式 1-系统发放，2-上级发放');
            $table->tinyInteger('period')->default(0)->comment('结算周期 1:日结 2:半月结 3:月结 4:周结 5:10日结 11:浮动日结 12:浮动半月结 13:浮动月结 14:浮动周结 15:浮动10日结');

            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->timestamp('send_at')->nullable()->comment('发放时间');

            $table->unique(['user_id', 'start_time', 'end_time', 'period']);
            $table->index(['user_id','status']);
            $table->index(['start_time', 'end_time', 'send_type', 'period']);
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
            Schema::dropIfExists('contract_dividends');
        }
    }
}
