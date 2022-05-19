<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRegisterCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->string('domain', 64)->comment('域名');
            $table->string('code', 32)->comment('注册码');
            $table->jsonb('rebates')->comment('注册用户返点 格式 {"lottery":0.08,"ag":0.12}');
            $table->tinyInteger('user_type')->default(0)->comment('注册用户类型，0 会员 1代理');
            $table->tinyInteger('expired')->default(0)->comment('失效时间，0 永久， 1一天， 2 3天 ，3 7天， 4 15天');
            $table->smallInteger('num')->default(0)->comment('注册用户数量');
            $table->timestamps();

            $table->unique(['user_id', 'code']);
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
            Schema::dropIfExists('register_codes');
        }
    }
}
