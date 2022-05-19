<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGamePlatform extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_platform', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('平台 ID');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 32)->unique()->comment('中文名称');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(0)->comment('状态，0-开放，1-禁用');
            $table->tinyInteger('rebate_type')->default(0)->comment('返点类型，0-使用第三方返点，1-使用彩票返点，2-不返点');

            $table->index(['sort']);
            $table->index(['status', 'rebate_type']);
        });

        $this->data();
    }

    private function data()
    {


        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon' => 'fa-exchange',
            'rule' => 'thirdgame',
            'name' => '第三方游戏',
        ]);

        DB::table('third_game_platform')->insert([
            //VR
            [
                'ident' => 'Vr',
                'name' => 'VR彩票',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // AG
            [
                'ident' => 'Ag',
                'name' => 'AG真人',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // Sunbet
            [
                'ident' => 'Sunbet',
                'name' => '太阳城',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // Ebet
            [
                'ident' => 'Ebet',
                'name' => ' Ebet',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // BbinSports
            [
                'ident' => 'BbinSports',
                'name' => ' Bbin体育',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // PT
            [
                'ident' => 'Pt',
                'name' => ' Pt',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // 沙巴
            [
                'ident' => 'Sb',
                'name' => ' Sb体育',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // UG体育
            [
                'ident' => 'Ug',
                'name' => 'UG体育',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // IM电竞
            [
                'ident' => 'Im',
                'name' => 'IM电竞',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // KY棋牌
            [
                'ident' => 'Ky',
                'name' => 'KY棋牌',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // AB真人
            [
                'ident' => 'Ab',
                'name' => '欧博真人',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //凤凰乐利时时彩
            [
                'ident' => 'FhLeli',
                'name' => '凤凰乐利彩',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //完美影城
            [
                'ident' => 'Wmc',
                'name' => '完美影城',
                'status' => '1',
                'rebate_type' => '2',
            ],
            //AV云链接
            [
                'ident' => 'AvCloud',
                'name' => 'AV云链接',
                'status' => '1',
                'rebate_type' => '2',
            ],
            //GG棋牌
            [
                'ident' => 'GgQiPai',
                'name' => 'GG棋牌',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //GG电游
            [
                'ident' => 'GgDy',
                'name' => 'GG电游',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //利记体育
            [
                'ident' => 'Sbo',
                'name' => '利记体育',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //LG幸运棋牌
            [
                'ident' => 'LgQp',
                'name' => '幸运棋牌',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //LC龙城棋牌
            [
                'ident' => 'LcQp',
                'name' => '龙城棋牌',
                'status' => '1',
                'rebate_type' => '0',
            ],

            //VG财神棋牌
            [
                'ident' => 'VgQp',
                'name' => '财神棋牌',
                'status' => '1',
                'rebate_type' => '0',
            ],

            //MG电游
            [
                'ident' => 'MgDy',
                'name' => 'MG电游',
                'status' => '1',
                'rebate_type' => '0',
            ],

            //BNG电游
            [
                'ident' => 'BngDy',
                'name' => 'BNG电游',
                'status' => '1',
                'rebate_type' => '0',
            ],

            //TCG体育
            [
                'ident' => 'Tcg',
                'name' => 'TCG体育',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //AIO沙巴体育(CIA)
            [
                'ident' => 'Aio',
                'name' => 'AIO沙巴体育',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // WML 完美真人(CIA)
            [
                'ident' => 'Wml',
                'name' => '完美真人',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // LEG 乐游棋牌(CIA)
            [
                'ident' => 'Leg',
                'name' => '乐游棋牌',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // AVIA 泛亚电竞(CIA)
            [
                'ident' => 'Avia',
                'name' => '泛亚电竞',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //MG2电游
            [
                'ident' => 'Mg2',
                'name' => 'MG2电游',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //BG大游
            [
                'ident' => 'Bg',
                'name' => 'BG大游',
                'status' => '1',
                'rebate_type' => '0',
            ],
            //DS电竞
            [
                'ident' => 'Ds',
                'name' => 'DS电竞',
                'status' => '1',
                'rebate_type' => '0',
            ],
            // SEA电游
            [
                'ident' => 'Sea',
                'name' => 'SEA电游',
                'status' => '1',
                'rebate_type' => '0',
            ],
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'thirdgame/index',
                'name' => '游戏平台',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgame/createplatform',
                'name' => '添加游戏平台',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgame/editplatform',
                'name' => '修改游戏平台',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgame/deleteplatform',
                'name' => '删除游戏平台',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgame/create',
                'name' => '添加游戏接口',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgame/edit',
                'name' => '修改游戏接口',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgame/delete',
                'name' => '删除游戏接口',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgame/test',
                'name' => '测试Api',
            ],
            [
                'parent_id' => $id,
                'rule' => 'thirdgame/log',
                'name' => '查看日志',
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
            Schema::dropIfExists('third_game_platform');
        }
    }
}
