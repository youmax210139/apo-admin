<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGame extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('接口 ID');
            $table->integer('third_game_platform_id')->comment('所属游戏平台ID');
            $table->string('ident', 16)->unique()->comment('英文标识,需要与接口类名保持一致');
            $table->string('name', 32)->unique()->comment('中文名称');
            $table->string('merchant', 50)->comment('商户号');
            $table->string('merchant_key', 100)->comment('商户密钥');
            $table->string('api_base', 255)->comment('正式环境API基础地址');
            $table->string('merchant_test', 50)->comment('商户测试号');
            $table->string('merchant_key_test', 100)->comment('商户测试密钥');
            $table->string('api_base_test', 255)->comment('测试环境API基础地址');
            $table->tinyInteger('status')->default(0)->comment('状态，0-开放，1-禁用');
            $table->tinyInteger('login_status')->default(0)->comment('状态，0 - 允许登入，1-不允许登入');
            $table->tinyInteger('transfer_status')->default(0)->comment('状态，0 - 允许转帐，1-不允许转帐');
            $table->tinyInteger('transfer_type')->default(0)->comment('转帐方式，0-平台调用第三方接口转出/转入，1-第三方调用平台接口转出/转入');
            $table->jsonb('deny_user_group')->default('[]')->comment('用户组黑名单');
            $table->timestamp('last_fetch_time')->nullable()->comment('最后抓取记录时间');

            $table->index(['ident']);
            $table->index(['third_game_platform_id']);
        });

        $this->data();
    }

    private function data()
    {

        $platform = DB::table('third_game_platform')->get()->keyBy('ident');

        DB::table('third_game')->insert([
            //VR
            [
                'ident' => 'Vr',
                'name' => 'VR彩票',
                'merchant' => '',
                'merchant_key' => '',
                'api_base' => 'https://xbw.vrbetapi.com',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base_test' => 'http://fe.vrbetdemo.com',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 1,
                'third_game_platform_id' => $platform['Vr']->id
            ],
            //CIA AG
            [
                'ident' => 'CiaAg',
                'name' => 'AG真人(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Ag']->id
            ],
            //CIA Sunbet
            [
                'ident' => 'CiaSunbet',
                'name' => '太阳城(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Sunbet']->id
            ],
            //CIA Ebet
            [
                'ident' => 'CiaEbet',
                'name' => 'EBET(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Ebet']->id
            ],
            //CIA BbinSports
            [
                'ident' => 'CiaBbinSports',
                'name' => 'BBIN体育(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['BbinSports']->id
            ],
            //CIA PT
            [
                'ident' => 'CiaPt',
                'name' => 'PT(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Pt']->id
            ],
            //CIA 沙巴
            [
                'ident' => 'CiaSb',
                'name' => '沙巴(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Sb']->id
            ],
            //CIA UG体育
            [
                'ident' => 'CiaUg',
                'name' => 'UG体育(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Ug']->id
            ],
            //CIA IM电竞
            [
                'ident' => 'CiaIm',
                'name' => 'IM电竞(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Im']->id
            ],
            //CIA KY棋牌
            [
                'ident' => 'CiaKy',
                'name' => 'KY棋牌(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Ky']->id
            ],
            //CIA AB真人
            [
                'ident' => 'CiaAb',
                'name' => '欧博真人(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => 'tssftkqexyatdvby',
                'merchant_key_test' => '27d7fcd35f187644cc74234f8e771ffb',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Ab']->id
            ],
            //凤凰乐利时时彩
            [
                'ident' => 'FhLeli',
                'name' => '凤凰乐利彩',
                'merchant' => '',
                'merchant_key' => '',
                'api_base' => 'https://api.fhll.online/hz',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base_test' => 'http://api.myclient01.com/hz',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 1,
                'third_game_platform_id' => $platform['FhLeli']->id
            ],
            //CIA wmc完美影城
            [
                'ident' => 'CiaWmc',
                'name' => '完美影城(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 1,
                'transfer_type' => 1,
                'third_game_platform_id' => $platform['Wmc']->id
            ],
            //CIA AV云链接
            [
                'ident' => 'CiaAvCloud',
                'name' => 'AV云链接(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 1,
                'transfer_type' => 1,
                'third_game_platform_id' => $platform['AvCloud']->id
            ],
            //CIA gg棋牌
            [
                'ident' => 'CiaGgQiPai',
                'name' => 'GG棋牌(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['GgQiPai']->id
            ],
            //CIA GG电游
            [
                'ident' => 'CiaGgDy',
                'name' => 'GG电游(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['GgDy']->id
            ],
            //CIA 利记体育
            [
                'ident' => 'CiaSbo',
                'name' => '利记体育(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Sbo']->id
            ],
            //CIA LG幸运棋牌
            [
                'ident' => 'CiaLgQp',
                'name' => 'LG幸运棋牌(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['LgQp']->id
            ],

            //CIA LC龙城棋牌
            [
                'ident' => 'CiaLcQp',
                'name' => 'LC龙城棋牌(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['LcQp']->id
            ],

            //CIA VG财神棋牌
            [
                'ident' => 'CiaVgQp',
                'name' => 'VG财神棋牌(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['VgQp']->id
            ],

            //CIA MG电游
            [
                'ident' => 'CiaMgDy',
                'name' => 'MG电游(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['MgDy']->id
            ],

            //CIA BNG电游
            [
                'ident' => 'CiaBngDy',
                'name' => 'BNG电游(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['BngDy']->id
            ],

            //CIA TCG体育
            [
                'ident' => 'CiaTcg',
                'name' => 'TCG体育(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Tcg']->id
            ],
            //CIA AIO沙巴体育
            [
                'ident' => 'CiaAio',
                'name' => 'AIO沙巴体育(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Aio']->id
            ],
            // WML 完美真人
            [
                'ident' => 'CiaWml',
                'name' => '完美真人(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Wml']->id
            ],
            // LEG 乐游棋牌
            [
                'ident' => 'CiaLeg',
                'name' => '乐游棋牌(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Leg']->id
            ],
            // AVIA 泛亚电竞
            [
                'ident' => 'CiaAvia',
                'name' => '泛亚电竞(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Avia']->id
            ],
            //CIA MG2电游
            [
                'ident' => 'CiaMg2',
                'name' => 'MG2电游(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Mg2']->id
            ],
            //CIA BG大游
            [
                'ident' => 'CiaBg',
                'name' => 'BG大游(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Bg']->id
            ],
            //CIA Ds电竞
            [
                'ident' => 'CiaDs',
                'name' => 'DS电竞(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Ds']->id
            ],
            //CIA SEA
            [
                'ident' => 'CiaSea',
                'name' => 'SEA电游(cia)',
                'merchant' => '',
                'merchant_key' => '',
                'merchant_test' => '',
                'merchant_key_test' => '',
                'api_base' => 'http://api.wm-cia.com/api/',
                'api_base_test' => 'http://uat.wmcia.net/api',
                'deny_user_group' => '[]',
                'status' => 1,
                'login_status' => 0,
                'transfer_status' => 0,
                'transfer_type' => 0,
                'third_game_platform_id' => $platform['Sea']->id
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
            Schema::dropIfExists('third_game');
        }
    }
}
