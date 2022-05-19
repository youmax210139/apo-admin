<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportTeamTotalDaily extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_team_total_daily', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->decimal('total_price', 14, 4)->default(0)->comment('用户投注');
            $table->decimal('total_bonus', 14, 4)->default(0)->comment('用户派奖');
            $table->decimal('total_rebate', 14, 4)->default(0)->comment('用户返点');
            $table->decimal('total_deposit', 14, 4)->default(0)->comment('用户存款');
            $table->decimal('total_deposit_fee', 14, 4)->default(0)->comment('用户手续费（存)');
            $table->decimal('total_withdrawal', 14, 4)->default(0)->comment('用户提款');
            $table->decimal('total_withdrawal_fee', 14, 4)->default(0)->comment('用户手续费（取');
            $table->decimal('total_wage', 14, 4)->default(0)->comment('日工资');
            $table->decimal('total_activity', 14, 4)->default(0)->comment('活动奖励');
            $table->decimal('total_profit', 14, 4)->default(0)->comment('最终盈亏');
            $table->decimal('total_balance', 14, 4)->default(0)->comment('用户余额');
            $table->date('date')->comment('统计日期');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('修改时间');
            $table->unique(['user_id', 'date']);
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
            Schema::dropIfExists('report_team_total_daily');
        }
    }
}
