<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameDeduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_deduct', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('third_game_platform_id')->comment('所属游戏平台id');
            $table->integer('user_id')->comment('用户id');
            $table->decimal('amount', 14, 4)->comment('金额');
            $table->string('comment', 32)->comment('备注');
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
            Schema::dropIfExists('third_game_deduct');
        }
    }
}
