<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDrawsource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drawsource', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('lottery_id')->comment('彩种 ID');
            $table->string('name', 30)->default('')->comment('号源名称');
            $table->string('ident', 32)->default('')->comment('号源标识');
            $table->string('url', 200)->default('')->comment('号源api地址');
            $table->boolean('status')->default(true)->comment('状态 1：启用 0:禁用');
            $table->smallInteger('rank')->default(100)->comment('权重 0 - 100');

            $table->index(['lottery_id', 'status']);
            $table->index(['name']);
        });

        $this->data();
    }

    private function data()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon' => 'fa-flag',
            'rule' => 'draw',
            'name' => '开奖管理',
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'drawsource/index',
                'name' => '号源列表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'drawsource/create',
                'name' => '添加号源',
            ],
            [
                'parent_id' => $id,
                'rule' => 'drawsource/edit',
                'name' => '修改号源',
            ],
            [
                'parent_id' => $id,
                'rule' => 'drawsource/delete',
                'name' => '删除号源',
            ],
            [
                'parent_id' => $id,
                'rule' => 'draw/index',
                'name' => '人工开号',
            ],
            [
                'parent_id' => $id,
                'rule' => 'draw/entercode',
                'name' => '录入号码',
            ],
            [
                'parent_id' => $id,
                'rule' => 'cancelbonus/index',
                'name' => '系统撤单',
            ],
            [
                'parent_id' => $id,
                'rule' => 'errorissue/index',
                'name' => '开奖异常',
            ],
            [
                'parent_id' => $id,
                'rule' => 'errorissue/reset',
                'name' => '重置异常状态',
            ]
        ]);

        $lotteries = DB::table('lottery')->get();
        $lottery_ident2id = [];
        foreach ($lotteries as $lottery) {
            $lottery_ident2id[$lottery->ident] = $lottery->id;
        }


        $this->_localApiData($lottery_ident2id); //自主彩种奖源
        $this->_apolloData($lottery_ident2id); //Apollo 奖源
        $this->_kaijiangData($lottery_ident2id); //聚合奖源
        $this->_winData($lottery_ident2id); //GG-WIN 奖源
        $this->_apiplusData($lottery_ident2id); //开彩网（apiplus）奖源
        $this->_kai168Data($lottery_ident2id); //168开奖网 奖源
        //$this->_fenghuangData($lottery_ident2id); //凤凰奖源
        $this->_qiquData($lottery_ident2id); //奇趣奖源
        $this->_qniupinData($lottery_ident2id); //奇趣奖源[备用线路]
        $this->_qiqucomData($lottery_ident2id); //奇趣com奖源
        $this->_playnowData($lottery_ident2id); //加拿大快乐8、加拿大PC28专用
        $this->_heneiData($lottery_ident2id); //河内分分彩、河内五分彩
        $this->_yuyun360Data($lottery_ident2id); //御云统计
        $this->_b1apiData($lottery_ident2id); //博易API（河内分分彩LJ、河内五分彩LJ）
        $this->_idl01Data($lottery_ident2id); //国际腾讯分分彩
        $this->_caipiaokongData($lottery_ident2id); //彩票控
        $this->_tj360Data($lottery_ident2id); //360安全统计
        $this->_tj360backupData($lottery_ident2id); //tj360backup
        $this->_manyCai($lottery_ident2id); //多彩奖源
        $this->_luckyairship($lottery_ident2id); //Luckyairship.com
        $this->_lucky188($lottery_ident2id);
        $this->_cssc591($lottery_ident2id);
        $this->_Singapore($lottery_ident2id);
        $this->_Singapore2($lottery_ident2id);
    }

    /** 添加到数据库
     * @param array $lottery_ident2id $lottery_ident2id 彩种ident 对应 彩种id
     * @param array $lottery_idents 彩种ident
     * @param string $drawsource_name 奖源名称
     * @param string $drawsource_ident_pre 奖源ident前缀，不包含斜杠\
     * @param string $url 奖源网址
     * @param string $status 奖源状态
     * @param array $lottery_idents_other_status  不同状态的彩种ident
     * @param int $rank 奖源预设分
     */
    private function _insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status = true, $lottery_idents_other_status = [], $rank = 100)
    {
        if (empty($lottery_idents) && empty($lottery_idents_other_status)) {
            return '';
        }
        $drawsource_ident_pre2set = [
            'LocalApi'=>'LocalApi',
            'Apollo'=>'Apollo\\Common',
        ];
        $data = [];
        foreach ($lottery_idents as $lottery_ident) {
            if (isset($lottery_ident2id[$lottery_ident])) {
                $lottery_id = $lottery_ident2id[$lottery_ident];
            } else {
                continue ;
            }
            $lottery_ident_ucfirst = ucfirst($lottery_ident);
            $data[] = [
                'lottery_id' => $lottery_id,
                'name' => $drawsource_name,
                'ident' => isset($drawsource_ident_pre2set[$drawsource_ident_pre]) ? $drawsource_ident_pre2set[$drawsource_ident_pre] : $drawsource_ident_pre.'\\'.$lottery_ident_ucfirst,
                'url' => $url,
                'status' => ($status === true || $status === 't') ? 't':'f',
                'rank' => $rank,
            ];
        }
        foreach ($lottery_idents_other_status as $lottery_ident) {
            if (isset($lottery_ident2id[$lottery_ident])) {
                $lottery_id = $lottery_ident2id[$lottery_ident];
            } else {
                continue ;
            }
            $lottery_ident_ucfirst = ucfirst($lottery_ident);
            $data[] = [
                'lottery_id' => $lottery_id,
                'name' => $drawsource_name,
                'ident' => isset($drawsource_ident_pre2set[$drawsource_ident_pre]) ? $drawsource_ident_pre2set[$drawsource_ident_pre] : $drawsource_ident_pre.'\\'.$lottery_ident_ucfirst,
                'url' => $url,
                'status' => ($status === true || $status === 't') ? 'f':'t',
                'rank' => $rank,
            ];
        }
        if ($data) {
            DB::table('drawsource')->insert($data);
        }
    }

    private function _localApiData($lottery_ident2id)
    {
        $lottery_idents = [
            '5fssc', //香港时时彩
            '3fssc', //澳门时时彩
            '2fssc', //台湾时时彩
            '1fssc', //重庆分分彩
            'jsmmc', //极速秒秒彩
            '5f11x5', //香港11选5
            '3f11x5', //澳门11选5
            '2f11x5', //台湾11选5
            '1f11x5', //曼谷11选5
            'jsmm11x5', //秒秒11选5
            '5f3d', //香港3D
            'jsmm3d', //秒秒3D
            '5fpk10', //海南赛车PK拾
            'xyffc', //幸运分分彩
            'xy3fc', //幸运三分彩
            'xyssc', //幸运时时彩
            'xyffpk10', //幸运分分PK10
            'xy3fpk10', //幸运三分PK10
            'xyscpk10', //幸运赛车PK10
            'jssmpk10', //极速赛马PK10
            'jsmmk3', //秒秒快三
            'zffk3', //分分快三
        ];

        $drawsource_name = 'LocalApi';
        $drawsource_ident_pre = 'LocalApi';
        $url = 'http://www.localapi.cn';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _apolloData(array $lottery_ident2id)
    {
        $lottery_idents = [
            'cqssc', //重庆欢乐生肖
            'xjssc', //新疆时时彩
            'tjssc', //天津时时彩
            'txffc', //腾讯分分彩
            'tx5fssc', //腾讯五分彩
            'bjssc', //北京时时彩
            'xglhc', //香港六合彩
            'gd11x5', //广东11选5
            'sd11x5', //山东11选5
            'jx11x5', //江西11选5
            'ah11x5', //安徽11选5
            'bjpk10', //北京PK10
            'jsk3', //江苏快三
            'hbk3', //湖北快三
            'ahk3', //安徽快三
            'fucai3d', //福彩3D
            'pl3pl5', //排列三五
            'bjkl8', //北京快乐8
            'shssl3d', //上海时时乐
            'hnkls', //湖南快乐10分
            'tjkls', //天津快乐10分
            'gdkls', //广东快乐10分
            'cqkls', //重庆快乐10分
            'pcdd', //PC蛋蛋
            'sh11x5', //上海11选5
            'zj11x5', //浙江11选5
            'jsscpk10', //极速赛车
            'btcffc', //比特币分分彩
            'jlffc', //吉利分分彩
            'fhjlssc', //凤凰吉利时时彩
            'xyftpk10', //皇家幸运飞艇
            'xypk10', //凤凰幸运飞艇
            'qiqutxffssc', //奇趣腾讯分分彩
            'qiqutx3fssc', //奇趣腾讯三分彩
            'qiqutx5fssc', //奇趣腾讯五分彩
            'qiqutx10fssc', //奇趣腾讯十分彩
            'qiqutxffpk10', //奇趣腾讯分分PK10
            'qiqutx5fpk10', //奇趣腾讯五分PK10
            'hlj11x5', //黑龙江11选5
            'js11x5', //江苏11选5
            'jndkl8', //加拿大快乐8
            'jndpcdd', //加拿大PC28
            'hne1fssc', //河内分分彩
            'hne5fssc', //河内5分彩
            'gjtxffssc', //国际腾讯分分彩
            'gjtx5fssc', //国际腾讯5分彩
            'gjtx10fssc', //国际腾讯10分彩
            'gjtxpk10', //国际腾讯赛车PK10
            'ln11x5', //辽宁11选5
            'nmg11x5', //内蒙古11选5
            'sx11x5', //山西11选5
            'cxg360ffc', //360分分彩
            'cxg3605fc', //360五分彩
            'jsxyftpk10', //官方幸运飞艇【联合】
            'hnk3', //河南快三
            'jlk3', //吉林快三
            'gxk3', //广西快三
            'jsxystpk10', //幸运赛艇
            'hl30s', //新加坡-欢乐30秒
            'fckl8', //福彩快乐8
            'hljssc', //黑龙江时时彩
            'hn11x5', //海南11选5
            'ggffc', //谷歌分分彩
            'ggffc2f', //谷歌二分彩
            'ggffc3f', //谷歌三分彩
            'ggffc5f', //谷歌五分彩
            'ggffc10f', //谷歌十分彩
            'aliyunffc', //阿里云分分彩
            'tx1fc', //腾讯分分彩 WIN
            'jsssc', //极速时时彩
            'azpk10', //澳洲PK10
            'jssm', //极速赛马
            'jsft', //极速飞艇
            'xyft168', //168幸运飞艇
            'xyst168', //168幸运赛艇
            'aliyun5fc', //阿里云五分彩
            'hlffc', //欢乐分分彩
            'azxy10', //澳洲幸运10
            'sgpk10', //新加坡SG飛艇
            'cxg360ffcpk10', //360五分飞艇
            'cxg3605fcpk10', //360分分飞艇
            'qiquorgtxffssc', //LF腾讯分分彩
            'xyft168b', //168幸运飞艇B
            'jsft168b', //168极速飞艇B
            'jssc168b', //168极速赛车B
            'azxy5b', //澳洲幸运5B
            'azxy10b', //澳洲幸运10B
            'htffc', //河腾分分彩
            'ht5fc', //河腾五分彩
            'ht10fc', //河腾十分彩
        ];

        $drawsource_name = 'Apollo奖源';
        $drawsource_ident_pre = 'Apollo';
        $url = 'http://www.gg-apollo.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _fenghuangData(array $lottery_ident2id)
    {
        $lottery_idents = [
            'cqssc', //重庆欢乐生肖
            'xjssc', //新疆时时彩
            'tjssc', //天津时时彩
            'txffc', //腾讯分分彩
            'tx5fssc', //腾讯五分彩
            'bjssc', //北京时时彩
            'xglhc', //香港六合彩
            'gd11x5', //广东11选5
            'sd11x5', //山东11选5
            'jx11x5', //江西11选5
            'ah11x5', //安徽11选5
            'bjpk10', //北京PK10
            'jsk3', //江苏快三
            'hbk3', //湖北快三
            'ahk3', //安徽快三
            'fucai3d', //福彩3D
            'pl3pl5', //排列三五
            'bjkl8', //北京快乐8
            'shssl3d', //上海时时乐
            'hnkls', //湖南快乐10分
            'tjkls', //天津快乐10分
            'gdkls', //广东快乐10分
            'cqkls', //重庆快乐10分
            'pcdd', //PC蛋蛋
            'sh11x5', //上海11选5
            'zj11x5', //浙江11选5
            'btcffc', //比特币分分彩
            'jlffc', //吉利分分彩
            'fhjlssc', //凤凰吉利时时彩
            //'xyftpk10', //皇家幸运飞艇
            'js11x5', //江苏11选5
        ];

        $drawsource_name = '凤凰奖源';
        $drawsource_ident_pre = 'Fenghuang';
        $url = 'http://www.fhlm.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _apiplusData(array $lottery_ident2id)
    {
        $lottery_idents = [
            'cqssc', //重庆欢乐生肖
            'xjssc', //新疆时时彩
            'tjssc', //天津时时彩
            'bjssc', //北京时时彩
            'xglhc', //香港六合彩
            'gd11x5', //广东11选5
            'sd11x5', //山东11选5
            'jx11x5', //江西11选5
            'ah11x5', //安徽11选5
            'bjpk10', //北京PK10
            'jsk3', //江苏快三
            'hbk3', //湖北快三
            'ahk3', //安徽快三
            'fucai3d', //福彩3D
            'pl3pl5', //排列三五
            'bjkl8', //北京快乐8
            'shssl3d', //上海时时乐
            'hnkls', //湖南快乐10分
            'gdkls', //广东快乐10分
            'cqkls', //重庆快乐10分
            'pcdd', //PC蛋蛋
            'sh11x5', //上海11选5
            'zj11x5', //浙江11选5
            'hlj11x5', //黑龙江11选5
            'js11x5', //江苏11选5
        ];

        $drawsource_name = '开彩网';
        $drawsource_ident_pre = 'Apiplus';
        $url = 'https://www.opencai.net';
        $status = 'f';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _kaijiangData(array $lottery_ident2id)
    {
        $lottery_idents = [
            'cqssc', //重庆欢乐生肖
            'xjssc', //新疆时时彩
            'tjssc', //天津时时彩
            'txffc', //腾讯分分彩
            'tx5fssc', //腾讯五分彩
            'bjssc', //北京时时彩
            'xglhc', //香港六合彩
            'gd11x5', //广东11选5
            'sd11x5', //山东11选5
            'jx11x5', //江西11选5
            'ah11x5', //安徽11选5
            'bjpk10', //北京PK10
            'jsk3', //江苏快三
            'hbk3', //湖北快三
            'ahk3', //安徽快三
            'fucai3d', //福彩3D
            'pl3pl5', //排列三五
            'bjkl8', //北京快乐8
            'shssl3d', //上海时时乐
            'hnkls', //湖南快乐10分
            'gdkls', //广东快乐10分
            'cqkls', //重庆快乐10分
            'pcdd', //PC蛋蛋
            'sh11x5', //上海11选5
            'zj11x5', //浙江11选5
            'btcffc', //比特币分分彩
            'jlffc', //吉利分分彩
            'fhjlssc', //凤凰吉利时时彩
            'xyftpk10', //皇家幸运飞艇
            'qiqutxffssc', //奇趣腾讯分分彩
            'qiqutx5fssc', //奇趣腾讯五分彩
            'qiqutx10fssc', //奇趣腾讯十分彩
            'qiqutxffpk10', //奇趣腾讯分分PK10
            'qiqutx5fpk10', //奇趣腾讯五分PK10
            'hlj11x5', //黑龙江11选5
            'js11x5', //江苏11选5
            'jndkl8', //加拿大快乐8
            'jndpcdd', //加拿大PC28
            'xcqssc', //新重庆时时彩
            'xxjssc', //新新疆时时彩
            'xbjpk10', //新北京PK10
            //'sxyftpk10', //官方幸运飞艇
        ];

        $drawsource_name = '聚合奖源';
        $drawsource_ident_pre = 'Kaijiang';
        $url = 'http://www.kaijiang.net';
        $status = 'f';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _winData(array $lottery_ident2id)
    {
        $lottery_idents = [
            'cqssc', //重庆欢乐生肖
            'xjssc', //新疆时时彩
            'tjssc', //天津时时彩
            'txffc', //腾讯分分彩
            'tx5fssc', //腾讯五分彩
            'bjssc', //北京时时彩
            'xglhc', //香港六合彩
            'gd11x5', //广东11选5
            'sd11x5', //山东11选5
            'jx11x5', //江西11选5
            'ah11x5', //安徽11选5
            'bjpk10', //北京PK10
            'jsk3', //江苏快三
            'hbk3', //湖北快三
            'ahk3', //安徽快三
            'fucai3d', //福彩3D
            'pl3pl5', //排列三五
            'bjkl8', //北京快乐8
            'shssl3d', //上海时时乐
            'hnkls', //湖南快乐10分
            'gdkls', //广东快乐10分
            'pcdd', //PC蛋蛋
            'sh11x5', //上海11选5
            'zj11x5', //浙江11选5
            'btcffc', //比特币分分彩
            'jlffc', //吉利分分彩
            'fhjlssc', //凤凰吉利时时彩
            'xyftpk10', //皇家幸运飞艇
            'qiqutxffssc', //奇趣腾讯分分彩
            'qiqutx3fssc', //奇趣腾讯三分彩
            'qiqutx5fssc', //奇趣腾讯五分彩
            'qiqutx10fssc', //奇趣腾讯十分彩
            'qiqutxffpk10', //奇趣腾讯分分PK10
            'qiqutx5fpk10', //奇趣腾讯五分PK10
            'hlj11x5', //黑龙江11选5
            'js11x5', //江苏11选5
        ];

        $drawsource_name = 'WIN奖源';
        $drawsource_ident_pre = 'Win';
        $url = 'http://www.gg-win.com';
        $status = 'f';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }


    private function _kai168Data(array $lottery_ident2id)
    {
        $lottery_idents = [
            'cqssc', //重庆欢乐生肖
            'xjssc', //新疆时时彩
            'tjssc', //天津时时彩
            'bjssc', //北京时时彩
            'xglhc', //香港六合彩
            'gd11x5', //广东11选5
            'sd11x5', //山东11选5
            'jx11x5', //江西11选5
            'ah11x5', //安徽11选5
            'bjpk10', //北京PK10
            'jsk3', //江苏快三
            'hbk3', //湖北快三
            'ahk3', //安徽快三
            'fucai3d', //福彩3D
            'pl3pl5', //排列三五
            'bjkl8', //北京快乐8
            'tjkls', //天津快乐10分
            'gdkls', //广东快乐10分
            'cqkls', //重庆快乐10分
            'pcdd', //PC蛋蛋
            'sh11x5', //上海11选5
            'zj11x5', //浙江11选5
            'jsscpk10', //极速赛车
            'xyftpk10', //皇家幸运飞艇
            'ln11x5', //辽宁11选5
            'nmg11x5', //内蒙古11选5
        ];

        $drawsource_name = '168开奖网';
        $drawsource_ident_pre = 'Kai168';
        $url = 'http://168kai.com';
        $status = 'f';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _qiquData(array $lottery_ident2id)
    {
        $lottery_idents = [
            'qiqutxffssc', //奇趣腾讯分分彩
            'qiqutx3fssc', //奇趣腾讯三分彩
            'qiqutx5fssc', //奇趣腾讯五分彩
            'qiqutx10fssc', //奇趣腾讯十分彩
            'qiqutxffpk10', //奇趣腾讯分分PK10
            'qiqutx5fpk10', //奇趣腾讯五分PK10
        ];

        $drawsource_name = '奇趣官网';
        $drawsource_ident_pre = 'Qiqu';
        $url = 'http://77tj.org';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    /**
     * 奇趣官网[备用线路]
     * @param array $lottery_ident2id
     */
    private function _qniupinData(array $lottery_ident2id)
    {
        $lottery_idents = [
            'qiqutxffssc', //奇趣腾讯分分彩
            'qiqutx5fssc', //奇趣腾讯五分彩
            'qiqutx10fssc', //奇趣腾讯十分彩
            'qiqutxffpk10', //奇趣腾讯分分PK10
            'qiqutx5fpk10', //奇趣腾讯五分PK10
        ];

        $drawsource_name = '奇趣官网[备用线路]';
        $drawsource_ident_pre = 'Qniupin';
        $url = 'http://qniupin.com';
        $status = 'f';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _qiqucomData($lottery_ident2id)
    {
        return ;

        $lottery_idents = [
        ];

        $drawsource_name = '奇趣com官网';
        $drawsource_ident_pre = 'Qiqucom';
        $url = 'http://77tj.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    /**
     *  Playnow官网
     * @param $lottery_ident2id
     */
    private function _playnowData($lottery_ident2id)
    {
        $lottery_idents = [
            'jndkl8', //加拿大快乐8
            'jndpcdd', //加拿大PC28
        ];

        $drawsource_name = 'Playnow官网';
        $drawsource_ident_pre = 'Playnow';
        $url = 'https://www.playnow.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    /**
     * 河内彩票
     * @param $lottery_ident2id
     */
    private function _heneiData($lottery_ident2id)
    {
        $lottery_idents = [
            'hne1fssc', //河内分分彩
            'hne5fssc', //河内5分彩
        ];

        $drawsource_name = '河内官网';
        $drawsource_ident_pre = 'Henei';
        $url = 'http://viet-lotto.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    /**
     * 御云统计
     * @param $lottery_ident2id
     */
    private function _yuyun360Data($lottery_ident2id)
    {
        return ;
        $lottery_idents = [

        ];

        $drawsource_name = '御云统计';
        $drawsource_ident_pre = 'Yuyun360';
        $url = 'https://www.yuyun360.com';
        $status = 'f';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    /**
     * 博易API
     * @param $lottery_ident2id
     */
    protected function _b1apiData($lottery_ident2id)
    {
        $lottery_idents = [
            'hn1fssc', //河内分分彩
            'hn5fssc', //河内云五分彩
        ];

        $drawsource_name = '博易API';
        $drawsource_ident_pre = 'B1api';
        $url = 'https://www.b1cp.com';
        $status = 'f';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    /**
     * 国际腾讯分分彩
     * @param $lottery_ident2id
     */
    private function _idl01Data($lottery_ident2id)
    {
        $lottery_idents = [
            'gjtxffssc', //国际腾讯分分彩
            'gjtx5fssc', //国际腾讯5分彩
            'gjtx10fssc', //国际腾讯10分彩
            'gjtxpk10', //国际腾讯赛车PK10
        ];

        $drawsource_name = '国际腾讯分分彩';
        $drawsource_ident_pre = 'Idl01';
        $url = 'http://www.qq.international';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    /**
     * 彩票控 奖源
     * @param $lottery_ident2id
     */
    private function _caipiaokongData($lottery_ident2id)
    {
        $lottery_idents = [
            'ln11x5', //辽宁11选5
            'nmg11x5', //内蒙古11选5
            'sx11x5', //山西11选5
        ];

        $drawsource_name = '彩票控';
        $drawsource_ident_pre = 'Caipiaokong';
        $url = 'https://www.caipiaokong.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _tj360Data($lottery_ident2id)
    {
        $lottery_idents = [
            'cxg360ffc', //360分分彩
            'cxg3605fc', //360五分彩
            'cxg360ffcpk10', //360五分飞艇
            'cxg3605fcpk10', //360分分飞艇
        ];

        $drawsource_name = 'tj360奖源';
        $drawsource_ident_pre = 'Tj360';
        $url = 'http://tj360.org/';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }


    private function _tj360backupData($lottery_ident2id)
    {
        $lottery_idents = [
            'cxg360ffc', //360分分彩
            'cxg3605fc', //360五分彩
            'cxg360ffcpk10', //360五分飞艇
            'cxg3605fcpk10', //360分分飞艇
        ];

        $drawsource_name = 'tj360奖源备用';
        $drawsource_ident_pre = 'Tj360backup';
        $url = 'http://5f6efc48fb12e1185559.304dg.cn/';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _manyCai($lottery_ident2id)
    {
        $lottery_idents = [
            'hnk3', //河南快三
            'jlk3', //吉林快三
            'gxk3', //广西快三
        ];

        $drawsource_name = '多彩奖源';
        $drawsource_ident_pre = 'Manycai';
        $url = 'http://www.manycai.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _luckyairship($lottery_ident2id)
    {
        $lottery_idents = [
            'yftpk10', //皇家幸运飞艇
        ];

        $drawsource_name = 'Luckyairship';
        $drawsource_ident_pre = 'Luckyairship';
        $url = 'http://www.luckyairship.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _lucky188($lottery_ident2id)
    {
        $lottery_idents = [
            'jsxyftpk10', //官方幸运飞艇【联合】
            'jsxystpk10', // 幸运赛艇
        ];

        $drawsource_name = 'Lucky188';
        $drawsource_ident_pre = 'Lucky188';
        $url = 'https://www.lucky-188.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _cssc591($lottery_ident2id)
    {
        $lottery_idents = [
            'jsxystpk10', // 幸运赛艇
        ];

        $drawsource_name = 'Cssc591';
        $drawsource_ident_pre = 'Cssc591';
        $url = 'http://api01.cssc591.com';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }

    private function _Singapore($lottery_ident2id)
    {
        $lottery_idents = [
            'hl30s', // 新加坡-欢乐30秒
            'hlffc', // 新加坡-欢乐分分彩
        ];

        $drawsource_name = '新加坡奖源';
        $drawsource_ident_pre = 'Singapore';
        $url = 'http://infonetwork.cn';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }


    private function _Singapore2($lottery_ident2id)
    {
        $lottery_idents = [
            'hl30s', // 新加坡-欢乐30秒
            'hlffc', // 新加坡-欢乐分分彩
        ];

        $drawsource_name = '新加坡奖源_备用';
        $drawsource_ident_pre = 'Singapore2';
        $url = 'http://brssys.com/';
        $status = 't';
        $this->_insert($lottery_ident2id, $lottery_idents, $drawsource_name, $drawsource_ident_pre, $url, $status);
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('drawsource');
        }
    }
}
