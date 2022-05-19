<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserBanks extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('bank_id')->comment('银行ID');
            $table->integer('province_id')->comment('开户省份');
            $table->integer('city_id')->comment('开户城市');
            $table->integer('district_id')->comment('开户地区');
            $table->string('branch', 32)->comment('支行名称');
            $table->string('account_name', 16)->comment('开户名');
            $table->string('account', 64)->comment('卡号');
            $table->boolean('is_default')->default(false)->comment('是否默认卡');
            $table->smallInteger('status')->default(1)->comment('状态1已绑定2已解绑3软删除');
            $table->string('reason', 64)->default('')->comment('解绑或者删除原因');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('更新时间');

            $table->index('created_at');
            $table->index('user_id');
            $table->index('status');
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
            Schema::dropIfExists('user_banks');
        }
    }
}
