<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePumpOutlets extends Migration
{
    /**
     * 彩票返水表
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('pump_outlets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inlet_id')->default(0)->comment('对应的抽水ID');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->integer('project_id')->default(0)->comment('订单ID');
            $table->decimal('cardinal', 14, 4)->default(0)->comment('基数');
            $table->decimal('scale', 6, 6)->comment('比例');
            $table->decimal('amount', 15, 4)->default(0)->comment('返水金额');
            $table->tinyInteger('status')->default(0)->comment('状态1计算完成2抽水完成3返水完成');
            $table->jsonb('extend')->default('[]')->comment('扩展备注');
            $table->timestamps();
            $table->index(['inlet_id','project_id','status','created_at']);//补发专用
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
            Schema::dropIfExists('pump_outlets');
        }
    }
}
