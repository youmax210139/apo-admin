<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentChannelAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_channel_attribute', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_channel_id')->comment('支付通道id');
            $table->string('type', 50)->comment('键名');
            $table->text('value')->comment('键值');
            $table->unique(['payment_channel_id', 'type'], 'channel_type');
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
            Schema::dropIfExists('payment_channel_attribute');
        }
    }
}
