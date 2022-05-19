<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableJackpotUserCode extends Migration
{
    public function up()
    {
        Schema::create('jackpot_user_code', function (Blueprint $table) {
            $table->increments('id');
            $table->string('period', 8)->comment('期号');
            $table->integer('user_id')->comment('用户ID');
            $table->string('code', 5)->comment('用户号码');
            $table->decimal('current_total_bet', 14, 4)->default(0)->comment('用户当前投注金额');
            $table->smallInteger('prize_level')->default(0)->comment('中奖状态：0不中奖; 1一等奖; 2二等奖');
            $table->timestamps();
            $table->index(['period']);
            $table->unique(['period', 'code']);
            $table->index(['period', 'prize_level']);
            $table->index(['period', 'user_id', 'created_at']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        if (app()->environment() != 'production') {
            Schema::dropIfExists('jackpot_user_code');
        }
    }
}
