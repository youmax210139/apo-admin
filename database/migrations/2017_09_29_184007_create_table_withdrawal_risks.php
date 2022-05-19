<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWithdrawalRisks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_risks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('withdrawal_id')->default(0)->comment('提款订单ID');
            $table->string('verifier_username', 30)->default('')->comment('风控审核管理员');
            $table->timestamp('verifier_at')->nullable()->comment("审核时间");
            $table->ipAddress('verifier_ip')->nullable()->comment("风控审核IP");
            $table->timestamp('done_at')->nullable()->comment("完成时间");
            $table->decimal('last_withdrawal_amount', 15, 4)->default(0)->comment('最后提现金额');
            $table->timestamp('last_withdrawal_at')->nullable()->comment('最后提现时间');
            $table->ipAddress('last_withdrawal_ip')->nullable()->comment("最后提现IP地址");
            $table->integer('last_withdrawal_id')->default(0)->comment('最后提现ID');
            $table->decimal('deposit_total', 15, 4)->default(0)->comment('充值总金额');
            $table->integer('deposit_times')->default(0)->comment('充值总次数');
            $table->decimal('bet_price', 15, 4)->default(0)->comment('离上一次提现的投注金额');
            $table->decimal('bet_bonus', 15, 4)->default(0)->comment('离上一次提现的返奖金额');
            $table->integer('bet_times')->defaut(0)->comment('离上一次提现的投注次数');
            $table->string('risk_remark', 200)->default('')->comment('风控备注');
            $table->string('refused_msg', 200)->default('')->comment('风控拒绝提款原因，发送站内信');
            $table->string('auto_risk_remark', 200)->default('')->comment('自动风控备注');
            $table->timestamp('risk_at')->nullable()->comment('审核时间');
            $table->tinyInteger('status')->default(0)->comment('0待审核  1审核通过 2审核拒绝3审核中4自动检查中');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('申请时间');

            $table->index(['withdrawal_id', 'status']);
            $table->index(['created_at', 'status']);
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
            Schema::dropIfExists('withdrawal_risks');
        }
    }
}
