<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserFund extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_fund', function (Blueprint $table) {
            $table->integer('user_id')->comment('用户ID');
            $table->decimal('balance', 14, 4)->default(0)->comment('帐户余额');
            $table->decimal('hold_balance', 14, 4)->default(0)->comment('冻结金额');
            $table->integer('points')->default(0)->comment('用户积分');
            $table->primary('user_id');
            $table->index(['user_id', 'balance']);
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
            Schema::dropIfExists('user_fund');
        }
    }
}
