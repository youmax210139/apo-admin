<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameVrRukuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * VR入款纪录
         */
        Schema::create('third_game_vr_rukuan', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->default(0)->comment('3:投注失败,4:追号失败,5:打赏失败,6:中奖后停止追号,7:撤单(取消投注),8:取消追号,9:取消打赏,13:颁奖,14:重新颁奖,15:整期撤单');
            $table->string('notify_id', 64)->comment('讯息号');
            $table->text('cashes')->comment('入款详情');
            $table->tinyInteger('channel_id')->default(0)->comment('频道ID');
            $table->string('issue_number', 32)->comment('期号');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');

            $table->unique('notify_id');
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
            Schema::dropIfExists('third_game_vr_rukuan');
        }
    }
}
