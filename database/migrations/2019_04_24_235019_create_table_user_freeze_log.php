<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserFreezeLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_freeze_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->smallInteger('freeze_type')->unsigned()->comment('冻结类型：0，解冻;1. 不可登录; 2. 可登录,不可投注,不可充提; 3. 可登录,不可投注,可充提');
            $table->string('admin', 64)->comment('操作管理员');
            $table->string('reason', 200)->default('')->comment('原因');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->index(['user_id','created_at']);
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
            Schema::dropIfExists('user_freeze_log');
        }
    }
}
