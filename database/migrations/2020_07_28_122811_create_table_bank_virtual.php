<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBankVirtual extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_virtual', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('ident', 64)->unique()->comment('唯一英文标识');
            $table->string('name', 64)->unique()->comment('虚拟货币银行名称');
            $table->string('currency', 64)->default('')->comment("币别");
            $table->decimal('rate', 14, 4)->default(0)->comment('汇率');
            $table->string('channel_idents', 256)->default('')->comment('使用渠道,填写填渠道英文标示,如有多个用”,”隔开');
            $table->boolean('withdraw')->default(0)->comment("是否接受提现,1允许提现");
            $table->boolean('disabled')->default(0)->comment("是否禁用,1禁用");
            $table->decimal('amount_max', 14, 4)->default(0)->comment('提现上限');
            $table->decimal('amount_min', 14, 4)->default(1000)->comment('提现下限');
            $table->time('start_time')->default('00:00')->comment('提现开始时间');
            $table->time('end_time')->default('23:59')->comment('提现结束时间');
            $table->string('api_fetch', 1)->default('0')->comment("汇率模式 0 人工汇率 1 RBCX汇率 2 OTC365汇率");
            $table->string('url', 256)->default('')->comment("汇率api url");
        });
        $this->data();
    }

    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '提现接口管理')->where('parent_id', 0)->first();

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'bankvirtual/index',
                'name' => '虚拟货币银行配置',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'bankvirtual/create',
                'name' => '创建虚拟货币银行配置',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'bankvirtual/edit',
                'name' => '编辑虚拟货币银行配置',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'bankvirtual/disabled',
                'name' => '启用或禁用虚拟货币银行配置',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'bankvirtual/delete',
                'name' => '删除虚拟货币银行配置',
            ]
        ]);

        DB::table('bank_virtual')->insert([
            [
                'ident' => 'USDT',
                'name' => 'USDT虚拟钱包',
                'currency' => 'CNY',
                'rate' => '1',
                'channel_idents' => 'offlinerbcxpay,xinrbcxpay,rbcxpay',
                'withdraw' => 't',
                'disabled' => 'f',
                'api_fetch' => '0',
                'url' => 'https://3d.rbcx.io/tick',
            ],
            [
                'ident' => 'USDTTRC20',
                'name' => 'TRC20虚拟钱包',
                'currency' => 'CNY',
                'rate' => '1',
                'channel_idents' => 'offlinerbcxpay,xinrbcxpay,rbcxpay',
                'withdraw' => 't',
                'disabled' => 'f',
                'api_fetch' => '1',
                'url' => 'https://apiv2.rbcx.io/tick',
            ]
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
            Schema::dropIfExists('bank_virtual');
        }
    }
}
