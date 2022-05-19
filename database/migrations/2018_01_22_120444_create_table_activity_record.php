<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActivityRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->default(0)->comment('活动id');
            $table->integer('user_id')->default(0)->comment('活动用户id');
            $table->decimal('draw_money', 14, 4)->default(0)->comment('奖励金额');
            $table->timestamp('record_time')->nullable()->comment('记录创建时间');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('是否已派送: 0:否, 1:是');
            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
            $table->string('extral', 64)->default('')->comment('额外字段，具体到某类活动可能用到');
            $table->integer('relation_id')->default(0)->comment('相关id');
            $table->ipAddress('ip')->nullable()->comment('用户ip');

            $table->index(['activity_id', 'user_id', 'record_time']);
            $table->index(['activity_id', 'ip', 'record_time']);

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
            Schema::dropIfExists('activity_record');
        }
    }
}
