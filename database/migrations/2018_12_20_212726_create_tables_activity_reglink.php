<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesActivityReglink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_reglink', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->smallInteger('count')->comment('注册用户数');
            $table->smallInteger('count_step')->comment('达到奖金规则人数');
            $table->decimal('prize', 14, 4)->comment('奖金');
            $table->timestamp('start_time')->nullable()->comment('开始统计时间');
            $table->timestamp('end_time')->nullable()->comment('结束统计时间');
            $table->tinyInteger('status')->default(0)->comment('状态，０未审核，1审核拒绝，２审核通过');
            $table->smallInteger('verified_admin_user_id')->default(0)->comment('审核员ID');
            $table->timestamp('verified_at')->nullable()->comment('审核时间');
            $table->string('comment', 256)->default('')->comment('备注');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');

            $table->unique(['user_id', 'start_time','end_time']);
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
            Schema::dropIfExists('activity_reglink');
        }
    }
}
