<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDeposits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->decimal('amount', 15, 4)->default(0)->comment('充值金额');
            $table->decimal('third_amount', 15, 4)->default(0)->comment('虚拟币金额');
            $table->decimal('user_fee', 15, 4)->default(0)->comment('用户的手续费，负数扣除，整数返还');
            $table->decimal('platform_fee', 15, 4)->default(0)->comment('平台手续费');
            $table->smallInteger('payment_channel_id')->default(0)->comment('支付通道ID');
            $table->smallInteger('payment_category_id')->default(0)->comment('支付渠道ID');
            $table->string('account_number', 50)->default('')->comment('商户号或银行卡号');
            $table->string('bank_order_no', 100)->default('')->comment('银行交易流水或是第三方交易流水');
            $table->string('order_id', 64)->default('')->comment('本站帐变流水号');
            $table->string('accountant_admin')->default('')->comment('会计，通过审核的管理员');
            $table->string('cash_admin')->default('')->comment('出纳，确认充值的管理员');
            $table->string('manual_postscript')->default('')->comment('人工输入附言');
            $table->decimal('manual_amount', 15, 4)->default(0)->comment('人工输入实际出款金额');
            $table->decimal('manual_fee', 15, 4)->default(0)->comment('人工输入实际手续费');
            $table->tinyInteger('status')->default(0)->comment('状态，０支付中，１已审核，２充值成功，３充值失败');
            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
            $table->string('error_type', 64)->default('')->comment('违规类型');
            $table->string('remark', 64)->default('')->comment('备注');
            $table->string('admin_remark', 64)->default('')->comment('管理员备注');
            $table->jsonb('extra')->nullable()->comment('扩展字段');
            //有些银行是没有附言，例如建设银行，所以加上一个时间限制，在这个时间里面才是有效的~在这个时间里面，这个用户无法用这个银行发起新的充值申请
            $table->ipAddress('ip')->nullable()->comment("用户IP地址");
            $table->timestamp('expired_at')->nullable()->comment("过期时间");
            $table->timestamp('deal_at')->nullable()->comment("管理处理时间");
            $table->timestamp('done_at')->nullable()->comment("到账实际");
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('申请时间');


            $table->index('user_id');
            $table->index('created_at');
            $table->index(['status', 'report_status']);
            $table->index(['status', 'created_at']);
            $table->index(['payment_channel_id', 'created_at']);
            $table->index(['payment_channel_id', 'status', 'created_at']);
            $table->index(['status', 'user_id', 'done_at']);
        });

        DB::statement('ALTER SEQUENCE IF EXISTS "deposits_id_seq" RESTART WITH 1000000');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('deposits');
        }
    }
}
