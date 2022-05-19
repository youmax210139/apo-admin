<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserDepositTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_deposit_total', function (Blueprint $table) {
            $table->integer('user_id')->comment('用户ID');
            $table->decimal('amount', 15, 4)->default(0)->comment('累计充值金额');
            $table->decimal('once_min', 15, 4)->default(0)->comment('单次最小充值金额');
            $table->decimal('once_max', 15, 4)->default(0)->comment('单次最大充值金额');
            $table->integer('times')->default(0)->comment('累计充值次数');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('更新时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间 首充时间');
            $table->primary('user_id');
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
            Schema::dropIfExists('user_deposit_total');
        }
    }
}
