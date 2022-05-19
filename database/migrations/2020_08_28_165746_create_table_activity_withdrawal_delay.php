<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActivityWithdrawalDelay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_withdrawal_delay', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->comment('活动id');
            $table->integer('withdrawal_id')->comment('提款单ID');
            $table->integer('user_id')->comment('用户ID');
            $table->decimal('amount', 15, 4)->comment('提款金额');
            $table->integer('delay_minutes')->comment('延迟分钟数');
            $table->decimal('percent', 5, 4)->comment('奖金比例');
            $table->decimal('prize', 14, 4)->comment('奖金');
            $table->tinyInteger('status')->default(0)->comment('状态，0未审核，1审核拒绝，2审核通过');
            $table->smallInteger('verified_admin_user_id')->default(0)->comment('审核员ID');
            $table->timestamp('verified_at')->nullable()->comment('审核时间');
            $table->string('comment', 256)->default('')->comment('备注');
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
            Schema::dropIfExists('activity_withdrawal_delay');
        }
    }
}
