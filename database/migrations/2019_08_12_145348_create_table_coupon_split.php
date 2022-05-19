<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCouponSplit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_split', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coupon_id')->comment('红包ID');
            $table->decimal('amount', 15, 4)->default(0)->comment('金额');
            $table->integer('collect_id')->default(0)->comment('领取用户ID');
            $table->ipAddress('collect_ip')->nullable()->comment('领取用户IP');
            $table->timestamps();

            $table->index(['coupon_id', 'collect_id']);
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
            Schema::dropIfExists('coupon_split');
        }
    }
}
