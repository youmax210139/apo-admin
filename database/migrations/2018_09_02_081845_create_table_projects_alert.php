<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectsAlert extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects_alert', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('project_id')->default(0)->comment('投注单ID');
            $table->jsonb('admin_ids')->default('[]')->comment('已读管理员ID');
            $table->jsonb('toast_admin_ids')->default('[]')->comment('已弹管理员ID');
            $table->tinyInteger('type')->default(0)->comment('通知类型: 0 高额中奖，1 久未活跃用户投注，2 重点观察用户上线，3 今日登录数过高');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->jsonb('extend')->default('{}')->comment('扩展字段');
            $table->timestamps();

            //自动填充高奖金报警专用索引
            $table->index(['project_id']);
            $table->index(['type','user_id']);
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
            Schema::dropIfExists('projects_alert');
        }
    }
}
