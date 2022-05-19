<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableChatDepositPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_deposit_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 32)->default('')->comment('类型ali,wx,bank');
            $table->integer('channel')->default(0)->comment('渠道id');
            $table->smallInteger('kefu')->default('0')->comment('客服编号');
            $table->string('name', 64)->default('')->comment('姓名');
            $table->string('account', 64)->default('')->comment('账号');
            $table->string('qrcode', 1000)->default('')->comment('二维码');
            $table->string('bank_name', 64)->default('')->comment('银行');
            $table->string('bank_branch', 64)->default('')->comment('银行支行');
            $table->string('last_name', 64)->default('')->comment('姓');
            $table->string('first_name', 64)->default('')->comment('名');
            $table->boolean('enabled')->comment('状态0不可用，1可用');
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
            Schema::dropIfExists('chat_deposit_payment');
        }
    }
}
