<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActivity extends Migration
{
    /**
     * 活动
     */
    public function up()
    {
        Schema::create('activity', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('ident', 64)->unique()->comment('唯一英文标识');
            $table->string('name', 64)->unique()->comment('活动名称');
            $table->smallInteger('sort')->default(1)->comment('活动列表前台显示排序');
            $table->jsonb('config')->default('{}')->comment("活动参数配置");
            $table->jsonb('config_ui')->default('{}')->comment("活动界面参数配置");
            $table->jsonb('deny_user_group')->default('[]')->comment("禁止参与用户组");
            $table->jsonb('allow_user_top_id')->default('[]')->comment('允许参与的总代线ID，缺省为全部允许');
            $table->jsonb('allow_user_level_id')->default('[]')->comment('允许参与的用户分层层级，缺省为全部允许');
            $table->string('summary', 200)->default('')->comment('活动简介');
            $table->text('description')->default('')->comment('活动详情');
            $table->timestamp('start_time')->nullable()->comment('活动开始时间');
            $table->timestamp('end_time')->nullable()->comment('活动结束时间');
            $table->smallInteger('draw_method')->default(0)->comment('发放方式：0. 用户领取; 1. 管理员发放; 2. 系统自动发放; 3. 充值触发; 4. 提现触发');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态:0禁用,1启用');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
        });

        $this->_data();
    }

    private function _data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '活动管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon' => 'fa-gift',
                'rule' => 'activity',
                'name' => '活动管理',
            ]);
        } else {
            $id = $row->id;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'activity/index',
                'name' => '活动列表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/create',
                'name' => '创建活动',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/edit',
                'name' => '编辑活动',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/status',
                'name' => '启用或禁用活动',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/record',
                'name' => '领取记录列表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/reglink',
                'name' => '邀请码注册统计',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/prizepool',
                'name' => '奖池活动管理',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/prizepoolverify',
                'name' => '奖池活动审核',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/verify',
                'name' => '邀请码活动审核',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/sendaward',
                'name' => '发放礼金',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/jackpot',
                'name' => '幸运大奖池管理',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/withdrawaldelay',
                'name' => '提款补偿金列表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'activity/withdrawaldelayverify',
                'name' => '提款补偿金审核',
            ],
        ]);

        $test_date = function ($time) {
            return date("Y-m-d {$time}");
        };

        DB::table('activity')->insert([
            [
                'ident' => 'sign29days',
                'name' => '29天每日签到活动',
                'config' => '{"hide":"0","config":[[[{"title":"指定彩种","value":""}]],[[{"title":"第1天充值金额","value":"1"},{"title":"第1天投注额","value":"1888"},{"title":"礼金","value":"6"}],[{"title":"第2天充值金额","value":"1"},{"title":"第2天投注额","value":"3666"},{"title":"礼金","value":"12"}],[{"title":"第3天充值金额","value":"1"},{"title":"第3天投注额","value":"5555"},{"title":"礼金","value":"18"}],[{"title":"第4天充值金额","value":"1"},{"title":"第4天投注额","value":"6888"},{"title":"礼金","value":"24"}],[{"title":"第5天充值金额","value":"1"},{"title":"第5天投注额","value":"8333"},{"title":"礼金","value":"30"}],[{"title":"第6天充值金额","value":"1"},{"title":"第6天投注额","value":"9999"},{"title":"礼金","value":"36"}],[{"title":"第7天充值金额","value":"1"},{"title":"第7天投注额","value":"11111"},{"title":"礼金","value":"42"}],[{"title":"第8天充值金额","value":"0"},{"title":"第8天投注额","value":"0"},{"title":"礼金","value":"48"}],[{"title":"第9天充值金额","value":"1"},{"title":"第9天投注额","value":"13579"},{"title":"礼金","value":"54"}],[{"title":"第10天充值金额","value":"1"},{"title":"第10天投注额","value":"14666"},{"title":"礼金","value":"60"}],[{"title":"第11天充值金额","value":"1"},{"title":"第11天投注额","value":"15777"},{"title":"礼金","value":"66"}],[{"title":"第12天充值金额","value":"1"},{"title":"第12天投注额","value":"16666"},{"title":"礼金","value":"72"}],[{"title":"第13天充值金额","value":"1"},{"title":"第13天投注额","value":"17777"},{"title":"礼金","value":"78"}],[{"title":"第14天充值金额","value":"1"},{"title":"第14天投注额","value":"18888"},{"title":"礼金","value":"84"}],[{"title":"第15天充值金额","value":"0"},{"title":"第15天投注额","value":"0"},{"title":"礼金","value":"90"}],[{"title":"第16天充值金额","value":"1"},{"title":"第16天投注额","value":"20500"},{"title":"礼金","value":"96"}],[{"title":"第17天充值金额","value":"1"},{"title":"第17天投注额","value":"21500"},{"title":"礼金","value":"102"}],[{"title":"第18天充值金额","value":"1"},{"title":"第18天投注额","value":"22222"},{"title":"礼金","value":"108"}],[{"title":"第19天充值金额","value":"1"},{"title":"第19天投注额","value":"22888"},{"title":"礼金","value":"114"}],[{"title":"第20天充值金额","value":"1"},{"title":"第20天投注额","value":"23555"},{"title":"礼金","value":"120"}],[{"title":"第21天充值金额","value":"1"},{"title":"第21天投注额","value":"24680"},{"title":"礼金","value":"126"}],[{"title":"第22天充值金额","value":"0"},{"title":"第22天投注额","value":"0"},{"title":"礼金","value":"132"}],[{"title":"第23天充值金额","value":"1"},{"title":"第23天投注额","value":"25555"},{"title":"礼金","value":"138"}],[{"title":"第24天充值金额","value":"1"},{"title":"第24天投注额","value":"26000"},{"title":"礼金","value":"144"}],[{"title":"第25天充值金额","value":"1"},{"title":"第25天投注额","value":"26789"},{"title":"礼金","value":"150"}],[{"title":"第26天充值金额","value":"1"},{"title":"第26天投注额","value":"27333"},{"title":"礼金","value":"156"}],[{"title":"第27天充值金额","value":"1"},{"title":"第27天投注额","value":"27999"},{"title":"礼金","value":"162"}],[{"title":"第28天充值金额","value":"1"},{"title":"第28天投注额","value":"28888"},{"title":"礼金","value":"168"}],[{"title":"第29天充值金额","value":"0"},{"title":"第29天投注额","value":"0"},{"title":"礼金","value":"188"}]]]}',
                'config_ui' => '{}',
                'summary' => '平台的所有用户，29天投注奖金送不停，每日有效投注达到标准后即可领钱。',
                'description' => '<div class="foot-content">
                                <div class="foot-title">活动内容</div>
                                <p class="yellow">1、每日有效投注达到标准后，即可点击领取按钮。</p>
                                <p>2、第八、十五、二十二、二十九天不需流水要求，登录后即可直接点击领取奖金。</p>
                                <p>3、每日有效投注统计时间为<span class="yellow">当日00:00:00~23:59:59。</span></p>
                                <p>4、用户在签到过程中，出现签到日期中断的情况，次日可继续签到，无需返回第一日。</p>
                            </div>
                            <div class="foot-info">
                                <div class="foot-title">活动细则</div>
                                <p class="yellow">1、采计投注额包含所有数字彩票，但竞速娱乐彩票不包含在内。</p>
                                <p>2、有效投注额解释为所有已开奖订单之实际销量加总（撤单不记入投注额）。</p>
                                <p>3、每日统计周期为<span class="yellow">当日00:00:00~23:59:59</span>，结算满足条件后请立即点击领取获取活动奖金。</p>
                                <p class="yellow">4、如发现任何恶意套利行为，平台将扣除所有违规所得，并且有权冻结其帐号。</p>
                                <p>5、同一账户，同一IP，同一电脑，每日只允许领取一次；若有重复申请帐号行为，公司保留/取消/收回会员优惠彩金的权力。</p>
                                <p>6、平台保留活动最终解释权。</p>
                            </div>',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'recharge20pct',
                'name' => '充值送百分之15彩金',
                'config' => '{"hide": "0", "config": [[[{"title": "充值返百分比", "value": "15"}], [{"title": "最低充值", "value": "100"}], [{"title": "最高礼金", "value": "8888"}]]]}',
                'config_ui' => '{}',
                'summary' => '凡在平台有充值消费记录的老用户，绑定银行卡、微信/QQ、手机号码即可领取88元礼包。',
                'description' => '<p>活动内容：</p>
                                <p>所有平台的用户，均可参与充值送彩金活动；</p>
                                <p>活动时间：2018-03-20 02:00:00 到 2019-08-14 15:02:00；</p>
                                <p>凡充值用户均可获赠充值金额的15%（奖金最低30元，最高8888元）备注：充值金额为当日充值总额，比如玩家充值两笔1000，则需要（2000+2000*0.15）* 15；</p>
                                <p>消费(本金+彩金)的15倍流水方可满足领取条件,流水统计时间为当日2:00至次日2:00；</p>
                                <p>同一账户，同一IP，同一电脑，每日只允许领取一次；</p>
                                <p>活动不限彩种，不限玩法，用户投注注数不可大于该玩法的总注数的70%（比如：一星不超过7注，二星不超过70注，三星不超过700注，四星不超过7000注）；</p>
                                <p>本活动只欢迎贵宾会真实玩家，拒绝白菜党，若发现有用户运用此活动进行套利行为，风控部有权将其账号进行冻结（连同本金）；</p>
                                <p>此活动最终解释权归贵宾会运营部活动组所有。</p>
                                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'dailytask',
                'name' => '每日任务',
                'config' => '{"hide": "1", "special_config": {"tasks": [{"url": "/lottery/cqssc", "name": "self_sale", "power": 10, "times": 0, "title": "每日自身实际销量 5000元", "value": 5000}, {"url": "/lottery/cqssc", "name": "team_sale", "power": 20, "times": 0, "title": "每日团队实际销量 10000元", "value": 10000}, {"url": "/lottery/cqssc", "name": "self_bet_times", "power": 20, "times": 30, "title": "每日自身下注5次"}, {"url": "/funds/deposit", "name": "self_recharge_times", "power": 10, "times": 2, "title": "每日自身充值2次"}, {"url": "/funds/deposit", "name": "self_recharge", "power": 20, "times": 0, "title": "每日自身充值金额1000元", "value": 1000}], "rewards": [{"got": 0, "money": 10, "power": 30}, {"got": 0, "money": 15, "power": 60}, {"got": 0, "money": 25, "power": 80}], "total_power": 80}}',
                'config_ui' => '{}',
                'summary' => '每日任务',
                'description' => '<p>活动规则：</p>
                            <p>活动时间：活动推出日起，常态举行；</p>
                            <p>活动对象：所有用户；</p>
                            <p>参与方式：进入“个人账户管理”，绑定QQ号码，然后点击下方——联系在线客服，获取QQ客服联系方式并成功添加即可找客服领取；</p>
                            <p>本活动最终解释权归平台运营部所有。</p>
                                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'dayfirstrecharge',
                'name' => '每日首充活动',
                'config' => '{"hide": "0", "config": [[[{"title": "充值金额", "value": 500}, {"title": "销量", "value": 0}, {"title": "礼金", "value": 18}], [{"title": "充值金额", "value": 1000}, {"title": "销量", "value": 0}, {"title": "礼金", "value": 38}], [{"title": "充值金额", "value": 5000}, {"title": "销量", "value": 0}, {"title": "礼金", "value": 188}], [{"title": "充值金额", "value": 10000}, {"title": "销量", "value": 0}, {"title": "礼金", "value": 288}], [{"title": "充值金额", "value": 50000}, {"title": "销量", "value": 0}, {"title": "礼金", "value": 888}]], [[{"title": "首冲彩票流水倍数", "value": 15}], [{"title": "是否取今天数据", "value": 0}]]]}',
                'config_ui' => '{}',
                'summary' => '每日首充送大奖，充值越多，奖金越高。',
                'description' => '
                    <p>1、所有平台的用户，均可参与；</p>
                    <p>2、发放奖励依据当天首次充值金额做奖金发放标准，非当日累计；</p>
                    <p>3、充值奖励仅限当天领取前一天的奖励。 </p>
                    <p>4、首充门槛：</p>
                    <div style="padding-left:20px;">
                        <p>500~999 奖励18元</p>
                        <p>1000~4999 奖励38元</p>
                        <p>5000 ~ 9999 奖励188元</p>
                        <p>10000 ~ 49999 奖励 388元</p>
                        <p>50000以上 奖励888元</p>
                    </div>
                    <p>例:用户A，2018-12-10 首次充值8700元，于2018-12-11 00:00可领取到奖励 188 元。用户A又于2018-12-10 充值2300元，无法获得其他奖励。即计算用户的每日00:00后的第一笔充值当奖励发放标准。"</p>
                    <p>5、同一账户，同一IP，同一电脑，每日只允许领取一次；</p>
                    <p>6、此活动最终解释权归运营部活动组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'binduserinfo',
                'name' => '新手绑定送红包',
                'config' => '{"hide": "0", "config": [[[{"title": "最低充值", "value": "20"},{"title": "礼金", "value": "8"}]]]}',
                'config_ui' => '{}',
                'summary' => '绑定手机号码、QQ号码、微信、Email即可领取红包奖励。',
                'description' => '
                    <p>1、所有平台的用户，均可参与；</p>
                    <p>2、在个人帐户管理中进行三个绑定，即可领取8元红包</p>
                    <p>4、绑定说明：</p>
                    <div style="padding-left:20px;">
                        <p>绑定一：绑定手机号码，收取验证码后进行绑定</p>
                        <p>绑定二：绑定QQ号码或微信帐号</p>
                        <p>绑定三：绑定Email</p>
                        <p>绑定四：需绑定银行卡</p>
                        <p>绑定五：充值达20元以上</p>
                    </div>
                    <p>5、同一账户，同一IP，同一电脑，只允许领取一次；</p>
                    <p>6、此活动最终解释权归运营部活动组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'reglink',
                'name' => '注册链接邀请码活动',
                'config' => '{"hide": "0", "config": [[[{"title": "用户充值>=", "value": 3000}]],[
                            [
                            {"title": "推荐人数>=", "value": 1},
                            {"title": "奖金", "value": 58}
                            ],
                            [
                            {"title": "推荐人数>=", "value": 5},
                            {"title": "奖金", "value": 388}
                            ],
                            [
                            {"title": "推荐人数>=", "value": 10},
                            {"title": "奖金", "value": 588}
                            ],
                            [
                            {"title": "推荐人数>=", "value": 20},
                            {"title": "奖金", "value": 1888}
                            ],
                            [
                            {"title": "推荐人数>=", "value": 30},
                            {"title": "奖金", "value": 3888}
                            ],
                            [
                            {"title": "推荐人数>=", "value": 50},
                            {"title": "奖金", "value": 8888}
                            ]
                            ]]}',
                'config_ui' => '{}',
                'summary' => '绑定手机号码、邀请码用户充值达到3000元以上才算有效人数。',
                'description' => '
                    <p>1、好友推荐佣金添加后，需达到1倍流水即可提款。</p>
                    <p>2、好友需累计充值3000+，并且绑定手机号码且加入客服ＱＱ号好友即计算＋１有效人数。</p>
                    <p>3、推荐人开户要早于被推荐人，且必须在推荐人下线。</p>
                    <p>4、活动计算区间为每周一00:00 ~每周日 23:59， 符合资格后，平台工作人员会进行审核，礼金将统一于每周一进行发送； 活动有效人数每周清0。</p>
                    <p>5、本活动意在答谢真诚推荐朋友的玩家，如发现任何人／团体／组织以不诚实的方式合伙套取红利或其他利益，账号及所有户内余额将被取消；</p>
                    <p>6、被推荐人与推荐人不能是同一人且不能使用相同登录IP，相同姓名等信息，如有发现将被取消，严重者有权驱逐平台；</p>
                    <p>7、为了避免对文字的理解差异，我们保留对以上方案的解释权。如果出现争执，本娱乐场的所有决定将是最终决定。</p>
                    <p>如果您填写的朋友信息不实，或是您邀请的会员已经在网站成功注册，或者您提供的信息中真实姓名与电话号码持有人姓名不符合之类似情况，您的邀请将无效，并且您与您邀请的朋友不能出现同IP、同电脑、同姓名的情况，您邀请的朋友必须在官网下注册，如在其他代理线下注册，邀请效果也将作废。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'yuandannewcharge',
                'name' => '开户优惠大放送，最高可领取8888元',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '开户优惠大放送，最高可领取8888元',
                'description' => '
                    <table border="0" cellspacing="0" style="width:100%">
                        <tbody>
                            <tr>
                                <th>活动对象</th>
                                <th>存款金额</th>
                                <th>彩金</th>
                                <th>彩金要求</th>
                                <th>申请方式</th>
                            </tr>
                            <tr>
                                <td rowspan="10" style="vertical-align:middle">即日起,新注册会员（超过24小时为老会员）<strong>仅限彩票类游戏</strong></td>
                                <td>100+</td>
                                <td>16</td>
                                <td rowspan="10" style="vertical-align:middle">所得彩金+本金两倍流水即可申请提款</td>
                                <td rowspan="10" style="vertical-align:middle">注册后首次充值,未进行投注前, 咨询客服申请有效</td>
                            </tr>
                            <tr>
                                <td>500+</td>
                                <td>36</td>
                            </tr>
                            <tr>
                                <td>1000+</td>
                                <td>88</td>
                            </tr>
                            <tr>
                                <td>2000+</td>
                                <td>168</td>
                            </tr>
                            <tr>
                                <td>5000+</td>
                                <td>288</td>
                            </tr>
                            <tr>
                                <td>10000+</td>
                                <td>588</td>
                            </tr>
                            <tr>
                                <td>50000+</td>
                                <td>1288</td>
                            </tr>
                            <tr>
                                <td>100000+</td>
                                <td>2088</td>
                            </tr>
                            <tr>
                                <td>200000+</td>
                                <td>3888</td>
                            </tr>
                            <tr>
                                <td>500000+</td>
                                <td>8888</td>
                            </tr>
                        </tbody>
                    </table>
                    <p>&nbsp;</p>
                    <p>如：玩家注册成功，充值2000元，未进行过任何投注前，加客服好友申请首次充值送168元，客服核实后即可为您添加（因核实需要时间，以及咨询玩家较多，若导致处理时间缓慢，敬请谅解）</p>
                    <p>&nbsp;</p>
                    <p>规则：</p>
                    <p>1.所得彩金+本金两倍流水即可申请提现，此活动与其他充值赠送活动不可同时参与，若同时参则取消其中一种活动资格；仅限于投注彩票类游戏；</p>
                    <p>2.每位会员（同姓名,同IP,手机号码等）仅限申请一次，请在成功注册后24小时内进行申请，逾期视为无效。如出现同姓名,IP,手机号码等,则视为同一个人，公司有权拒绝派送彩金；</p>
                    <p>3.投注注数不可超过玩法总注数的70%（组选玩法通过换算为直选计算），如出现对打,套利等违反常态游戏规则的行为（例：同时押大小,单双等），公司有权收回彩金及彩金产生的盈利；</p>
                    <p>4.只要您首次存款后，未进行投注，联系我们客服即可申请，如果已经进行投注就无法参与我们的优惠活动；</p>
                    <p>5.AG贵宾会的所有优惠特为玩家而设，如发现任何团队或个人，以不正当方式套取红利或任何威胁，滥用公司优惠等行为，公司有保留冻结、取消该团队或个人账户以及账户余额的权利。若会员对活动有争议时、为确保双方利益，杜绝身份盗用行为，贵宾会有权要求会员向我们提供充足有效的文件，确认是否享有优惠的资格。</p>
                    <p>6.贵宾会保留对活动的最终解释权，以及在无通知的情况下修改，终止活动的权利。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'yuandanag',
                'name' => '元旦真人优惠',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '元旦真人优惠',
                'description' => '
                    <table width="426">
                    <tbody>
                    <tr>
                    <td width="107">
                    <p><strong>活动</strong></p>
                    </td>
                    <td width="107">
                    <p><strong>比例</strong></p>
                    </td>
                    <td width="107">
                    <p><strong>流水</strong></p>
                    </td>
                    <td width="107">
                    <p><strong>红利上限</strong></p>
                    </td>
                    </tr>
                    <tr>
                    <td width="107">
                    <p><strong>真人首存</strong></p>
                    </td>
                    <td width="107">
                    <p><strong>30%</strong></p>
                    </td>
                    <td width="107">
                    <p><strong>15</strong><strong>倍</strong></p>
                    </td>
                    <td width="107">
                    <p><strong>888</strong><strong>元</strong></p>
                    </td>
                    </tr>
                    <tr>
                    <td width="107">
                    <p><strong>真人再存</strong></p>
                    </td>
                    <td width="107">
                    <p><strong>20%</strong></p>
                    </td>
                    <td width="107">
                    <p><strong>35</strong><strong>倍</strong></p>
                    </td>
                    <td width="107">
                    <p><strong>1888</strong><strong>元</strong></p>
                    </td>
                    </tr>
                    </tbody>
                    </table>
                    <p><strong>活动规则：</strong></p>
                    <p><strong>1.</strong><strong>活动期间首次存款真人的会员，无论新老会员，只要绑定邮箱、</strong><strong>QQ</strong><strong>、微信、手机任意两种或以上均可申请此优惠，首存优惠只可以申请一次；</strong></p>
                    <p><strong>2.</strong><strong>通过在线客服或者</strong><strong>QQ</strong><strong>客服申请参与真人优惠活动，因核实需要时间，且参与人数众多，如有延迟敬请谅解；优惠礼金到账前，会员本金不可以进行投注，否则将视为自动放弃申请此优惠</strong></p>
                    <p><strong>3.</strong><strong>完成此优惠所需要的有效投注，仅计算真人平台（</strong><strong>AG</strong><strong>、</strong><strong>BBIN</strong><strong>）中的百家乐、龙虎和骰宝游戏。如未按活动要求投注其他游戏平台或电子游戏、彩票类游戏，将视为自动放弃此优惠，我们将在提款时扣除红利以及盈利；</strong></p>
                    <p><strong>4.</strong><strong>优惠礼金到账后，会员需要在</strong><strong>7</strong><strong>天内完成此优惠所需要的有效投注，逾期将视为自动放弃此优惠；</strong></p>
                    <p><strong>5.</strong><strong>此优惠本金以及红利所产生的投注，不与官网其他优惠活动共享；</strong></p>
                    <p><strong>6.</strong><strong>每位会员（同姓名</strong><strong>,</strong><strong>同</strong><strong>IP,</strong><strong>手机号码等）仅限申请一次，如出现同姓名</strong><strong>,IP,</strong><strong>手机号码等</strong><strong>,</strong><strong>则视为同一个人，公司有权拒绝派送彩金；</strong></p>
                    <p><strong>7.</strong><strong>【贵宾会】拥有本次活动的最终解释权，并有权在未进行通知对活动进行更改，如有任何疑问随时咨询在线客服</strong><strong>;</strong></p>
                    <p>&nbsp;</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'ebank',
                'name' => '网银单单充！笔笔送1%',
                'config' => '{
                    "min_deposit": 100,
                    "rate_percent": 1,
                    "payment_method_idents": ["netbank"]
                }',
                'config_ui' => '{}',
                'summary' => '网银充值存款每笔赠送彩金1%，次次存，次次送，无上限，无需申请，自动到账，充值成功后可立即获得反馈金。',
                'description' => '
                    <h4>一、活动内容</h4>
                    <p>网银充值存款每笔赠送彩金1%，次次存，次次送，无上限，无需申请，自动到账，充值成功后可立即获得反馈金。</p>
                    <h4>二、活动说明</h4>
                    <table width="100%">
                    <tr>
                    <th style="width:25%;">单笔存款金额</th>
                    <th style="width:25%;">立即赠送</th>
                    <th style="width:25%;">流水倍数</th>
                    <th style="width:25%;">彩金上限</th>
                    </tr>
                    <tr>
                    <td>100元以上</td>
                    <td>1%</td>
                    <td>1倍</td>
                    <td>无上限</td>
                    </tr>
                    </table>
                    <p>1、请会员登录账号点击充值-选择在线充值，在线1、在线2、在线三均可。</p>
                    <p>2、若检测到会员有套取彩金行为，平台有权要求该会员消费3-5倍流水进行提款。</p>
                    <p>3、该优惠无需申请，存款后自动到账。</p>
                    <p>4、参与此优惠即视为同意平台优惠规则条款。</p>
                    <h4>三、优惠规则条款。</h4>
                    <p>1、所有平台的用户，均可参与；</p>
                    <p>2、同一账户，同一IP，同一电脑，相同支付卡/信用卡号码限一个账号参与。</p>
                    <p>3、此活动最终解释权归运营部活动组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 3,
                'status' => 0
            ],
            [
                'ident' => 'consumecommision',
                'name' => '消费满即送佣金',
                'config' => '{"hide": "0", "config": [[[{"title": "消费满", "value": "1000"}, {"title": "上级礼金", "value": "5"}, {"title": "上上级礼金", "value": "3"}], [{"title": "消费满", "value": "5000"}, {"title": "上级礼金", "value": "25"}, {"title": "上上级礼金", "value": "5"}], [{"title": "消费满", "value": "10000"}, {"title": "上级礼金", "value": "35"}, {"title": "上上级礼金", "value": "10"}]], [[{"title": "最低消费", "value": "1000"}], [{"title": "是否限制为活动时间内注册的用户", "value": 1}]]]}',
                'config_ui' => '{}',
                'summary' => '用户每日消费满一定金额，上级即可获赠奖励',
                'description' => '
                    <h4>一、活动对象</h4>
                    <p>针对贵宾会所有用户。</p>
                    <h4>二、活动规则</h4>
                    <table width="100%">
                    <tr>
                    <th style="width:25%;">用户每日消费满</th>
                    <th style="width:25%;">上级奖励</th>
                    <th style="width:25%;">上上级奖励</th>
                    </tr>
                    <tr>
                    <td>1000元</td>
                    <td>5元</td>
                    <td>3元</td>
                    </tr>
                    <tr>
                    <td>5000元</td>
                    <td>25元</td>
                    <td>5元</td>
                    </tr>
                    <tr>
                    <td>10000元</td>
                    <td>35元</td>
                    <td>10元</td>
                    </tr>
                    </table>
                    <p>此活动最终解释权归贵宾会活动策划组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'rechargecommision',
                'name' => '代理佣金活动',
                'config' => '{"hide": "0", "config": [[
                            [{"title": "活动期间首充", "value": 500},{"title": "上级礼金", "value": 3}],
                            [{"title": "活动期间首充", "value": 2000},{"title": "上级礼金", "value": 8}],
                            [{"title": "活动期间首充", "value": 5000},{"title": "上级礼金", "value": 18}],
                            [{"title": "活动期间首充", "value": 10000},{"title": "上级礼金", "value": 40}],
                            [{"title": "活动期间首充", "value": 20000},{"title": "上级礼金", "value": 80}],
                            [{"title": "活动期间首充", "value": 40000},{"title": "上级礼金", "value": 150}],
                            [{"title": "活动期间首充", "value": 80000},{"title": "上级礼金", "value": 300}],
                            [{"title": "活动期间首充", "value": 100000},{"title": "上级礼金", "value": 400}]
                            ]]}',
                'config_ui' => '{}',
                'summary' => '代理佣金活动',
                'description' => '
                    <h4>一、活动对象</h4>
                    <p>针对全部代理。</p>
                    <h4>二、活动规则</h4>
                    <table width="100%">
                    <tr>
                    <th style="width:50%;">活动期间首充</th>
                    <th style="width:50%;">奖励上级</th>
                    </tr>
                    <tr>
                    <td>500元</td>
                    <td>3元</td>
                    </tr>
                    <tr>
                    <td>2000元</td>
                    <td>8元</td>
                    </tr>
                    <tr>
                    <td>5000元</td>
                    <td>18元</td>
                    </tr>
                    <tr>
                    <td>10000元</td>
                    <td>40元</td>
                    </tr>
                    <tr>
                    <td>20000元</td>
                    <td>80元</td>
                    </tr>
                    <tr>
                    <td>40000元</td>
                    <td>150元</td>
                    </tr>
                    <tr>
                    <td>80000元</td>
                    <td>300元</td>
                    </tr>
                    <tr>
                    <td>100000元</td>
                    <td>400元</td>
                    </tr>
                    </table>
                    <p>此活动最终解释权归运营部活动组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'service',
                'name' => '专属客服MM来了',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '尊敬的AG贵宾会会员，如果您长时间联系不到上级，请及时添加我们的新微信，祝您游戏愉快多多盈利！',
                'description' => '
                    <p>尊敬的AG贵宾会会员，如果您长时间联系不到上级，请及时添加我们的新微信，祝您游戏愉快多多盈利！</p>
                    <p>微信图片链接地址：</p>
                    <p><img src="http://thyrsi.com/t6/670/1550110824x2890202420.jpg" /></p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'bankfirstrecharge',
                'name' => '网银首充送好礼',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '现在使用银行账号进行网银转账、ATM转账、ATM现金转账、手机银行支付、支付宝转银行卡等方式存款，即可获得公司赠送1.2%存款优惠！',
                'description' => '
                    <h4>活动详情：</h4>
                    <p>现在使用银行账号进行网银转账、ATM转账、ATM现金转账、手机银行支付、支付宝转银行卡等方式存款，即可获得公司赠送1.2%存款优惠！</p>
                    <table width="100%">
                    <tr>
                    <th style="width:20%;">存款方式</th>
                    <th style="width:20%;">存款金额</th>
                    <th style="width:20%;">立即回馈</th>
                    <th style="width:20%;">最高上限</th>
                    <th style="width:20%;">流水限制</th>
                    </tr>
                    <tr>
                    <td>公司入款</td>
                    <td>100元+</td>
                    <td>1.2%</td>
                    <td>无上限</td>
                    <td>1倍</td>
                    </tr>
                    </table>
                    <p>申请方式：无需申请，自动派发。</p>
                    <h4>规则与条款：</h4>
                    <p>1.优惠以人民币为结算货币，以北京时间为计算区间。</p>
                    <p>2.每一位玩家，每一个住址，每一个电子邮箱地址，每一个电话号码，相同ip以及同一银行卡号视为同一个人，只享有一次优惠；若会员有重复注册账号的行为，公司保留或收回会员优惠的权利。</p>
                    <p>3.若会员对活动有争议，为确保双方利益，杜绝身份滥用行为，双博娱乐城有权要求会员提供详细资料，用以确认是否享有该优惠的资质。</p>
                    <p>4.为避免文字理解的差异，双博娱乐城享有最终解释权，以及在无通知情况下更改、停止、取消优惠或者回收优惠的权利。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'startbusiness',
                'name' => '开市大礼包 投注就送168',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '此活动仅针对腾讯时时彩或者谷歌分分彩彩种的消费统计，当日消费满5000+金额以上，可领取开市红包168元。',
                'description' => '
                    <h4>活动详情：</h4>
                    <p>1.此活动仅针对腾讯时时彩或者谷歌分分彩彩种的消费统计，当日消费满5000+金额以上，可领取开市红包168元。</p>
                    <p>2.每日每个账号每条IP每张同姓名银行卡仅可领取一次，红包封顶168元。</p>
                    <p>3.若发现一人多账户或分打全码、 对刷，刷奖励一律冻结账号，余额无效清空处理！</p>
                    <p>4.如有疑问请联系在线客服，平台保留对本活动的最终解释权和解决权。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'bindgooglekey',
                'name' => '新贵红包2元送 绑定谷歌验证立即领',
                'config' => '{"hide": "0", "config": [[[{"title": "红包金额", "value": "2"}]]]}',
                'config_ui' => '{}',
                'summary' => '活动期间进行谷歌验证绑定(个人中心 > 个人账户管理)，即可至活动页面领取红包。',
                'description' => '
                    <p>活动对象：全平台所有用户</p>
                    <p>领奖方式：活动期间进行谷歌验证绑定(个人中心 > 个人账户管理)，即可至活动页面领取红包。</p>
                    <p>本平台保留对活动的最终解释权。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'dayfirstrecharge2',
                'name' => '首充送18%奖金 最高8888等你拿!',
                'config' => '{"hide": "0", "config": [[[{"title": "充值金额>=", "value": "556"},{"title": "奖金比例(%)", "value": "18"},{"title": "流水倍数", "value": "15"}]]]}',
                'config_ui' => '{}',
                'summary' => '所有平台用户，均可参与首充送彩金活动，每日限领一次，活动奖金为当日首充金额的18%（奖金最低100，最高8888）',
                'description' => '
                    <p>1.所有平台用户，均可参与首充送彩金活动，每日限领一次</p>
                    <p>2.充值金额达556元即可参加活动，满足首充金额与奖金共计的15倍流水后方可于活动页领取奖金</p>
                    <p>3.活动奖金为当日首充金额的18%（奖金最低100，最高8888）</p>
                    <p>4.流水计算时间为首充后开始计算至00:00 AM</p>
                    <p>5.活动不限彩种与玩法，用户投注注数不可大于或等于该玩法的总注数的70%（比如：一星不超过7注，二星不超过70注，三星不超过700注，四星不超过7000注）；</p>
                    <p>6.同一账户，同一IP，同一电脑，每日只允许领取一次；</p>
                    <p>7.本活动只欢迎真实玩家，拒绝白菜党，若发现有用户运用此活动进行套利行为，风控部有权将其账号进行冻结（连同本金）；</p>
                    <p>8.此活动最终解释权归本平台运营部活动组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'givecommission',
                'name' => '佣金大方送',
                'config' => '{"hide": "0", "config": [[[{"title": "最低消费", "value": 1000}]],[
                            [
                            {"title": "消费满", "value": 1000},
                            {"title": "上级礼金", "value": 5},
                            {"title": "上上级礼金", "value": 3},
                            {"title": "上上上级礼金", "value": 2}
                            ],
                            [
                            {"title": "消费满", "value": 3000},
                            {"title": "上级礼金", "value": 15},
                            {"title": "上上级礼金", "value": 10},
                            {"title": "上上上级礼金", "value": 5}
                            ],
                            [
                            {"title": "消费满", "value": 6000},
                            {"title": "上级礼金", "value": 30},
                            {"title": "上上级礼金", "value": 15},
                            {"title": "上上上级礼金", "value": 10}
                            ],
                            [
                            {"title": "消费满", "value": 10000},
                            {"title": "上级礼金", "value": 40},
                            {"title": "上上级礼金", "value": 20},
                            {"title": "上上上级礼金", "value": 15}
                            ],
                            [
                            {"title": "消费满", "value": 20000},
                            {"title": "上级礼金", "value": 66},
                            {"title": "上上级礼金", "value": 40},
                            {"title": "上上上级礼金", "value": 25}
                            ],
                            [
                            {"title": "消费满", "value": 40000},
                            {"title": "上级礼金", "value": 99},
                            {"title": "上上级礼金", "value": 60},
                            {"title": "上上上级礼金", "value": 38}
                            ],
                            [
                            {"title": "消费满", "value": 100000},
                            {"title": "上级礼金", "value": 165},
                            {"title": "上上级礼金", "value": 100},
                            {"title": "上上上级礼金", "value": 60}
                            ]
                            ]]}',
                'config_ui' => '{}',
                'summary' => '用户每日消费满一定金额 上级可获赠佣金奖励',
                'description' => '
                    <p>活动对象：本平台所有用户</p>
                    <p>活动规则：每日点击领取昨日佣金奖励，流水计算时间为昨日00:00AM至 今日00:00AM</p>
                    <p>此活动最终解释权归本平台运营部活动组所有。</p>
                    <table width="100%">
                    <tr>
                    <th style="width:25%;">单一用户每日消费</th>
                    <th style="width:25%;">上级奖励</th>
                    <th style="width:25%;">上上级奖励</th>
                    <th style="width:25%;">上上上级奖励</th>
                    </tr>
                    <tr>
                    <td>满1000元</td>
                    <td>奖励5元</td>
                    <td>奖励3元</td>
                    <td>奖励2元</td>
                    </tr>
                    <tr>
                    <td>满3000元</td>
                    <td>奖励15元</td>
                    <td>奖励10元</td>
                    <td>奖励5元</td>
                    </tr>
                    <tr>
                    <td>满6000元</td>
                    <td>奖励30元</td>
                    <td>奖励15元</td>
                    <td>奖励10元</td>
                    </tr>
                    <tr>
                    <td>满10000元</td>
                    <td>奖励40元</td>
                    <td>奖励20元</td>
                    <td>奖励15元</td>
                    </tr>
                    <tr>
                    <td>满20000元</td>
                    <td>奖励66元</td>
                    <td>奖励40元</td>
                    <td>奖励25元</td>
                    </tr>
                    <tr>
                    <td>满40000元</td>
                    <td>奖励99元</td>
                    <td>奖励60元</td>
                    <td>奖励38元</td>
                    </tr>
                    <tr>
                    <td>满100000元</td>
                    <td>奖励165元</td>
                    <td>奖励100元</td>
                    <td>奖励60元</td>
                    </tr>
                    </table>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'offlinetransfer',
                'name' => '线下银行卡转帐 狂送1%',
                'config' => '{"hide": "1", "config": [[[{"title": "充值金额>=", "value": "100"},{"title": "奖金比例(%)", "value": "1"},{"title": "支付通道编号(多个需用(,)号隔开)", "value": "10"}]]]}',
                'config_ui' => '{}',
                'summary' => '次次存！次次送！无上限！无需申请！自动到账！充值成功后立即获得反馈金。',
                'description' => '
                    <h4>一、活动内容</h4>
                    <p>线下银行卡转帐充值存款，每笔赠送彩金1%</p>
                    <p>次次存！次次送！无上限！无需申请！自动到账！充值成功后立即获得反馈金。</p>
                    <h4>二、活动说明</h4>
                    <table width="100%">
                    <tr>
                    <th style="width:25%;">单笔存款金额</th>
                    <th style="width:25%;">立即赠送</th>
                    <th style="width:25%;">流水倍数</th>
                    <th style="width:25%;">彩金上限</th>
                    </tr>
                    <tr>
                    <td>100元以上</td>
                    <td>1%</td>
                    <td>1倍</td>
                    <td>无上限</td>
                    </tr>
                    </table>
                    <p>1、请会员联系在线客服，客服QQ索取公司入款账号。</p>
                    <p>2、若检测到会员有套取彩金行为，平台有权要求该会员消费3-5倍流水进行提款。</p>
                    <p>3、该优惠无需申请，存款后自动到账。</p>
                    <p>4、参与此优惠即视为同意平台优惠规则条款。</p>
                    <h4>三、优惠规则条款。</h4>
                    <p>1、所有平台的用户，均可参与；</p>
                    <p>2、同一账户，同一IP，同一电脑，相同支付卡/信用卡号码限一个账号参与。</p>
                    <p>3、此活动最终解释权归运营部活动组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 3,
                'status' => 0
            ],
            [
                'ident' => 'singlerecharge',
                'name' => '神秘彩金 亿万红包',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '每月可享受两次申请机会，1号到15号期间可以申请第一次，16号到月底可以申请第二次，一倍流水即可出款。',
                'description' => '
                    <p>活动详情：</p>
                    <p>即日起存钱就送！</p>
                    <p>单笔存款500送38元</p>
                    <p>单笔存款1000送88元</p>
                    <p>单笔存款10000送188元</p>
                    <p>单笔存款50000送588元</p>
                    <p>单笔存款100000送1888元</p>
                    <p>单笔存款500000送18888元</p>
                    <p>单笔存款1000000送88888元</p>
                    <p>申请方式：联系客服进行申请。</p>
                    <p>每月可享受两次申请机会，1号到15号期间可以申请第一次，16号到月底可以申请第二次，一倍流水即可出款。（活动长期有效，最终解释权归双博娱乐城官网所有）</p>
                    <br>
                    <p>规则与条款：</p>
                    <p>1.优惠以人民币为结算货币，以北京时间为计算区间。</p>
                    <p>2.每一位玩家，每一个住址，每一个电子邮箱地址，每一个电话号码，相同ip以及同一银行卡号视为同一个人，只享有一次优惠；若会员有重复注册账号的行为，公司保留或收回会员优惠的权利。</p>
                    <p>3.若会员对活动有争议，为确保双方利益，杜绝身份滥用行为，双博娱乐集团有权要求会员提供详细资料，用以确认是否享有该优惠的资质。</p>
                    <p>4.为避免文字理解的差异，双博娱乐集团享有最终解释权，以及在无通知情况下更改、停止、取消优惠或者回收优惠的权利。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'rescuehandsel',
                'name' => '救援彩金',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '每位会员于每周只可以申请一次并获得一次救援金，且申请时须同时满足负盈利及账户余额要求。',
                'description' => '
                    <table width="100%">
                    <tr>
                    <th>申请要求</th>
                    <th>救援金</th>
                    <th>有效投注</th>
                    </tr>
                    <tr>
                    <td>当日负盈利超5000元，余额不足5元</td>
                    <td>88元</td>
                    <td>5倍</td>
                    </tr>
                    <tr>
                    <td>当日负盈利超5万元，余额不足5元</td>
                    <td>888元</td>
                    <td>5倍</td>
                    </tr>
                    <tr>
                    <td>当日负盈利超20万元，余额不足5元</td>
                    <td>1888元</td>
                    <td>5倍</td>
                    </tr>
                    </table>
                    <p>申请方式：联系客服进行申请。</p>
                    <p>活动鉴定</p>
                    <p>1、每位会员于每周只可以申请一次并获得一次救援金，且申请时须同时满足负盈利及账户余额要求；</p>
                    <p>2、符合此优惠活动的会员，请于当日内向"在线客服"提出申请，专员审核后将在5分钟内送出救援金，达到有效投注即可提取。</p>
                    <p>3、活动长期有效，最终解释权归双博娱乐官网所有。</p>
                    <p>规则与条款：</p>
                    <p>1.优惠以人民币为结算货币，以北京时间为计算区间。</p>
                    <p>2.每一位玩家，每一个住址，每一个电子邮箱地址，每一个电话号码，相同ip以及同一银行卡号视为同一个人，只享有一次优惠；若会员有重复注册账号的行为，公司保留或收回会员优惠的权利。</p>
                    <p>3.若会员对活动有争议，为确保双方利益，杜绝身份滥用行为，双博娱乐集团有权要求会员提供详细资料，用以确认是否享有该优惠的资质。</p>
                    <p>4.为避免文字理解的差异，双博娱乐集团享有最终解释权，以及在无通知情况下更改、停止、取消优惠或者回收优惠的权利。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'mysteryhandsel',
                'name' => '神秘彩金',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '双博娱乐集团将向会员不定期派送神秘彩金，彩金无门槛限制，不设上限金额，只要您在双博娱乐集团成功注册绑定真实银行账号，就算您不曾在我司存款游戏，也有可能收到我们为您随机派发的惊喜礼金。',
                'description' => '
                    <p>活动详情：</p>
                    <p>双博娱乐集团将向会员不定期派送神秘彩金，彩金无门槛限制，不设上限金额，只要您在双博娱乐集团成功注册绑定真实银行账号，就算您不曾在我司存款游戏，也有可能收到我们为您随机派发的惊喜礼金。</p>
                    <table width="100%">
                    <tr><td>神秘惊喜、随机派发，微信一响、彩金万两！</td></tr>
                    <tr><td>神秘惊喜奖金无需申请，直接划入会员账号</td></tr>
                    </table>
                    <p>规则与条款：</p>
                    <p>1.优惠以人民币为结算货币，以北京时间为计算区间。</p>
                    <p>2.每一位玩家，每一个住址，每一个电子邮箱地址，每一个电话号码，相同ip以及同一银行卡号视为同一个人，只享有一次优惠；若会员有重复注册账号的行为，公司保留或收回会员优惠的权利。</p>
                    <p>3.若会员对活动有争议，为确保双方利益，杜绝身份滥用行为，双博娱乐集团有权要求会员提供详细资料，用以确认是否享有该优惠的资质。</p>
                    <p>4.为避免文字理解的差异，双博娱乐集团享有最终解释权，以及在无通知情况下更改、停止、取消优惠或者回收优惠的权利。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'serialwin',
                'name' => '真人连赢，好运不停',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '仅限真人视讯百家乐中投注庄闲的注单参与此活动，投注“和”“庄对”“闲对”不计算在内。',
                'description' => '
                    <p>活动详情：</p>
                    <table width="100%">
                    <tr>
                    <th>连赢局数</th>
                    <th>单局最低赌注</th>
                    <th>可获彩金</th>
                    <th>提款要求</th>
                    </tr>
                    <tr>
                    <td>连赢七局</td>
                    <td>100元</td>
                    <td>68元</td>
                    <td>一倍流水</td>
                    </tr>
                    <tr>
                    <td>连赢八局</td>
                    <td>100元</td>
                    <td>88元</td>
                    <td>倍流水</td>
                    </tr>
                    <tr>
                    <td>连赢九局</td>
                    <td>100元</td>
                    <td>128元</td>
                    <td>一倍流水</td>
                    </tr>
                    <tr>
                    <td>连赢十局</td>
                    <td>100元</td>
                    <td>288元</td>
                    <td>一倍流水</td>
                    </tr>
                    </table>
                    <p>注：仅限真人视讯百家乐中投注庄闲的注单参与此活动，投注“和”“庄对”“闲对”不计算在内。</p>
                    <p>规则与条款：</p>
                    <p>1.优惠以人民币为结算货币，以北京时间为计算区间。</p>
                    <p>2.每一位玩家，每一个住址，每一个电子邮箱地址，每一个电话号码，相同ip以及同一银行卡号视为同一个人，只享有一次优惠；若会员有重复注册账号的行为，公司保留或收回会员优惠的权利。</p>
                    <p>3.若会员对活动有争议，为确保双方利益，杜绝身份滥用行为，双博娱乐集团有权要求会员提供详细资料，用以确认是否享有该优惠的资质。</p>
                    <p>4.为避免文字理解的差异，双博娱乐集团享有最终解释权，以及在无通知情况下更改、停止、取消优惠或者回收优惠的权利。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'playallplatform',
                'name' => '首存畅玩所有平台',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '即日起，凡是在双博娱乐注册并最低存款100元起即有好礼相送，无游戏限制，奖金最高可达888元！',
                'description' => '
                    <p>活动详情：</p>
                    <p>即日起，凡是在双博娱乐注册并最低存款100元起即有好礼相送，无游戏限制，奖金最高可达888元！</p>
                    <table width="100%">
                    <tr>
                    <th>单笔存款金额</th>
                    <th>赠送彩金</th>
                    <th>流水消费</th>
                    </tr>
                    <tr>
                    <td>100元</td>
                    <td>8元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>300元</td>
                    <td>18元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>500元</td>
                    <td>38元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>1000元</td>
                    <td>58元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>3000元</td>
                    <td>88元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>5000元</td>
                    <td>138元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>10000元</td>
                    <td>188元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>20000元</td>
                    <td>288元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>30000元</td>
                    <td>388元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>50000元</td>
                    <td>588元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    <tr>
                    <td>100000元</td>
                    <td>888元</td>
                    <td>本金加彩金3倍流水</td>
                    </tr>
                    </table>
                    <p>规则与条款：</p>
                    <p>1.优惠以人民币为结算货币，以北京时间为计算区间。</p>
                    <p>2.每一位玩家，每一个住址，每一个电子邮箱地址，每一个电话号码，相同ip以及同一银行卡号视为同一个人，只享有一次优惠；若会员有重复注册账号的行为，公司保留或收回会员优惠的权利。</p>
                    <p>3.若会员对活动有争议，为确保双方利益，杜绝身份滥用行为，双博娱乐集团有权要求会员提供详细资料，用以确认是否享有该优惠的资质。</p>
                    <p>4.为避免文字理解的差异，双博娱乐集团享有最终解释权，以及在无通知情况下更改、停止、取消优惠或者回收优惠的权利。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'elefirstdeposit',
                'name' => '电子100%首存',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '电子首存最低存款100元，需要完成存款家本金的15倍流水，最高可申请1288元。',
                'description' => '
                    <p>活动详情：</p>
                    <p>电子首存最低存款100元，需要完成存款家本金的15倍流水，最高可申请1288元</p>
                    <p>1.本优惠需联系在线客服进行申请。</p>
                    <p>2.申请天天首存后，没有完成流水要求无法申请其他倍数要求的其他优惠。</p>
                    <p>3.玩家所获得的奖金需要至少投注10倍以上才可以提款。游戏投注不包括所有21点游戏，所有轮盘游戏，所有百家乐游戏，所有骰宝游戏，所有视频扑克游戏，所有Pontoon游戏，各种Craps游戏，赌场战争游戏，娱乐场Hold\'em游戏，牌九游戏,多旋转老虎机和老虎机奖金翻倍投注。等投注是不计算在内的。</p>
                    <p>4.此优惠促销只适用于拥有一个独立账户的玩家。住址、电子邮箱地址﹑电话号码﹑支付方式（相同借记卡/信用卡/银行账户号码）IP地址，同一网络环境等将可以作为判定是否独立玩家的条件。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'veteranplayer',
                'name' => '老玩家回归签到有奖',
                'config' => '{"hide": "0", "config": [[[{"title": "当天充值>=", "value": "100"},{"title": "当天消费>=", "value": "5000"},{"title": "礼金", "value": "18"}]]]}',
                'config_ui' => '{}',
                'summary' => '当天充值100以上，消费满5000，即可参加老玩家回归礼金18元活动！',
                'description' => '
                    <p>活动内容：</p>
                    <p>1.活动限制：活动期间每个用户最多领取5天奖金！</p>
                    <p>2.活动资格： 用户为2019年4月1日前注册。</p>
                    <p>3.活动内容：参与活动的用户在彩票游戏场充值100元且消费满5000，均符合签到有效活动条件。</p>
                    <p>若在参加活动过程中遇到任何疑问，请您联系在线客服为您解答。</p>
                    <p>4.用户每日签到可领取奖金18元，可持续签到五天。</p>
                    <p>5.领取方式：达标点击活动页面领取，活动金额计入报表。</p>
                    <p>活动须知：</p>
                    <p>1.同一账户，同一IP，同一电脑，每日只允许领取一次；</p>
                    <p>2.活动不限彩种，不限玩法；</p>
                    <p>3.统计时间：00:00:00到23:59:59 ，当日达标即可领取；</p>
                    <p>4.本活动只欢迎真实玩家，拒绝白菜党，若发现有用户运用此活动进行套利行为，风控部有权将其账号进行冻结（连同本金）；</p>
                    <p>5.此活动最终解释权归本平台营运部活动策划组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'thirdgamesb',
                'name' => '沙巴体育、UG体育活动大派送',
                'config' => '{"hide": "0", "config": [[
                            [
                            {"title": "消费满", "value": 10000},
                            {"title": "奖励", "value": 18}
                            ],
                            [
                            {"title": "消费满", "value": 100000},
                            {"title": "奖励", "value": 190}
                            ],
                            [
                            {"title": "消费满", "value": 500000},
                            {"title": "奖励", "value": 1000}
                            ],
                            [
                            {"title": "消费满", "value": 1000000},
                            {"title": "奖励", "value": 2100}
                            ],
                            [
                            {"title": "消费满", "value": 2000000},
                            {"title": "奖励", "value": 4400}
                            ],
                            [
                            {"title": "消费满", "value": 5000000},
                            {"title": "奖励", "value": 10500}
                            ],
                            [
                            {"title": "消费满", "value": 10000000},
                            {"title": "奖励", "value": 24000}
                            ]
                            ]]}',
                'config_ui' => '{}',
                'summary' => '庆祝平台沙巴（UG）体育游戏上线，推出消费活动奖励。',
                'description' => '
                    <p>活动详情：</p>
                    <table width="100%">
                    <tr>
                    <th>消费金额</th>
                    <th>奖励金额</th>
                    </tr>
                    <tr>
                    <td>当日消费达标1W</td>
                    <td>18元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标10W</td>
                    <td>190元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标50W</td>
                    <td>1000元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标100W</td>
                    <td>2100元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标200W</td>
                    <td>4400元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标500W</td>
                    <td>10500元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标1000W</td>
                    <td>24000元</td>
                    </tr>
                    </table>
                    <p>活动须知：</p>
                    <p>1. 活动对象：本平台所有用户</p>
                    <p>2. 此活动仅针UG体育及SB体育的消费统计，统计数据可一起计算。</p>
                    <p>3. 每日每个账号每条IP每张同姓名银行卡仅可领取一次。</p>
                    <p>4. 领取活动彩金需消费金额的1倍流水可提款。（不限游戏）</p>
                    <p>5. 统计时间：00:00:00到23:59:59 ，当日达标次日4点即可开始领取。</p>
                    <p>6. 此活动最终解释归本平台营运部活动策划组所有。</p>
                    <p>7. 领取方式：达标点击活动页面领取，活动金额计入报表。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'thirdgamezr',
                'name' => '真人AG/开元棋牌/PT电游活动大派送',
                'config' => '{"hide": "0", "config": [[
                            [
                            {"title": "消费满", "value": 10000},
                            {"title": "奖励", "value": 38}
                            ],
                            [
                            {"title": "消费满", "value": 100000},
                            {"title": "奖励", "value": 390}
                            ],
                            [
                            {"title": "消费满", "value": 500000},
                            {"title": "奖励", "value": 2000}
                            ],
                            [
                            {"title": "消费满", "value": 1000000},
                            {"title": "奖励", "value": 4100}
                            ],
                            [
                            {"title": "消费满", "value": 2000000},
                            {"title": "奖励", "value": 8400}
                            ],
                            [
                            {"title": "消费满", "value": 5000000},
                            {"title": "奖励", "value": 21500}
                            ],
                            [
                            {"title": "消费满", "value": 10000000},
                            {"title": "奖励", "value": 44000}
                            ]
                            ]]}',
                'config_ui' => '{}',
                'summary' => '为庆祝平台AG真人/PT电游/开元棋牌等游戏上线，推出消费活动奖励。',
                'description' => '
                    <p>活动详情：</p>
                    <table width="100%">
                    <tr>
                    <th>消费金额</th>
                    <th>奖励金额</th>
                    </tr>
                    <tr>
                    <td>当日消费达标1W</td>
                    <td>38元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标10W</td>
                    <td>390元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标50W</td>
                    <td>2000元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标100W</td>
                    <td>4100元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标200W</td>
                    <td>8400元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标500W</td>
                    <td>21500元</td>
                    </tr>
                    <tr>
                    <td>当日消费达标1000W</td>
                    <td>44000元</td>
                    </tr>
                    </table>
                    <p>活动须知：</p>
                    <p>1. 活动对象：本平台所有用户</p>
                    <p>2. 此活动仅针对AG真人/PT电游/开元棋牌的消费统计，统计数据可一起计算。</p>
                    <p>3. 每日每个账号每条IP每张同姓名银行卡仅可领取一次。</p>
                    <p>4. 领取活动彩金需消费金额的1倍流水可提款。（不限游戏）</p>
                    <p>5. 统计时间：00:00:00到23:59:59 ，当日达标次日4点即可开始领取。</p>
                    <p>6. 此活动最终解释归本平台营运部活动策划组所有。</p>
                    <p>7. 领取方式：达标点击活动页面领取，活动金额计入报表。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'sportmember',
                'name' => '双博新体育会员',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '新会员玩体育拿奖金，最高红利588等你拿。',
                'description' => '
                    <p>活动详情：</p>
                    <table width="100%">
                    <tr>
                    <th>平台</th>
                    <th>充值金额</th>
                    <th>红利百分比</th>
                    <th>红利上限</th>
                    <th>流水倍数</th>
                    </tr>
                    <tr>
                    <td>BBIN、UG体育、沙巴体育</td>
                    <td>≥100</td>
                    <td>50%</td>
                    <td>588</td>
                    <td>12</td>
                    </tr>
                    </table>
                    <p>申请方式：请先将需要申请的金额转入相应的场馆后，向客服提出申请，核对金额无误后即可参加。</p>
                    <p>领取方式：活动达标后，次日向客服提出申请。</p>
                    <p>活动须知：</p>
                    <p>1. 每位新用户可申请1次50%奖金，有效投注额达到【（本金+ 红利）x12倍流水】即可提款。</p>
                    <p>&nbsp;&nbsp;例如：会员存款100元，则需要有效投注（100+50）x12=1800的投注额即可申请提款。</p>
                    <p>2. 本活动仅和返水优惠共享，不与其它任何优惠共享。</p>
                    <p>3. 在申请此优惠前，请您先完善真实姓名、手机、邮箱等个人信息，该优惠活动成功申请后不能取消，如解除账户限制，需完成活动指定流水要求或申请该活动的指定账户所有注单结算完毕后金额低于5元时，请从主账户转入任意金额至锁定账户，再从锁定账户转出金额，系统识别后即可解除限制。</p>
                    <p>4. 任何虚拟体育/金融投注/彩票投注将不被计算在有效投注内。</p>
                    <p>5. 若发现有套利客户，对赌或不诚实获取盈利之行为，将取消其优惠资格。</p>
                    <p>6. 双博仅对已结算并产生输赢结果的投注额进行计算，任何平局、取消的赛事将不计算在有效投注。</p>
                    <p>7. 每位有效玩家、每一手机号码、电子邮箱、相同银行卡、每一个IP地址、每一台电脑者只能享受一次优惠，如发现有违规者我们将保留无限期审核扣回红利及所产生的利润权利。</p>
                    <p>8. 此活动遵循双博一般规则与条款。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'firstrecharge',
                'name' => '双博首存即送38%，仅需8倍流水',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '玩体育首充拿奖金，38%红利仅需8倍流水。',
                'description' => '
                    <p>活动详情：</p>
                    <p>1. 由***年*月**日(北京时间12:00 am)起至待定，只要首次存款RMB 100起到双博娱乐，并全额转账至双博娱乐任意体育平台，完成活动流水前中途不可相互转换，然后点击“我的优惠”中【申请参与】按钮申请该活动优惠，即可领取38%首存红利奖金，最高可获RMB 288高额红利。</p>
                    <p>2. 存款前账户余额需低于5元（返水不计算在内），存款后不得进行投注。如果同时进行多笔存款，将无法申请该活动。必须将充值金额全部转账至需要游戏的平台后申请红利。申请红利时，账户有未结算注单将无法成功申请本红利奖金。</p>
                    <p>3. 最高可申请首存红利奖金数额为RMB 288 。</p>
                    <p>4. 有效存款加红利总额必须在获得红利后的首次全额转入的指定游戏平台内投注累计达到最高8倍有效流水之后方可申请提款。</p>
                    <p>5. 此优惠每一位玩家只可申请一次，没有申请过本优惠的老玩家亦可申请。如果出现连续多月推出本活动，并非每月均可申请一次，请各位玩家注意。</p>
                    <p>6. 申请本优惠的玩家可同时申请非存款优惠活动(例如微信/QQ转发，VIP成长活动等)。</p>
                    <p>7. 玩家在获得红利后的首次全额转入的指定游戏平台内获得首存红利的内申请返水，在计算返水时会将首存红利所要求的流水额先扣除。</p>
                    <p>体育平台有效流水细则：</p>
                    <p>例如：</p>
                    <p>首次存款= RMB 100</p>
                    <p>转入双博体育后申请红利= 100 x 38% = RMB 38</p>
                    <p>如申请提取红利需在双博体育下注（100 + 38）x 8 = RMB 1,104</p>
                    <p>发放方式：达标后向客服申请领取。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'agentoffline',
                'name' => '在线代充送0.1%',
                'config' => '{
                    "min_deposit": 100,
                    "rate_percent": 0.1,
                    "payment_method_idents": ["agent_weixin","agent_qq","agent_alipay","agent_chat"],
                    "hide":1
                }',
                'config_ui' => '{}',
                'summary' => '在线代充每笔赠送彩金0.1%，次次存，次次送，无上限，无需申请，自动到账，充值成功后可立即获得反馈金。',
                'description' => '',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 3,
                'status' => 0
            ],
            [
                'ident' => 'findbug',
                'name' => '找BUG 赚大钱',
                'config' => '{}',
                'config_ui' => '{"show_kf": 1}',
                'summary' => '公测找bug，红包拿不停。',
                'description' => '
                    <p>活动内容：</p>
                    <p>公测期间，开启全民寻找BUG之旅 ，一旦发现平台任何BUG，及时提交在线客服，就有机会获得188-8888的大红包！</p>
                    <p>活动须知：</p>
                    <p>1. 活动对象：本平台所有公测用户</p>
                    <p>2. 需留下个人银行卡信息及开业后需要注册的正式账号</p>
                    <p>3. 开业后运营部门根据提交BUG的程度，赠送188-8888的红包</p>
                    <p>4 此活动最终解释归本平台营运部活动策划组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'hongbaorain',
                'name' => '百万红包雨 等你来领钱',
                'config' => '{"hide": "1", "config": [
                            [
                                [
                                    {"title": "用户充值>=", "value": 100},
                                    {"title": "奖金", "value": 8}
                                ],
                                [
                                    {"title": "用户充值>=", "value": 500},
                                    {"title": "奖金", "value": 16}
                                ],
                                [
                                    {"title": "用户充值>=", "value": 1000},
                                    {"title": "奖金", "value": 26}
                                ],
                                [
                                    {"title": "用户充值>=", "value": 3000},
                                    {"title": "奖金", "value": 36}
                                ],
                                [
                                    {"title": "用户充值>=", "value": 5000},
                                    {"title": "奖金", "value": 46}
                                ],
                                [
                                    {"title": "用户充值>=", "value": 8000},
                                    {"title": "奖金", "value": 56}
                                ],
                                [
                                    {"title": "用户充值>=", "value": 10000},
                                    {"title": "奖金", "value": 66}
                                ]
                            ]
                            ]}',
                'config_ui' => '{}',
                'summary' => '为庆祝平台正式开业，推出首充活动奖励',
                'description' => '
                    <p>活动对象：</p>
                    <p>活动须知：</p>
                    <p>1.活动对象：本平台所有用户</p>
                    <p>2.活动期间：7月16日开始 结束时间待定</p>
                    <p>3.活动派发条件：需绑定银行卡与手机号，再达成上述充值标准</p>
                    <p>4.每位用户、银行卡、手机号、IP只能参与一次，若有重复申请帐号的行为，平台保留/取消/收     回会员活动奖励的权力</p>
                    <p>5.如发现任何恶意套利行为，平台将扣除所有违规所得，并且有权冻结其帐号</p>
                    <p>6.此活动最终解释权归本平台营运部活动策划组所有</p>
                    <p>7.领取方式：达标点击活动页面领取</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'oldplayercharge',
                'name' => '老会员充值送1%',
                'config' => '{}',
                'config_ui' => '{"show_kf": 1}',
                'summary' => '每天可以领取首次充值金额的1%',
                'description' => '
                    <p>活动内容：</p>
                    <p>1、请会员登录账号点击充值-选择专员直冲即可参与；</p>
                    <p>2、所有平台的用户，均可参与，活动仅限于【专员直冲通道】完成充值的客户；</p>
                    <p>3、凡是参与活动的会员，需完成首冲金额1倍的流水（仅适用于彩票类游戏）；</p>
                    <p>4、该优惠请向客服申请，存款申请后管理员派发；</p>
                    <p>活动须知：</p>
                    <p>1、活动对象：在2019年8月16日 以前有过充值记录的1彩会员；</p>
                    <p>2、同一账户，同一IP，同一电脑，相同支付卡/信用卡号码等相同信息限一个账号参与；</p>
                    <p>3、若检测到会员有套取彩金行为，平台有权要求该会员消费3-5倍流水进行提款，情节严重者有权没收所有资金并冻结账号；</p>
                    <p>4、参与此优惠即视为同意平台优惠规则条款；</p>
                    <p>5、此活动最终解释权归运营部活动组所有。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'hongbaorain2',
                'name' => '百万红包雨 登陆来领钱',
                'config' => '{}',
                'config_ui' => '{}',
                'summary' => '为庆祝平台正式开业，9号推出登入抢红包',
                'description' => '
                    <p>活动须知：</p>
                    <p>1.活动对象：本平台所有用户</p>
                    <p>2.活动期间：2019-09-19开始 至 2019-10-19</p>
                    <p>3.活动派发条件：无</p>
                    <p>4.同用户、同IP每小时只能参与一次，若有重复申请帐号的行为，平台保留/取消/收回会员活动奖励的权力</p>
                    <p>注意：同IP位置只会有一个用户会飘落红包雨</p>
                    <p>5.如发现任何恶意套利行为，平台将扣除所有违规所得，并且有权冻结其帐号</p>
                    <p>6.此活动最终解释权归本平台营运部活动策划组所有</p>
                    <p>7.领取方式：整点点击页面飘落的红包领取</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'firstrecharge2',
                'name' => '开业典礼－首充送奖励',
                'config' => '{"hide": "0", "config": [[[{"title": "首充金额>=", "value": "100"},{"title": "礼金", "value": "8"}]]]}',
                'config_ui' => '{}',
                'summary' => '符合条件可点击领取优惠政策',
                'description' => '
                    <p>活动对象：本平台所有用户</p>
                    <p>活动期间：2019-11-xx开始 至 2019-11-xx</p>
                    <p>活动派发条件：首次充值100元    赠送8元奖励</p>
                    <p>投注不能超过总注数的75%，流水消费本金+活动金额的2倍方可提款</p>
                    <p>同一账户，同一IP，同一电脑，只允许领取一次</p>
                    <p>如发现任何恶意套利行为，平台将扣除所有违规所得，并且有权冻结其帐号</p>
                    <p>此活动最终解释权归本平台营运部活动策划组所有</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'bindbankcard',
                'name' => '开业典礼－绑定银行卡送奖励',
                'config' => '{"hide": "0", "config": [[[{"title": "礼金", "value": "6"}]]]}',
                'config_ui' => '{}',
                'summary' => '符合条件可点击领取优惠政策',
                'description' => '
                    <p>活动对象：本平台所有用户</p>
                    <p>活动期间：2019-11-xx开始 至 2019-11-xx</p>
                    <p>活动派发条件：绑定银行卡资料，赠送6元奖励</p>
                    <p>同一账户，同一IP，同一电脑，只允许领取一次</p>
                    <p>如发现任何恶意套利行为，平台将扣除所有违规所得，并且有权冻结其帐号</p>
                    <p>此活动最终解释权归本平台营运部活动策划组所有</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'rechargereward',
                'name' => '开业典礼－充值满额送奖励',
                'config' => '{"hide": "0", "config": [[[{"title": "累计充值金额>=", "value": "5000"},{"title": "礼金", "value": "36"}]]]}',
                'config_ui' => '{}',
                'summary' => '符合条件可点击领取优惠政策',
                'description' => '
                    <p>活动对象：本平台所有用户</p>
                    <p>活动期间：2019-11-xx开始 至 2019-11-xx</p>
                    <p>活动派发条件：充值累计5000元以上 赠送36元奖励</p>
                    <p>同一账户，同一IP，同一电脑，只允许领取一次</p>
                    <p>如发现任何恶意套利行为，平台将扣除所有违规所得，并且有权冻结其帐号</p>
                    <p>此活动最终解释权归本平台营运部活动策划组所有</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'consumereward',
                'name' => '开业典礼－销量满额送奖励',
                'config' => '{"hide": "0", "config": [[[{"title": "消费流水>=", "value": "1000"},{"title": "礼金", "value": "18"}]]]}',
                'config_ui' => '{}',
                'summary' => '符合条件可点击领取优惠政策',
                'description' => '
                    <p>活动对象：本平台所有用户</p>
                    <p>活动期间：2019-11-xx开始 至 2019-11-xx</p>
                    <p>活动派发条件：消费流水1000元以上，赠送18元奖励</p>
                    <p>活动采计销量：仅适用于彩票</p>
                    <p>投注不能超过总注数的75%，流水消费本金+活动金额的2倍方可提款</p>
                    <p>同一账户，同一IP，同一电脑，只允许领取一次</p>
                    <p>如发现任何恶意套利行为，平台将扣除所有违规所得，并且有权冻结其帐号</p>
                    <p>此活动最终解释权归本平台营运部活动策划组所有</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'prizepool',
                'name' => '奖池活动',
                'config' => '{"hide": "0", "config": [[[{"title": "奖池比例(%)", "value": "0.025"}]]]}',
                'config_ui' => '{}',
                'summary' => '送福利拿奖金',
                'description' => '',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0
            ],
            [
                'ident' => 'jackpot',
                'name' => '幸运大奖池',
                'config' => '{"hide": "0", "config": [[[{"title": "奖池占销量总额比例(%)", "value": "0.2"},{"title": "继承上期奖池比例(%)", "value": "52"},{"title": "开奖彩种英文标识", "value": "cqssc"},{"title": "期号", "value": "058"}]]]}',
                'config_ui' => '{}',
                'summary' => '投注抽大奖',
                'description' => '
                <p>1.只要您累计投注10000元，我们就随机送出一串不重复的抽奖码(机选5位数)，根据指定时间重庆时时彩(每周日的第58期为开奖依据)的开奖号码为中奖号码，只要您的抽奖码与重庆时时彩的号码数字.位置一致，即为中奖，中奖的幸运玩家可以获得对应现金奖励，本活动做到真正的公平.公正.公开。</p>
                <p>2.任意玩家投注，即自动提取该玩家投注金额的0.2%进入奖池。</p>
                <p>3.从活动开始直至最终开奖，整个奖池的金额都在持续累积。</p>
                <p>4.第一期开奖后，所有的抽奖码全部清空，第二期活动开始后重新发放抽奖码。</p>
                <p>5.每天获得的抽奖码不设上限，投注越多，抽奖码越多。</p>
                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0 //默认禁用
            ],
            [
                'ident' => 'loginhongbaorain',
                'name' => '天天红包雨 登录来领钱',
                'config' => '{"hide": "0", "config": [[[{"title": "当日累积充值金额≥", "value": 100}], [{"title": "每日限制红包数量", "value": 2000}], [{"title": "用户注册日为指定日之前", "value": "2020-05-01"}]], [[{"title": "当前余额≥", "value": 0}, {"title": "红包金额(元)", "value": 8}], [{"title": "当前余额≥", "value": 300}, {"title": "红包金额(元)", "value": 18}], [{"title": "当前余额≥", "value": 600}, {"title": "红包金额(元)", "value": 28}], [{"title": "当前余额≥", "value": 1000}, {"title": "红包金额(元)", "value": 38}], [{"title": "当前余额≥", "value": 3000}, {"title": "红包金额(元)", "value": 48}], [{"title": "当前余额≥", "value": 6000}, {"title": "红包金额(元)", "value": 58}], [{"title": "当前余额≥", "value": 10000}, {"title": "红包金额(元)", "value": 68}], [{"title": "当前余额≥", "value": 30000}, {"title": "红包金额(元)", "value": 78}], [{"title": "当前余额≥", "value": 60000}, {"title": "红包金额(元)", "value": 88}]]]}',
                'config_ui' => '{}',
                'summary' => '【大唐Ⅱ盛世】红包雨活动',
                'description' => '<p>尊敬的玩家，您好！大唐Ⅱ盛世娱乐平台为了回馈新老客户，开启红包雨活动，红包参抢资格规则如下：<br />
                一、2020/05月01日前注册的客户；<br />
                二、当日有进行充值金额的客户；<br />
                三、账号金额越多，抢到大红包的机率越高；<br />
                四、如发现利用此活动任何恶意套利行为，平台将扣除所有违规所得，并且有权冻结其帐号；<br />
                五、每位用户、邮箱、手机号、银行卡、IP地址，每天只能享受一次优惠红包活动；若有重复申请帐号行为，公司保留/取消/收回会员优惠红包的权力。</p>',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 1,
                'status' => 0 //默认禁用
            ],
            [
                'ident' => 'recharge10pct',
                'name' => '充值送彩金活动',
                'config' => '{"hide": "0", "config": [[[{"title": "最高礼金", "value": "8888"}],[{"title": "指定彩种英文标识用,号分割", "value": "cxg360ffc,cxg3605fc,hne1fssc,hne5fssc,jsxyftpk10"}]],[[{"title": "充值金额(万)≥", "value": "0"},{"title": "流水倍数", "value": "3"},{"title": "奖励(%)", "value": "2"}],[{"title": "充值金额(万)≥", "value": "1"},{"title": "流水倍数", "value": "5"},{"title": "奖励(%)", "value": "3"}],[{"title": "充值金额(万)≥", "value": "5"},{"title": "流水倍数", "value": "8"},{"title": "奖励(%)", "value": "4"}],[{"title": "充值金额(万)≥", "value": "20"},{"title": "流水倍数", "value": "10"},{"title": "奖励(%)", "value": "5"}]]]}',
                'config_ui' => '{}',
                'summary' => '投注360分分彩、360五分彩、河内分分彩、河内五分彩、幸运飞艇彩种的用户，均可参与充值送彩金活动',
                'description' => '<p>活动规则介绍内容:</p>
                                <table width="100%">
                                <tr>
                                <th>充值金额</th>
                                <th>消费金额</th>
                                <th>奖励比例(%)</th>
                                </tr>
                                <tr>
                                <td>0-1万</td>
                                <td>消费（充值＋彩金）总金额3倍以上</td>
                                <td>2</td>
                                </tr>
                                <tr>
                                <td>1万-5万</td>
                                <td>消费（充值＋彩金）总金额5倍以上</td>
                                <td>3</td>
                                </tr>
                                <tr>
                                <td>5万-20万</td>
                                <td>消费（充值＋彩金）总金额8倍以上</td>
                                <td>4</td>
                                </tr>
                                <tr>
                                <td>20万-50万</td>
                                <td>消费（充值＋彩金）总金额10倍以上</td>
                                <td>5</td>
                                </tr>
                                </table>
                                <p>1.活动对象：本平台所有用户</p>
                                <p>2.活动派发条件：前一日有进行充值，并且前一日消费(本金+彩金)流水满足领取条件</p>
                                <p>3.活动奖励：奖金最高8888元</p>
                                <p>4.活动限制：活动只限对应彩种，不限玩法，用户投注注数不可大于该玩法的总注数的90%（比如：一星不超过9注，二星不超过90注，三星不超过900注，四星不超过9000注）</p>
                                <p>&nbsp;&nbsp;限制的彩种：360分分彩、360五分彩、河内分分彩、河内五分彩、幸运飞艇</p>
                                <p>5.领取方式：达成条件后，于活动页面点击领取</p>
                                <p>6.同一账户，同一IP，同一电脑，每日只允许参与一次</p>
                                <p>7.本活动只欢迎真实玩家，拒绝白菜党，若发现有用户运用此活动进行套利行为，风控部有权将其账号进行冻结（连同本金）</p>
                                <p>8.此活动最终解释权归运营部活动组所有</p>
                                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 0,
                'status' => 0
            ],
            [
                'ident' => 'withdrawaldelay',
                'name' => '提款延迟补偿金活动',
                'config' => '{"hide": "0", "config": [[[{"title": "提款延迟(分钟)≥", "value": 30}, {"title": "礼金百分比", "value": 0.07}], [{"title": "提款延迟(分钟)≥", "value": 60}, {"title": "礼金百分比", "value": 0.08}], [{"title": "提款延迟(分钟)≥", "value": 90}, {"title": "礼金百分比", "value": 0.09}], [{"title": "提款延迟(分钟)≥", "value": 120}, {"title": "礼金百分比", "value": 1.00}]]]}',
                'config_ui' => '{}',
                'summary' => '任何一个玩家，提款延迟到账都有奖励',
                'description' => '<p>活动规则:</p>
                                <p>1.参加对象：任何一个玩家，提款延迟到账都有奖励</p>
                                <p>2.以半小时为阶梯，最高奖励提款金额的1%</p>
                                <p>3.奖励内容：</p>
                                <table width="100%">
                                <tr>
                                <th>提款延迟时间</th>
                                <th>提款奖励比例(%)</th>
                                </tr>
                                <tr>
                                <td>≥30分钟</td>
                                <td>0.07</td>
                                </tr>
                                <tr>
                                <td>≥60分钟</td>
                                <td>0.08</td>
                                </tr>
                                <tr>
                                <td>≥90分钟</td>
                                <td>0.09</td>
                                </tr>
                                <tr>
                                <td>≥120分钟</td>
                                <td>1.00</td>
                                </tr>
                                </table>
                                <p>4.此活动最终解释权归运营部活动组所有</p>
                                ',
                'start_time' => $test_date('00:00:00'),
                'end_time' => $test_date('23:59:59'),
                'draw_method' => 4,
                'status' => 0
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
            Schema::dropIfExists('activity');
        }
    }
}
