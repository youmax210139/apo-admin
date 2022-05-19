<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserChannelLock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_channel_lock', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('payment_channel_id')->comment('支付通道id');
            $table->integer('invalid_count')->comment('无效申请次数');
            $table->tinyInteger('status')->default(0)->comment('状态：0 锁定，1 充值成功解锁');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('修改时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
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
            Schema::dropIfExists('user_channel_lock');
        }
    }
}
