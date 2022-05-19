<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePrizePool extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prize_pool', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('每月只生成一条记录');     //如：2019-11-01
            $table->jsonb('data')->comment('数据信息');
            $table->decimal('percent', 4, 4)->comment('奖池比例');
            $table->tinyInteger('status')->default(0)->comment('审核状态：0 未审核，1 同意 2 拒绝');
            $table->tinyInteger('frozen')->default(0)->comment('冻结列表：0 否，1 是');
            $table->smallInteger('verified_admin_user_id')->default(0)->comment('审核员ID');
            $table->timestamp('verified_at')->nullable()->comment('审核时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');

            $table->unique('date');
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
            Schema::dropIfExists('prize_pool');
        }
    }
}
