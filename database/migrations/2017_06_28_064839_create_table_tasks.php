<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('top_id')->comment('总代 ID')->unsigned();
            $table->integer('user_id')->comment('用户 ID')->unsigned();
            $table->smallInteger('lottery_id')->comment('彩种 ID')->unsigned();
            $table->integer('lottery_method_id')->comment('玩法 ID')->unsigned();
            $table->bigInteger('package_id')->comment('销售单ID')->unsigned();
            $table->string('title', 256)->comment('追号任务标题');
            $table->mediumText('code')->comment('购买号码');
            $table->string('code_position', 16)->default('')->comment('投注位置 个十百千万');
            $table->smallInteger('issue_count')->comment('追号总期数')->unsigned();
            $table->smallInteger('finished_count')->default(0)->comment('完成期数')->unsigned();
            $table->smallInteger('cancel_count')->default(0)->comment('取消期数')->unsigned();
            $table->decimal('single_price', 14, 4)->comment('每期单倍价格');
            $table->decimal('task_price', 14, 4)->comment('追号总金额');
            $table->decimal('finish_price', 14, 4)->default(0)->comment('完成总金额');
            $table->decimal('cancel_price', 14, 4)->default(0)->comment('取消的总金额');
            $table->string('begin_issue', 32)->comment('开始期数');
            $table->smallInteger('win_count')->default(0)->comment('赢的期数')->unsigned();
            $table->jsonb('user_diff_rebate')->default('[]')->comment('用户返点差');
            $table->smallInteger('status')->default(0)->comment('追号状态(0. 进行中; 1. 取消; 2. 已完成)')->unsigned();
            $table->smallInteger('stop_on_win')->default(0)->comment('追中即停(1:追中即停)')->unsigned();
            $table->smallInteger('mode')->default(0)->comment('模式 ID')->unsigned();
            $table->decimal('rebate', 4, 4)->default(0)->comment('返点');
            $table->decimal('project_fee_rate', 4, 4)->default(0)->comment('服务费比例');
            $table->smallInteger('client_type')->comment('客户端类型');
            $table->ipAddress('ip')->nullable()->comment('用户 IP');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('追号发起时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('更新时间');

            $table->index(['created_at', 'ip']);
            $table->index(['lottery_id', 'lottery_method_id']);
            $table->index(['lottery_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        DB::statement('ALTER SEQUENCE IF EXISTS "tasks_id_seq" RESTART WITH 10000000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('tasks');
        }
    }
}
