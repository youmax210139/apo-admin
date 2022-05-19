<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameVrKoukuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * VR扣款纪录
         */
        Schema::create('third_game_vr_koukuan', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->default(0)->comment('0:投注,1:追号,2:打赏,10:撤单(取消投注)失败,11:取消追号失败,12:取消打赏失败');
            $table->string('notify_id', 64)->comment('讯息号');
            $table->text('cashes')->comment('扣款详情');
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
            Schema::dropIfExists('third_game_vr_koukuan');
        }
    }
}
