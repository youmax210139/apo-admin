<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Service\API\Pump\Rule as ApiPumpRule;

class CreateTableConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('配置 ID');
            $table->SmallInteger('parent_id')->default(0)->comment('配置父 ID');
            $table->string('title', 64)->comment('配置标题');
            $table->string('key', 64)->unique()->comment('系统配置名称');
            $table->string('value', 256)->comment('系统配置值');
            $table->smallInteger('input_type')->default(0)->comment('配置输入类型 0输入框，1下拉框，2复选框');
            $table->smallInteger('value_type')->default(0)->comment('配置值 验证类型 0字符串，1数字，2大于零正数');
            $table->string('input_option', 256)->default('')->comment('输入选项，当input_type 为下拉框或者复选框使用');
            $table->string('description', 256)->default('')->comment('配置描述');
            $table->boolean('is_disabled')->index()->default(0)->comment('配置项是否禁用');
            $table->timestamps();
        });

        $this->data();
    }

    private function data()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon' => 'fa-cog',
            'rule' => 'site',
            'name' => '网站管理',
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'config/index',
                'name' => '配置管理',
            ],
            [
                'parent_id' => $id,
                'rule' => 'config/create',
                'name' => '添加配置',
            ],
            [
                'parent_id' => $id,
                'rule' => 'config/edit',
                'name' => '编辑配置',
            ],
            [
                'parent_id' => $id,
                'rule' => 'config/set',
                'name' => '设置配置',
            ],
            [
                'parent_id' => $id,
                'rule' => 'config/refresh',
                'name' => '刷新配置',
            ],
            [
                'parent_id' => $id,
                'rule' => 'config/disable',
                'name' => '启用或禁用配置',
            ],
            [
                'parent_id' => $id,
                'rule' => 'config/delete',
                'name' => '删除配置',
            ],
            [
                'parent_id' => $id,
                'rule' => 'monitor/index',
                'name' => '系统异常监控',
            ],
            [
                'parent_id' => $id,
                'rule' => 'upload/index',
                'name' => '图片上传',
            ],
            [
                'parent_id' => $id,
                'rule' => 'upload/delpic',
                'name' => '删除图片',
            ],
            [
                'parent_id' => $id,
                'rule' => 'config/refreshapp',
                'name' => '发送刷新页面广播',
            ],
        ]);
        //网站基础设置
        $app_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '网站设置',
            'key' => 'app',
            'value' => '1',
            'description' => '网站设置',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $app_parent_id,
                'title' => '关闭访问',
                'key' => 'app_closed',
                'value' => '0',
                'description' => '0或1',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '网站标题',
                'key' => 'web_title',
                'value' => '阿波罗娱乐系统',
                'description' => '',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '在线客服地址',
                'key' => 'kefu_url',
                'value' => '',
                'description' => '在线客服地址',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '客服热线',
                'key' => 'kefu_hotline',
                'value' => '',
                'description' => '客服电话',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '是否启用导入用户的数据',
                'key' => 'has_import_user',
                'value' => '0',
                'description' => '0-否,1-是。是否启用导入用户的数据，用于登陆密码及资金密码验证等。默认【0】',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '图片上传地址',
                'key' => 'remote_pic_url',
                'value' => 'https://picture.12znz.com',
                'description' => '图片上传地址',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '图片上传秘钥',
                'key' => 'remote_pic_key',
                'value' => 'J2IyGSJ4LzArZBA3aWfy31RqwPDVWwmM',
                'description' => '图片上传秘钥',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '网站标识',
                'key' => 'app_ident',
                'value' => 'test',
                'description' => '网站标识',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '是否开启上下级聊天',
                'key' => 'show_chat',
                'value' => '1',
                'description' => '是否开启上下级聊天 0-关闭 1-开启',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => 'app端热门游戏',
                'key' => 'hot_games_app',
                'value' => '',
                'description' => '一行一个游戏 格式：名称|标识|第三方地址（可以不填)',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => 'PC端轮播图',
                'key' => 'carousel_pc',
                'value' => 'common/yc/20190710/5d2579c22776f.jpg
common/yc/20190710/5d2579d007550.jpg|activity/findbug
common/yc/20190710/5d2579d99cbee.jpg
common/yc/20190710/5d2579f16a44f.jpg',
                'description' => '一行一张图片换行分开 格式：图片地址|转跳地址（不转可以不填',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => 'app端轮播图',
                'key' => 'carousel_app',
                'value' => 'common/yc/20190710/5d2579c22776f.jpg
