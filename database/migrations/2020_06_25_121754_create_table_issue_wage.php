<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIssueWage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_wage', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->integer('user_id')->unsigned()->comment('受益用户ID');
            $table->tinyInteger('type')->comment('工资类型');
            $table->smallInteger('lottery_id')->comment('彩种 ID');
            $table->string('issue', 32)->comment('奖期');
            $table->decimal('cardinal', 14, 4)->comment('工资基数');
            $table->decimal('rate', 6, 6)->comment('工资比例');
            $table->decimal('amount', 15, 4)->default(0)->comment('用户派发金额');
            $table->integer('source_user_id')->default(0)->comment('施益用户ID');
            $table->timestamp('sale_start')->comment('本期销售开始时间');
            $table->timestamp('sale_end')->comment('本期销售截至时间');
            $table->decimal('price', 14, 4)->comment('投注额');
            $table->decimal('bonus', 14, 4)->comment('奖金');
            $table->decimal('rebate', 14, 4)->comment('返点');
            $table->decimal('profit', 14, 4)->comment('利润');
            $table->string('remark')->default('')->comment('备注');
            $table->tinyInteger('status')->default(0)->comment('0-待确认,1-待发放,2-已发放');
            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('修改时间');

            $table->index(['type', 'lottery_id','issue','source_user_id']);
            $table->index(['status', 'report_status']);//用于生成报表
            $table->unique(['user_id','type', 'lottery_id','issue','source_user_id']);
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
            Schema::dropIfExists('issue_wage');
        }
    }
}
