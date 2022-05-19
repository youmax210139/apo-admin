<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSmsChannel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_channel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->unique()->comment('中文名称');
            $table->integer('category_id')->comment('短信渠道ID');
            $table->string('account', 64)->unique()->comment('短信客户标识或账号');
            $table->string('key', 255)->comment('短信客户密钥');
            $table->string('key2', 255)->comment('短信客户密钥2');
            $table->string('signature', 32)->nullable()->comment('签名');
            $table->text('sync_status')->default('')->comment('同步各个服务器情况');
            $table->boolean('enabled')->default(true)->comment('是否启用');
            $table->timestamps();
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
            Schema::dropIfExists('sms_channel');
        }
    }
}
