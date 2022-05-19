<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportWithdrawalTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_withdrawal_total', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->integer('user_id')->comment('用户 ID');
            $table->decimal('amount', 14, 4)->comment('充值金额');
            $table->decimal('platform_fee', 14, 4)->comment('平台手续费');

            $table->timestamp('created_at')->comment('写入时间');

            $table->unique(['user_id', 'created_at']);
            $table->index(['user_id', 'created_at', 'amount', 'platform_fee']);
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
            Schema::dropIfExists('report_withdrawal_total');
        }
    }
}
