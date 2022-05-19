<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_method', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 32)->comment('中文名称');
            $table->boolean('sync')->default(1)->comment('是否同步');
            $table->boolean('status')->default(0)->comment('是否启用');
        });

        $this->data();
    }

    private function data()
    {

        DB::table('payment_method')->insert([
            [
                'ident'      => 'transfer',
                'name'       => '手工转账',
                'sync'       => false,
                'status'     => true,
            ],
            [
                'ident'      => 'netbank',
                'name'       => '在线网银',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'weixin_scan',
                'name'       => '微信扫码',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'alipay_scan',
                'name'       => '支付宝扫码',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'qq_scan',
                'name'       => 'QQ扫码',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'jd_scan',
                'name'       => '京东扫码',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'unionpay_scan',
                'name'       => '银联扫码',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'weixin_h5',
                'name'       => '微信H5',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'alipay_h5',
                'name'       => '支付宝H5',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'qq_h5',
                'name'       => 'QQH5',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'jd_h5',
                'name'       => '京东H5',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'unionpay_h5',
                'name'       => '银联H5',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'quick',
                'name'       => '快捷支付',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'credit',
                'name'       => '信用卡',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'cashier',
                'name'       => '收银台',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'qrcode_offline',
                'name'       => '线下扫码',
                'sync'       => false,
                'status'     => true,
            ],
            [
                'ident'      => 'third_offline',
                'name'       => '第三方线下转账',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'agent_qq',
                'name'       => '代理QQ',
                'sync'       => false,
                'status'     => true,
            ],
            [
                'ident'      => 'agent_weixin',
                'name'       => '代理微信',
                'sync'       => false,
                'status'     => true,
            ],
            [
                'ident'      => 'agent_alipay',
                'name'       => '代理支付宝',
                'sync'       => false,
                'status'     => true,
            ],
            [
                'ident'      => 'agent_chat',
                'name'       => '会话充值',
                'sync'       => false,
                'status'     => true,
            ],
            [
                'ident'      => 'digital_currency',
                'name'       => '数字货币',
                'sync'       => true,
                'status'     => true,
            ],
            [
                'ident'      => 'offline_scan',
                'name'       => '线下扫码链结',
                'sync'       => false,
                'status'     => true,
            ],
            [
                'ident'      => 'dc_scan',
                'name'       => '数字货币扫码',
                'sync'       => true,
                'status'     => true,
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
            Schema::dropIfExists('payment_method');
        }
    }
}
