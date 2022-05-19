<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameDs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_ds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_num', 32)->comment('订单号');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('platform_id')->default(0)->comment('第三方游戏平台 ID');
            $table->decimal('amount', 15, 4)->comment('订单金额');
            $table->string('status', 32)->default(0)->comment('注单状态');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('更新时间');
            $table->ipAddress('created_ip', 32)->nullable()->comment('订单插入IP');
            $table->string('remark', 128)->comment('备注');

            $table->index(['user_id', 'created_at']);
            $table->index(['order_num']);
            $table->index(['platform_id']);
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
            Schema::dropIfExists('third_game_ds');
        }
    }
}
