<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHourlyWage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hourly_wage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->tinyInteger('type')->comment('工资类型：1：中单 2:挂单 3:挂单单挑');
            $table->decimal('amount', 15, 4)->default(0)->comment('用户派发金额');
            $table->timestamp('start_date')->comment('结算开始时间');
            $table->timestamp('end_date')->comment('结算结束时间');
            $table->tinyInteger('status')->default(0)->comment('0-待确认,1-待发放,2-已发放');
            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除删除时间');
            $table->jsonb('remark')->default('{}')->comment('备注');

            $table->unique(['user_id','type', 'start_date','end_date']);
            $table->index('deleted_at');
            $table->index(['status', 'report_status']);
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
            Schema::dropIfExists('hourly_wage');
        }
    }
}
