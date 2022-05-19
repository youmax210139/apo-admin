<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTaskdetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_details', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('追号详情表 ID')->unsigned();
            $table->bigInteger('task_id')->comment('追号方案 ID')->unsigned();
            $table->bigInteger('project_id')->comment('方案 ID')->unsigned();
            $table->integer('multiple')->comment('追号倍数')->unsigned();
            $table->string('issue', 32)->comment('追号奖期');
            $table->smallInteger('status')->default(0)->comment('追号状态(0:未生成;1:生成追号单;2:追号单取消)')->unsigned();

            $table->index('project_id');
            $table->index(['task_id', 'issue']);
        });

        DB::statement('ALTER SEQUENCE IF EXISTS "task_details_id_seq" RESTART WITH 1000000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('task_details');
        }
    }
}
