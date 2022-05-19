<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentDomain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_domain', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('domain', 128)->comment('域名');
            $table->smallInteger('payment_category_id')->comment('渠道id');
            $table->smallInteger('intermediate_servers_id')->comment('服务器id');
            $table->boolean('status')->default(0)->comment('是否启用');
            $table->string('remark')->default('')->comment('备注');
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
            Schema::dropIfExists('payment_domain');
        }
    }
}
