<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_category', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 32)->comment('中文名称');
            $table->json('methods')->default('[]')->comment('支付方式');
            $table->boolean('status')->default(0)->comment('是否启用');
        });

        $this->__data();
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('payment_category');
        }
    }

    private function __data()
    {
        DB::table('payment_category')->insert([
            [
                'ident' => 'yeepay',
                'name' => '易宝',
                'methods'=>'["netbank","weixin_scan","alipay_scan","qq_scan","jd_scan","weixin_h5","alipay_h5","qq_h5","jd_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'dinpay',
                'name' => '智付',
                'methods'=>'["weixin_scan","alipay_scan","qq_scan","jd_scan"]',
                'status' => 1,
            ],
            [
                'ident' => 'sulong',
                'name' => '速龙',
                'methods'=>'["weixin_scan","alipay_scan","alipay_h5","qq_h5","jd_h5","credit"]',
                'status' => 1,
            ],
            [
                'ident' => 'tonghui',
                'name' => '通汇',
                'methods'=>'[]',
                'status' => 1,
            ],
            [
                'ident' => 'selftransfer',
                'name' => '手工转账',
                'methods'=>'["transfer"]',
                'status' => 1,
            ],
            [
                'ident' => 'zhifu',
                'name' => '直付',
                'methods'=>'["weixin_scan","alipay_scan"]',
                'status' => 1,
            ],
            [
                'ident' => 'lakala',
                'name' => '拉卡拉',
                'methods'=>'["qrcode_offline"]',
                'status' => 1,
            ],
            [
                'ident' => 'tianyan',
                'name' => '天眼',
                'methods'=>'["netbank","weixin_scan","alipay_scan","weixin_h5","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'yunhuitong',
                'name' => '云汇通',
                'methods'=>'["weixin_scan","alipay_scan","qq_scan","weixin_h5","alipay_h5","qq_h5","jd_h5","quick"]',
                'status' => 1,
            ],
            [
                'ident' => 'goodpay',
                'name' => '聚合支付',
                'methods'=>'["weixin_scan","alipay_scan","qq_scan","jd_scan","weixin_h5","alipay_h5","qq_h5","jd_h5","quick","unionpay_scan","cashier","unionpay_h5","credit","digital_currency"]',
                'status' => 1,
            ],
            [
                'ident' => 'smilepay',
                'name' => '微笑',
                'methods'=>'["netbank","weixin_scan","alipay_scan","qq_scan","jd_scan","qq_h5","jd_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'jinshun',
                'name' => '金顺',
                'methods'=>'["weixin_scan","alipay_scan","qq_scan","jd_scan","alipay_h5","qq_h5","jd_h5","unionpay_scan","cashier"]',
                'status' => 1,
            ],
            [
                'ident' => 'mtpay',
                'name' => 'MT支付',
                'methods'=>'["weixin_scan","alipay_scan","qq_scan","jd_scan","weixin_h5","alipay_h5","qq_h5","jd_h5","quick","unionpay_scan","cashier"]',
                'status' => 1,
            ],
            [
                'ident' => 'weilifu',
                'name' => '威力付',
                'methods'=>'["weixin_scan","alipay_scan","qq_scan","jd_scan","weixin_h5","alipay_h5","qq_h5","jd_h5","quick","unionpay_h5","cashier"]',
                'status' => 1,
            ],
            [
                'ident' => 'duobao',
                'name' => '多宝',
                'methods'=>'["netbank","weixin_scan","alipay_scan","qq_scan","weixin_h5","alipay_h5","unionpay_h5","quick"]',
                'status' => 1,
            ],
            [
                'ident' => 'beeepay',
                'name' => '必付',
                'methods'=>'["netbank","weixin_scan","alipay_scan","qq_scan","weixin_h5","quick"]',
                'status' => 1,
            ],
            [
                'ident' => 'huitianfu',
                'name' => '汇天付',
                'methods'=>'["netbank","weixin_scan","alipay_scan","qq_scan","jd_scan","unionpay_scan","weixin_h5","alipay_h5","qq_h5","jd_h5","quick"]',
                'status' => 1,
            ],
            [
                'ident' => 'ifeepay',
                'name' => '艾付',
                'methods'=>'["weixin_scan","alipay_scan","qq_scan","jd_scan","quick","cashier"]',
                'status' => 1,
            ],
            [
                'ident' => 'goodatpay',
                'name' => '新艾付GP',
                'methods'=>'["netbank","weixin_scan","alipay_scan","qq_scan","jd_scan","weixin_h5","alipay_h5","qq_h5","quick","unionpay_scan","unionpay_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'xinhui',
                'name' => '新汇',
                'methods'=>'["weixin_scan","alipay_scan","qq_scan","jd_scan","weixin_h5","alipay_h5","qq_h5","jd_h5","quick","unionpay_scan","cashier","unionpay_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'epay9',
                'name' => '九域',
                'methods'=>'["netbank","weixin_scan","alipay_scan","alipay_h5","qq_h5","jd_h5","weixin_h5","unionpay_scan","quick","cashier"]',
                'status' => 1,
            ],
            [
                'ident' => 'badafu',
                'name' => '八达付',
                'methods'=>'["alipay_scan"]',
                'status' => 1,
            ],
            [
                'ident' => 'alipay51',
                'name' => '51支付宝',
                'methods'=>'["weixin_scan","alipay_scan","weixin_h5","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'shangrubao',
                'name' => '商入宝',
                'methods'=>'["weixin_h5","alipay_h5","qq_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'haofupay',
                'name' => '豪富',
                'methods'=>'["alipay_scan","alipay_h5","weixin_scan","weixin_h5","jd_scan","jd_h5","cashier"]',
                'status' => 1,
            ],
            [
                'ident' => 'jiayipay',
                'name' => '嘉亿',
                'methods'=>'["netbank","cashier"]',
                'status' => 1,
            ],
            [
                'ident' => 'fiveonepay',
                'name' => '新51支付',
                'methods'=>'["alipay_scan","alipay_h5","weixin_scan","weixin_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'yifupay',
                'name' => '新易付',
                'methods'=>'["alipay_scan","alipay_h5","weixin_scan"]',
                'status' => 1,
            ],
            [
                'ident' => 'aobangpay',
                'name' => '奥邦',
                'methods'=>'["netbank","weixin_scan","alipay_scan","qq_scan","jd_scan","unionpay_scan","weixin_h5","alipay_h5","qq_h5","unionpay_h5","quick"]',
                'status' => 1,
            ],
            [
                'ident' => 'daifubao',
                'name' => '代付宝',
                'methods'=>'["netbank","weixin_scan","qq_scan","jd_scan","unionpay_scan","weixin_h5","alipay_h5","qq_h5","quick"]',
                'status' => 1,
            ],
            [
                'ident' => 'tonglueyun',
                'name' => '同略云',
                'methods'=>'["third_offline"]',
                'status' => 1,
            ],
            [
                'ident' => 'agent_offline',
                'name' => '代理充值',
                'methods'=>'["agent_weixin","agent_qq","agent_alipay","agent_chat"]',
                'status' => 1,
            ],
            [
                'ident' => 'jubaopen',
                'name' => '聚宝盆',
                'methods'=>'["unionpay_scan"]',
                'status' => 1,
            ],
            [
                'ident' => 'xinkoudai',
                'name' => '新口袋',
                'methods'=>'["weixin_scan"]',
                'status' => 1,
            ],
            [
                'ident' => 'bofutongpay2',
                'name' => '新加宝',
                'methods'=>'["unionpay_scan","alipay_scan"]',
                'status' => 1,
            ],
            [
                'ident' => 'taishanpay',
                'name' => '泰山',
                'methods'=>'["weixin_scan","alipay_scan","weixin_h5","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'yunpay',
                'name' => '云付',
                'methods'=>'["weixin_scan","alipay_scan","qq_scan","jd_scan","unionpay_scan"]',
                'status' => 1,
            ],
            [
                'ident' => 'wanmapentium',
                'name' => '万马奔腾',
                'methods'=>'["alipay_scan"]',
                'status' => 1,
            ],
            [
                'ident' => 'jiuding',
                'name' => '九鼎',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
            [
                'ident' => 'bigphase',
                'name' => '大相',
                'methods'=>'["quick","cashier"]',
                'status' => 1,
            ],
            [
                'ident' => 'xinliansheng',
                'name' => '新联盛',
                'methods'=>'["alipay_scan","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident' => 'qualitypay',
                'name' => '质付',
                'methods'=>'["alipay_scan","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'bofutongpay3',
                'name'   => '加宝3',
                'methods'=>'["alipay_scan","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'jubo',
                'name  ' => '巨博',
                'methods'=>'["netbank","weixin_scan","alipay_scan","alipay_h5","jd_scan","unionpay_scan","quick"]',
                'status' => 1,
            ],
            [
                'ident'  => 'xinchipay',
                'name'   => '鑫驰',
                'methods'=>'["unionpay_scan","unionpay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'shunlong',
                'name'   => '顺隆',
                'methods'=>'["alipay_scan","alipay_h5","unionpay_scan","unionpay_h5","cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'goodpaygp',
                'name'   => 'GP码农_聚合优付系列',
                'methods'=>'["CREDIT"]',
                'status' => 1,
            ],
            [
                'ident'  => 'xinfutong',
                'name'   => '信付通',
                'methods'=>'["netbank","weixin_scan","alipay_scan","unionpay_scan","weixin_h5","alipay_h5","unionpay_h5","quick","cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'fengyunfu',
                'name'   => '风云付',
                'methods'=>'["weixin_scan","alipay_scan","unionpay_scan","weixin_h5","alipay_h5","unionpay_h5","quick"]',
                'status' => 1,
            ],
            [
                'ident'  => 'mibao',
                'name'   => '米宝',
                'methods'=>'["weixin_scan","weixin_h5","alipay_scan","alipay_h5","unionpay_scan","qq_scan","qq_h5","jd_scan","jd_h5","quick","netbank"]',
                'status' => 1,
            ],
            [
                'ident'  => 'zhongbao',
                'name'   => '众宝',
                'methods'=>'["weixin_scan","weixin_h5","alipay_scan","alipay_h5","unionpay_scan","qq_scan","qq_h5","jd_scan","jd_h5","quick","netbank"]',
                'status' => 1,
            ],
            [
                'ident'  => 'kzpay',
                'name'   => 'KZ支付',
                'methods'=>'["weixin_scan","weixin_h5","alipay_scan","alipay_h5","unionpay_scan","cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'quanyin',
                'name'   => '全銀支付',
                'methods'=>'["netbank","weixin_scan","weixin_h5","alipay_scan","alipay_h5","unionpay_scan","cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'jiufu',
                'name'   => '玖付支付',
                'methods'=>'["netbank","weixin_scan","weixin_h5","alipay_scan","alipay_h5","unionpay_scan","unionpay_h5","cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'haoyun',
                'name'   => '好运支付',
                'methods'=>'["weixin_scan","weixin_h5","alipay_scan","alipay_h5","unionpay_scan","unionpay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'xinpangu',
                'name'   => '新盘古支付',
                'methods'=>'["cashier","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'kuaiyin',
                'name'   => '快銀支付',
                'methods'=>'["weixin_scan","weixin_h5","alipay_scan","alipay_h5","jd_scan","jd_h5","quick"]',
                'status' => 1,
            ],
            [
                'ident'  => 'orange',
                'name'   => '橘子支付',
                'methods'=>'["weixin_scan","alipay_scan","unionpay_scan","cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'otc365',
                'name'   => 'otc365支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'tianhong',
                'name'   => '天宏支付',
                'methods'=>'["weixin_scan","alipay_scan","unionpay_scan","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'jubaofu',
                'name'   => '聚宝付支付',
                'methods'=>'["netbank", "weixin_scan","alipay_scan","qq_scan","weixin_h5","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'citypay',
                'name'   => 'citypay支付',
                'methods'=>'["alipay_scan", "unionpay_scan", "cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'bixufu',
                'name'   => '必须付支付',
                'methods'=>'["alipay_scan", "weixin_scan", "cashier", "weixin_h5", "qq_h5","qq_scan", "jd_scan", "unionpay_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'azpay',
                'name'   => 'Az支付',
                'methods'=>'["weixin_scan","alipay_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'jiuliu',
                'name'   => '96支付',
                'methods'=>'["alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'qianmai',
                'name'   => '钱麦支付',
                'methods'=>'["alipay_h5","weixin_scan","netbank","unionpay_scan","alipay_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'shandian',
                'name'   => '闪电支付',
                'methods'=>'["alipay_h5","weixin_scan","alipay_scan", "weixin_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'zhifupay2',
                'name'   => '极付支付',
                'methods'=>'["alipay_h5","weixin_scan","alipay_scan", "weixin_h5", "cashier","unionpay_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'menglian',
                'name'   => '梦联支付',
                'methods'=>'["alipay_h5","weixin_scan","alipay_scan", "weixin_h5", "cashier","unionpay_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'yifutong',
                'name'   => '亿付通',
                'methods'=>'["weixin_scan","alipay_scan",  "cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'diyi',
                'name'   => '第一支付',
                'methods'=>'["unionpay_scan", "alipay_h5", "weixin_h5", "weixin_scan", "alipay_scan", "cashier", "quick"]',
                'status' => 1,
            ],
            [
                'ident'  => 'ecpay',
                'name'   => 'Ecpay支付',
                'methods'=>'["unionpay_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'chaofanpay',
                'name'   => '超凡支付',
                'methods'=>'["netbank","weixin_scan","alipay_scan","weixin_h5","alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'dafupay',
                'name'   => '大付支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'xiangyunpay',
                'name'   => '祥云支付',
                'methods'=>'["weixin_scan","alipay_scan","weixin_h5","alipay_h5","cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'wwpay',
                'name'   => 'WW支付',
                'methods'=>'["alipay_scan","weixin_h5","alipay_h5","cashier","quick"]',
                'status' => 1,
            ],
            [
                'ident'  => 'guofupay',
                'name'   => '果付支付',
                'methods'=>'["weixin_scan","alipay_scan","unionpay_scan","weixin_h5","alipay_h5","unionpay_h5","quick","cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'xingchengpay',
                'name'   => '星城支付',
                'methods'=>'["weixin_scan","alipay_scan","unionpay_scan","weixin_h5","alipay_h5","cashier","qq_h5","qq_scan","jd_scan","quick"]',
                'status' => 1,
            ],
            [
                'ident'  => 'yonhengpay',
                'name'   => '永恒支付',
                'methods'=>'["weixin_scan","alipay_scan","cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'okpay',
                'name'   => 'Ok支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'youyifu',
                'name'   => '优易付支付',
                'methods'=>'["cashier", "alipay_scan", "weixin_h5", "alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'boqingpay',
                'name'   => '博晴支付',
                'methods'=>'["alipay_scan", "alipay_h5", "weixin_scan", "weixin_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'gapypay',
                'name'   => 'gapy支付',
                'methods'=>'["alipay_scan", "alipay_h5", "weixin_scan", "weixin_h5", "cashier", "quick"]',
                'status' => 1,
            ],
            [
                'ident'  => 'haishenpay',
                'name'   => '海神支付',
                'methods'=>'["alipay_h5", "weixin_h5", "cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'haojiepay',
                'name'   => '豪杰支付',
                'methods'=>'["alipay_scan", "alipay_h5", "weixin_scan", "weixin_h5", "cashier", "quick"]',
                'status' => 1,
            ],
            [
                'ident'  => 'juyihepay',
                'name'   => '聚义和支付',
                'methods'=>'["alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'jgpay',
                'name'   => 'JG支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'wanhepay',
                'name'   => '万和支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'tongdapay',
                'name'   => '通达支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
			[
                'ident'  => 'mashangpay',
                'name'   => '码上支付',
                'methods'=>'["alipay_scan", "alipay_h5", "weixin_scan", "cashier"]',
                'status' => 1,
            ],
			[
                'ident'  => 'ifpay',
                'name'   => '指付支付',
                'methods'=>'["weixin_h5", "alipay_h5", "digital_currency", "cashier"]',
                'status' => 1,
            ],
			[
                'ident'  => 'jiutoupay',
                'name'   => '九头支付',
                'methods'=>'["weixin_scan", "alipay_h5", "alipay_scan"]',
                'status' => 1,
            ],
			[
                'ident'  => 'caishenpay',
                'name'   => '财神支付',
                'methods'=>'["weixin_scan", "weixin_h5", "alipay_h5", "alipay_scan", "cashier", "quick"]',
                'status' => 1,
            ],
            [
                'ident'  => 'usdt',
                'name' => 'USDT线下',
                'methods'=>'["offline_scan"]',
                'status' => 1,
            ],
			[
                'ident'  => 'rbcxpay',
                'name'   => 'RBCX支付',
                'methods'=>'["digital_currency"]',
                'status' => 1,
            ],
            [
                'ident'  => 'qichengpay',
                'name'   => '启程支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'haibaopay',
                'name'   => '海宝支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'akpay',
                'name'   => 'AK支付',
                'methods'=>'["alipay_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'stpay',
                'name'   => 'ST支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'bbcpay',
                'name'   => 'BBC支付',
                'methods'=>'["weixin_scan", "alipay_scan", "cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'otccnypay',
                'name'   => 'OTCCNY支付',
                'methods'=>'["digital_currency"]',
                'status' => 1,
            ],
            [
                'ident'  => 'otcusdtpay',
                'name'   => 'OTCUSDT支付',
                'methods'=>'["digital_currency"]',
                'status' => 1,
            ],
            [
                'ident'  => 'xinrbcxpay',
                'name'   => '新RBCX支付',
                'methods'=>'["digital_currency"]',
                'status' => 1,
            ],
            [
                'ident'  => 'offlinerbcxpay',
                'name'   => '线下RBCX支付',
                'methods'=>'["dc_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'shanghaipay',
                'name'   => '上海支付',
                'methods'=> '["netbank"]',
                'status' => 1,
            ],
            [
                'ident'  => 'haoshiduo',
                'name'   => '好事多支付',
                'methods'=> '["cashier"]',
                'status' => 1,
            ],
            [
                'ident'  => 'dafapay',
                'name'   => '大发支付',
                'methods'=>'["alipay_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'gypay',
                'name'   => 'GY支付',
                'methods'=>'["cashier", "alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'badepay',
                'name'   => '八德支付',
                'methods'=>'["unionpay_scan", "alipay_h5", "alipay_scan", "weixin_scan", "weixin_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'yiqifupay',
                'name'   => '一起付支付',
                'methods'=>'["cashier", "alipay_h5", "alipay_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'yingjiapay',
                'name'   => '盈嘉支付',
                'methods'=>'["cashier", "alipay_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'zhongbang',
                'name'   => '众邦支付',
                'methods'=>'["cashier", "alipay_h5", "alipay_scan", "weixin_scan", "weixin_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'youfubao',
                'name'   => '优付宝支付',
                'methods'=>'["digital_currency", "dc_scan"]',
                'status' => 1,
            ],
            [
                'ident'  => 'baotongpay',
                'name'   => '宝通支付',
                'methods'=>'["cashier", "alipay_h5", "weixin_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'ubpay',
                'name'   => 'UB支付',
                'methods'=>'["digital_currency"]',
                'status' => 1,
            ],
            [
                'ident'  => 'plgpay',
                'name'   => 'PLG支付',
                'methods'=>'["cashier", "alipay_h5", "weixin_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'diandianpay',
                'name'   => '点点支付',
                'methods'=>'["netbank"]',
                'status' => 1,
            ],
            [
                'ident'  => 'hkpay',
                'name'   => 'HK支付',
                'methods'=>'["cashier", "alipay_h5", "alipay_scan", "weixin_scan", "weixin_h5"]',
                'status' => 1,
            ],
            [
                'ident'  => 'yilianpay',
                'name'   => '易联支付',
                'methods'=>'["cashier"]',
                'status' => 1,
            ],
        ]);
    }
}
