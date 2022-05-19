<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWithdrawalCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('withdrawal_category', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 30)->comment('支付渠道名称');
            $table->string('ident', 30)->unique()->comment('标识');
            $table->string('request_url', 128)->default('')->comment('提交地址');
            $table->string('verify_url', 128)->default('')->comment('确认地址');
            $table->string('notify_url', 128)->default('')->comment('通知地址');
            $table->text('banks')->default('')->comment('支持银行ID');
            $table->boolean('status')->default(true)->comment('1正常 0停用');
            $table->timestamps();
        });

        $this->__addPermissions();
        $this->__data();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        if (app()->environment() != 'production') {
            Schema::dropIfExists('withdrawal_category');
        }
    }

    /**
     * 添加权限
     */
    private function __addPermissions()
    {
        $row = DB::table('admin_role_permissions')->where('name', '提现渠道管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon'      => 'fa-bank',
                'rule'      => 'withdrawalcategory',
                'name'      => '提现接口管理',
            ]);
        } else {
            $id = $row->id;
        }

        //提现渠道
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule'      => 'withdrawalcategory/index',
                'name'      => '提现渠道管理',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'withdrawalcategory/create',
                'name'      => '添加提现渠道',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'withdrawalcategory/edit',
                'name'      => '编辑提现渠道',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'withdrawalcategory/delete',
                'name'      => '删除提现渠道',
            ],
        ]);

        //提现通道
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule'      => 'withdrawalchannel/index',
                'name'      => '提现通道管理',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'withdrawalchannel/create',
                'name'      => '添加提现通道',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'withdrawalchannel/edit',
                'name'      => '编辑提现通道',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'withdrawalchannel/delete',
                'name'      => '删除提现通道',
            ],
        ]);

        //受付银行管理
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule'      => 'bank/index',
                'name'      => '受付银行管理',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'bank/create',
                'name'      => '添加银行',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'bank/edit',
                'name'      => '编辑银行',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'bank/disabled',
                'name'      => '启用或禁用银行',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'bank/delete',
                'name'      => '删除银行',
            ]
        ]);
    }

    /**
     * 添加数据
     */
    private function __data()
    {
        $bankIds = [];
        $banks = DB::table('banks')->where(['disabled' => false, 'withdraw' => true])->get();
        foreach ($banks as $bank) {
            $bankIds[] = $bank->id;
        }
        $bankIds = implode(',', $bankIds);

        DB::table('withdrawal_category')->insert([
            [
                'name'          => '通汇卡',
                'ident'         => 'thkpay',
                'request_url'   => 'http://pay.woozf.com:8000/remit',
                'verify_url'    => 'http://pay.woozf.com:8000/remit/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '新汇',
                'ident'         => 'xinhui',
                'request_url'   => 'https://gateway.gd95516.com/api/v1/remit',
                'verify_url'    => 'https://gateway.gd95516.com/api/v1/remit_query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '九域',
                'ident'         => 'epay9',
                'request_url'   => 'https://www.9-epay.com/Payment_Dfpay_add.html',
                'verify_url'    => 'https://www.9-epay.com/Payment_Dfpay_query.html',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '艾付GP',
                'ident'         => 'goodatpay',
                'request_url'   => 'https://pay1.goodatpay.com/withdraw/singleWithdraw',
                'verify_url'    => 'https://pay1.goodatpay.com/withdraw/queryOrder',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => 'MT支付',
                'ident'         => 'mtpay',
                'request_url'   => 'http://df.mtpayvip.com/df/api_df',
                'verify_url'    => 'http://query.mtpayvip.com/df/df_query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '代付宝',
                'ident'         => 'daifubao',
                'request_url'   => 'https://gateway.shlangdi.com/controller.action',
                'verify_url'    => 'https://gateway.shlangdi.com/controller.action',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '奥邦',
                'ident'         => 'aobang',
                'request_url'   => 'https://www.aobangapi.com/settlement/v2',
                'verify_url'    => 'https://www.aobangapi.com/settlement/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '汇天付',
                'ident'         => 'huitianfu',
                'request_url'   => 'https://gateway.999pays.com/Payment/BatchTransfer.aspx',
                'verify_url'    => 'https://gateway.999pays.com/Payment/BatchQuery.aspx',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '快付宝',
                'ident'         => 'kuaifubao',
                'request_url'   => 'http://api.bestflashpay.com:8089/api/Thirdparty/Withdrawal',
                'verify_url'    => 'https://中间站/withdrawal/check',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '云付',
                'ident'         => 'yunpay',
                'request_url'   => 'http://pay.sulongpay.com/cash/payment',
                'verify_url'    => 'http://pay.sulongpay.com/cash/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '威力付',
                'ident'         => 'weilifu',
                'request_url'   => 'http://paygate.power-gateway.com/powerpay-gateway-onl/txn',
                'verify_url'    => 'http://paygate.power-gateway.com/powerpay-gateway-onl/txn',
                'notify_url'    => 'http://www.baidu.com',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '优付',
                'ident'         => 'goodpay',
                'request_url'   => 'https://api.sumvic.com/api/v0.1/Withdrawal/Order',
                'verify_url'    => 'https://api.sumvic.com/api/v0.1/Withdrawal/Query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '豪富',
                'ident'         => 'haofupay',
                'request_url'   => 'https://mmpvrtarxx.6785151.com/payCenter/agentPay',
                'verify_url'    => 'https://mmpvrtarxx.6785151.com/payCenter/orderQuery',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '鼎付',
                'ident'         => 'toppay',
                'request_url'   => 'http://gateway.xbvnnn.top/api/v0.1/Withdrawal/Order',
                'verify_url'    => 'http://gateway.xbvnnn.top/api/v0.1/Withdrawal/Query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '巨博',
                'ident'         => 'jubo',
                'request_url'   => 'http://gw.jpaying.com/standard/getway/depositiface',
                'verify_url'    => 'http://gw.jpaying.com/standard/getway/orderstatus',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '信付通',
                'ident'         => 'xinfutong',
                'request_url'   => 'https://client.xfuoo.com/agentPay/v1/batch/',
                'verify_url'    => 'https://client.xfuoo.com/agentPay/v1/batch/',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '风云付',
                'ident'         => 'fengyunfu',
                'request_url'   => 'https://gateway.fengyunpay.net/supApi/singlePenTransfer',
                'verify_url'    => 'https://gateway.fengyunpay.net/supApi/queryTransferResult',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '全银',
                'ident'         => 'quanyin',
                'request_url'   => 'http://agentpay.qyzfvip.com/rb-pay-web-merchant/agentPay/pay',
                'verify_url'    => 'http://agentpay.qyzfvip.com/rb-pay-web-merchant/agentPay/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '九鼎',
                'ident'         => 'jiuding',
                'request_url'   => 'https://www.epg99.com:11256/withdraw',
                'verify_url'    => 'https://www.epg99.com:11256/withdrawinquery',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '捷顺',
                'ident'         => 'jieshun',
                'request_url'   => 'http://xfapi.disanju.xyz/v1.0/pay/df/sendpay',
                'verify_url'    => 'http://xfapi.disanju.xyz/v1.0/pay/df/querDf',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '酷熊',
                'ident'         => 'kuxiong',
                'request_url'   => 'http://104.233.252.36:9081/kuxiong/gateway/api/backTransReq',
                'verify_url'    => 'http://104.233.252.36:9081/kuxiong/gateway/api/backTransReq',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '极付',
                'ident'         => 'jifu',
                'request_url'   => 'http://www.gpay99.cn/service/carry',
                'verify_url'    => 'http://www.gpay99.cn/service/queryWithdrawalResult',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '超凡支付',
                'ident'         => 'chaofanpay',
                'request_url'   => 'http://pay.qu2w.com/gateway',
                'verify_url'    => 'http://pay.qu2w.com/gateway',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '大付',
                'ident'         => 'dafu',
                'request_url'   => 'https://gateway.ecmedia.vip/api/v1.0/Trade/Withdraw',
                'verify_url'    => 'https://gateway.ecmedia.vip/api/v1.0/Trade/Query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '祥云',
                'ident'         => 'xiangyun',
                'request_url'   => 'https://api.abl668.com/agentPay',
                'verify_url'    => 'https://api.abl668.com/orderQuery',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => 'WW',
                'ident'         => 'ww',
                'request_url'   => 'https://wp.thepay.co.nz/payout/create',
                'verify_url'    => 'https://wp.thepay.co.nz/payout/status',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '星城代付',
                'ident'         => 'xingcheng',
                'request_url'   => 'http://qz.zhebweb.com:8086/forward_jtsr/service',
                'verify_url'    => 'http://qz.zhebweb.com:8086/forward_jtsr/service',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '加宝代付',
                'ident'         => 'jiabao',
                'request_url'   => 'https://api.ctzbao.com/pay/center/withdrawal/apply',
                'verify_url'    => 'https://api.ctzbao.com/pay/center/withdrawal/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '永恒代付',
                'ident'         => 'yonheng',
                'request_url'   => 'https://www.aa168zf.com/Payment_Dfpay_add.html',
                'verify_url'    => 'https://www.aa168zf.com/Payment_Dfpay_query.html',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => 'Ok代付',
                'ident'         => 'ok',
                'request_url'   => 'http://okpay.cash/pay/order/acp',
                'verify_url'    => 'http://okpay.cash/pay/order/acp/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '万付代付',
                'ident'         => 'wanfu',
                'request_url'   => 'https://api.whpayment.com/rsa/withdraw',
                'verify_url'    => 'https://api.whpayment.com/rsa/query-order',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '鑫代付',
                'ident'         => 'xin',
                'request_url'   => 'http://gateway.np178899.com:9909/api/v1/payout/placeorder',
                'verify_url'    => 'http://gateway.np178899.com:9909/api/v1/payout/getinfo',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '海神代付',
                'ident'         => 'haishen',
                'request_url'   => 'https://www.binli1688.com/gateway/pay.json',
                'verify_url'    => 'https://www.binli1688.com/gateway/pay.json',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '豪杰代付',
                'ident'         => 'haojie',
                'request_url'   => 'http://47.254.44.144:3020/api/trans/create_order',
                'verify_url'    => 'http://47.254.44.144:3020/api/trans/query_order',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => 'JG代付',
                'ident'         => 'jg',
                'request_url'   => 'https://jgpayonline.com/api/payment-transaction/',
                'verify_url'    => 'https://jgpayonline.com/api/payment-transaction/',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '易迅代付',
                'ident'         => 'yixun',
                'request_url'   => 'https://api.edscrt.com/open/form/transfer',
                'verify_url'    => 'https://api.edscrt.com/open/form/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '51K代付',
                'ident'         => 'wuyik',
                'request_url'   => 'http://www.51sykt.com/trade/api/applyAgentPay',
                'verify_url'    => 'http://www.51sykt.com/trade/api/queryAgentPay',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
			[
                'name'          => '瀚银代付',
                'ident'         => 'hanyin',
                'request_url'   => 'http://qz.zhebweb.com:8086/forward_jtsr/service',
                'verify_url'    => 'http://qz.zhebweb.com:8086/forward_jtsr/service',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
			[
                'name'          => '码上代付',
                'ident'         => 'mashang',
                'request_url'   => 'https://payment.dev.msz.cash/jinxin/orders/pay',
                'verify_url'    => 'https://payment.dev.msz.cash/jinxin/orders/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
			[
                'name'          => '众邦代付',
                'ident'         => 'zhongbang',
                'request_url'   => 'http://simulation.zbankpays.com/payment/payment/submitBatchOrder',
                'verify_url'    => 'http://simulation.zbankpays.com/query/queryOrder',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
			[
                'name'          => 'RBCX代付',
                'ident'         => 'rbcx',
                'request_url'   => 'https://3d.rbcx.io/pay',
                'verify_url'    => 'https://3d.rbcx.io/records',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
			[
                'name'          => 'STPAY代付',
                'ident'         => 'stpay',
                'request_url'   => 'http://payapi.3hmo8xcf.stpay01.com:6080/api/obpay/transfer',
                'verify_url'    => 'http://payapi.3hmo8xcf.stpay01.com:6080/api/obpay/getinterorderV2',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '海宝代付',
                'ident'         => 'haibao',
                'request_url'   => 'https://www.hibo168.com/transfer/apply',
                'verify_url'    => 'https://www.hibo168.com/transfer/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '上银代付',
                'ident'         => 'shangyin',
                'request_url'   => 'https://api.836210.com/fgw-web/api/gateway',
                'verify_url'    => 'https://api.836210.com/fgw-web/api/gateway',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => 'BBC代付',
                'ident'         => 'bbcpay',
                'request_url'   => 'https://gateway.bbcpay.net/api/Pay/Payout',
                'verify_url'    => 'https://gateway.bbcpay.net/api/Merchant/QueryPayout',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => 'OTC代付',
                'ident'         => 'otc',
                'request_url'   => 'https://open-v2.otc365.com/cola/order/addOrder',
                'verify_url'    => 'https://open-v2.otc365.com/cola/order/common/getOrderInfo',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '天府代付',
                'ident'         => 'tianfu',
                'request_url'   => 'https://hp.thepay.co.nz/payout/create',
                'verify_url'    => 'https://hp.thepay.co.nz/payout/status',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '好事多代付',
                'ident'         => 'haoshiduo',
                'request_url'   => 'https://cs.thepay.co.nz/payout/create',
                'verify_url'    => 'https://cs.thepay.co.nz/payout/status',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '大发代付',
                'ident'         => 'dafa',
                'request_url'   => 'https://gateway.dfpay88.cn/api/v1.1/Trade/Withdraw',
                'verify_url'    => 'https://gateway.dfpay88.cn/api/v1.1/Trade/Query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '乐付代付',
                'ident'         => 'lefu',
                'request_url'   => 'http://cf-api.accpay.cn/api/v0.1/Withdrawal/Order',
                'verify_url'    => 'http://cf-api.accpay.cn/api/v0.1/Withdrawal/Query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '八德代付',
                'ident'         => 'bade',
                'request_url'   => 'https://qxpay.xyz/payfor/trans',
                'verify_url'    => 'https://qxpay.xyz/payfor/orderquery',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '盈嘉代付',
                'ident'         => 'yingjia',
                'request_url'   => 'http://api.jiasheng28.com:8081/app/DFPayFun',
                'verify_url'    => 'http://api.jiasheng28.com:8081/app/DFPayCheckOrder',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '大发代付',
                'ident'         => 'dafapay',
                'request_url'   => 'http://api.dafapay666.com/api/issue/issued/',
                'verify_url'    => 'http://api.dafapay666.com/api/issue/status/',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '优付宝代付',
                'ident'         => 'youfubao',
                'request_url'   => 'https://usdt.tgpas.com/payout/send',
                'verify_url'    => 'https://usdt.tgpas.com/payout/status',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => 'UB代付',
                'ident'         => 'ubpay',
                'request_url'   => 'https://ubtxn.com/api/payment',
                'verify_url'    => 'https://ubtxn.com/api/payment',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '万银代付',
                'ident'         => 'wanyin',
                'request_url'   => 'http://47.115.30.251/api/withdrawal',
                'verify_url'    => 'http://47.115.30.251/api/orders',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '宝通代付',
                'ident'         => 'baotong',
                'request_url'   => 'http://shop.tb8000.vip/api/sett/apply',
                'verify_url'    => 'http://shop.tb8000.vip/api/sett/query',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '金星代付',
                'ident'         => 'Jinxing',
                'request_url'   => 'http://starpay888.org:35426/deal/wit',
                'verify_url'    => 'http://starpay888.org:35426/deal/findOrder',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => 'PLG代付',
                'ident'         => 'plg',
                'request_url'   => 'http://stgpayapi.uas-gw.info/v2/withdrawal/DIGUO/forward',
                'verify_url'    => 'http://stgpayapi.uas-gw.info/v2/withdrawal/DIGUO/order',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '点点代付',
                'ident'         => 'diandian',
                'request_url'   => 'https://openapi.dd-zf.com/Transfers/uniorder',
                'verify_url'    => 'https://openapi.dd-zf.com/Transfers/getOrder',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => '易联代付',
                'ident'         => 'yilian',
                'request_url'   => 'https://pay.epaylink.cc/api/payment',
                'verify_url'    => 'https://pay.epaylink.cc/api/payment/order',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
            [
                'name'          => 'HK代付',
                'ident'         => 'hkpay',
                'request_url'   => 'http://93.179.127.76:8990/payjson/api',
                'verify_url'    => 'http://93.179.127.76:8990/payjson/api',
                'notify_url'    => '',
                'banks'         => $bankIds,
                'status'        => '1',
            ],
        ]);
    }
}
