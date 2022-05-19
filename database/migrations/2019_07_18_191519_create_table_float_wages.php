<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFloatWages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('float_wages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->date('date')->comment('哪一天的日工资');
            $table->decimal('total_price', 15, 4)->default(0)->comment('日销量');
            $table->decimal('total_rebate', 15, 4)->default(0)->comment('累计返点');
            $table->smallInteger('activity')->default(0)->comment('活跃人数');
            $table->decimal('rate', 15, 4)->default(0)->comment('分红比例');
            $table->decimal('amount', 15, 4)->default(0)->comment('工资金额');
            $table->decimal('total_amount', 15, 4)->default(0)->comment('总计工资金额');
            $table->decimal('child_amount', 15, 4)->default(0)->comment('下级工资金额');
            $table->tinyInteger('status')->default(0)->comment('0-待确认,1-待发放,2-已发放');
            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('send_at')->nullable()->comment('派发时间');
            $table->jsonb('remark')->default('{}')->comment('备注');
            $table->unique(['user_id','date']);
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
            Schema::dropIfExists('float_wages');
        }
    }
}
