<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableJackpotPeriodCodeData extends Migration
{
    public function up()
    {
        Schema::create('jackpot_period_code_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('period', 8)->comment('期号');
            $table->string('code', 5)->comment('号码');
            $table->boolean('is_got')->default(false)->comment('是否被获取');
            $table->unique(['period', 'code']);
            $table->index(['period', 'is_got']);
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
            Schema::dropIfExists('jackpot_period_code_data');
        }
    }
}
