<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserWithdrawalTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_withdrawal_total', function (Blueprint $table) {
            $table->integer('user_id')->comment('用户ID');
            $table->decimal('amount', 15, 4)->default(0)->comment('累计金额');
            $table->decimal('once_max', 15, 4)->default(0)->comment('单次最大提现金额');
            $table->integer('times')->default(0)->comment('累计次数');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('更新时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->primary('user_id');
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
            Schema::dropIfExists('user_withdrawal_total');
        }
    }
}
