<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserFundDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_fund_date', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->decimal('balance', 14, 4)->default(0)->comment('帐户余额(可用+冻结)');
            $table->decimal('hold_balance', 14, 4)->default(0)->comment('冻结金额');
            $table->integer('points')->default(0)->comment('用户积分');
            $table->date('belong_date')->comment('哪一天的余额');

            $table->unique(['user_id', 'belong_date']);
            $table->index(['belong_date']);
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
            Schema::dropIfExists('user_fund_date');
        }
    }
}
