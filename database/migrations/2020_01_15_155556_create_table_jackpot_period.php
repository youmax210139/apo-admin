<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableJackpotPeriod extends Migration
{
    public function up()
    {
        Schema::create('jackpot_period', function (Blueprint $table) {
            $table->increments('id');
            $table->string('period', 8)->comment('期号');
            $table->string('code', 5)->default('')->comment('号码');
            $table->tinyInteger('code_status', 0)->default(0)->comment('开奖状态：0未开奖，1已开奖，2官方未开奖');
            $table->timestamp('start_at')->comment('本期开始时间');
            $table->timestamp('end_at')->comment('本期结束时间');
            $table->integer('user_code_counter')->default(0)->comment('领取抽奖号码数量');
            $table->decimal('calculate_prize', 14, 4)->default(0)->comment('本期统计的奖池');
            $table->decimal('calculate_total_bet', 14, 4)->default(0)->comment('本期总投注额');
            $table->timestamp('calculate_at')->nullable()->comment('统计更新时间');
            $table->decimal('calculate_percent', 7, 4)->default(0)->comment('奖池比例');
            $table->decimal('inheritance_prize', 14, 4)->default(0)->comment('继承上期奖金');
            $table->decimal('inheritance_percent', 7, 4)->default(0)->comment('继承上期的比例');
            $table->boolean('inheritance_status')->default(false)->comment('是否已继承上期');
            $table->decimal('operation_prize', 14, 4)->default(0)->comment('操作的奖金');
            $table->timestamps();
            $table->unique(['period']);
            $table->index(['start_at','end_at']);
            $table->index(['code_status','end_at']);
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
            Schema::dropIfExists('jackpot_period');
        }
    }
}
