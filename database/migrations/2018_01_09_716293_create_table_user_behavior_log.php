<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserBeHaviorLog extends Migration
{
    /**
     * 活动
     */
    public function up()
    {
        Schema::create('user_behavior_log', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->integer('user_id')->comment('用户名');
            $table->string('db_user', 20)->default('')->comment('DB 用户名');
            $table->smallInteger('level')->default(0)->comment("危险级别： 0. 一般； 1. 非常危险");
            $table->string('action', 32)->default('')->comment("行为类型");
            $table->mediumText('description')->default('')->comment("行为描述");
            $table->boolean('is_alerted')->default(0)->comment('是否已提醒');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('时间');

            $table->index(['created_at', 'user_id', 'level', 'action']);
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
            Schema::dropIfExists('user_behavior_log');
        }
    }
}
