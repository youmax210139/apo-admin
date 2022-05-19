<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFrontMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('front_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->comment('菜单种类名称');
            $table->string('ident', 32)->unique()->comment('菜单种类英文标识');
            $table->string('category', 16)->comment('分类');
            $table->boolean('status')->default(0)->comment('状态，0 为禁用，1 为启用');
            $table->jsonb('data')->default('[]')->comment('菜单JSON数据');
            $table->string('last_editor', 64)->default('')->comment('最后修改的管理员');
            $table->timestamps();

            $table->index(['status']);
        });

        $this->data();
    }

    private function data()
    {
        $parent_id = DB::table('admin_role_permissions')->where('rule', 'site')
            ->where('parent_id', 0)
            ->value('id');
        if (empty($parent_id)) {
            return;
        }
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $parent_id,
                'rule' => 'frontmenu/index',
                'name' => '前台菜单配置',
            ],
            [
                'parent_id' => $parent_id,
                'rule' => 'frontmenu/create',
                'name' => '添加前台菜单种类',
            ],
            [
                'parent_id' => $parent_id,
                'rule' => 'frontmenu/edit',
                'name' => '修改前台菜单种类',
            ],
            [
                'parent_id' => $parent_id,
                'rule' => 'frontmenu/editdata',
                'name' => '编辑前台菜单内容',
            ],
            [
                'parent_id' => $parent_id,
                'rule' => 'frontmenu/refresh',
                'name' => '刷新前台菜单缓存',
            ],
            [
                'parent_id' => $parent_id,
                'rule' => 'frontmenu/menudelete',
                'name' => '删除前台菜单',
            ],
            [
                'parent_id' => $parent_id,
                'rule' => 'frontmenu/output',
                'name' => '导出前台菜单数据',
            ],
        ]);

        $date_time = date('Y-m-d H:i:s');
        $data = [
            [
                'name' => '数字彩票PC',
                'ident' => 'lottery_pc',
                'category' => 'pc',
                'data' => json_encode($this->_lotteryPcMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => '数字彩票H5',
                'ident' => 'lottery_h5',
                'category' => 'h5',
                'data' => json_encode($this->_lotteryH5MenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => 'VR彩票',
                'ident' => 'vr_lottery',
                'category' => 'h5',
                'data' => json_encode($this->_vrLotteryMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => '乐利彩票',
                'ident' => 'lolly_lottery',
                'category' => 'h5',
                'data' => json_encode($this->_lollyLotteryMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => 'PT电游',
                'ident' => 'pt_game',
                'category' => 'common',
                'data' => json_encode($this->_ptGameMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => 'GG电游',
                'ident' => 'gg_game',
                'category' => 'common',
                'data' => json_encode($this->_ggGameMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => '开元棋牌',
                'ident' => 'ky_poker',
                'category' => 'common',
                'data' => json_encode($this->_kyPokerMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => 'GG棋牌',
                'ident' => 'gg_poker',
                'category' => 'common',
                'data' => json_encode($this->_ggPokerMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => '幸运棋牌',
                'ident' => 'lg_qp',
                'category' => 'common',
                'data' => json_encode($this->_lgQpMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => '龙城棋牌',
                'ident' => 'lc_qp',
                'category' => 'common',
                'data' => json_encode($this->_lcQpMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => '财神棋牌',
                'ident' => 'vg_qp',
                'category' => 'common',
                'data' => json_encode($this->_VgQpMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => 'MG电游',
                'ident' => 'mg_game',
                'category' => 'common',
                'data' => json_encode($this->_mgGameMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => 'BNG电游',
                'ident' => 'bng_game',
                'category' => 'common',
                'data' => json_encode($this->_bngGameMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => 'MG2电游',
                'ident' => 'mg2_game',
                'category' => 'common',
                'data' => json_encode($this->_mg2GameMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => '真人彩票',
                'ident' => 'live_lottery',
                'category' => 'common',
                'data' => json_encode($this->_liveLotteryMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => '首页热门彩种',
                'ident' => 'lottery_pc_hot',
                'category' => 'pc',
                'data' => json_encode($this->_lotteryPcHotMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
            [
                'name' => 'H5视讯游艺',
                'ident' => 'thirdparty_lobby',
                'category' => 'h5',
                'data' => json_encode($this->_thirdpartyLobbyMenuInit(), JSON_UNESCAPED_UNICODE),
                'status' => 1,
                'created_at' => $date_time,
                'updated_at' => $date_time,
            ],
        ];
        DB::table('front_menu')->insert($data);

        //初始化到缓存
        \Service\API\FrontMenu::refreshAllCache();
    }

    /**
     * 数字彩票PC
     * @return array
     */
    private function _lotteryPcMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '时时彩',
            'path' => 'ssc',
            'sort' => 0,
            'children' => [
                ['name' => '重庆欢乐生肖', 'path' => '/lottery/cqssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '乐利时时彩', 'path' => '/live/leli/120', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '乐利1.5分彩', 'path' => '/live/leli/119', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '腾讯分分彩', 'path' => '/lottery/txffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '奇趣腾讯分分彩', 'path' => '/lottery/qiqutxffssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '腾讯五分彩', 'path' => '/lottery/tx5fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '奇趣腾讯五分彩', 'path' => '/lottery/qiqutx5fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '比特币分分彩', 'path' => '/lottery/btcffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '吉利分分彩', 'path' => '/lottery/jlffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '凤凰吉利时时彩', 'path' => '/lottery/fhjlssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '天津时时彩', 'path' => '/lottery/tjssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '新疆时时彩', 'path' => '/lottery/xjssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '香港时时彩', 'path' => '/lottery/5fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '澳门时时彩', 'path' => '/lottery/3fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '台湾时时彩', 'path' => '/lottery/2fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '重庆分分彩', 'path' => '/lottery/1fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极速秒秒彩', 'path' => '/lottery/jsmmc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR三分彩', 'path' => '/live/vr/11', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR1.5分彩', 'path' => '/live/vr/1', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '河内分分彩', 'path' => '/lottery/hne1fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '河内5分彩', 'path' => '/lottery/hne5fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '360分分彩', 'path' => '/lottery/cxg360ffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '360五分彩', 'path' => '/lottery/cxg3605fc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '11选5',
            'path' => '11x5',
            'sort' => 1,
            'children' => [
                ['name' => '山东11选5', 'path' => '/lottery/sd11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '广东11选5', 'path' => '/lottery/gd11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '江西11选5', 'path' => '/lottery/jx11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '上海11选5', 'path' => '/lottery/sh11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '浙江11选5', 'path' => '/lottery/zj11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '香港11选5', 'path' => '/lottery/5f11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '澳门11选5', 'path' => '/lottery/3f11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '台湾11选5', 'path' => '/lottery/2f11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '曼谷11选5', 'path' => '/lottery/1f11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '秒秒11选5', 'path' => '/lottery/jsmm11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '安徽11选5', 'path' => '/lottery/ah11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '快三',
            'path' => 'k3',
            'sort' => 2,
            'children' => [
                ['name' => '江苏快三', 'path' => '/lottery/jsk3', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '湖北快三', 'path' => '/lottery/hbk3', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '安徽快三', 'path' => '/lottery/ahk3', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '乐利快三', 'path' => '/live/leli/121', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '快乐10分',
            'path' => 'kls',
            'sort' => 2,
            'children' => [
                ['name' => '湖南快乐10分', 'path' => '/lottery/hnkls', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '广东快乐10分', 'path' => '/lottery/gdkls', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '天津快乐10分', 'path' => '/lottery/tjkls', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '重庆快乐10分', 'path' => '/lottery/cqkls', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '快乐8',
            'path' => 'kl8',
            'sort' => 2,
            'children' => [
                ['name' => '北京快乐8', 'path' => '/lottery/bjkl8', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '加拿大快乐8', 'path' => '/lottery/jndkl8', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => 'PC蛋蛋',
            'path' => 'pcdd',
            'sort' => 3,
            'children' => [
                ['name' => 'PC蛋蛋', 'path' => '/lottery/pcdd', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '加拿大PC28', 'path' => '/lottery/jndpcdd', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => 'PK10',
            'path' => 'pk10',
            'sort' => 4,
            'children' => [
                ['name' => '北京PK10', 'path' => '/lottery/bjpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运飞艇', 'path' => '/lottery/xyftpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运赛艇', 'path' => '/lottery/jsxystpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '海南赛车PK拾', 'path' => '/lottery/5fpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极速赛车', 'path' => '/lottery/jsscpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '腾讯PK10', 'path' => '/lottery/tx5fpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极速赛马PK10', 'path' => '/lottery/xyftpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR木星赛车', 'path' => '/live/vr/35', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR北京赛车', 'path' => '/live/vr/13', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR赛车', 'path' => '/live/vr/2', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR赛马', 'path' => '/live/vr/36', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR游泳', 'path' => '/live/vr/37', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR自行车', 'path' => '/live/vr/38', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '联销彩',
            'path' => 'lxc',
            'sort' => 5,
            'children' => [
                ['name' => '福彩3D', 'path' => '/lottery/fucai3d', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '香港3D', 'path' => '/lottery/5f3d', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '秒秒3D', 'path' => '/lottery/jsmm3d', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '排列三、五', 'path' => '/lottery/pl3pl5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '香港六合彩', 'path' => '/lottery/xglhc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '上海时时乐', 'path' => '/lottery/shssl3d', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR六合彩', 'path' => '/live/vr/16', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR百家乐', 'path' => '/live/vr/15', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '乐利六合彩', 'path' => '/live/leli/118', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * 数字彩票H5
     * @return array
     */
    private function _lotteryH5MenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '热门游戏',
            'path' => 'hot',
            'sort' => 0,
            'children' => [
                ['name' => '重庆时时彩', 'path' => '/lottery/cqssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '比特币分分彩', 'path' => '/lottery/btcffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '吉利分分彩', 'path' => '/lottery/jlffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '凤凰吉利时时彩', 'path' => '/lottery/fhjlssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '腾讯PK10', 'path' => '/lottery/tx5fpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '山东11选5', 'path' => '/lottery/sd11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '香港六合彩', 'path' => '/lottery/xglhc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '江苏快三', 'path' => '/lottery/jsk3', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '北京快乐8', 'path' => '/lottery/bjkl8', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '湖南快乐10分', 'path' => '/lottery/hnkls', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'PC蛋蛋', 'path' => '/lottery/pcdd', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '腾讯分分彩', 'path' => '/lottery/txffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '奇趣腾讯分分彩', 'path' => '/lottery/qiqutxffssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '腾讯五分彩', 'path' => '/lottery/tx5fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '时时彩',
            'path' => 'ssc',
            'sort' => 1,
            'children' => [
                ['name' => '重庆欢乐生肖', 'path' => '/lottery/cqssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '腾讯分分彩', 'path' => '/lottery/txffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '奇趣腾讯分分彩', 'path' => '/lottery/qiqutxffssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '腾讯五分彩', 'path' => '/lottery/tx5fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '奇趣腾讯五分彩', 'path' => '/lottery/qiqutx5fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '比特币分分彩', 'path' => '/lottery/btcffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '吉利分分彩', 'path' => '/lottery/jlffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '凤凰吉利时时彩', 'path' => '/lottery/fhjlssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '天津时时彩', 'path' => '/lottery/tjssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '新疆时时彩', 'path' => '/lottery/xjssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '香港时时彩', 'path' => '/lottery/5fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '澳门时时彩', 'path' => '/lottery/3fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '台湾时时彩', 'path' => '/lottery/2fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '重庆分分彩', 'path' => '/lottery/1fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极速秒秒彩', 'path' => '/lottery/jsmmc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '河内分分彩', 'path' => '/lottery/hne1fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '河内5分彩', 'path' => '/lottery/hne5fssc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '360分分彩', 'path' => '/lottery/cxg360ffc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '360五分彩', 'path' => '/lottery/cxg3605fc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '11选5',
            'path' => '11x5',
            'sort' => 2,
            'children' => [
                ['name' => '山东11选5', 'path' => '/lottery/sd11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '广东11选5', 'path' => '/lottery/gd11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '江西11选5', 'path' => '/lottery/jx11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '上海11选5', 'path' => '/lottery/sh11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '浙江11选5', 'path' => '/lottery/zj11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '香港11选5', 'path' => '/lottery/5f11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '澳门11选5', 'path' => '/lottery/3f11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '台湾11选5', 'path' => '/lottery/2f11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '曼谷11选5', 'path' => '/lottery/1f11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '秒秒11选5', 'path' => '/lottery/jsmm11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '安徽11选5', 'path' => '/lottery/ah11x5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '快三',
            'path' => 'k3',
            'sort' => 3,
            'children' => [
                ['name' => '江苏快三', 'path' => '/lottery/jsk3', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '湖北快三', 'path' => '/lottery/hbk3', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '安徽快三', 'path' => '/lottery/ahk3', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '快乐10分',
            'path' => 'kls',
            'sort' => 4,
            'children' => [
                ['name' => '湖南快乐10分', 'path' => '/lottery/hnkls', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '广东快乐10分', 'path' => '/lottery/gdkls', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '天津快乐10分', 'path' => '/lottery/tjkls', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '重庆快乐10分', 'path' => '/lottery/cqkls', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '快乐8',
            'path' => 'kl8',
            'sort' => 5,
            'children' => [
                ['name' => '北京快乐8', 'path' => '/lottery/bjkl8', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '加拿大快乐8', 'path' => '/lottery/jndkl8', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => 'PC蛋蛋',
            'path' => 'pcdd',
            'sort' => 6,
            'children' => [
                ['name' => 'PC蛋蛋', 'path' => '/lottery/pcdd', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '加拿大PC28', 'path' => '/lottery/jndpcdd', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => 'PK10',
            'path' => 'pk10',
            'sort' => 7,
            'children' => [
                ['name' => '北京PK10', 'path' => '/lottery/bjpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运飞艇', 'path' => '/lottery/xyftpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运赛艇', 'path' => '/lottery/jsxystpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '海南赛车PK拾', 'path' => '/lottery/5fpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极速赛车', 'path' => '/lottery/jsscpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '腾讯PK10', 'path' => '/lottery/tx5fpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极速赛马PK10', 'path' => '/lottery/xyftpk10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '联销彩',
            'path' => 'lxc',
            'sort' => 8,
            'children' => [
                ['name' => '福彩3D', 'path' => '/lottery/fucai3d', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '香港3D', 'path' => '/lottery/5f3d', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '秒秒3D', 'path' => '/lottery/jsmm3d', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '排列三、五', 'path' => '/lottery/pl3pl5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '香港六合彩', 'path' => '/lottery/xglhc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '上海时时乐', 'path' => '/lottery/shssl3d', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * VR彩票
     * @return array
     */
    private function _vrLotteryMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '热门游戏',
            'path' => 'hot',
            'sort' => 0,
            'children' => [
                ['name' => 'VR三分彩', 'path' => '/live/vr/11', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR1.5分彩', 'path' => '/live/vr/1', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR六合彩', 'path' => '/live/vr/16', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '时时彩',
            'path' => 'ssc',
            'sort' => 1,
            'children' => [
                ['name' => 'VR三分彩', 'path' => '/live/vr/11', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR1.5分彩', 'path' => '/live/vr/1', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => 'PK10',
            'path' => 'pk10',
            'sort' => 2,
            'children' => [
                ['name' => 'VR木星赛车', 'path' => '/live/vr/35', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR北京赛车', 'path' => '/live/vr/13', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR赛车', 'path' => '/live/vr/2', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR赛马', 'path' => '/live/vr/36', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR游泳', 'path' => '/live/vr/37', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR自行车', 'path' => '/live/vr/38', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '联销彩',
            'path' => 'lxc',
            'sort' => 3,
            'children' => [
                ['name' => 'VR六合彩', 'path' => '/live/vr/16', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR百家乐', 'path' => '/live/vr/15', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * 乐利彩票
     * @return array
     */
    private function _lollyLotteryMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '热门游戏',
            'path' => 'hot',
            'sort' => 0,
            'children' => [
                ['name' => '乐利时时彩', 'path' => '/live/leli/120', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '乐利1.5分彩', 'path' => '/live/leli/119', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '乐利六合彩', 'path' => '/live/leli/118', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '乐利快三', 'path' => '/live/leli/121', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '时时彩',
            'path' => 'ssc',
            'sort' => 1,
            'children' => [
                ['name' => '乐利时时彩', 'path' => '/live/leli/120', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '乐利1.5分彩', 'path' => '/live/leli/119', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '快三',
            'path' => 'k3',
            'sort' => 2,
            'children' => [
                ['name' => '乐利快三', 'path' => '/live/leli/121', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '联销彩',
            'path' => 'lxc',
            'sort' => 3,
            'children' => [
                ['name' => 'VR六合彩', 'path' => '/live/vr/16', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'VR百家乐', 'path' => '/live/vr/15', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '乐利六合彩', 'path' => '/live/leli/118', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * PT电游
     * @return array
     */
    private function _ptGameMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '上古文明 神秘力量',
            'path' => 'myth',
            'sort' => 0,
            'children' => [
                ['name' => '神灵时代：命运姐妹', 'path' => '/game/play/impt/ftsis', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '神的时代：奥林匹斯之国王', 'path' => '/game/play/impt/zeus', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '神的时代：雷霆4神', 'path' => '/game/play/impt/furf', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '众神时代：风暴之神TM', 'path' => '/game/play/impt/aeolus', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '神的时代', 'path' => '/game/play/impt/aogs', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '超级法老王宝藏', 'path' => '/game/play/impt/phtd', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '法老王的秘密', 'path' => '/game/play/impt/pst', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '荣耀罗马', 'path' => '/game/play/impt/gtsrng', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '印加大奖', 'path' => '/game/play/impt/aztec', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '泰国神庙', 'path' => '/game/play/impt/thtk', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '亚特兰蒂斯女王', 'path' => '/game/play/impt/gtsatq', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '胆大戴夫和拉之眼', 'path' => '/game/play/impt/gtsdrdv', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '动物明星 魅力上场',
            'path' => 'animal',
            'sort' => 1,
            'children' => [
                ['name' => '小猪与狼', 'path' => '/game/play/impt/paw', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '熊之舞', 'path' => '/game/play/impt/bob', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '狐狸的宝藏', 'path' => '/game/play/impt/fxf', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '金钱蛙', 'path' => '/game/play/impt/jqw', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '企鹅假期', 'path' => '/game/play/impt/pgv', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '豹月', 'path' => '/game/play/impt/pmn', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '猫赌神', 'path' => '/game/play/impt/ctiv', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '野牛闪电战', 'path' => '/game/play/impt/bfb', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '热带动物园', 'path' => '/game/play/impt/sfh', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '怀特王', 'path' => '/game/play/impt/whk', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '四象', 'path' => '/game/play/impt/sx', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '海豚礁', 'path' => '/game/play/impt/dnr', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],

            ],
        ];

        $menu_data[] = [
            'name' => '中国文化 好运连连',
            'path' => 'chinese',
            'sort' => 2,
            'children' => [
                ['name' => '大明帝国', 'path' => '/game/play/impt/gtsgme', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '飞龙在天', 'path' => '/game/play/impt/gtsflzt', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '中国厨房', 'path' => '/game/play/impt/cm', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '云从龙', 'path' => '/game/play/impt/yclong', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '五虎将', 'path' => '/game/play/impt/ftg', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '五路财神', 'path' => '/game/play/impt/wlcsh', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '六福兽', 'path' => '/game/play/impt/kfp', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '吉祥8', 'path' => '/game/play/impt/gtsjxb', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '壮志凌云', 'path' => '/game/play/impt/topg', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '好运来', 'path' => '/game/play/impt/sol', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '招财进宝', 'path' => '/game/play/impt/zcjb', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '招财进宝彩池', 'path' => '/game/play/impt/zcjbjp', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '武则天', 'path' => '/game/play/impt/heavru', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '龙之战士', 'path' => '/game/play/impt/drgch', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '龙龙龙', 'path' => '/game/play/impt/longlong', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '舞龙', 'path' => '/game/play/impt/paw', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '奇幻故事 刺激好玩',
            'path' => 'fantasy',
            'sort' => 3,
            'children' => [
                ['name' => '白雪公主', 'path' => '/game/play/impt/ashfta', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '宝石皇后', 'path' => '/game/play/impt/gemq', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '杰克与魔豆', 'path' => '/game/play/impt/ashbob', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '权杖女王', 'path' => '/game/play/impt/qnw', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '仙境冒险', 'path' => '/game/play/impt/ashadv', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '三剑客和女王的钻石', 'path' => '/game/play/impt/tmqd', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '弓箭手', 'path' => '/game/play/impt/arc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '神秘夏洛克', 'path' => '/game/play/impt/shmst', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '猿人传奇', 'path' => '/game/play/impt/epa', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '富有的唐吉可德', 'path' => '/game/play/impt/donq', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '青春之泉', 'path' => '/game/play/impt/foy', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '洛奇', 'path' => '/game/play/impt/rky', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '美女船长', 'path' => '/game/play/impt/ashcpl', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '狂野精灵', 'path' => '/game/play/impt/wis', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '狂躁的海盗', 'path' => '/game/play/impt/gts52', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '樱花之恋', 'path' => '/game/play/impt/chl', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '鬼屋', 'path' => '/game/play/impt/hh', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '神奇的栈', 'path' => '/game/play/impt/mgstk', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '边境之心', 'path' => '/game/play/impt/ashhof', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '招财进宝 赚最多',
            'path' => 'money',
            'sort' => 4,
            'children' => [
                ['name' => '巨额财富', 'path' => '/game/play/impt/gtspor', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '万圣节财富2', 'path' => '/game/play/impt/hlf2', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '开心假期', 'path' => '/game/play/impt/er', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '开心假期加强版', 'path' => '/game/play/impt/vcstd', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '日日生财', 'path' => '/game/play/impt/ririshc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '返利先生', 'path' => '/game/play/impt/mcb', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '金土地', 'path' => '/game/play/impt/lndg', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '超级888', 'path' => '/game/play/impt/chao', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运月', 'path' => '/game/play/impt/ashfmf', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '银弹', 'path' => '/game/play/impt/sib', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黄金游戏', 'path' => '/game/play/impt/glg', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '热力宝石', 'path' => '/game/play/impt/gts50', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '疯狂7', 'path' => '/game/play/impt/c7', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '疯狂麻将', 'path' => '/game/play/impt/fkmj', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '纯金翅膀', 'path' => '/game/play/impt/gtswng', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '烈焰钻石', 'path' => '/game/play/impt/ght_a', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '奖金巨人', 'path' => '/game/play/impt/jpgt', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '野外宝藏', 'path' => '/game/play/impt/legwld', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '甜蜜派对', 'path' => '/game/play/impt/cnpr', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '世界奇迹 疯狂畅玩',
            'path' => 'world',
            'sort' => 5,
            'children' => [
                ['name' => '巴西森宝', 'path' => '/game/play/impt/gtssmbr', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '泰国天堂', 'path' => '/game/play/impt/tpd2', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '艺伎故事', 'path' => '/game/play/impt/ges', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '足球狂欢节', 'path' => '/game/play/impt/gtsfc', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '狂野亚马逊', 'path' => '/game/play/impt/ashamw', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极地冒险', 'path' => '/game/play/impt/gtsir', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百慕达三角洲', 'path' => '/game/play/impt/bt', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '牛仔和外星人', 'path' => '/game/play/impt/gtscbl', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '沉默的武士', 'path' => '/game/play/impt/sis', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '武士元素', 'path' => '/game/play/impt/pisa', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '亚马逊的秘密', 'path' => '/game/play/impt/samz', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '埃斯梅拉达', 'path' => '/game/play/impt/esmk', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '冰穴', 'path' => '/game/play/impt/ashicv', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '湛蓝深海', 'path' => '/game/play/impt/bib', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '湛蓝深海彩池TM', 'path' => '/game/play/impt/grbjp', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '海滨嘉年华', 'path' => '/game/play/impt/bl', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '金刚-世界的第八大奇迹', 'path' => '/game/play/impt/kkg', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * GG电游
     * @return array
     */
    public function _ggGameMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '全部',
            'path' => 'all',
            'sort' => 0,
            'children' => [
                ['name' => '甜心空姐', 'path' => '/game/play/GgDy/1001', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '阳光沙滩', 'path' => '/game/play/GgDy/1002', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '欢乐原始人', 'path' => '/game/play/GgDy/1003', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运美人鱼', 'path' => '/game/play/GgDy/1004', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'SM', 'path' => '/game/play/GgDy/1005', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '夜上海', 'path' => '/game/play/GgDy/1006', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '青楼梦', 'path' => '/game/play/GgDy/1007', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '盗窃谜案', 'path' => '/game/play/GgDy/1008', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '中华厨娘', 'path' => '/game/play/GgDy/1009', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '比基尼拳赛', 'path' => '/game/play/GgDy/1010', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '天宫仙乐', 'path' => '/game/play/GgDy/1011', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '小红帽', 'path' => '/game/play/GgDy/1012', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '机车辣妹', 'path' => '/game/play/GgDy/1013', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '彩绘电音派对', 'path' => '/game/play/GgDy/1014', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '赌城脱衣娘', 'path' => '/game/play/GgDy/1016', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '西部酒吧', 'path' => '/game/play/GgDy/1017', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * 开元棋牌
     * @return array
     */
    private function _kyPokerMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '全部',
            'path' => 'all',
            'sort' => 0,
            'children' => [
                ['name' => '德州扑克', 'path' => '/game/play/poker/620', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '二八杠', 'path' => '/game/play/poker/720', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢庄牛牛', 'path' => '/game/play/poker/830', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '三公', 'path' => '/game/play/poker/860', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '押庄龙虎', 'path' => '/game/play/poker/900', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '21点', 'path' => '/game/play/poker/600', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '通比牛牛', 'path' => '/game/play/poker/870', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极速炸金花', 'path' => '/game/play/poker/230', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢庄牌九', 'path' => '/game/play/poker/730', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '十三水', 'path' => '/game/play/poker/630', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百家乐', 'path' => '/game/play/poker/910', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '森林舞会', 'path' => '/game/play/poker/920', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人牛牛', 'path' => '/game/play/poker/930', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '万人炸金花', 'path' => '/game/play/poker/1950', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '斗地主', 'path' => '/game/play/poker/610', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '炸金花', 'path' => '/game/play/poker/220', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * GG棋牌
     * @return array
     */
    public function _ggPokerMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '全部',
            'path' => 'all',
            'sort' => 0,
            'children' => [
                ['name' => '大众麻将', 'path' => '/game/play/GgQiPai/1', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '牛牛', 'path' => '/game/play/GgQiPai/2', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '十三水', 'path' => '/game/play/GgQiPai/3', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '炸金花', 'path' => '/game/play/GgQiPai/4', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '三公', 'path' => '/game/play/GgQiPai/5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '21点', 'path' => '/game/play/GgQiPai/6', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人牛牛', 'path' => '/game/play/GgQiPai/500', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人诈金花', 'path' => '/game/play/GgQiPai/501', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人三公', 'path' => '/game/play/GgQiPai/502', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人二八杠', 'path' => '/game/play/GgQiPai/503', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人牌九', 'path' => '/game/play/GgQiPai/504', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人十点半', 'path' => '/game/play/GgQiPai/505', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人龙虎', 'path' => '/game/play/GgQiPai/506', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人骰宝', 'path' => '/game/play/GgQiPai/507', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人百家乐', 'path' => '/game/play/GgQiPai/508', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }


    /**
     * 幸运棋牌
     * @return array
     */
    private function _lgQpMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '全部',
            'path' => 'all',
            'sort' => 0,
            'children' => [
                ['name' => '炸金花', 'path' => '/game/play/LgQp/100001', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '看牌抢庄牛牛', 'path' => '/game/play/LgQp/100018', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '经典牛牛', 'path' => '/game/play/LgQp/100029', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢庄牌九', 'path' => '/game/play/LgQp/100028', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '通比牛牛', 'path' => '/game/play/LgQp/100006', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢红包', 'path' => '/game/play/LgQp/100020', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '龙虎斗', 'path' => '/game/play/LgQp/100010', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢庄牛牛', 'path' => '/game/play/LgQp/100002', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '二十一点', 'path' => '/game/play/LgQp/100005', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极速狂飙', 'path' => '/game/play/LgQp/100019', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '二八杠', 'path' => '/game/play/LgQp/100003', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '飞禽走兽', 'path' => '/game/play/LgQp/100009', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人牛牛', 'path' => '/game/play/LgQp/100012', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '骰宝', 'path' => '/game/play/LgQp/100022', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '极速炸金花', 'path' => '/game/play/LgQp/100007', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '红黑大战', 'path' => '/game/play/LgQp/100016', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '梭哈', 'path' => '/game/play/LgQp/100011', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '三公', 'path' => '/game/play/LgQp/100013', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百家乐', 'path' => '/game/play/LgQp/100015', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * 龙城棋牌
     * @return array
     */
    private function _lcQpMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '全部',
            'path' => 'all',
            'sort' => 0,
            'children' => [
                ['name' => '德州扑克', 'path' => '/game/play/LcQp/620', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '二八杠', 'path' => '/game/play/LcQp/720', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢庄牛牛', 'path' => '/game/play/LcQp/830', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '炸金花', 'path' => '/game/play/LcQp/220', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '三公', 'path' => '/game/play/LcQp/860', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '龙虎斗', 'path' => '/game/play/LcQp/900', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '21点', 'path' => '/game/play/LcQp/600', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '通比牛牛', 'path' => '/game/play/LcQp/870', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢庄牌九', 'path' => '/game/play/LcQp/730', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '十三水', 'path' => '/game/play/LcQp/630', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '斗地主', 'path' => '/game/play/LcQp/610', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '看四张抢庄牛', 'path' => '/game/play/LcQp/890', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百家乐', 'path' => '/game/play/LcQp/910', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '二人麻将', 'path' => '/game/play/LcQp/740', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人牛牛', 'path' => '/game/play/LcQp/930', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '跑得快', 'path' => '/game/play/LcQp/640', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '捕鱼', 'path' => '/game/play/LcQp/510', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运水果机', 'path' => '/game/play/LcQp/960', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '红黑大战', 'path' => '/game/play/LcQp/950', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢庄五选三', 'path' => '/game/play/LcQp/990', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '随机庄百变牛', 'path' => '/game/play/LcQp/8100', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人骰宝', 'path' => '/game/play/LcQp/8200', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢庄21点', 'path' => '/game/play/LcQp/8300', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '看牌抢庄三公', 'path' => '/game/play/LcQp/8400', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '欢乐斗牛', 'path' => '/game/play/LcQp/8500', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * 财神棋牌
     * @return array
     */
    private function _VgQpMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '全部',
            'path' => 'all',
            'sort' => 0,
            'children' => [
                ['name' => '斗地主', 'path' => '/game/play/VgQp/1', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢庄牛牛', 'path' => '/game/play/VgQp/3', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '百人牛牛', 'path' => '/game/play/VgQp/4', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '龙王捕鱼', 'path' => '/game/play/VgQp/5', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '竞咪楚汉德州', 'path' => '/game/play/VgQp/7', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '推筒子', 'path' => '/game/play/VgQp/8', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '加倍斗地主', 'path' => '/game/play/VgQp/9', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '保险楚汉德州', 'path' => '/game/play/VgQp/10', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '血战麻将', 'path' => '/game/play/VgQp/11', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '炸金花', 'path' => '/game/play/VgQp/12', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '必下德州', 'path' => '/game/play/VgQp/13', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * MG电游
     * @return array
     */
    public function _mgGameMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '老虎机1',
            'path' => 'slot1',
            'sort' => 0,
            'children' => [
                ['name' => '阿瓦隆', 'path' => '/game/play/MgDy/26601', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '海底世界', 'path' => '/game/play/MgDy/26611', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '女仕之夜', 'path' => '/game/play/MgDy/26619', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '特务珍金', 'path' => '/game/play/MgDy/26623', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '炫富一族', 'path' => '/game/play/MgDy/26633', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '银行爆破', 'path' => '/game/play/MgDy/26635', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '万圣节', 'path' => '/game/play/MgDy/26655', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '雷电击', 'path' => '/game/play/MgDy/26661', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '埃及女神伊西絲', 'path' => '/game/play/MgDy/26663', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '终极杀手', 'path' => '/game/play/MgDy/26675', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '5卷的驱动器', 'path' => '/game/play/MgDy/26685', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '昆虫派对', 'path' => '/game/play/MgDy/26687', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '卡萨缦都', 'path' => '/game/play/MgDy/26728', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '疯狂的帽子', 'path' => '/game/play/MgDy/26730', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '史地大发现', 'path' => '/game/play/MgDy/28692', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '卡萨缦都', 'path' => '/game/play/MgDy/28940', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '橄榄球明星', 'path' => '/game/play/MgDy/45535', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '篮球巨星', 'path' => '/game/play/MgDy/45539', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '快乐假日', 'path' => '/game/play/MgDy/45609', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运的锦鲤', 'path' => '/game/play/MgDy/45611', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '比基尼派对', 'path' => '/game/play/MgDy/46810', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黑绵羊咩咩叫5轴', 'path' => '/game/play/MgDy/55007', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '挥金如土', 'path' => '/game/play/MgDy/59219', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '爱丽娜', 'path' => '/game/play/MgDy/60167', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '老虎机2',
            'path' => 'slot2',
            'sort' => 1,
            'children' => [
                ['name' => '冰球突破', 'path' => '/game/play/MgDy/60467', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '哈维斯的晚餐', 'path' => '/game/play/MgDy/60805', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '旋转大战', 'path' => '/game/play/MgDy/61557', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'K歌乐韵', 'path' => '/game/play/MgDy/66078', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '丛林吉姆-黄金国', 'path' => '/game/play/MgDy/66605', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '迷失拉斯维加斯', 'path' => '/game/play/MgDy/66916', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运龙宝贝', 'path' => '/game/play/MgDy/67415', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '轩辕帝传', 'path' => '/game/play/MgDy/68622', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '富裕人生', 'path' => '/game/play/MgDy/68623', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '不朽情缘', 'path' => '/game/play/MgDy/68624', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '水果VS糖果', 'path' => '/game/play/MgDy/69002', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '青龙出海', 'path' => '/game/play/MgDy/69660', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '花粉之国', 'path' => '/game/play/MgDy/69664', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '运财酷儿-5卷轴', 'path' => '/game/play/MgDy/70143', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '巨额现金乘数', 'path' => '/game/play/MgDy/70303', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '禁忌王座', 'path' => '/game/play/MgDy/70305', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '金库甜心', 'path' => '/game/play/MgDy/70307', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '梦果子乐园', 'path' => '/game/play/MgDy/70313', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '美丽骷髅', 'path' => '/game/play/MgDy/70527', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '侏罗纪世界', 'path' => '/game/play/MgDy/70656', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '108好汉', 'path' => '/game/play/MgDy/70715', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '财运疯狂', 'path' => '/game/play/MgDy/70794', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '疯狂变色龙', 'path' => '/game/play/MgDy/70796', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黄金囊地鼠', 'path' => '/game/play/MgDy/70798', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '老虎机3',
            'path' => 'slot3',
            'sort' => 2,
            'children' => [
                ['name' => '靶心', 'path' => '/game/play/MgDy/70836', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '怪兽曼琪肯', 'path' => '/game/play/MgDy/70844', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '卷行使价', 'path' => '/game/play/MgDy/70846', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运生肖', 'path' => '/game/play/MgDy/70850', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '108好汉乘数财富', 'path' => '/game/play/MgDy/70868', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '矮木头', 'path' => '/game/play/MgDy/70885', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '太阳神之忒伊亚', 'path' => '/game/play/MgDy/70897', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '万圣劫', 'path' => '/game/play/MgDy/70919', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '怪物赛车', 'path' => '/game/play/MgDy/70921', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '白鲸记', 'path' => '/game/play/MgDy/71045', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '歌剧魅影', 'path' => '/game/play/MgDy/71175', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '躲猫猫', 'path' => '/game/play/MgDy/71273', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '大象之王', 'path' => '/game/play/MgDy/71283', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '时空英豪', 'path' => '/game/play/MgDy/71334', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '圣诞企鹅', 'path' => '/game/play/MgDy/71336', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '囧囧熊猫', 'path' => '/game/play/MgDy/71344', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黄金工厂', 'path' => '/game/play/MgDy/71406', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '持枪王者', 'path' => '/game/play/MgDy/71408', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢劫银行', 'path' => '/game/play/MgDy/71476', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黄金花花公子', 'path' => '/game/play/MgDy/71490', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '梦幻邂逅', 'path' => '/game/play/MgDy/71509', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '钻石帝国', 'path' => '/game/play/MgDy/71525', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '魔力撒哈拉', 'path' => '/game/play/MgDy/72227', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '蒂基维京', 'path' => '/game/play/MgDy/72235', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '老虎机4',
            'path' => 'slot4',
            'sort' => 3,
            'children' => [
                ['name' => '酷犬酒店', 'path' => '/game/play/MgDy/71571', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '征服钱海', 'path' => '/game/play/MgDy/71588', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '财富之都', 'path' => '/game/play/MgDy/71596', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '雪伍德的罗宾汉', 'path' => '/game/play/MgDy/71671', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '异域狂兽', 'path' => '/game/play/MgDy/71851', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '凯蒂卡巴拉', 'path' => '/game/play/MgDy/71855', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黑暗故事神秘深红', 'path' => '/game/play/MgDy/71881', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '水晶裂谷', 'path' => '/game/play/MgDy/71947', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '冰球突破豪华版', 'path' => '/game/play/MgDy/71974', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'Oz之书', 'path' => '/game/play/MgDy/71978', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '秘密行动雪诺和塞布尔', 'path' => '/game/play/MgDy/71982', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '有你的校园', 'path' => '/game/play/MgDy/72000', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '招财鞭炮', 'path' => '/game/play/MgDy/72002', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '丧尸来袭', 'path' => '/game/play/MgDy/72041', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '伟大魔术师', 'path' => '/game/play/MgDy/72051', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '特工简布隆德归来', 'path' => '/game/play/MgDy/72062', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '好运经纪人', 'path' => '/game/play/MgDy/72066', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '宙斯古代财富', 'path' => '/game/play/MgDy/72081', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '探陵人', 'path' => '/game/play/MgDy/72083', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '萝拉神庙古墓', 'path' => '/game/play/MgDy/72089', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '篮球明星豪华版', 'path' => '/game/play/MgDy/72113', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '神龙碎片', 'path' => '/game/play/MgDy/72117', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '型男舞步', 'path' => '/game/play/MgDy/72125', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '海底宝城', 'path' => '/game/play/MgDy/72217', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * BNG电游
     * @return array
     */
    public function _bngGameMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '新年快乐 好运连连',
            'path' => 'slot1',
            'sort' => 0,
            'children' => [
                ['name' => '淘金猴', 'path' => '/game/play/BngDy/145', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '福财神', 'path' => '/game/play/BngDy/142', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '福大门', 'path' => '/game/play/BngDy/134', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '8星报喜', 'path' => '/game/play/BngDy/131', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '龙珠–集鸿运', 'path' => '/game/play/BngDy/151', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '发财星', 'path' => '/game/play/BngDy/12', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '走运一路8', 'path' => '/game/play/BngDy/15', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '嫦娥与后羿', 'path' => '/game/play/BngDy/50', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '11.11光棍节', 'path' => '/game/play/BngDy/78', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '埃及艳后的任务', 'path' => '/game/play/BngDy/79', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '埃及艳后的任务2', 'path' => '/game/play/BngDy/82', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '传奇文明 寻找密宝',
            'path' => 'slot2',
            'sort' => 1,
            'children' => [
                ['name' => '钱滚滚圣甲虫', 'path' => '/game/play/BngDy/168', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '诸神荣耀', 'path' => '/game/play/BngDy/166', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '法老宝典:多胜彩', 'path' => '/game/play/BngDy/157', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '法老宝典', 'path' => '/game/play/BngDy/139', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '神龙秘宝', 'path' => '/game/play/BngDy/95', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '上帝的殿堂豪华版', 'path' => '/game/play/BngDy/86', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '太阳神殿–集鸿运', 'path' => '/game/play/BngDy/173', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '罗马帝国', 'path' => '/game/play/BngDy/80', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '北欧争霸战', 'path' => '/game/play/BngDy/60', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '希腊神话', 'path' => '/game/play/BngDy/77', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '上帝的殿堂', 'path' => '/game/play/BngDy/51', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '童话传奇 刺激有趣',
            'path' => 'slot3',
            'sort' => 2,
            'children' => [
                ['name' => '致命毒苹果', 'path' => '/game/play/BngDy/64', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '致命毒苹果2', 'path' => '/game/play/BngDy/170', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '水果传奇', 'path' => '/game/play/BngDy/24', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '疯狂水果店', 'path' => '/game/play/BngDy/48', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '小矮人寻宝记', 'path' => '/game/play/BngDy/49', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '经典小玛莉', 'path' => '/game/play/BngDy/43', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '圣诞快乐', 'path' => '/game/play/BngDy/17', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '失落的乐园', 'path' => '/game/play/BngDy/20', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '摇滚地狱', 'path' => '/game/play/BngDy/54', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '维京寒战', 'path' => '/game/play/BngDy/163', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '维京:神灵黄金', 'path' => '/game/play/BngDy/85', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '经典电游 原始玩法',
            'path' => 'slot4',
            'sort' => 3,
            'children' => [
                ['name' => '克隆–超级7', 'path' => '/game/play/BngDy/161', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '超级7', 'path' => '/game/play/BngDy/148', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '超级7转运', 'path' => '/game/play/BngDy/175', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '宝石星球:全面启动', 'path' => '/game/play/BngDy/154', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '宝石星球', 'path' => '/game/play/BngDy/84', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '霸龙', 'path' => '/game/play/BngDy/83', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '非洲之魂', 'path' => '/game/play/BngDy/16', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '牛逼爱尔兰大佬', 'path' => '/game/play/BngDy/62', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '快乐鸟', 'path' => '/game/play/BngDy/14', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '淘气圣诞', 'path' => '/game/play/BngDy/59', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '万圣美魔女', 'path' => '/game/play/BngDy/52', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * MG2电游
     * @return array
     */
    public function _mg2GameMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '老虎机1',
            'path' => 'slot1',
            'sort' => 0,
            'children' => [
                ['name' => '阿瓦隆', 'path' => '/game/play/Mg2/1013', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '海底世界', 'path' => '/game/play/Mg2/1308', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '女仕之夜', 'path' => '/game/play/Mg2/1389', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '特务珍金', 'path' => '/game/play/Mg2/1155', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '炫富一族', 'path' => '/game/play/Mg2/1245', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '银行爆破', 'path' => '/game/play/Mg2/1097', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '万圣节', 'path' => '/game/play/Mg2/1047', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '雷电击', 'path' => '/game/play/Mg2/1293', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '埃及女神伊西絲', 'path' => '/game/play/Mg2/1250', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '终极杀手', 'path' => '/game/play/Mg2/1321', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '5卷的驱动器', 'path' => '/game/play/Mg2/1035', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '昆虫派对', 'path' => '/game/play/Mg2/1366', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '卡萨缦都', 'path' => '/game/play/Mg2/1151', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '疯狂的帽子', 'path' => '/game/play/Mg2/1314', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '史地大发现', 'path' => '/game/play/Mg2/1246', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '橄榄球明星', 'path' => '/game/play/Mg2/1287', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '篮球巨星', 'path' => '/game/play/Mg2/1159', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '快乐假日', 'path' => '/game/play/Mg2/1072', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运的锦鲤', 'path' => '/game/play/Mg2/1060', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '比基尼派对', 'path' => '/game/play/Mg2/1290', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黑绵羊咩咩叫5轴', 'path' => '/game/play/Mg2/1788', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '挥金如土', 'path' => '/game/play/Mg2/1197', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '爱丽娜', 'path' => '/game/play/Mg2/1021', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '老虎机2',
            'path' => 'slot2',
            'sort' => 1,
            'children' => [
                ['name' => '冰球突破', 'path' => '/game/play/Mg2/1229', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '哈维斯的晚餐', 'path' => '/game/play/Mg2/1139', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '旋转大战', 'path' => '/game/play/Mg2/1294', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'K歌乐韵', 'path' => '/game/play/Mg2/1053', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '丛林吉姆-黄金国', 'path' => '/game/play/Mg2/1244', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '迷失拉斯维加斯', 'path' => '/game/play/Mg2/1420', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运龙宝贝', 'path' => '/game/play/Mg2/1424', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '轩辕帝传', 'path' => '/game/play/Mg2/1849', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '富裕人生', 'path' => '/game/play/Mg2/1851', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '不朽情缘', 'path' => '/game/play/Mg2/1103', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '水果VS糖果', 'path' => '/game/play/Mg2/1878', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '青龙出海', 'path' => '/game/play/Mg2/1882', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '花粉之国', 'path' => '/game/play/Mg2/1881', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '运财酷儿-5卷轴', 'path' => '/game/play/Mg2/1884', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '巨额现金乘数', 'path' => '/game/play/Mg2/1885', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '禁忌王座', 'path' => '/game/play/Mg2/1887', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '金库甜心', 'path' => '/game/play/Mg2/1888', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '梦果子乐园', 'path' => '/game/play/Mg2/1886', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '美丽骷髅', 'path' => '/game/play/Mg2/1890', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '侏罗纪世界', 'path' => '/game/play/Mg2/1891', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '108好汉', 'path' => '/game/play/Mg2/1302', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '财运疯狂', 'path' => '/game/play/Mg2/1393', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '疯狂变色龙', 'path' => '/game/play/Mg2/1202', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黄金囊地鼠', 'path' => '/game/play/Mg2/1216', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '老虎机3',
            'path' => 'slot3',
            'sort' => 2,
            'children' => [
                ['name' => '靶心', 'path' => '/game/play/Mg2/1718', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '怪兽曼琪肯', 'path' => '/game/play/Mg2/1008', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '卷行使价', 'path' => '/game/play/Mg2/1157', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运生肖', 'path' => '/game/play/Mg2/1273', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '108好汉乘数财富', 'path' => '/game/play/Mg2/1897', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '矮木头', 'path' => '/game/play/Mg2/1900', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '太阳神之忒伊亚', 'path' => '/game/play/Mg2/1385', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '万圣劫', 'path' => '/game/play/Mg2/1904', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '怪物赛车', 'path' => '/game/play/Mg2/1903', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '白鲸记', 'path' => '/game/play/Mg2/1905', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '歌剧魅影', 'path' => '/game/play/Mg2/1906', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '躲猫猫', 'path' => '/game/play/Mg2/1147', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '大象之王', 'path' => '/game/play/Mg2/1908', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '时空英豪', 'path' => '/game/play/Mg2/1909', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '圣诞企鹅', 'path' => '/game/play/Mg2/1910', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '囧囧熊猫', 'path' => '/game/play/Mg2/1911', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黄金工厂', 'path' => '/game/play/Mg2/1267', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '持枪王者', 'path' => '/game/play/Mg2/1160', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '抢劫银行', 'path' => '/game/play/Mg2/1204', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黄金花花公子', 'path' => '/game/play/Mg2/1946', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '梦幻邂逅', 'path' => '/game/play/Mg2/1948', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '钻石帝国', 'path' => '/game/play/Mg2/1949', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '魔力撒哈拉', 'path' => '/game/play/Mg2/4288', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '蒂基维京', 'path' => '/game/play/Mg2/4290', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        $menu_data[] = [
            'name' => '老虎机4',
            'path' => 'slot4',
            'sort' => 3,
            'children' => [
                ['name' => '酷犬酒店', 'path' => '/game/play/Mg2/1063', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '征服钱海', 'path' => '/game/play/Mg2/1399', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '财富之都', 'path' => '/game/play/Mg2/1993', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '雪伍德的罗宾汉', 'path' => '/game/play/Mg2/1994', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '异域狂兽', 'path' => '/game/play/Mg2/2060', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '凯蒂卡巴拉', 'path' => '/game/play/Mg2/1286', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '黑暗故事神秘深红', 'path' => '/game/play/Mg2/2064', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '水晶裂谷', 'path' => '/game/play/Mg2/2069', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '冰球突破豪华版', 'path' => '/game/play/Mg2/2074', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => 'Oz之书', 'path' => '/game/play/Mg2/2075', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '秘密行动雪诺和塞布尔', 'path' => '/game/play/Mg2/2076', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '有你的校园', 'path' => '/game/play/Mg2/2073', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '招财鞭炮', 'path' => '/game/play/Mg2/1126', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '丧尸来袭', 'path' => '/game/play/Mg2/2085', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '伟大魔术师', 'path' => '/game/play/Mg2/2087', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '特工简布隆德归来', 'path' => '/game/play/Mg2/2088', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '好运经纪人', 'path' => '/game/play/Mg2/2089', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '宙斯古代财富', 'path' => '/game/play/Mg2/4111', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '探陵人', 'path' => '/game/play/Mg2/4109', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '萝拉神庙古墓', 'path' => '/game/play/Mg2/4110', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '篮球明星豪华版', 'path' => '/game/play/Mg2/4256', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '神龙碎片', 'path' => '/game/play/Mg2/4257', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '型男舞步', 'path' => '/game/play/Mg2/4271', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '海底宝城', 'path' => '/game/play/Mg2/4286', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    //真人彩票
    public function _liveLotteryMenuInit()
    {
        $return_menu = [];
        $return_menu[] = [
            'name' => '全部',
            'path' => 'all',
            'sort' => 0,
            'children' => [
                [
                    'name' => 'vr三分彩',
                    'path' => '/live/vr/11',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'VR1.5分彩',
                    'path' => '/live/vr/1',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'VR木星赛车',
                    'path' => '/live/vr/35',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'VR北京赛车',
                    'path' => '/live/vr/13',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'VR赛车',
                    'path' => '/live/vr/2',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'VR赛马',
                    'path' => '/live/vr/36',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'VR游泳',
                    'path' => '/live/vr/37',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'VR自行车',
                    'path' => '/live/vr/38',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'VR六合彩',
                    'path' => '/live/vr/16',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'VR百家乐',
                    'path' => '/live/vr/15',
                    'sort' => 0,
                    'ishot' => 0,
                    'isnew' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
            ],
        ];

        return $return_menu;
    }

    //首页热门彩种
    public function _lotteryPcHotMenuInit()
    {
        $return_menu = [];
        $return_menu[] = [
            'name' => '首页热门彩种',
            'path' => 'all',
            'sort' => 1,
            'children' => [
                [
                    'name' => '河内五分彩',
                    'path' => '/lottery/hne5fssc',
                    'sort' => 1,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => '360分分彩',
                    'path' => '/lottery/cxg360ffc',
                    'sort' => 2,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => '腾讯分分彩',
                    'path' => '/lottery/txffc',
                    'sort' => 3,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
                [
                    'name' => '欢乐30秒',
                    'path' => '/lottery/hl30s',
                    'sort' => 4,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => '',
                    'logo_pc' => '',
                ],
            ],
        ];

        return $return_menu;
    }

    //H5视讯游艺
    public function _thirdpartyLobbyMenuInit()
    {
        $return_menu = [];
        $return_menu[] =
            [
                'name' => '棋牌游戏',
                'path' => 'poker_game',
                'sort' => 1,
                'children' => [
                    [
                        'name' => '开元棋牌',
                        'path' => '/game/play/poker/0',
                        'sort' => 1,
                        'ishot' => 0,
                        'isnew' => 0,
                        'isguan' => 0,
                        'ishot2' => 0,
                        'logo_h5' => 'Ky',
                        'logo_pc' => '',
                    ],
                    [
                        'name' => 'GG棋牌',
                        'path' => '/game/play/GgQiPai/0',
                        'sort' => 2,
                        'ishot' => 0,
                        'isnew' => 0,
                        'isguan' => 0,
                        'ishot2' => 0,
                        'logo_h5' => 'GgQiPai',
                        'logo_pc' => '',
                    ],
                    [
                        'name' => '幸运棋牌',
                        'path' => '/game/play/lgqp/0',
                        'sort' => 3,
                        'ishot' => 0,
                        'isnew' => 0,
                        'isguan' => 0,
                        'ishot2' => 0,
                        'logo_h5' => 'LgQp',
                        'logo_pc' => '',
                    ],
                    [
                        'name' => '财神棋牌',
                        'path' => '/game/play/VgQp/0',
                        'sort' => 4,
                        'ishot' => 0,
                        'isnew' => 0,
                        'isguan' => 0,
                        'ishot2' => 0,
                        'logo_h5' => 'VgQp',
                        'logo_pc' => '',
                    ],
                    [
                        'name' => '龙城棋牌',
                        'path' => '/game/play/LcQp/0',
                        'sort' => 5,
                        'ishot' => 0,
                        'isnew' => 0,
                        'isguan' => 0,
                        'ishot2' => 0,
                        'logo_h5' => 'LcQp',
                        'logo_pc' => '',
                    ],
                ],
            ];
        $return_menu[] = [
            'name' => '真人视讯',
            'path' => 'live_game',
            'sort' => 2,
            'children' => [
                [
                    'name' => 'AG旗舰厅',
                    'path' => '/game/play/ag',
                    'sort' => 1,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Ag',
                    'logo_pc' => '',
                ],
                [
                    'name' => '完美真人',
                    'path' => '/game/play/wml',
                    'sort' => 2,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Wml',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'EBET真人',
                    'path' => '/game/play/ebet',
                    'sort' => 3,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Ebet',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'Ab欧博真人',
                    'path' => '/game/play/Ab',
                    'sort' => 4,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Ab',
                    'logo_pc' => '',
                ],
            ],
        ];
        $return_menu[] = [
            'name' => '体育游戏',
            'path' => 'sport_game',
            'sort' => 3,
            'children' => [
                [
                    'name' => 'BBIN体育',
                    'path' => '/game/play/tcg',
                    'sort' => 1,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Tcg',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'UG体育',
                    'path' => '/game/play/ug',
                    'sort' => 2,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Ug',
                    'logo_pc' => '',
                ],
                [
                    'name' => '沙巴体育',
                    'path' => '/game/play/aio',
                    'sort' => 3,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Aio',
                    'logo_pc' => '',
                ],
            ],
        ];
        $return_menu[] = [
            'name' => '电子游戏',
            'path' => 'slot_game',
            'sort' => 4,
            'children' => [
                [
                    'name' => 'GG电子',
                    'path' => '/game/play/GgDy/0',
                    'sort' => 1,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'GgDy',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'MG电游',
                    'path' => 'mg_game',
                    'sort' => 2,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Mg2',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'PT老虎机',
                    'path' => 'pt_game',
                    'sort' => 3,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Pt',
                    'logo_pc' => '',
                ],
                [
                    'name' => 'SEA电游',
                    'path' => 'sea_game',
                    'sort' => 4,
                    'ishot' => 0,
                    'isnew' => 0,
                    'isguan' => 0,
                    'ishot2' => 0,
                    'logo_h5' => 'Sea',
                    'logo_pc' => '',
                ],
            ],
        ];

        return $return_menu;
    }

    /**
     * SEA电游
     * @return array
     */
    public function _seaGameMenuInit()
    {
        $menu_data = [];
        $menu_data[] = [
            'name' => '全部',
            'path' => 'all',
            'sort' => 0,
            'children' => [
                ['name' => '红胡子的宝藏', 'path' => '/game/play/Sea/339', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '经典四星', 'path' => '/game/play/Sea/338', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '狂野时速', 'path' => '/game/play/Sea/315', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '尼罗河神庙', 'path' => '/game/play/Sea/335', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '水果红绿灯', 'path' => '/game/play/Sea/337', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '龙凤呈祥', 'path' => '/game/play/Sea/334', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运兔子', 'path' => '/game/play/Sea/336', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '彩虹连击', 'path' => '/game/play/Sea/333', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '渔霸', 'path' => '/game/play/Sea/601', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '环游世界', 'path' => '/game/play/Sea/328', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '红鲤鱼与绿鲤鱼', 'path' => '/game/play/Sea/324', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '疯狂挖金', 'path' => '/game/play/Sea/329', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '推倒胡', 'path' => '/game/play/Sea/327', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '疯狂超8', 'path' => '/game/play/Sea/332', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '濠江之夜', 'path' => '/game/play/Sea/331', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '家有萌宝', 'path' => '/game/play/Sea/330', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '古墓玄奇', 'path' => '/game/play/Sea/322', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '农场物语', 'path' => '/game/play/Sea/326', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '炼狱', 'path' => '/game/play/Sea/325', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '万圣夜惊魂', 'path' => '/game/play/Sea/323', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '5PK', 'path' => '/game/play/Sea/801', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '上仙劫', 'path' => '/game/play/Sea/321', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '哪吒', 'path' => '/game/play/Sea/320', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '皇冠小玛莉', 'path' => '/game/play/Sea/319', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '尼莫的遗产', 'path' => '/game/play/Sea/318', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '呱呱！呱呱！', 'path' => '/game/play/Sea/317', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '量子矩阵', 'path' => '/game/play/Sea/316', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '幸运钟声', 'path' => '/game/play/Sea/314', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '忍者联萌', 'path' => '/game/play/Sea/312', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '大唐水果传', 'path' => '/game/play/Sea/311', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '经典 7', 'path' => '/game/play/Sea/310', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
                ['name' => '五色丹炉', 'path' => '/game/play/Sea/308', 'sort' => 0, 'logo_pc' => '', 'logo_h5' => '', 'isnew' => 0, 'ishot' => 0],
            ],
        ];

        return $menu_data;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('front_menu');
        }
    }
}