common/yc/20190710/5d2579d007550.jpg|activity/findbug
common/yc/20190710/5d2579d99cbee.jpg
common/yc/20190710/5d2579f16a44f.jpg',
                'description' => '一行一张图片换行分开 格式：图片地址|转跳地址（不转可以不填',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '允许弹出劫持页面的域名',
                'key' => 'notice_allow_domain',
                'value' => '',
                'description' => '多个域名请以小写逗号分隔',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => '工资计入盈亏报表时间',
                'key' => 'calculate_today_wage_to_tomorrow',
                'value' => '0',
                'description' => '0：当天，1：明天',
            ],
            [
                'parent_id' => $app_parent_id,
                'title' => 'PC底部右下角广告',
                'key' => 'ad_pc_bottom_right',
                'value' => '1|common/yc/20190710/5d2579f16a44f.jpg|game_poker?type=2|12',
                'description' => '格式：状态(0隐,1显)|图片地址|链接地址|关闭不显示时间(小时)',
            ],
        ]);

        //添加充值配置
        $deposit_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '用户充值',
            'key' => 'deposit',
            'value' => '1',
            'description' => '用户充值',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $deposit_parent_id,
                'title' => '充值中间站合法来源IP',
                'key' => 'deposit_middleman_legal_ip',
                'value' => '127.0.0.l',
                'description' => '多个IP请以小写逗号分隔',
            ],
            [
                'parent_id' => $deposit_parent_id,
                'title' => '代理充值自动回复',
                'key' => 'chat_deposit_auto_reply',
                'value' => '0',
                'description' => '0或者1',
            ],
            [
                'parent_id' => $deposit_parent_id,
                'title' => '原始订单金额是否允许误差不超过 1 元',
                'key' => 'allow_deposit_deviation',
                'value' => '0',
                'description' => '0:关闭 1:开启 使用原始订单金额误差值上分(上下 1 元误差内)',
            ],
            [
                'parent_id' => $deposit_parent_id,
                'title' => '使用第三方实际充值金额为主上分给用户',
                'key' => 'use_deposit_third_amount',
                'value' => '0',
                'description' => '关闭：使用原始订单金额上分 开启：使用三方实际充值金额上分（预设关闭)',
            ],
            [
                'parent_id' => $deposit_parent_id,
                'title' => 'USDT的充值汇率',
                'key' => 'usdt_deposit_exchange_rate',
                'value' => '0',
                'description' => '例：填入700，意思为1元人民币＝700U币',
            ],
        ]);

        //添加提现配置
        $withdrawal_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '用户提现',
            'key' => 'withdrawal',
            'value' => '1',
            'description' => '用户提现',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '新用户提现时间限制',
                'key' => 'withdrawal_timeout_new_user',
                'value' => '1',
                'description' => '单位：小时',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '新卡绑定提现时间限制',
                'key' => 'withdrawal_timeout_new_card',
                'value' => '1',
                'description' => '单位：小时',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '用户提现开始时间',
                'key' => 'withdrawal_time_begin',
                'value' => '10:00',
                'description' => '格式 01:10',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '用户提现结束时间',
                'key' => 'withdrawal_time_end',
                'value' => '02:00',
                'description' => '格式 01:10',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '用户提现最小金额',
                'key' => 'withdrawal_money_min',
                'value' => '100',
                'description' => '公用',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '用户提现最大金额',
                'key' => 'withdrawal_money_max',
                'value' => '49999',
                'description' => '公用',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '用户提现基础次数',
                'key' => 'withdrawal_times_base',
                'value' => '20',
                'description' => '公用',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '绑定新卡后再绑卡时间限制',
                'key' => 'bind_new_card_time_limit',
                'value' => '1',
                'description' => '单位：小时',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '解绑后再绑新卡时间限制',
                'key' => 'unbind_card_time_limit',
                'value' => '6',
                'description' => '单位：小时',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '全局手续费是否开启',
                'key' => 'withdrawal_global_fee_status',
                'value' => '0',
                'description' => '0:关闭 1:开启 默认[0]',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '全局手续费费率',
                'key' => 'withdrawal_global_fee_step',
                'value' => '0,0,0,1',
                'description' => '(第一个数代表用户第一次发起提现，后面已此类推、最后一个逗号代表这个次数之后的都是这个比例) 使用[,]分隔不同次的手续费',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '全局手续费封顶',
                'key' => 'withdrawal_global_fee_max',
                'value' => '50',
                'description' => '全局手续费封顶值',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '是否自动关闭第三方自动出款通道',
                'key' => 'withdrawal_auto_close_thirdchannel',
                'value' => '1',
                'description' => '如果第三方自动出款单多次失败，程序是否自动关闭提现通道 1:自动关闭 0:不自动关闭  默认「1」',
            ],
            [
                'parent_id' => $withdrawal_parent_id,
                'title' => '前台提现手续费提示文字',
                'key' => 'withdrawal_fee_tips',
                'value' => '提款前3笔手续费全免，后续每笔提现2%手续费',
                'description' => '',
            ],

        ]);
        //注册配置
        $reg_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '登录注册配置',
            'key' => 'reg',
            'value' => '1',
            'description' => '',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $reg_parent_id,
                'title' => '是否禁止公开注册',
                'key' => 'reg_public_disabled',
                'value' => '0',
                'description' => '0,1',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '公开注册上级ID',
                'key' => 'reg_public_parent_id',
                'value' => '',
                'description' => '多个用,号隔开',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '公开注册彩票返点',
                'key' => 'reg_public_lottery_rebate',
                'value' => '7',
                'description' => '7',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '是否开启登录即注册',
                'key' => 'reg_login_as_register',
                'value' => '0',
                'description' => '是否开启登录即注册：0关闭，1开启',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '公开注册第三方返水',
                'key' => 'reg_public_thrid_rebate',
                'value' => '0',
                'description' => '',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '是否禁止试玩注册',
                'key' => 'reg_test_disabled',
                'value' => '0',
                'description' => '0,1',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '试玩注册上级ID',
                'key' => 'reg_test_parent_id',
                'value' => '',
                'description' => '多个用,号隔开',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '是否禁止推广注册',
                'key' => 'reg_invite_disabled',
                'value' => '0',
                'description' => '0,1',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '是否禁止代理注册',
                'key' => 'reg_proxy_disabled',
                'value' => '0',
                'description' => '0,1',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '代理线最大层级',
                'key' => 'reg_proxy_user_max',
                'value' => '6',
                'description' => '',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '代理注册直属下级最大人数',
                'key' => 'reg_proxy_level_max',
                'value' => '100',
                'description' => '',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '配额最小返点',
                'key' => 'reg_quota_min',
                'value' => '8',
                'description' => '检查配额的最小返点',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '是否开启异地登录真实姓名验证',
                'key' => 'deff_login_ip_verify',
                'value' => '1',
                'description' => '0,1',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '用户首次登陆是否需要修改密码',
                'key' => 'first_login_change_password',
                'value' => '0',
                'description' => '0关闭,1开启',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '正式组是否继承上级禁用状态(冻结、添加下级、转账、提款)',
                'key' => 'reg_formal_inherit',
                'value' => '1',
                'description' => '0 不继承 1 继承',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '同IP注册限制数量',
                'key' => 'reg_ip_limit_num',
                'value' => '10',
                'description' => '同Ip注册限制时长内，同IP注册限制数量。默认：10个',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '同Ip注册限制时长',
                'key' => 'reg_ip_limit_time',
                'value' => '360',
                'description' => '单位：分钟。默认：360',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '同IP登录帐号数最大值',
                'key' => 'login_ip_limit_num',
                'value' => '30',
                'description' => '同IP登录帐号数检查时间内，同IP登录帐号最大值。默认：30个，设为0则不开启',
            ],
            [
                'parent_id' => $reg_parent_id,
                'title' => '同IP登录帐号数检查时间',
                'key' => 'login_ip_limit_time',
                'value' => '60',
                'description' => '单位：分钟。默认：60',
            ],
        ]);
        //运营参数配置
        $operation_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '运营参数',
            'key' => 'operation',
            'value' => '1',
            'description' => '',
        ]);

        DB::table('config')->insert([
            [
                'parent_id' => $operation_parent_id,
                'title' => '是否开启单点登录',
                'key' => 'op_sso',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '管理员单笔撤单最多时间（以录入号码时间为准，单位:分钟）',
                'key' => 'admin_cancel_limit',
                'value' => '1200',
                'description' => '公司管理员后台进行单笔撤单时在以录入号码时间为准，多少时间范围内允许撤单',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '充提高额变色提醒',
                'key' => 'op_alert_amount',
                'value' => '30000',
                'description' => '充提高额变色提醒（元）',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '是否自动扣除单期超额奖金',
                'key' => 'op_auto_deduct_bouns',
                'value' => '1',
                'description' => '是否自动扣除单期超额奖金',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '单挑中奖比例（投注金额:中奖金额）',
                'key' => 'op_bonus_rate',
                'value' => '0.05',
                'description' => '单挑中奖比例（投注金额:中奖金额）',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '单挑最多派奖金额（小于比例后的派奖金额）',
                'key' => 'op_bonus_rate_value',
                'value' => '20000',
                'description' => '单挑最多派奖金额（小于比例后的派奖金额）',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '高频彩最高奖金',
                'key' => 'high_limit_bonus',
                'value' => '300000',
                'description' => '高频彩最高奖金（元）',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '低频彩最高奖金',
                'key' => 'low_limit_bonus',
                'value' => '150000',
                'description' => '低频彩最高奖金（元）',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '特定限额彩种ident',
                'key' => 'assign_high_limit_lottery_ident',
                'value' => '',
                'description' => '多个彩种ident使用英文逗号,分割',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '特定限额彩种ident最高奖金',
                'key' => 'assign_high_limit_bonus',
                'value' => '300000',
                'description' => '特定限额彩种ident最高奖金（元），若设置为空或0，使用高频彩最高奖金（元）',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '总代到一级代理的最小彩票返点差',
                'key' => 'lottery_rebate_top_min_diff',
                'value' => '0',
                'description' => '输入正数或0，例如：输入1表示返点差为1%',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '各级代理间的最小彩票返点差(总代除外)',
                'key' => 'lottery_rebate_agent_min_diff',
                'value' => '0',
                'description' => '输入正数或0，例如：输入1表示返点差为1%',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '设置彩票返点滑条的最小刻度',
                'key' => 'operation_lottery_rebate_min_scale',
                'value' => '0.1',
                'description' => '该参数将影响所有彩票的返水滑条',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '设置第三方游戏返点滑条的最小刻度',
                'key' => 'operation_third_rebate_min_scale',
                'value' => '0.1',
                'description' => '该参数将影响所有第三方游戏的返水滑条',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '是否开启自动生成奖期',
                'key' => 'auto_generate_issue',
                'value' => '1',
                'description' => '1开启0关闭，每天凌晨5点执行（香港六合彩秒秒彩除外）',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => 'h5端热门彩种',
                'key' => 'op_h5_hot_lottery',
                'value' => '重庆时时彩|cqssc,乐利时时彩|llssc|/live/leli/120,北京PK10|bjpk10,PC蛋蛋|pcdd,山东11选5|sd11x5,香港六合彩|xglhc,江苏快三|jsk3,北京快乐8|bjkl8,湖南快乐10分|hnkls',
                'description' => '格式 彩种名称|彩种标识 多个用,号隔开',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '用户单注最低投注金额',
                'key' => 'op_user_sigle_bet_min',
                'value' => '0',
                'description' => '',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '用户单注最搞投注金额',
                'key' => 'op_user_sigle_bet_max',
                'value' => '100000',
                'description' => '',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '用户单期最搞投注金额',
                'key' => 'op_user_issue_bet_max',
                'value' => '200000',
                'description' => '',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '是否禁止用户撤单',
                'key' => 'is_forbidden_cancel_project',
                'value' => '0',
                'description' => '1禁止,0不禁止',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '是否禁止用户追号终止操作',
                'key' => 'is_forbidden_cancel_task',
                'value' => '0',
                'description' => '1禁止,0不禁止',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '是否显示禁用玩法',
                'key' => 'show_deny_method',
                'value' => '0',
                'description' => '1禁止,0不禁止',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '跨级充值是否验证密保问题',
                'key' => 'op_check_user_security_question',
                'value' => '0',
                'description' => '1验证,0不验证',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '跨级充值最小充值金额',
                'key' => 'team_sub_recharge_min',
                'value' => '10',
                'description' => '',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '跨级充值是否开启验证码',
                'key' => 'op_check_user_captcha',
                'value' => '0',
                'description' => '1验证,0不验证',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '腾讯分分彩抓不到号码自动撤单',
                'key' => 'op_auto_cancel_issue',
                'value' => '0',
                'description' => '0关闭1开启。说明不开启如果抓不到号码请自行录入号码',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '彩票分红报表(实时版本)查询天数限制',
                'key' => 'lottery_bonus_realtime_day_limit',
                'value' => '31',
                'description' => '报表查询时间范围限制，按天计算，默认【31】天',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '用户必须绑定银行卡后才能进行投注',
                'key' => 'op_must_bind_bankcard',
                'value' => '1',
                'description' => '1是,0否',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => 'pk10彩种冠亚和值大小单双开11通杀(彩种id)',
                'key' => 'pk10_guanyahezhi_dxds_11tongsha_lottery_ids',
                'value' => '',
                'description' => '多个彩种id使用英文逗号,分割',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '前台报表查询默认时间(小时)',
                'key' => 'front_default_search_time',
                'value' => '0',
                'description' => '0-23小时',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '用户必须绑定银行卡后才能进行充值',
                'key' => 'op_must_bind_bankcard_deposit',
                'value' => '1',
                'description' => '1.开启 0关闭',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '团队预览中有效人数最小销量',
                'key' => 'op_teamview_sale_min',
                'value' => '500',
                'description' => '至少达到所填数值的销量才能算1个有效人数。',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '前台报表可搜索天数',
                'key' => 'report_limit_days',
                'value' => '90',
                'description' => '前台所有报表可以往前搜索的天数。默认：90',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '时时彩龙虎和玩法的『和』是否当做单挑',
                'key' => 'ssc_lhh_single',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '修改密码强制是否登出',
                'key' => 'op_change_password_to_force_logout',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '投注服务费比例',
                'key' => 'project_fee_rate',
                'value' => '0',
                'description' => '取值必须在0至1之间的4位小数，0则关闭服务费',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '投注服务费是否隐藏',
                'key' => 'project_fee_hidden',
                'value' => '0',
                'description' => '值为1则隐藏投注服务费显示',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '投注服务费限制彩种',
                'key' => 'project_fee_lottery_ident',
                'value' => '',
                'description' => '彩种标识,若有多个彩种则使用英文,隔开。不填写则全彩种皆抽取服务费。默认:空',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '启用上下级转账通知',
                'key' => 'transfer_notification',
                'value' => '0',
                'description' => '默认:0不开启，1开启',
            ],
            [
                'parent_id' => $operation_parent_id,
                'title' => '是否显示彩种介绍',
                'key' => 'lotteryintroduce_display',
                'value' => '0',
                'description' => '0:隐藏彩种介绍，1:显示彩种介绍。默认0',
            ],
        ]);

        DB::table('config')->insert(
            [
                'parent_id' => $operation_parent_id,
                'title' => '新开用户是否默认开启下级充值权限',
                'key' => 'sub_recharge_status',
                'value' => '0',
                'input_type' => '1',
                'input_option' => '0|关闭,1|允许直属下级,2|允许所有下级',
                'description' => '0-关闭,1-允许直属下级,2-允许所有下级',
            ]
        );

        //第三方游戏配置
        $third_game_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '第三方游戏',
            'key' => 'third_game',
            'value' => '1',
            'description' => '',
        ]);

        DB::table('config')->insert([
            [
                'parent_id' => $third_game_parent_id,
                'title' => '用户名前缀',
                'key' => 'third_game_account_prefix',
                'value' => '',
                'description' => '第三方登入用户名将自动添加此前缀',
            ],
        ]);

        DB::table('config')->insert([
            [
                'parent_id' => $third_game_parent_id,
                'title' => '固定返点树[倒序]',
                'key' => 'third_game_rebate_tree',
                'value' => '',
                'description' => '第三方倒序固定返点用户树, 自身0.005,上级0.002, 上上级0.002, 上上上级0.002 例如 {"live": [0.005, 0.002, 0.002, 0.002],"sport": [0.003, 0.001, 0.001, 0.001]} ',
            ], [
                'parent_id' => $third_game_parent_id,
                'title' => '固定返点树[正序]',
                'key' => 'third_game_rebate_tree_asc',
                'value' => '',
                'description' => '本配置为正序固定返点，会覆盖之前的固定返点，例如固定返点上上级是0.002，而上上级是2代 0.001，那么这里的2代返点会覆盖之前的上上级返点。 {"live":{"lv_2":0.001},"sport":{"lv_2":0.001}}',
            ], [
                'parent_id' => $third_game_parent_id,
                'title' => '固定返点树最高层级',
                'key' => 'third_game_rebate_tree_max_level',
                'value' => 0,
                'description' => '如果设置为2，则最高只发到2级代理，总代1代不会发放固定返点',
            ],
        ]);

        DB::table('config')->insert([
            [
                'parent_id' => $third_game_parent_id,
                'title' => '总代最大返点值',
                'key' => 'third_game_rebate_limit',
                'value' => '0.012',
                'description' => '总代最大返点值',
            ],
        ]);

        DB::table('config')->insert([
            [
                'parent_id' => $third_game_parent_id,
                'title' => '返点类型',
                'key' => 'third_game_rebate_type',
                'value' => '0',
                'description' => '返点类型 0-每个第三方使用独立的返点设置，1-第三方除特殊指定，都使用Third一个返点设置',
            ],
        ]);

        //客户端下载
        $mobile_client_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '客户端',
            'key' => 'client',
            'value' => '1',
            'description' => '',
        ]);

        DB::table('config')->insert([
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => 'Android客户端版本号',
                'key' => 'android_version',
                'value' => '1.0.0',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => 'Android客户端下载URL',
                'key' => 'android_url',
                'value' => 'https://aapp.ph/demo/android.html',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => 'IOS客户端版本号',
                'key' => 'ios_version',
                'value' => '1.0.0',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => 'IOS客户端下载URL',
                'key' => 'ios_url',
                'value' => 'https://aapp.ph/demo/ios.html',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '计划软件 PC电脑版',
                'key' => 'plan_pc_url',
                'value' => 'https://aapp.ph/demo/plan-pc.html',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '计划软件 IOS版',
                'key' => 'plan_ios_url',
                'value' => 'https://aapp.ph/demo/plan-ios.html',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '计划软件 Android版',
                'key' => 'plan_android_url',
                'value' => 'https://aapp.ph/demo/plan-android.html',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '挂机软件 PC电脑版',
                'key' => 'guaji_pc_url',
                'value' => 'https://aapp.ph/demo/guaji-pc.html',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '挂机软件 IOS版',
                'key' => 'guaji_ios_url',
                'value' => 'https://aapp.ph/demo/guaji-ios.html',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '挂机软件 Android版',
                'key' => 'guaji_android_url',
                'value' => 'https://aapp.ph/demo/guaji-android.html',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '币安钱包下载连结',
                'key' => 'bi_mobile_url',
                'value' => 'https://www.binance.com/cn/download',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '谷歌验证器 使用帮助',
                'key' => 'google_help_url',
                'value' => 'https://support.google.com/accounts/answer/1066447',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '谷歌验证器 Android版',
                'key' => 'google_android_url',
                'value' => 'https://shouji.baidu.com/software/22417419.html',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '谷歌验证器 IOS版',
                'key' => 'google_ios_url',
                'value' => 'https://itunes.apple.com/cn/app/google-authenticator/id388497605?mt=8',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '云挂机软件登陆地址',
                'key' => 'cloud_plan',
                'value' => 'http://guaji.ruanjian.com/api/third-login',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '云挂机加密salt',
                'key' => 'cloud_plan_salt',
                'value' => 'ae125efkkeff444ferfkny6oxi8',
                'description' => '',
            ],
            [
                'parent_id' => $mobile_client_parent_id,
                'title' => '云挂机商户平台名称',
                'key' => 'cloud_plan_platform',
                'value' => 'APOLlO',
                'description' => '',
            ],

        ]);
        //日工资配置
        $daily_wage_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '日工资配置',
            'key' => 'dailywage_config',
            'value' => '1',
            'description' => '',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '是否启用日工资',
                'key' => 'dailywage_available',
                'value' => '0',
                'description' => '0-不启用，1-启用。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '是否启用多线工资模式',
                'key' => 'wage_line_multi_available',
                'value' => '0',
                'description' => '0-不启用，1-启用。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '最大日工资比例',
                'key' => 'dailywage_limit',
                'value' => '2',
                'description' => '填入数字，代表全局系统能接受的最大日工资比例。设置时填入数字或拉杆需小于等于最大值（不要填写百分号）。默认『2』%',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '日工资比例步长',
                'key' => 'dailywage_step',
                'value' => '0.1',
                'description' => '选择 0.1％或 0.01％，代表全局设置日工资最小区间，影响比例显示的小数点第几位、调整比例拉杆的最小刻度。',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '日工资比例可平级',
                'key' => 'dailywage_same_level',
                'value' => '0',
                'description' => '0-可平级, 1-不可平级。代表上下级的最大日奖比例可否一样高。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '日工资比例交错',
                'key' => 'dailywage_cross_level',
                'value' => '1',
                'description' => '0-否,1-是。代表自身最大日奖比例不得高于上级最小日奖比例。1-是，则不受上述限制.默认『1』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '日工资对应销量取整单位',
                'key' => 'dailywage_rank_unit',
                'value' => '10000',
                'description' => '例如 万元取整，销量 12345，日奖比例 1.2%，日奖为 120 元',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '检查活跃人数',
                'key' => 'dailywage_check_user',
                'value' => '0',
                'description' => '0-是,1-否。代表是否需要采计这个达标条件。如果否，则前后台设置契约时，不显示活跃人数栏位。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '有效人数最小销量',
                'key' => 'dailywage_sale_min',
                'value' => '500',
                'description' => '至少达到所填数值的销量才能算1个有效人数。',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '最大拒绝派发层级',
                'key' => 'dailywage_pay_level_max',
                'value' => '0',
                'description' => '如填入 2 代表，2 级代理(含)以上无论有无契约，皆不派发日工资。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '前台不可编辑契约最大层级',
                'key' => 'dailywage_edit_level_max',
                'value' => '1',
                'description' => '如填入 2 代表，2 级代理(含)以上，上级不得从前台编辑该用户日工资。默认『1』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '是否扣减团队给上级的返点',
                'key' => 'dailywage_minus_uplevel_rebate',
                'value' => '0',
                'description' => '0-不扣减，1-扣减。计算日工资销量前是否先扣除团队给上级的返点。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '投注销量是否需要计算VR销量',
                'key' => 'dailywage_bet_include_vr',
                'value' => '0',
                'description' => '0-不计算，1-计算。投注销量是否需要计算VR销量。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '是否自动审核',
                'key' => 'dailywage_auto_audit',
                'value' => '0',
                'description' => '0-需要人工后台审核，1-不需要审核，计算后自动变成已审核。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '是否自动发放',
                'key' => 'dailywage_auto_send',
                'value' => '0',
                'description' => '0-关闭发放，1-计算后自动发放已审核记录。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '默认工资类型',
                'key' => 'dailywage_default_type',
                'value' => '',
                'description' => '1-日工资（2点统一结算前一天投注），2-实时工资（实时结算每一注工资），3-小时工资（每小时结算一次） 。默认『1』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '小时工资中单最高比例',
                'key' => 'dailywage_max_win_rate',
                'value' => '1',
                'description' => '中奖注单最高工资比例，默认 1%',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '小时工资挂单最高比例',
                'key' => 'dailywage_max_loss_rate',
                'value' => '3',
                'description' => '不中奖注单最高工资比例，默认 3%',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '时薪单挑率配置',
                'key' => 'hourly_wage_challenge_rete',
                'value' => '25',
                'description' => '1%-100%,百分比',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '实时工资最大比例限制',
                'key' => 'realtime_wage_max_rate',
                'value' => '2.1',
                'description' => '1%-100%,百分比',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '浮动工资是否开启自动发放',
                'key' => 'float_wage_auto_audit',
                'value' => '0',
                'description' => '0-关闭发放，1-计算后自动发放已审核记录。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '日工资类型路径标识',
                'key' => 'dailywage_type_ident',
                'value' => '',
                'description' => '标记当前日工资使用的类所在目录，例如Feiyu，代表日工资使用Service\DailyWage\Feiyu这个目录的类',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '日工资是否检查盈亏',
                'key' => 'dailywage_check_profit',
                'value' => '0',
                'description' => '0-不检查盈亏，1-检查盈亏。默认『0』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '日工资是否允许同销量不同工资比例',
                'key' => 'dailywage_same_rule',
                'value' => '1',
                'description' => '0-允许，1-不允许。默认『1』',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '用户角色会员是否禁止签订日工资',
                'key' => 'forbid_member_wage_sign',
                'value' => '0',
                'description' => '0允许，1禁止',
            ],
            [
                'parent_id' => $daily_wage_parent_id,
                'title' => '奖期工资开始计算时间',
                'key' => 'issue_wage_calculate_start',
                'value' => date('Y-m-d 00:00:00'),
                'description' => '格式YYYY-MM-DD HH:II:SS，例如‘' . date('Y-m-d 00:00:00') . '’，从什么时候开始统计时间，哪个奖期包含这个时间，就从哪个奖期开始算',
            ],
        ]);
        //分红契约配置
        $dividend_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '契约分红配置',
            'key' => 'dividend_config',
            'value' => '1',
            'description' => '',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $dividend_parent_id,
                'title' => '是否启用契约分红',
                'key' => 'dividend_available',
                'value' => '0',
                'description' => '0-不启用，1-启用。默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '最大分红比例',
                'key' => 'dividend_limit',
                'value' => '30',
                'description' => '填入数字，代表全局系统能接受的最大分红比例。设置时填入数字或拉杆需小于等于最大值（不要填写百分号）。默认『2』%',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '分红比例步长',
                'key' => 'dividend_step',
                'value' => '0.1',
                'description' => '选择 0.1％或 0.01％，代表全局设置分红最小区间，影响比例显示的小数点第几位、调整比例拉杆的最小刻度。',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '分红比例可平级',
                'key' => 'dividend_same_level',
                'value' => '1',
                'description' => '0-可平级, 1-不可平级。代表上下级的最大分红比例可否一样高。默认『1』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '分红比例可交错',
                'key' => 'dividend_cross_level',
                'value' => '1',
                'description' => '0-否, 1-是。代表自身最大分红比例不得高于上级最小分红比例。1-是，则不受上述限制.默认『1』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '后台分红多阶梯条件设置',
                'key' => 'dividend_backend_multi_level',
                'value' => '1',
                'description' => '0-否, 1-是。如果是，则可以设置多个阶梯条件。默认『1』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '前台分红多阶梯条件设置',
                'key' => 'dividend_front_multi_level',
                'value' => '1',
                'description' => '0-否, 1-是。如果是，则可以设置多个阶梯条件。默认『1』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '检查活跃人数',
                'key' => 'dividend_check_user',
                'value' => '1',
                'description' => '0-否,1-是。代表是否需要采计这个达标条件。如果否，则前后台设置契约时，不显示活跃人数栏位。默认『1』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '有效人数最小销量',
                'key' => 'dividend_sale_min',
                'value' => '1000',
                'description' => '至少达到所填数值的销量才能算1个有效人数。',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '是否检查契约销量比例',
                'key' => 'dividend_check_consume_amount',
                'value' => '1',
                'description' => '0-否,1-是。默认『1』',
            ], [
                'parent_id' => $dividend_parent_id,
                'title' => '是否检查契约有效人数',
                'key' => 'dividend_check_daus',
                'value' => '1',
                'description' => '0-否,1-是。代表是否需要采计这个达标条件。如果否，则前后台设置契约时，不显示活跃人数栏位。默认『1』',
            ], [
                'parent_id' => $dividend_parent_id,
                'title' => '是否检查契约盈亏',
                'key' => 'dividend_check_profit',
                'value' => '1',
                'description' => '0-否,1-是。代表是否需要采计这个达标条件。如果否，则前后台设置契约时，不显示活跃人数栏位。默认『1』',
            ],
            /*
            [
                'parent_id' => $dividend_parent_id,
                'title' => '是否扣减团队给上级的返点',
                'key' => 'dividend_minus_uplevel_rebate',
                'value' => '0',
                'description' => '0-不扣减，1-扣减。计算分红盈亏前是否先扣除团队给上级的返点。默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '投注销量是否需要计算VR销量',
                'key' => 'dividend_bet_include_vr',
                'value' => '0',
                'description' => '0-不计算，1-计算。投注销量是否需要计算VR销量。默认『0』',
            ],
            */
            // ：
            [
                'parent_id' => $dividend_parent_id,
                'title' => '需要加入分红结算的产品',
                'key' => 'dividend_calculate_game',
                'value' => '彩票',
                'description' => ' 默认『彩票』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '平台系统派发层级的派发方式',
                'key' => 'dividend_auto_send',
                'value' => '0',
                'description' => '0-需要人工后台审核后才能发放，1-不需要审核，计算后自动发放。默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '平台非系统派发层级的派发方式',
                'key' => 'dividend_auto_send_regular',
                'value' => '0',
                'description' => '0-需要上级前台审核后才能发放”，1-不需要审核，计算后自动发放，后台分红审核包括“上级审核”状态。默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '平台承担的最高派发层级',
                'key' => 'dividend_send_high_level',
                'value' => '2',
                'description' => '0-代表总代帐号皆能获得平台派发的分红。默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '平台承担的最低派发层级',
                'key' => 'dividend_send_low_level',
                'value' => '2',
                'description' => '0-代表总代帐号皆能获得平台派发的分红。默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => 'A线[佣金模式]默认契约',
                'key' => 'dividend_type_a_default_content',
                'value' => '',
                'description' => '功能未实现',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => 'B线[分红模式]总代默认契约',
                'key' => 'dividend_default_content_0',
                'value' => '0-5-0-28|0-20-0-30|0-50-0-33|0-100-0-35|0-200-0-38|0-300-0-40',
                'description' => '格式：周期累计亏损-活跃人数-分红比率|消费量-周期累计亏损-活跃人数-分红比率,使用『|』分隔，示例：0-50-0-2|0-100-0-3|0-200-0-4|0-500-0-5',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => 'B线[分红模式]一代默认契约',
                'key' => 'dividend_default_content_1',
                'value' => '',
                'description' => '格式：周期累计亏损-活跃人数-分红比率|消费量-周期累计亏损-活跃人数-分红比率,使用『|』分隔，示例：0-50-0-2|0-100-0-3|0-200-0-4|0-500-0-5',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '总代结算周期',
                'key' => 'dividend_default_interval_0',
                'value' => '3',
                'description' => '可作为总代契约开关 1:日结 2:半月结 3:月结 4:周结 默认『3』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '一代结算周期',
                'key' => 'dividend_default_interval_1',
                'value' => '2',
                'description' => '可作为一代契约开关 1:日结 2:半月结 3:月结 4:周结 默认『2』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '代理结算周期',
                'key' => 'dividend_default_interval_agency',
                'value' => '2',
                'description' => '可作为代理契约开关 1:日结 2:半月结 3:月结 4:周结 默认『2』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '后台分红模式设置显示',
                'key' => 'dividend_backend_show_mode',
                'value' => '1',
                'description' => '0-不显示 1-显示  默认『1』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '后台分红模式开放编辑',
                'key' => 'dividend_backend_mode_canedit',
                'value' => '0',
                'description' => '0-不可编辑 1-可编辑  默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '前台分红模式设置显示',
                'key' => 'dividend_front_show_mode',
                'value' => '0',
                'description' => '0-不显示 1-显示  默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '前台分红模式开放编辑',
                'key' => 'dividend_front_mode_canedit',
                'value' => '0',
                'description' => '0-不可编辑 1-可编辑  默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '前台可编辑分红契约的最高层',
                'key' => 'dividend_front_canedit_max_level',
                'value' => '0',
                'description' => '填入0，代表总代(含)以下帐号皆能编辑直屬下级分红契约 默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '前台可编辑分红契约的最低层',
                'key' => 'dividend_front_canedit_min_level',
                'value' => '20',
                'description' => '填入20，代表20級代理(含)以上帐号皆能编辑直屬下级分红契约 默认『20』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '前台派发分红开启',
                'key' => 'dividend_front_send_enable',
                'value' => '1',
                'description' => '0-关闭 1-开启，选择是，代表自身有分红契约才会显示，並能執行派發動作。选择否，代表无论自身有无分红都不显示，且不能執行派發動作。默认『1』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '前台可派发分红契约的最高层级',
                'key' => 'dividend_front_cansend_max_level',
                'value' => '0',
                'description' => '填入0，代表总代(含)以下帐号皆能派發直屬下级分红契约 默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '前台可派发分红契约的最低层级',
                'key' => 'dividend_front_cansend_min_level',
                'value' => '20',
                'description' => '填入20，代表20級代理(含)以上帐号皆能派發直屬下级分红契约 默认『20』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '前台契约生效需要下级确认',
                'key' => 'dividend_front_need_child_confirm',
                'value' => '1',
                'description' => '0-不需要 1-需要 默认『1』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '默认分红类型',
                'key' => 'dividend_default_type',
                'value' => '2',
                'description' => '分红类型：1-A线[佣金模式]  2-B线[比例模式] 默认『2』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => 'A线[佣金模式]是否运行',
                'key' => 'dividend_type_a_run',
                'value' => '0',
                'description' => '0-不启用，1-启用。默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => 'B线[分红模式]是否运行',
                'key' => 'dividend_type_b_run',
                'value' => '1',
                'description' => '0-不启用，1-启用。默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '分红类型路径标识',
                'key' => 'dividend_type_ident',
                'value' => '',
                'description' => '标记当前分红使用的类所在目录，例如Feiyu，代表分红使用Service\Dividend\Feiyu这个目录的类',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '盈亏报表是否计算分红',
                'key' => 'dividend_to_report',
                'value' => '1',
                'description' => '盈亏报表是否计算分红。0-不显示在盈亏报表，1-显示在盈亏报表',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => '盈亏报表是否计算前期分红',
                'key' => 'dividend_last_amount_to_report',
                'value' => '0',
                'description' => '盈亏报表是否计算前期分红。0-不显示在盈亏报表，1-显示在盈亏报表',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => 'B线[分红模式]是否启用代理默认契约',
                'key' => 'dividend_default_content_agency_enable',
                'value' => '0',
                'description' => '0-不启用，1-启用。默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => 'B线[分红模式]使用默认契约最大层级',
                'key' => 'dividend_default_content_max_level',
                'value' => '0',
                'description' => '填入0，代表总代(含)以下帐号皆能派發直屬下级分红契约 默认『0』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => 'B线[分红模式]使用默认契约最小层级',
                'key' => 'dividend_default_content_min_level',
                'value' => '20',
                'description' => '填入20，代表20級代理(含)以上帐号皆能编辑直屬下级分红契约 默认『20』',
            ],
            [
                'parent_id' => $dividend_parent_id,
                'title' => 'B线[分红模式]代理默认契约',
                'key' => 'dividend_default_content_agency',
                'value' => '0-300-0-40|0-200-0-38|0-100-0-35|0-50-0-33|0-20-0-30|0-5-0-28',
                'description' => '格式：周期累计亏损-活跃人数-分红比率|消费量-周期累计亏损-活跃人数-分红比率,使用『|』分隔，示例：0-500-0-5|0-200-0-4|0-100-0-3|0-50-0-2',
            ],
        ]);
        //后台管理配置
        $admin_config_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '管理后台设置',
            'key' => 'admin_config',
            'value' => '1',
            'description' => '',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '是否强制动态验证码',
                'key' => 'admin_google_key',
                'value' => '0',
                'description' => '',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '后台IP白名单开关',
                'key' => 'admin_ip_whitelist_switch',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '可见银行卡账号的管理员id',
                'key' => 'visible_bank_account_adminids',
                'value' => '',
                'description' => '多个id使用英文逗号,分隔',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '可见银行卡姓名的管理员id',
                'key' => 'visible_bank_account_name_adminids',
                'value' => '',
                'description' => '多个id使用英文逗号,分隔',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '可见用户手机号码的管理员id',
                'key' => 'visible_telephone_adminids',
                'value' => '',
                'description' => '多个id使用英文逗号,分隔',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '可见用户email的管理员id',
                'key' => 'visible_email_adminids',
                'value' => '',
                'description' => '多个id使用英文逗号,分隔',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '可见用户微信的管理员id',
                'key' => 'visible_weixin_adminids',
                'value' => '',
                'description' => '多个id使用英文逗号,分隔',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '可见用户QQ的管理员id',
                'key' => 'visible_qq_adminids',
                'value' => '',
                'description' => '多个id使用英文逗号,分隔',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '默认查询时间点',
                'key' => 'default_search_time',
                'value' => '0',
                'description' => '0-23',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '自主彩手工录入号码',
                'key' => 'self_lottery_manual_input',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $admin_config_parent_id,
                'title' => '彩票分红报表查询开启团队类型条件',
                'key' => 'report_lottery_bonus_team_enable',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
        ]);
        //奖源相关配置
        $drawsource_config_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '奖源相关配置',
            'key' => 'drawsource_config',
            'value' => '1',
            'description' => '',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'Apollo奖源请求key',
                'key' => 'drawsource_apollo_key',
                'value' => '',
                'description' => '',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '凤凰奖源请求key',
                'key' => 'drawsource_fhlm_key',
                'value' => '',
                'description' => '',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'Apollo推送开关',
                'key' => 'pushdraw_apollo_switch',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'Apollo推送密钥',
                'key' => 'pushdraw_apollo_key',
                'value' => '',
                'description' => '',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'Apollo推送IP白名单',
                'key' => 'pushdraw_apollo_ips',
                'value' => '',
                'description' => '多个IP以英文逗号分隔',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '推送【开错号码】开关',
                'key' => 'pushdraw_auto_modify_switch',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '推送【官方未开奖】开关',
                'key' => 'pushdraw_auto_cancel_switch',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '凤凰推送密钥',
                'key' => 'pushdraw_fenghuang_key',
                'value' => '',
                'description' => '',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '凤凰推送IP白名单',
                'key' => 'pushdraw_fenghuang_ips',
                'value' => '',
                'description' => '多个IP以英文逗号分隔',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '聚合开奖中心推送开关',
                'key' => 'pushdraw_kaijiang_switch',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '聚合开奖中心推送密钥',
                'key' => 'pushdraw_kaijiang_key',
                'value' => '',
                'description' => '',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '聚合开奖中心推送IP白名单',
                'key' => 'pushdraw_kaijiang_ips',
                'value' => '',
                'description' => '多个IP以英文逗号分隔',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '加拿大快乐8 playnow 奖源代理',
                'key' => 'draw_playnow_use_proxy',
                'value' => '',
                'description' => '中间站地址',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '接收通用推送开关',
                'key' => 'pushthing_receiver_switch',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '联合奖源推送代理做号地址',
                'key' => 'henei_proxy_url',
                'value' => 'http://中间站域名/proxy/query',
                'description' => '中间站地址',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '开启联合奖源彩种演算推送',
                'key' => 'henei_codes_enabled',
                'value' => '0',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '联合奖源彩种必杀用户名',
                'key' => 'henei_kill_users',
                'value' => '',
                'description' => '多个用户名使用英文逗号,分隔',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'UNAI奖源开关',
                'key' => 'win_codes_enabled',
                'value' => '1',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'UNAI奖源来源IP白名单',
                'key' => 'win_codes_ip_whitelist',
                'value' => '13.75.122.38',
                'description' => '多个IP使用英文逗号,分割',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'UNAI奖源发送代理地址',
                'key' => 'win_codes_proxy_url',
                'value' => '',
                'description' => '留空不使用代理。示例 http://中间站域名/proxy/query',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'UNAI奖源必杀用户名',
                'key' => 'win_codes_kill_users',
                'value' => '',
                'description' => '多个用户名使用英文逗号,分隔',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'UNAI奖源需要CLI多线程处理的彩种',
                'key' => 'win_codes_cli',
                'value' => '',
                'description' => '这里为UNAI彩种标识，多个标识请用,分隔',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '杀号日志自动清理随机几率',
                'key' => 'kill_code_log_clear_odds',
                'value' => '60',
                'description' => '每次杀号任务完成后清理日志的几率，区间为0~100，默认60，0不清理，大于等于100则百分百清理',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'Win2版本杀号推送地址',
                'key' => 'winkill_version2_url',
                'value' => 'https://splitnumber.kjw2.cc',
                'description' => '示例: https://splitnumber.kjw2.cc',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'Win2版本杀号APPID',
                'key' => 'winkill_version2_appid',
                'value' => 'SbQxQwti',
                'description' => '示例: SbQxQwti',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => 'Win2版本杀号APPKEY',
                'key' => 'winkill_version2_appkey',
                'value' => 'cf8d5df6d4c86d62416222f3a804c01a4a01af43',
                'description' => '示例: cf8d5df6d4c86d62416222f3a804c01a4a01af43',
            ],
            /*
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '聚合奖源预开号码接收推送开关',
                'key' => 'kaijiang_codes_enabled',
                'value' => '1',
                'description' => '0|关闭,1|开启',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '聚合奖源预开号码代理',
                'key' => 'kaijiang_codes_proxy_host',
                'value' => '',
                'description' => '中间站域名 http://xxpay.abc.com。如果不使用，请留空',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '聚合奖源预开号码密钥',
                'key' => 'kaijiang_codes_key',
                'value' => '',
                'description' => '',
            ],
            [
                'parent_id' => $drawsource_config_parent_id,
                'title' => '聚合奖源预开号码推送IP白名单',
                'key' => 'kaijiang_codes_ips',
                'value' => '18.162.226.168',
                'description' => '多个IP使用英文逗号分隔',
            ],
            */

        ]);

        // 计划任务配置
        $crontab_config_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '计划任务设置',
            'key' => 'crontab_config',
            'value' => '1',
            'description' => '',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '日工资',
                'key' => 'crontab_DailyWageAll',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '日工资发放',
                'key' => 'crontab_DailyWageSend',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '实时工资[普通]发放',
                'key' => 'crontab_RealtimeWage',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '奖期工资[中挂单]发放',
                'key' => 'crontab_IssueWageA',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '小时工资[中挂单]计算',
                'key' => 'crontab_hourCalculate',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '小时工资[中挂单]发放',
                'key' => 'crontab_hourSend',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '小时工资[普通]计算',
                'key' => 'crontab_hourBCalculate',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '小时工资[普通]发放',
                'key' => 'crontab_hourBSend',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '主管工资计算',
                'key' => 'crontab_zhuguanWage',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '浮动工资计算',
                'key' => 'crontab_floatCalculate',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '浮动工资发放',
                'key' => 'crontab_floatSend',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '日私返计算',
                'key' => 'crontab_private_return_calculate_daily',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '小时私返计算',
                'key' => 'crontab_private_return_calculate_hourly',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $crontab_config_parent_id,
                'title' => '私返发放',
                'key' => 'crontab_private_return_send',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
        ]);

        //红包配
        $coupon_config_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '红包雨配置',
            'key' => 'coupon',
            'value' => '#',
            'description' => '',
        ]);
        DB::table('config')->insert([
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '是否开启',
                'key' => 'coupon_enabled',
                'value' => '0',
                'description' => '关闭这前台领不了红包',
            ],
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '是否开启小时定时红包',
                'key' => 'hourly_coupon_enabled',
                'value' => '0',
                'description' => '1：计划任务启用 0：计划任务关闭',
            ],
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '领取红包充值金额',
                'key' => 'coupon_limit_deposit',
                'value' => '0',
                'description' => '0为不限制',
            ],
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '领取红包充值金额是否统计昨天',
                'key' => 'coupon_limit_deposit_is_yesterday',
                'value' => '1',
                'description' => '1昨天，0今天',
            ],
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '领取红包投注金额（汇总）',
                'key' => 'coupon_limit_bet',
                'value' => '0',
                'description' => '0为不限制',
            ],
            [
                'parent_id' => $coupon_config_parent_id,
                'title' => '领取红包投注金额（汇总）是否统计昨天',
                'key' => 'coupon_limit_bet_is_yesterday',
                'value' => '1',
                'description' => '1昨天，0今天',
            ]
        ]);
        //私返配置
        $private_return_config_parent_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '私返配置',
            'key' => 'private_return',
            'value' => '#',
            'description' => '',
        ]);
        DB::table('config')->insert([[
            'parent_id' => $private_return_config_parent_id,
            'title' => '是否开启',
            'key' => 'private_return_enabled',
            'value' => '0',
            'description' => '私返功能开关，1：开启 0：关闭',
        ], [
            'parent_id' => $private_return_config_parent_id,
            'title' => '是否开启计算',
            'key' => 'private_return_calculate',
            'value' => '0',
            'description' => '私返计算开关，1：开启 0：关闭',
        ], [
            'parent_id' => $private_return_config_parent_id,
            'title' => '是否开启发放',
            'key' => 'private_return_send',
            'value' => '0',
            'description' => '私返发放开关，1：开启 0：关闭',
        ], [
            'parent_id' => $private_return_config_parent_id,
            'title' => '私返路径标识',
            'key' => 'private_return_type_ident',
            'value' => '',
            'description' => '标记当私返使用的类所在目录，例如Haicai，代表日工资使用Service\PrivateReturn\Haicai这个目录的类',
        ], [
            'parent_id' => $private_return_config_parent_id,
            'title' => '私返比例最大值',
            'key' => 'private_return_rate_max',
            'value' => '0.05',
            'description' => '填入数字，1代表1%，默认：0.05%。限制私返契约最大比例上限',
        ], [
            'parent_id' => $private_return_config_parent_id,
            'title' => '私返比例步长',
            'key' => 'private_return_rate_step',
            'value' => '0.01',
            'description' => '填入数字，0.1代表契约比例可调整最小间距为0.1%，默认：0.01%',
        ], [
            'parent_id' => $private_return_config_parent_id,
            'title' => '私返基数取整单位',
            'key' => 'private_return_rank_unit',
            'value' => '10000',
            'description' => '填入数字，10000代表万元取整，默认：10000。万元取整时，团队销量为19800时计算为10000',
        ], [
            'parent_id' => $private_return_config_parent_id,
            'title' => '私返是否扣减团队给上级的返点',
            'key' => 'private_return_sub_uplevel_rebate',
            'value' => '0',
            'description' => '0:不扣除，1:扣除，默认：0。私返团队销量扣除自身及下级提供的返点',
        ], [
            'parent_id' => $private_return_config_parent_id,
            'title' => '前一日有效用户最低销售额',
            'key' => 'private_return_active_sale_min',
            'value' => '1000',
            'description' => '至少达到所填数值的销量才能算1个有效人数。',
        ]]);

        // 自动风控配置
        $risk_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '自动风控控制配置',
            'key' => 'auto_risk',
            'value' => '#',
            'description' => '用户提现自动风控审核',
        ]);

        DB::table('config')->insert([
            [
                'parent_id' => $risk_id,
                'title' => '自动风控开关',
                'key' => 'auto_risk_check',
                'value' => '0',
                'description' => '自动风控开关（默认0，0:关闭，1:启用）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '最大自动风控审核金额',
                'key' => 'risk_money_max',
                'value' => '0',
                'description' => '最大自动风控审核金额（默认：0 不进入）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '最小自动风控审核金额',
                'key' => 'risk_money_min',
                'value' => '0',
                'description' => '最小自动风控审核金额（默认：0 不进入）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => 'USDT最大自动风控审核金额',
                'key' => 'risk_usdt_max',
                'value' => '0',
                'description' => 'USDT最大自动风控审核金额（默认：0 不进入）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => 'USDT最小自动风控审核金额',
                'key' => 'risk_usdt_min',
                'value' => '0',
                'description' => 'USDT最小自动风控审核金额（默认：0 不进入）',
            ],
            // 退出自动风控流程条件：
            [
                'parent_id' => $risk_id,
                'title' => '投注比充值最大倍数门槛',
                'key' => 'risk_ctb_max',
                'value' => '1000',
                'description' => '投注比充值最大倍数门槛：（默认：1000%）－充投比需包含上级充值。审核时间：上次提现到本次期间',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '盈利比充值多的最大倍数',
                'key' => 'risk_cyb_max',
                'value' => '1000',
                'description' => '盈利比充值多的最大倍数（默认：1000%）。审核时间：上次提现到本次期间',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '异地登录检查天数',
                'key' => 'risk_login_day',
                'value' => '10',
                'description' => '异地登录检查天数（默认：10天）－含当天',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '异地登录退回人工',
                'key' => 'risk_login_check',
                'value' => '0',
                'description' => '异地登录退回人工 0-否，1-是（默认：否）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '首次提款退回人工',
                'key' => 'risk_first_withdrawal',
                'value' => '1',
                'description' => '首次提款退回人工 0-否，1-是（默认：是）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '存在解绑或删除银行卡的用户退回人工',
                'key' => 'risk_unbound',
                'value' => '1',
                'description' => '存在解绑或删除银行卡的用户退回人工 0-否，1-是（默认：是）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '最近曾变更资金密码退回人工',
                'key' => 'risk_security_password_check',
                'value' => '1',
                'description' => '最近曾变更资金密码退回人工 0-否，1-是（默认：是）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '变更资金密码检查天数',
                'key' => 'risk_security_password_day',
                'value' => '10',
                'description' => '变更资金密码检查天数（默认：10）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '单笔提现金额过高检查天数',
                'key' => 'risk_high_money_day',
                'value' => '1',
                'description' => '单笔提现金额过高检查天数（默认：1）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '单笔提现金额过高门槛金额',
                'key' => 'risk_high_money_value',
                'value' => '30000',
                'description' => '单笔提现金额过高门槛金额（默认：30000）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '当日高于盈利上限金额',
                'key' => 'risk_profit_max',
                'value' => '30000',
                'description' => '当日高于盈利上限金额（默认：30000）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '个人盈亏报表连续盈利天数',
                'key' => 'risk_profit_day',
                'value' => '7',
                'description' => '个人盈亏报表连续盈利天数（默认：7）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '累计提现金额过高检查天数',
                'key' => 'risk_withdrawal_money_day',
                'value' => '1',
                'description' => '累计提现金额过高检查天数（默认：1）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '累计提现金额过高门槛金额',
                'key' => 'risk_withdrawal_money_max',
                'value' => '30000',
                'description' => '累计提现金额过高门槛金额（默认：30000）',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '重点观察退回人工',
                'key' => 'risk_user_observe',
                'value' => '0',
                'description' => '用户列表帐号状态为重点观察时自动风控系统退回人工（默认0，0:关闭，1:启用）'
            ],
            [
                'parent_id' => $risk_id,
                'title' => '流水检查总开关',
                'key' => 'risk_user_consume_check',
                'value' => '0',
                'description' => '流水检查总开关（默认0，0:关闭，1:启用）'
            ],
            [
                'parent_id' => $risk_id,
                'title' => '彩票最低流水倍数要求',
                'key' => 'risk_user_consume',
                'value' => '0',
                'description' => '彩票最低流水倍数设定（默认0，设置0.3即30%）'
            ],
            [
                'parent_id' => $risk_id,
                'title' => '三方游戏最低流水倍数要求',
                'key' => 'risk_user_third_consume',
                'value' => '0',
                'description' => '三方游戏最低流水倍数要求（默认0，设置0.3即30%）'
            ],
            [
                'parent_id' => $risk_id,
                'title' => '转帐三方退回人工',
                'key' => 'risk_third_transfer',
                'value' => '0',
                'description' => '检查用户是否有转帐三方或是从三方转回，有转帐记录跳回人工审核（默认0，0:关闭，1:启用）'
            ]
        ]);

        // 自动风控配置
        $risk_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '自动风控通知配置',
            'key' => 'auto_risk_notice',
            'value' => '#',
            'description' => '自动风控通知配置',
        ]);

        DB::table('config')->insert([
            [
                'parent_id' => $risk_id,
                'title' => '是否启用后台推送通知',
                'key' => 'auto_risk_enabled',
                'value' => '0',
                'description' => '自动风控功能开关，1:开启，0:关闭。默认：0',
            ], [
                'parent_id' => $risk_id,
                'title' => '哪些风控提示类型开启弹窗',
                'key' => 'auto_risk_notice_toast',
                'value' => '0,1',
                'description' => '默认0,1，多个业务用,隔开。0高额中奖，1久未活跃用户投注，2重点观察用户上线，3今日登录数过高',
            ],
            [
                'parent_id' => $risk_id,
                'title' => '推播通知显示持续时间',
                'key' => 'auto_risk_show_duration',
                'value' => '5000',
                'description' => '单位：毫秒 默认：5000',
            ], [
                'parent_id' => $risk_id,
                'title' => '久未活跃用户投注通知',
                'key' => 'auto_risk_inactivity_bets_alert',
                'value' => '0',
                'description' => '1:开启，0:关闭。默认：0',
            ], [
                'parent_id' => $risk_id,
                'title' => '久未活跃用户投注前连续几日未投注',
                'key' => 'auto_risk_inactivity_bets_days',
                'value' => '15',
                'description' => '单位：天。默认：15',
            ], [
                'parent_id' => $risk_id,
                'title' => '久未活跃用户投注持有馀额上限',
                'key' => 'auto_risk_inactivity_bets_balance',
                'value' => '10000',
                'description' => '单位：元。默认：10000。投注前彩票馀额。',
            ], [
                'parent_id' => $risk_id,
                'title' => '重点观察用户上线通知',
                'key' => 'auto_risk_observe_login_alert',
                'value' => '0',
                'description' => '1:开启，0:关闭。默认：0',
            ], [
                'parent_id' => $risk_id,
                'title' => '今日登录用户数过高通知',
                'key' => 'auto_risk_logins_tomuch_alert',
                'value' => '0',
                'description' => '1:开启，0:关闭。默认：0',
            ], [
                'parent_id' => $risk_id,
                'title' => '今日登录用户数上限',
                'key' => 'auto_risk_logins_tomuch_count',
                'value' => '1000',
                'description' => '单位：人。默认：1000 或者 格式：1000|2000|3000',
            ], [
                'parent_id' => $risk_id,
                'title' => '高额中奖通知',
                'key' => 'auto_risk_high_bonus_alert',
                'value' => '0',
                'description' => '1:开启，0:关闭。默认：0',
            ], [
                'parent_id' => $risk_id,
                'title' => '高额中奖提醒金额',
                'key' => 'op_bonus_alert',
                'value' => '50000',
                'description' => '单位：元。默认：50000。',
            ]
        ]);


        //抽返水配置
        $pump_id = DB::table('config')->insertGetId([
            'parent_id' => 0,
            'title' => '抽返水配置',
            'key' => 'pump',
            'value' => '#',
            'description' => '抽返水配置',
        ]);

        DB::table('config')->insert([
            [
                'parent_id' => $pump_id,
                'title' => '抽水目录标识',
                'key' => 'pump_type_ident',
                'value' => '',
                'description' => '抽水目录名，无则使用默认模式',
            ],
            [
                'parent_id' => $pump_id,
                'title' => '是否计算抽水',
                'key' => 'pump_inlet_enabled',
                'value' => '0',
                'description' => '抽水开关，1:开启，0:关闭。默认：0',
            ],[
                'parent_id' => $pump_id,
                'title' => '是否扣除抽水',
                'key' => 'pump_inlet_order_enabled',
                'value' => '0',
                'description' => '扣除抽水开关，1:开启，0:关闭。默认：0',
            ],[
                'parent_id' => $pump_id,
                'title' => '是否计算返水',
                'key' => 'pump_outlet_enable',
                'value' => '0',
                'description' => '返水开关，1:开启，0:关闭。默认：0',
            ],[
                'parent_id' => $pump_id,
                'title' => '是否发放返水',
                'key' => 'pump_outlet_order_enable',
                'value' => '0',
                'description' => '发放返水开关，1:开启，0:关闭。默认：0',
            ],[
                'parent_id' => $pump_id,
                'title' => '默认抽水配置',
                'key' => 'pump_default_rule',
                'value' => '{"enable":1,"conditions":["bonus"],"inlet":[{"scale":0.1,"bonus":10000,"outlet":[{"level":2,"scale":0.1}]},{"scale":0.05,"bonus":1000,"outlet":[{"level":2,"scale":0.05}]}]}',
                'description' => 'Pump/Rule类文件有示例',
            ],[
                'parent_id' => $pump_id,
                'title' => '指定抽水彩种',
                'key' => 'pump_lottery_ident',
                'value' => '',
                'description' => '针对哪些彩种抽水，多个彩种标识用,隔开，默认为空，全部都抽',
            ],[
                'parent_id' => $pump_id,
                'title' => '初始时间',
                'key' => 'pump_begin_time',
                'value' => '',
                'description' => '格式 YYYY-MM-DD HH:II:SS 例如 2021-04-16 02:08:00',
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
            Schema::dropIfExists('config');
        }
    }
}
