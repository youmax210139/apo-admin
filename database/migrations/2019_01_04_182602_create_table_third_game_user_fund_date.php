<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameUserFundDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_user_fund_date', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('third_game_id')->unsigned()->comment('所属平台ID');
            $table->decimal('balance', 14, 4)->default(0)->comment('帐户余额');
            $table->date('belong_date')->comment('哪一天的余额');

            $table->unique(['user_id', 'third_game_id', 'belong_date']);
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
            Schema::dropIfExists('third_game_user_fund_date');
        }
    }
}
