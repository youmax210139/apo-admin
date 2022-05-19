<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWithdrawals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->decimal('amount', 15, 4)->default(0)->comment('提款金额');
            $table->decimal('third_amount', 15, 4)->default(0)->comment('第三方金额');
            $table->decimal('user_fee', 15, 4)->default(0)->comment('用户的手续费，负数扣除，整数返还');
            $table->decimal('platform_fee', 15, 4)->default(0)->comment('平台手续费');
            $table->integer('user_bank_id')->default(0)->comment('用户提款银行ID');
            $table->string('cashier_username', 30)->default('')->comment('出纳管理员');
            $table->ipAddress('cashier_ip')->nullable()->comment("出纳IP地址");
            $table->timestamp('cashier_at')->nullable()->comment("认领时间");
            $table->timestamp('done_at')->nullable()->comment("完成时间");
            $table->integer('bank_id')->default(0)->comment('手工出款使用的银行');
            $table->string('bank_order_no', 64)->default('')->comment('银行流水号');
            $table->string('remark', 64)->default('')->comment('出纳备注');
            $table->tinyInteger('error_no')->default(0)->comment('错误代码');
            $table->tinyInteger('status')->default(0)->comment('处理状态:0等待出款 1出款成功 2出款失败 3操作中 4正在出款 5自动出款失败人工处理');
            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
            $table->tinyInteger('operate_type')->default(0)->comment('操作类型，1手工2第三方3软件');
            //第三方出款操作状态：15、排队发送汇款信息中；14、发送汇款信息失败；13、排队确认汇款是否成功；12、对方确认汇款失败,11对方确认汇款成功
            $table->tinyInteger('operate_status')->default(0)->comment('操作状态');
            $table->integer('third_id')->default(0)->comment('第三方接口ID');
            //因为这个出款接口经常会换出款商户，所以需要纪录这条出款申请是由哪个商户出来的
            $table->string('third_merchant', 64)->default('')->comment('第三方接口商户口');
            $table->integer('third_add_count')->default(0)->comment("向第三方添加订单次数");
            $table->integer('third_check_count')->default(0)->comment("向第三方确认订单次数");
            $table->timestamp('third_add_at')->nullable()->comment("第三方添加订单时间");
            $table->timestamp('third_check_at')->nullable()->comment("第三方确认订单时间");
            $table->string('third_response', 200)->default('')->comment('第三方反馈');
            $table->ipAddress('ip')->nullable()->comment("用户IP地址");
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('申请时间');

            $table->index(['id', 'created_at']);
            $table->index(['status', 'report_status']);
            $table->index(['status', 'operate_type', 'operate_status']);
            $table->index(['created_at', 'ip']);
            $table->index(['created_at', 'user_id']);
            $table->index(['created_at', 'status']);
        });
        $this->data();
    }
    private function data()
    {
         $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon' => 'fa-credit-card',
                'rule' => 'withdrawal',
                'name' => '充提管理',
         ]);

        DB::table('admin_role_permissions')->insert([
                [
                        'parent_id' => $id,
                        'rule' => 'withdrawal/index',
                        'name' => '提现申请',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'withdrawal/deal',
                        'name' => '[提现]人工审核',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'deposit/index',
                        'name' => '充值申请',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'deposit/deal',
                        'name' => '[充值]人工审核',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'deposit/carry',
                        'name' => '[充值]加款到账',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'deposit/detail',
                        'name' => '[充值]详情',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'withdrawal/detail',
                    'name' => '[提现]详情',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'withdrawal/dealthird',
                    'name' => '[提现]第三方出款审核',
                ],

         ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('withdrawals');
        }
    }
}
