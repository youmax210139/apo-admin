<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Service\Models\Config;
use Service\Models\OrderType;
use Service\Models\User;

class ExecuteSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ExecuteSql:run {method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '执行SQL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $method_name = $this->argument('method');
        if (method_exists($this, $method_name)) {
            $this->$method_name();
        } else {
            $this->info('找不到要执行的方法');
        }
    }

    /**
     * 删除users表触发器
     * 暂时先注释，如果 add_users_trigger 有问题 就马上执行
     *
     * public function delete_users_trigger()
     * {
     * //出了问题马上可以删除
     * $this->line('platform => ' . get_config('app_ident', ''));
     * DB::beginTransaction();
     * try {
     * DB::statement('DROP TRIGGER users_group_update ON users;');
     * DB::statement('DROP TRIGGER users_name_update ON public.users;');
     * DB::statement('DROP TRIGGER users_id_update ON public.users;');
     * DB::statement('DROP FUNCTION public.users_group_update();');
     * DB::statement('DROP FUNCTION public.users_name_update();');
     * DB::statement('DROP FUNCTION public.users_id_update();');
     * DB::commit();
     * $this->info('删除触发器执行成功');
     * } catch (\Exception $e) {
     * DB::rollBack();
     * $this->info("执行失败" . PHP_EOL . $e->getMessage());
     * }
     * }*/


    /**
     * 添加users表触发器
     */
    public function add_users_trigger()
    {
        $this->line('platform => ' . get_config('app_ident', ''));
        DB::beginTransaction();
        try {
            DB::statement('
        CREATE OR REPLACE FUNCTION users_group_update() RETURNS trigger AS $$
            BEGIN

                INSERT INTO user_behavior_log (
                    user_id,
                    db_user,
                    level,
                    action,
                    description
                ) VALUES (
                    OLD.id,
                    user,
                    1,
                    \'用户组变更\',
                    \'用户尝试修改\' || OLD.username || \' 权限 \' || OLD.user_group_id || \' 为\' || NEW.user_group_id || \'，执行语句：\' || current_query()
                );
                RETURN NULL;
            END;
        $$ LANGUAGE plpgsql
        ');

            DB::statement('
        CREATE OR REPLACE FUNCTION users_name_update() RETURNS trigger AS $$
            BEGIN

                INSERT INTO user_behavior_log (
                    user_id,
                    db_user,
                    level,
                    action,
                    description
                ) VALUES (
                    OLD.id,
                    user,
                    1,
                    \'用户名变更\',
                    \'用户尝试修改用户名 \' || OLD.username || \' 为\' || NEW.username || \'，执行语句：\' || current_query()
                );
                RETURN NULL;
            END;
        $$ LANGUAGE plpgsql
        ');

            DB::statement('
        CREATE OR REPLACE FUNCTION users_id_update() RETURNS trigger AS $$
            BEGIN

                INSERT INTO user_behavior_log (
                    user_id,
                    db_user,
                    level,
                    action,
                    description
                ) VALUES (
                    OLD.id,
                    user,
                    1,
                    \'用户ID变更\',
                    \'用户尝试修改用户ID \' || OLD.id || \' 为\' || NEW.id || \'，执行语句：\' || current_query()
                );
                RETURN NULL;
            END;
        $$ LANGUAGE plpgsql
        ');

            DB::statement('
        CREATE TRIGGER users_group_update
            BEFORE UPDATE ON users
            FOR EACH ROW
            WHEN (OLD.user_group_id IS DISTINCT FROM NEW.user_group_id)
            EXECUTE PROCEDURE users_group_update();
        ');
            DB::statement('
        CREATE TRIGGER users_name_update
            BEFORE UPDATE ON users
            FOR EACH ROW
            WHEN (OLD.username IS DISTINCT FROM NEW.username)
            EXECUTE PROCEDURE users_name_update();
        ');

            DB::statement('
        CREATE TRIGGER users_id_update
            BEFORE UPDATE ON users
            FOR EACH ROW
            WHEN (OLD.id IS DISTINCT FROM NEW.id)
            EXECUTE PROCEDURE users_id_update();
        ');

            DB::commit();
            $this->info('添加触发器执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //修改代理佣金活动
    private function update_activity_rechargecommision()
    {
        DB::beginTransaction();
        try {
            \Service\Models\Activity::where("ident", "rechargecommision")->update([
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
            ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    // 修改消费满即送佣金活动
    private function update_activity_consumecommision()
    {
        DB::beginTransaction();
        try {
            \Service\Models\Activity::where("ident", "consumecommision")->update([
                'config' => '{"hide": "0", "config": [[[{"title": "消费满", "value": "1000"}, {"title": "上级礼金", "value": "5"}, {"title": "上上级礼金", "value": "3"}], [{"title": "消费满", "value": "5000"}, {"title": "上级礼金", "value": "25"}, {"title": "上上级礼金", "value": "5"}], [{"title": "消费满", "value": "10000"}, {"title": "上级礼金", "value": "35"}, {"title": "上上级礼金", "value": "10"}]], [[{"title": "最低消费", "value": "1000"}], [{"title": "是否限制为活动时间内注册的用户", "value": 1}]]]}',
            ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    // 修改每日首充活动 增加销量
    private function update_activity_dayfirstrecharge()
    {
        DB::beginTransaction();
        try {
            $data = \Service\Models\Activity::where('ident', 'dayfirstrecharge')->first();

            $config = json_decode($data->config, true);
            $new_config = [];

            if (!isset($config['config'][0]) || !isset($config['config'][1])) {
                $this->info('执行失败，缺少基本配置');
                return;
            }

            if (count($config['config'][0][0]) === 3) {
                $this->info('执行失败，已执行过');
                return;
            }

            foreach ($config['config'][0] as $key => $_config) {
                $new_config[] = [
                    $_config[0],
                    ['title' => '销量', 'value' => 0],
                    $_config[1]
                ];
            }

            $config['config'][0] = $new_config;
            $config['config'][1][1][0] = ['title' => '是否取今天数据', 'value' => 0];

            \Service\Models\Activity::where("ident", "dayfirstrecharge")->update([
                'config' => json_encode($config),
            ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    // 修改星火每日首充活动条件 由原本5阶更新至8阶
    private function xinghuo_activity_dayfirstrecharge()
    {
        if (get_config('app_ident') != 'xinghuo') {
            $this->info('执行失败，平台: ' . get_config('app_ident'));
            return;
        }

        DB::beginTransaction();
        try {
            \Service\Models\Activity::where('ident', 'dayfirstrecharge')
                ->update([
                    'config' => '{"hide": "0", "config": [[[{"title": "当日首次充值", "value": "100"}, {"title": "当日累积消费", "value": "1000"}, {"title": "奖励金额", "value": "8"}], [{"title": "当日首次充值", "value": "500"}, {"title": "当日累积消费", "value": "5000"}, {"title": "奖励金额", "value": "18"}], [{"title": "当日首次充值", "value": "1000"}, {"title": "当日累积消费", "value": "10000"}, {"title": "奖励金额", "value": "28"}], [{"title": "当日首次充值", "value": "2000"}, {"title": "当日累积消费", "value": "20000"}, {"title": "奖励金额", "value": "38"}], [{"title": "当日首次充值", "value": "5000"}, {"title": "当日累积消费", "value": "50000"}, {"title": "奖励金额", "value": "58"}], [{"title": "当日首次充值", "value": "10000"}, {"title": "当日累积消费", "value": "100000"}, {"title": "奖励金额", "value": "88"}], [{"title": "当日首次充值", "value": "20000"}, {"title": "当日累积消费", "value": "200000"}, {"title": "奖励金额", "value": "188"}], [{"title": "当日首次充值", "value": "50000"}, {"title": "当日累积消费", "value": "500000"}, {"title": "奖励金额", "value": "588"}]], [[{"title": "首充彩票流水倍数", "value": "15"}], [{"title": "是否取今天数据", "value": "1"}]]]}'
                ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    // 输出平台冻结用户
    private function _check_frozen_user_fund($user_list)
    {
        echo '执行余额清零的用户列表：' . PHP_EOL;
        $total_users = 0;
        $total_amount = 0;
        $result = User::select('users.username', 'user_fund.balance', 'users.last_time', 'users.frozen', 'users.id')
            ->leftjoin('user_fund', 'user_fund.user_id', '=', 'users.id')
            ->whereIn('users.username', $user_list)
            ->orderBy('username')
            ->get();
        foreach ($result as $value) {
            $total_users += 1;
            $total_amount += $value->balance;
            echo '用户ID：' . $value->id . ', 用户名：' . $value->username . ', 用户余额：' . $value->balance . PHP_EOL;
        }

        echo '清理的用户数量总计为：' . $total_users . PHP_EOL . '金额总计为：' . $total_amount . PHP_EOL;
    }

    // 平台冻结用户余额清理
    private function _clear_frozen_user_fund($user_list)
    {
        DB::statement('CREATE TABLE IF NOT exists user_fund20201231 AS TABLE user_fund;');
        DB::beginTransaction();
        try {
            echo '执行余额清零的用户列表：' . PHP_EOL;
            $total_users = 0;
            $total_amount = 0;
            $result = User::select('users.username', 'user_fund.balance', 'users.last_time', 'users.frozen', 'users.id')
                ->leftjoin('user_fund', 'user_fund.user_id', '=', 'users.id')
                ->whereIn('users.username', $user_list)
                ->orderBy('username')
                ->get();

            foreach ($result as $value) {
                $total_users += 1;
                $total_amount += $value->balance;

                echo '用户ID：' . $value->id . ', 用户名：' . $value->username . ', 用户余额：' . $value->balance . PHP_EOL;

                //执行清零用户额度
                $user_found = \Service\Models\UserFund::where('user_id', $value->id)->first();
                $user_found->balance = 0;
                $user_found->hold_balance = 0;
                $user_found->save();
            }

            echo '清理的用户数量总计为：' . $total_users . PHP_EOL . '金额总计为：' . $total_amount . PHP_EOL;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function check_frozen_user_fund_feiyu()
    {
        if (get_config('app_ident', '') != 'fy') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataFeiyu());
    }

    private function clear_frozen_user_fund_feiyu()
    {
        if (get_config('app_ident', '') != 'fy') {
            $this->info('执行平台名称不匹配');
            return;
        }
        DB::enableQueryLog();
        $this->_clear_frozen_user_fund($this->getUserDataFeiyu());
    }

    private function getUserDataFeiyu()
    {
        return [
            'clkl888', 'xxg126126', 'xlg1990', 'tian88888', 'fc16868', 'inphemin888', 'lj19851008', 'kkpp04', 'rsjc222', 'xc8888', 'zd666888', 'lgb777', 'wangjian5188', 'cq6688', 'zhulinsen', 'liu998', 'zjy0914', 'hh8133', 'yf888666', 'ljy413', 'wj131494', 'dt8888', 'wjb1986a', 'xdx88888', 'xh71733', 'lmn002', 'hc00002', 'xl2468', 'heppy666', 'fypt178', 'bobo3399', 'hejiu168', 'daili9988', 'xin0910', 'aww4860', 'w521521', 'nb666888', 'pangz11', 'nnyy001', 'tccaojie', 'xp73333', 'qq258741', 'zck161313', 'ss0726', 'ewtx1699', 'bb88888', 'xdy123456', 'nuanyang999', 'woruogudu', 'babyfei', 'abc168558', 'yzfc728', 'qq251463376', 'xrx139', 'hongyun', 'tian86', 'xdj2020', 'zhong1234', 'ab7619788', 'sanbao11', 'ww88888', 'aa9901', 'wangxin838', 'zhang117', 'zm1988', 'czf1358', 'sj16888', 'quanxiu', 'ji336699', 'ka12345', 'lxm66666', 'qianduoduo', 'lin2019333', 'jyq1915', 'guo771521', 'adg131492', 'xixihaha', 'wpdy518', 'ali5188', 'whay110', 'aa116677', 'szb415415', 'zgt16980', 'yqf168', 'pjw0001', 'prz88888', 'xiaoyu518', 'lxm1689', 'good1chao', 'zz77777', 'hhh128', 'qin899', 'gewen888', 'fei777888', 'qwe5566', 'ws1111', 'q1692188', 'xiaojin666', 'sz6676', 'vera1314', 'a7559565', 'lixin1', 'tto999', 'wjl88888', 'xiaohu2286', 'liu838003', 'bdp135234', 'adg622588', 'yan232425', 'jszx888', 'lele2014', 'zmd6666', 'lxg777', 'qaz660', 'abc995533', 'sunwan', 'liu123456789', 'xiong1985', 'jszn001', 'ghy666', 'jgdjm00', 'luosen123', 'zr9475', 'lsr1688', 'dahai1166', 'hx112693', 'anzi6688', 'gs8866', 'sxb888', 'dawj556688', 'yyc333777', 'ws1314', 'myh6666', 'www1188', 'yz9999', 'xzt168', 'lz66778', 'bb0033', 'achen666', 'smc5219999', 'dj1977', 'njy52888', 'yl131419', 'xiaopang888', 'aa68686888', 'cai888', 'a960829445', 'chichi', 'xz0121', 'tongxin889', 'guo771', 'lxl666999', 'feitian', 'ych1314', 'gb00002', 'gzzc123', 'xz0168', 'wj6666', 'xiangxiang123', 'tianjie1981', 'hyy25836999', 'lanbao888', 'bg88888', 'qin888999', 'wxm888', 'c5728919', 'kaixin678999', 'su1069819503', 'yfj666', 'fyzbd888', 'cyx95413', 'facai017000', 'liuyang6668', 'qiang9999', 'zhang888', 'bw12398', 'jian8866', 'ywq888', 'qiang199', 'li1992', 'zhongheng', 'm309408705', 'wjj6299', 'xw168968', 'yu13111', 'xiaming1699', 'yaozemian', 'zzz8880', 'cf1314199', 'fc88888888', 'ss199604', 'xhb1018', 'jj5078', 'fc1686', 'im778899', 'hxy5509', 'wxh8888', 'pkk555', 'lol8888', 'xia888999', 'sds1479', 'q10000', 'shanshui8', 'hr1982', 'wcy8851', 'ffyy123', 'zsh123456', 'yo123008', 'k1195440929', 'mxm1545', 'caonimade666', 'dofeng', 'wot111', 'jw8888', 'ldc16897', 'wkl2018', 'lyd196303', 'jzy888', 'a739286047', 'ccs118', 'jmh678', 'ggg369', 'm66666', 'hp8888', 'm319818', 'feitian66', 'lyl77777', 'yuyuyi', 'wgg168888', 'wyj829', 'rw1319', 'yq2019', 'heng1314', 'a1134391149', 'zou123456', 'shw7788', 'yichuan', 'hhh588', 'shaniu1314520', 'txvip88', 'qibaobao8', 'tj1943', 'fihikn', 'gg668866', 'qq52399', 'xiaxue66', 'xejxsl', 'aa998866', 'l123456a', 'cc1978', 'ljl131419', 'zg8308', 'tanziming319', 'woshihao', 'lolo9966', 'lxp888', 'guotu147', 'yu0302', 'swj9079', 'lsxym1788', 'xc0744', 'ljf168', 'xiaojuan666', 'enen123', 'calm1900', 'xiaoji', 'shy137137', 'bbb55667788', 'q1870352', 'fyzs16888', 'lmm168', 'l969696w', 'm188588', 'qch6789', 'suibain025', 'zbd16888', 'xuling131443', 'wqing001', 'dxm99999', 'ch131419', 'sd8588sd', 'xiaoxu2', 'dapeng88888', 'dabao112233', 'zhangyu818', 'xingxing168', 'jani168', 'n222222', 'fy836873234', 'zjh1314', 'l800900', 'gjq123456', 'zz0823', 'yufeng666', 'x131419', 'zhx88888', 'lottery789', 'jspz818', 'j812cm', 'mnmn1313', 'qw8888', 'ljc747132489', 'fyxj88888', 'zhong9889', 'laomao0212', 'jhw6363542', 'www090', 'yunyunyun', 'z316000801', 'thm111', 'bobo1188', 'ok789987', 'ym6666', 'xianren666', 'vip888', 'nnuu0002', 'jia16888', 'laok2345', 'sph123', 'fypp999', 'r496924', 'hgy123', 'aa18776542', 'fwl51333', 'kelly888', 'qinqin_58', 'kakarote35', 'yzy789', 'zhukui123', 'lw2020', 'qige7777', 'lyl644507', 'l96289613', 'ergou777', 'xujunjia6', 'ljy1314', 'ggghhh89', 'lb66866', 'hqx000', 'shuangzi', 'vip89131', 'xym1994', 'zcxx002003', 'vip1117', 'lhgy168', 'zhy131499', 'jinyuedaye', 'peng1888', 'qwer8888', 'hei168168', 'huizhu88', 'zhen2007606', 'txy148', 'xjs8888', 'win777777', 'cxm520', 'gpsolar', 'zjt6677', 'ms16888', 'jian4647', 'kz0001', 'ssc96363', 'ml159357', 'wqc131419', 'jhb16888', 'tt1980', 'a1129593610', 'weishao168', 'yt8858', 'wang335524', 'cpj168', 'hhy3272', 'jl0306', 'zsq3789', 'zsx8888', 'tt5689', 'qjh6688', 'ztt1202', 'gh8888', 'dida1314', 'vipwz1688', 'a3364863074', 'wzq666888', 'dhm168', 'ly1319', 'yqs131419', 'bc589916', 'winnie8888', 'lfq123321', 'lxf131419', 'hz070707', 'jiang088', 'dast123', 'lgh5588', 'gambler', 'qgq613728', 'lucky88168', 'daan666', 'a1502472', 'lebao520', 'liubei188', 'ltt410782', 'zhaomm520', 'jdqs2020', 'xiaoxiaokun', 'zaz826328', 'x15250529095', 'wang2000', 'whj840207', 'wu1151159', 'dongrong76', 'fdc88889999', 'xuehua330', 'sxx2019', 'ydj131419', 'wwk1991', 'zhao1314', 'chang6677', 'seasky1', 'lws888888', 'pp5523', 'qq4311', 'xj123123', 'bb0922', 'mgl6688', 'wuyi51', 'hsj168', 'phl2772', 'wkj555369', 'zhuguan18518', 'xrc138', 'ying1688888', 'nima2018', 'qing0258', 'lt585858', 'lan1163757499', 'zjyhrn999', 'fb123123', 'ww77888', 'livling4', 'clic16888', 'han180603', 'tyh131419', 'asd6868', 'lin328', 'ld8888', 'khbz666', 'pf6666', 'yun3815', 'whl123321', 'xt888999', 'guo222', 'gm16888', 'lg7788', 'zhouge888', 'gq0005', 'xz520168', 'yybb100', 'lmn001', 'qwe1133', 'wl888999', 'chaojidan', 'laosi888', 'swtc91', 'csp178178', 'ts0000', 'suyi1000', 'lhlsjg', 'zc8065632', 'lzq4371', 'blueyunsky', 'zhanlang888', 'a65472370', 'zxg88888', 'ld6789', 'ybb4444', 'bushi668', 'qzy27455', 'lx1314', 'd841212_12', 'dlz111', 'h51888', 'zuozhe008', 'lcc1888', 'aa9000', 'dwj667788', 'liuyang', 'liu88518', 'hs7777', 'xy588119', 'zhao9900', 'dd6688', 'szy63699', 'zouchu18', 'cw2869', 'ymm520', 'zn1688', 'xinying2006', 'ya8888', 'fy2020', 'sdy888', 'lin330', 'wangge888', 'li79950379', 'fc88889999', 'ykx123456', 'wjh668', 'wangxu099', 'hzx13876946067', 'n168168', 'xc888888', 'hq5205', 'my131419', 'zy1228', 'xx995129', 'zm55555', 'shuai808080', 'jg8899', 'lmj8080', 'gh9958', 'xingk2020', 'qq31450', 'lxh1864', 'a276744058', 'dj132603', 'ld1314', 'charni888', 'gu123123123', 'hihihihi', 'zgt001002', 'hulu39111', 'bb5482', 'w779872369', 'yg16888', 'abcd1355215', 'hhh666', 'lu15670858616', 'lxe0620', 'xiao1616', 'guan123', 'hmy061121', 'cdm168168', 'xhqxhq88', 'yd636636', 'm88999', 'tzhl666888', 'dbg123456', 'yuangfeng123', 'xl705491781', 'feixiang123', 'hl16888', 'yxl1964', 'ww67888', 'aaa6666', 'yanmi1983', 'wentt123', 'tank220', 'fyzxj888', 'panpan1234', 'xjm1234', 'zg6666', 'blz131419', 'zb1234', 'ww6789', 'jw84642366', 'dabieshu', 'ljh313', 'ghy667788', 'baige2020', 'lqf1975', 'tfk014', 'zgq168', 'falali25', 'yuppp999', 'zdd928', 'y090909', 'xiaoxu88', 'facaigame3', 'shun7788', 'hyl456789', 'deng259', 'hyll1314', 'lz6677', 'hao456', 'zyd0902', 'xu9888', 'hf15895181547', 'jingde777', 'yhm8899', 'df6688', 'liuyuxing', 'wxh6666', 'ldx111', 'jingjing', 'a805760459', 'ngls5918', 'cai11018', 'jingxi8888', 'zh8899', 'ding16888', 'yh666888', 'zgphl88', 'ck9988', 'facaigame', 'wyh123', 'imhuangling', 'hy123456', 'lj668899', 'ff71007', 'haoyun666', 'm2019m', 'zfj168', 'sbjt000', 'wtzhong', 'mlxy5188', 'fa5678', 'zgc666', 'xx8980', 'ouri168', 'lfc88888888', 'jackdanny888', 'springbing', 'wujingbo132132', 'sqp158', 'meng008', 'tang521', 'kk8866', 'aaa886', 'duoye8888', 'xyy13141718', 'xina690jj', 'xiaohu788', 'wzg111333', 'zwln19888', 'dnf666', 'hihihi01', 'xiaonan123', 'a419012786', 'jiang99999', 'zyx1999', 'caishen008', 'queen1818', 'pp1314521', 'zxx1319', 'zsq9988', 'hyx543261332', 'hxh5188', 'a2339999888', 'yc1234567', 'a562569110', 'lie888', 'xiu777', 'dy88899', 'kun8888', 'ws2019', 'a15518772366', 'xy8886', 'kenan2020fy', 'dangdang999', 'qq6912274', 'plm3131', 'l996399993', 'xc12345', 'syl16888', 'as888888', 'zhangxiaoyi', 'wt3333', 'zhuang88', 'vip188188', 'kabu999', 'cyy6688', 'cw1234', 'anran666', 'lky666999', 'cxh88888', 'fulushou', 'w45177188', 'liutu123', 'xey888', 'ly8886', 'qwe999', 'sl1688', 'wy1125', 'cyj1214', 'bmw168', 'fuyun520', 'as1174761053', 'zhongge20', 'hch0412', 'zhang003', 'ck8888', 'zhang002', 'xeq177896', 'zheng2020', 'tp123154', 'baicai', 'a1719452', 'xiaopeng', 'fy168888bb', 'xxss111', 'ab1188', 'aa48613', 'zhuanxian666', 'cao0011', 'a851919185', 'csc168', 'a695511912', 'dy5666', 'zjzj5588', 'venus188', 'fy8699', 'hzh500', 'qwr666666', 'dangdang', 'ltr131', 'wd666666', 'xiao1688', 'lglg123', 'huaqi006', 'youshang', 'hong88888', 'hh155389', 'aishiniwo88', 'won20199', 'h466060257', 'xiaozhang1', 'nannan6698', 'a8588860', 'ggh1234', 'dyh1314', 'cnm844957401', 'ygm333999', 'ygm888999', 'zjzj9988', 'yb13980810177', 'jzhi1188', 'xp1913', 'wzy1319', 'lucky88', 'wb6688', 'wang201901', 'tty426', 'lyl8989', 'fada5188', 'xty00642', 'yll0806', 'pgj123', 'mengluanwawa521', 'lyy888', 'chy1978', 'gemifang1029', 'lw20140617', 'hxy8858', 'wfm123888', 'gene168', 'qq105122', 'pinguo000', 'wana123', 'ghy888999', 'aaa999888', 'zhizu8888', 'ghy123123', 'lvh666', 'lhf12345', 'vvk7777', 'edg9731', 'wwly223', 'baizong666', 'wuq123', 'qmy111', 'rhy125521', 'jing2212', 'hshgs16689', 'juanjuan888', 'mk56969', 'xs6866', 'weimin1', 'kaka12369', 'dong88588', 'zsl122333', 'hh1688', 'lingluren', 'chenzl', 'kim1024', 'xu1818', 'wangchao7', 'shasha168', 'zhuguan01', 'zhaoshang01', 'zhishu01'
        ];
    }

    private function check_frozen_user_fund_feiyu2()
    {
        if (get_config('app_ident', '') != 'fy2') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataFeiyu2());
    }

    private function clear_frozen_user_fund_feiyu2()
    {
        if (get_config('app_ident', '') != 'fy2') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_clear_frozen_user_fund($this->getUserDataFeiyu2());
    }

    private function getUserDataFeiyu2()
    {
        return [
            'wy2688', 'gggggsss', 'yrx0700', 'zhu1314', 'sk2000', 'ly12345678', 'liang888', 'cq332211', 'wz2000', 'dtygdfy', 'ying9888', 'minglin99', 'yll13312312626', 'lv6808', 'bbe258', 'lxq1588', 'gao123', 'tb7885', 'qwertyuiopasdfgh', 'l789789', 'a2460810303', 'wx1257', 'hao820', 'qaz368', 'dfggydl', 'sushun1', 'gyq820108', 'wyj226', 'aj1668', 'qq66688', 'xyy8643', 'shu1071', 'tt5720', 'taozi2', 'wj8756', 'ssb20001015', 'sz123123', 'xia888', 'ghm666888', 'zoc998', 'ayssc329', 'cz999999', 'hz57563', 'aslove', 'mm5071', 'c121675536', 'ghj566', 'qwe358', 'peng1888', 'mycsunn', 'wyj369', 'vvs666', 'gx123123', 'daiwei888', 'l214412', 'h79413', 'tt666888', 'puy365', 'jiang001', 'bing888', 'www1234', 'yd102577', 'guo66666', 'ansh551', 'zh2008', 'ghgmrad', 'aa0898', 'hwq121366', 'mll666', 'liuqingmei', 'zln888', 'tang123', 'app666', 'lcp123', 'fy1987', 'xh5964', 'yj12345678', 'wh1821', 'gte4878', 'fdsb235', 'hh6789', 'fes587', 'gcr2223', 'qaq7260', 'v781332462', 'hy8899', 'cyy666968', 'qwer7890', 'csp8888', 'xiao48322', 'zy13330348658', 'zhang2776', 'jyx888', 'vip6569', 'fgfchyda', 'gqj9999', 'lhm123', 'ckk418888', 'wx5410', 'j987654', 'fghcdghv', 'ljy787', 'liu698', 'zz1818', 'zyc2010', 'huage88', 'ming7888', 'xx0099', 'zhangsuru', 'binge6188', 'go2022', 'qianlai888', 'awd798', 'gao2678', 'ywh1257', 'hh870526', 'jh8572', 'qwess12345', 'aa0123', 'wsh168100', 'ghf5678', 'twq888', 'baiyan151151', 'rg4913', 'zzw617888', 'wj1314', 'm666666', 'aaaa11112', 'lhl5678', 'wjb198685', 'cgm123', 'ludehua40', 'sen001', 'xcjw1319', 'wangcaiwangcai', 'abc571', 'cyx77888', 'zwj1314888', 'wzy160319', 'hsj168', 'huai45678', 'zxcv9999', 'zhi6699', 'mmc1688', 'tange8818', 'zxy963', 'zsliujun1314', 'aa9027777', 'wzy555', 'wang7777777', 'liujianle', 'ah16789', 'yyy1988', 'ac56789', 'a556677', 'xxc1361082', 'sdm000888', 'liren2020', 'xy456789', 'hyt1314888', 'xiuquan888', 'hy5588', 'pan8888888', 'zbr5462', 'qaz1234', 'ac6666', 'fang522', 'qsk1950', 'boss9999', 'ma528278945', 'zhang1314', 'bx5888', 'lf1314', 'zh19950508', 'popo543', 'yhl585858', 'hani888', 'yy52018', 'zzl8866', 'uiop8888', 'qad1319', 'paa278', 'yp757588', 'ys770103988', 'cy112233', 'a870723413', 'a757242424', 'txx999', 'wjg888', 'yy2727', 'xiao88889', 'wzh3357', 'ld389344', 'yrc666', 'song55643', 'zh1999', 'ahua88', 'ai0022', 'jiujiu888', 'lmm520', 'm430421', 'y382138', 'dyf1319', 'tcx6688', 'm18881100789', 'hjs1314999', 'ojr518868', 'xdd123456', 'wfe123654', 'w15271306231', 'wangjie888', 'fyf555888', 'ly990994', 'ljc8560', 'meng6888', 'sp900404', 'ly3333', 'tk518168', 'jsr888', 'zlm1983', 'zll666', 'jxy123456789', 'gsf111', 'lu2010', 'zjp123', 'fan940219', 'suhao1999', 'qx7777', 'wen2007', 'l123456', 'lyz368', 'hong789123', 'hnq1689', 'a123ajnxbxbhxj', 'jyw123', 'a15135700967', 'ttttyp123', 'gyl8812066', 'jhs66666', 'dlw1688', 'gaoyongmin111', 'lyj151022', 'liu75049', 'yh0007', 'gyl7595', 'gaojianmei', 'gaoshunping', 'fy08028', 'wwl888', 'z520120', 'a279166397', 'hx8888', 'zlp2020', 'lcy2078', 'ldx111', 'bc7101', 'az8889', 'jie0719', 'www66666', 'bj1688', 'yzt1234', 'vipzzy', 'zc123456', 'dyl123', 't130162', 'licheng888', 'de6699', 'l787878', 'md5588', 'yaoxin999', 'khh123456', 'yx051640', 'w18779464327', 'tianzong520', 'luo888888', 'qiang0830', 'tiyh8396', 'tonghao2', 'tonghao1', 'wa4458167', 'yu1688', 'c396331', 'kp014599', 'bbb100', 'czz0308', 'yzb9928546', 'a26999', 'zh166188', 'yy45678', 'jack0125', 'wxh5396888', 'pp1980', 'asd444', 'zdd978', 'dfq999990', 'law2019', 'll675962692', 'q128588', 'lzping', 'bole733622', 'shuzhong123456', 'lu9999', 'tjq1979', 'lb941520', 'www578', 'facai16888', 'hai5858', 'acc1680801', 'pp58888', 'buandian1', 'yb5201314', 'lp060288', 'ggxx6666', 'fy1986', 'feiyu26699', 'jingde789', 'wangdng123', 'dydy8888', 'l6688l', 'tongze520', 'v781332461', 'zlm803322', 'cyf8545', 'xia8866', 'ly518519', 'zhuguan001', 'zhaoshang01'
        ];
    }

    private function check_frozen_user_fund_feiyu3()
    {
        if (get_config('app_ident', '') != 'FY3') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataFeiyu3());
    }

    private function clear_frozen_user_fund_feiyu3()
    {
        if (get_config('app_ident', '') != 'FY3') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_clear_frozen_user_fund($this->getUserDataFeiyu3());
    }

    private function getUserDataFeiyu3()
    {
        return [
            'dada2021', 'ceshi001', 'xiaozh3000', 'daozu678', 'qinger1995', 'lp6668', 'yy2218', 'ok2356', 'qq77999', 'xnly2019', 'zz0909', 'dan1818', 'qwe9898', 'sj373773', 'fg1863', 'chy5656', 'wx7878', 'l123456', 'jk1596', 'dxf1314', 'peng135', 'xinxin5353', 'fy1990', 'zgl168', 'shuan1990', 'mlp136868', 'wzy654', 'cq466566', 'sy5810919', 'zgt888', 'zhi453600', 'krq1234', 'sssttt123', 'yuanzi69', 'wdw9898', 'ywb520136', 'dfg137', 'dawei888', 'lantian688', 'liushu60', 'wenjie33', 'bokere212', 'xf11158', 'fang0204', 'jppp666', 'lolo3999', 'facai22258', 'erma1558', 'sshan1995', 'zy13149', 'myx1258', 'dxf1313', 'dan5858', 'za18351752514', 'dan6868', 'dan4848', 'zz1825523451', 'yl5858', 'mao9898', 'hzx258888', 'gpp2222', 'wxl888', 'zhm1314', 'a88688', 'dfh565', 'li1860093', 'wu8888', 'cjcj168', 'yl2028', 'yaoyao168', 'zzz1688', 'aaa88688', 'nu9999', 'sm5588', 'mao1818', 'abc666666', 'aa88688', 'weihe588', 'wdc3335', 'mao3838', 'xzw1991', 'wc1996', 'mao7878', 'dan2828', 'duoduo2020', 'mwt111', 'mao2828', 'www578', 'liang5858', 'fy88088', 'sqz0001', 'mao4848', 'jingde789', 'dan3838', 'vip362293551', 'feiyu365', 'zzz1111', 'xx6688', 'jingde918'
        ];
    }

    private function check_frozen_user_fund_yicai2()
    {
        if (get_config('app_ident', '') != 'yc2') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataYicai2());
    }

    private function clear_frozen_user_fund_yicai2()
    {
        if (get_config('app_ident', '') != 'yc2') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_clear_frozen_user_fund($this->getUserDataYicai2());
    }

    private function getUserDataYicai2()
    {
        return [
            'zyh8899', 'xw5959', 'ycai2gz', 'yck2019', 'zongbu888', 'ceo2020', 'sx87763119', 'cs1221', 'bmw666', 'xu1982', 'dj2019', 'guozi520', 'zyt1314', 'wen1093720868', 'ngg777', 'qq139499', 'k01sss', 'ifound', 'gx1125', 'xufeng11', 'zay1413', 'wen59145', 'cdx662573', 'oy8882', 'liang147', 'qzw520', 'zhang19951995', 'chnn0001', 'vip7788', 'cxl001', 'xsy999', 'cyy1054257673', 'pangpang88', 'chenbiao123', 'q214253', 'dhz13793978264', 'v168168', 'weimei99', 'lgs198312', 'bibi888', 'fa1319', 'shengshi88', 'yl5372', 'aaaa4444', 'yc0916', 'daidai127', 'qq6561236', 'ms16888', 'xingfu2021', 'a16888', 'jbzz520', 'wq123456', 'wzl6699', 'aa518518', 'mrchen', 'kz9600', 'ylf888', 'niu2020', 'sun1313', 'zx123456', 'yulong8888', 'dandy888', 'hyz12345678', 'syp960628', 'ff188188', 'charly', 'kukudejun', 'tuhao688', 'bua2020', 'z13464368288', 'zsm131419', 'cctv20', 'ty8888', 'whx168', 'jnh999', 'w13500267986', 'wmm7725', 'gfafa6688', 'hyn1314', 'ql51888', 'whw150621', 'xiongqi518', 'songguo666', 'fu5897', 'dujie2021', 'ddk666', 'zhangnan1', 'xdsh909', 'lw158158', 'zd2518253', 'zc0623', 'zhu666jf', 'pgl15688', 'kako1234', 'a1c2478658', 'nw88888', 'cyh888', 'fuyuan888', 'zl6888', 'yclgd123', 'renyawen971107', 'laolang188', 'qbgt11', 'qwer64551123', 'cgp168', 'pop2020', 'fyf8888', 'luck1788', 'qq888666', 'vip1cai', 'jry1314', 'vip999', 'tt123456', 'lhylhy', 'qq7788995', 'yongheng1', 'xingzi1209', 'zx131418', 'pqx3424', 'jl66686668', 'n390066834', 'gao168', 'hyl668899', 'sz1319', 'zhu13579259828', 'xian6396', 'lb7777', 'ww123456', 'pxh1314', 'sss123', 'xyp6789', 'xw518518', 'a1647062814', 'dxl0343', 'wzl319', 'cj66666', 'qwe12345', 'xd199412', 'ff322312', 'fa5988', 'hao41088112', 'dalao918', 'my520000', 's17769346761', 'xiaojia18888', 'chen365521', 'wfyc2020', 'ryl888', 'bageba', 'wy6688168', 'ly8023', 'zh2020', 'lqh111', 'zj2020', 'asdasd001', 'lfz123', 'yy2021', 'chy6688', 'zgx15888', 'ck888888', 'w175485', 'a13220680202', 'dongfei1986', 'lyf13479', 'qqq214253', 'b7221151', 'w110120', 'qwe9898', 'cxl666777', 'hyl6688', 'a15187231079', 'zs131419', 'fgli0925', 'zx8690458', 'jm1234', 'njl1215', 'woainvpai123', 'wsj182226', 'wy1123', 'sx88888', 'ayam88', 'han8888', 'buli99', 'lxq1313', 'a006006', 'wbd1919', 'hjy520', 'zyf8009', 'ry1314679', 'tencent888', 'aj9999', 'ssc998', 'a496412281', 'sxqx888', 'facai2021', 'wl147258', 'jiajia5716', 'hy6868', 'jq1688', 'qy1020', 'wx888999', 'yza9988', 'wlm168168', 'cgt789', 'kyf000', 'wys168886', 'zql178', 'taol1689', 'liuwen63045', 'xgx1314', 'ok191919', 'lxm000', 'yxh87613', 'q100140096', 'lyq678', 's475575529', 'zdl6688', 'qq319418', 'lxm789', 'ff8822', 'ss0001', 'nj2020', 'lifan168', 'wzy56789', 'xz8888', 'ygx888', 'www123', 'wz1688', 'fhlm0729', 'qz535000', 'hych888', 'svip9413', 'zqb6666', 'j19850921', 'qin888', 'hhj686', 'guaji6000', 'hds13141988', 'fanghua666', 'huokuan', 'w042087', 'xq45013', 'hj122333', 'zbk1595733', 'zj15999', 'guo6515234', 'zwy15999', 'zj15666', 'ff19901', 'luopan', 'xcy1987', 'cy1166', 'xiaomi16888', 'nh1750', 'pbw318', 's123456258', 'yin123', 'kong666666', 'qlx4341', 'a12030', 'xl0008', 'cqh67890', 'zj202020', 'vip888', 'bbb52014', 'wu801555', 'nbahaoa', 'cxl679679', 'shuang999', 'lwg888', 'zuoyou26646', 'dd886699', 'qq80798', 'a349587314', 'yycch12368', 'wqfad5126', 'kakak888', 'liuze123', 'a15589322912', 'abc325609', 'a16881688', 'wym131419', 'y19951944904', 'fnx689', 'linjie888', 'chaoyue333', 'yr8888', 'yy131419', 'xiaoping1', 'yingqian99', 'caoda520', 'yybf666', 'zdp131313', 'ycliu1', 'q688788', 'wq7777999', 'zhuzhuxia', 'luoxu123', 'dbl888', 'www9988', 'yi1517039', 'asd67802', 'ylf666', 'wj1123', 'ccc56789', 'jun202018', 'krj14134', 'ji432100', 'zmm826', 'lzc20201', 'hzf170095176', 'cjd6688', 'kuai627', 'leizong888', 'zhili888', 'facai123', 'wj1125', 'ff101748', 'wcx8899', 'yx130808', 'kuangren202018', 'sht888', 'zm1789546428', 'njy1314679', 'wlm9188', 'qwe7265858', 'dgy123456', 'qq1976', 'toto123', 'xl777999', 'yueying666', 'gyn123456', 'xiaopijing888', 'lyl88888', 'boss810', 'zxn131419', 'ch888168', 'aa59698', 'a15225066', 'falali', 'vip6866', 'hh622808', 'lsg008', 'qfl888', 'pb8888', 'qs88888', 'sjk66888', 'xy2867', 'ycc12369', 'xgf888888', 'jusonglin1', 'xiaoyong666', 'ld5555', 'yc1987', 'mm131419', 'hmw131419', 'abc18677833692', 'mx112233', 'hyxb000', 'syq1382', 'xcc508', 'hsy6688', 'zb961314', 'dongfei111', 'laoan507794', 'hang888', 'zjq131419', 'mofei999', 'gjg841214', 'xc1413', 'zly1315', 'just2020', 'cf9155', 'qq5288261', 'laoban6688', 'shenlan87', 'zyn19840', 'fff8666', 'yx1313', 'zxy1314199', 'cai1319', 'qq15977907528', 'zjy8866', 'biu666', 'wxt168', 'bg0933', 'chen186812', 'gxfc6688', 'qing888', 'yrn9999', 'wdx888', 'ychy99111', 'ylm117', 'qsl131419', 'hhw7788', 'hgl901212', 'song1314', 'ying588588', 'xc666888', 'zhujiao001', 'ywhaqy', 'asc007', 'nb5566', 'xiao823', '1cwzx8888', 'liuye6666', '1cabin666', '1cyicai1699', '1ctmd188', '1czhuguan888', '1cvip7758521', '1cycceo2019', '1cqq475889', '1cwang8259', '1cme584520', 'a613572', '1cpxs448', '1cyca1234', 'y1994h', '1cming8086', '1cdaiyu2', '1cddb888', '1ca000012', '1cpengyulin6', '1ctmd168', '1chsj168', '1csuyaya', '1ca15271001511', 'huiguo888', 'han888', '1cwulin666', '1ctianyue123', 'kkkqqqq368', '1cxujie123', '1clol0202', '1cyueyuege', '1cyicai159', '1claodeng520', '1czhq778899', '1cdmz123', '1claochen168', '1cld5555', '1cz478263817', '1ch666888', '1cyahan011', '1cmeifang99', '1cchen147258', '1czjp7570', '1clqx2959', '1cjjy123456', '1cskywin2', '1cfhwang8259', 'ybhdg91', '1cdaidi166', '1ckyle123', 'shuai152227', 'lvh666', '1ca865913923', '1cstwt6688', '1claoge88', '1cbb7101', '1cg88888', '1cjicai11', 'pzh555', '1cmaomao', '1csj908123', '1cnz1888', '1cff56789', '1cbnw668899', '1cyue0311', '1cman8899', '1csuiyi123', '1cjfz888', '1cye6666', '1cws1992', '1czxcvbnm1', '1csb74110', '1cwx3344', '1cshala8899', '1chyx666666', '1csj1999', '1ctts888', '1cmm65690_6', '1cfugui2020', '1ctts88801', '1clcc999', '1cbs9988', '1cwsm520', '1cqy1382', '1czhuguan03', '1cwanglaoye123', '1clang56789', '1cohs168', '1ckoko2019', '1ctrh199110246827', '1csj1333', '1chuaxing126', '1changtian', '1caa3243', '1ccjh1393', '1cabc6688g', '1cyuer2019', '1clzx666', '1cfzy1993', '1cyg123456', '1cgyl070810', '1clsl1413', '1cdanide', '1chll8899', '1cgxm3452', '1cshi787878', '1cls12345', '1ckunsong147', '1cz595595', '1cxinxin521', '1cz522835312', '1ca1140186', '1claoqing666', '1cwli2020', '1czsl5678', '1cllt6885', '1cshange1314', '1cwya1319', '1czjr666', '1clxh9237', '1cduocai2088', '1czxh6118', '1crbb666888', '1clsy3288', '1csyj880880', '1cljl123456', '1czd51888', '1cgyz168899', '1czz800311', '1cmm12222', '1czgx131719', '1cabcd18069598656', '1cy15240735186', '1cxiangrikui', '1cal18888', '1cg123456', '1clz1349', '1cwxh1314', '1cyk19900701', '1caa1346', '1chuhu171105', '1cwzqs678', '1cok919999', '1cchj1314', '1cdark_preke', '1cwangyifan001', '1cwenming889', '1cww24464271', '1cweige2020', '1czs1995', '1cxy89888', '1clxr7988', '1crzy2288', '1cpt123123', '1csxj789', '1clyj222', '1cdatou2', '1crj0123', '1cz23410', '1cting99', '1ckxl888', '1cbiao8910', '1ca13330500189', '1caaa351414688', '1cvip123456789', '1css1819', '1cjxt1986', '1ccgzl789', '1cczs123456', '1cxdjn4982', '1cdandan4530', '1czhaoshang', '1cyc5888', '1cboss16888', '1cyfjvbn520', '1cpu4038', '1cvip0899', '1cporsche777', '1cabin168', '1cchuan851', '1ctzs1866', '1cjingzong', '1cfh1cvip006', '1cdaili188', '1chjkl988', '1cying5598', '1cheng555', '1czzz991033238', '1cfei7931', '1cronaerx', '1cdz11111', '1cbv9384', '1cwujiceo2', '1chrk1222', '1cmuzi888', '1cyy6666', '1cyj1858', '1czhuguan02', '1czhaoshang02', '1cyicai51888', '1cxiaoyu6606', '1cfugui8866', '1cjia206930', '1cwjy8888', '1cruidi124', '1czxzx3688', '1cmiao6666', '1cnb777888', '1cning6688', '1chums001', '1cwzm1339', '1cgbl5288', '1ckeke690', '1chsc666888', '1cwxb16888', '1clcy1491', '1cqq828444', '1cwen7788', '1chy8208', '1cry16999', '1cruidi125', '1cyyy5188', '1cjiajia456', '1cyongcj888', '1cgdg888', '1clietou168', '1cgym123', '1cxjy8815', '1cashao169', '1clijinrong', '1cgk8888', '1cmin668668', '1ccjl780820', '1cyumao88', '1cag168168', '1cnm666222', '1cxiaorenwu', '1cws1994', '1caa100555', '1cfu1990', '1cmofei888', '1chh789789', '1clx88888', '1ckx986520', '1czz9900', '1cwch2980', '1cliu54788446', '1cdkqc6688', '1chzc888888', '1czz19880326', '1cabc6411', '1cok131419', '1cf7203085', '1cl569888562com', '1cwwwsyt682596', '1cshunfa999', '1czyt141319', '1clsl131419', '1chjl668899', '1cyuwen2020', '1cxiong515', '1ccal1234', '1ca562288ff', '1cdushen888', '1cli4268', '1cjiajia5341', '1cyy15279320861', '1ccph6688', '1cmm8888m', '1cqs7398', '1cjhb888', '1czn112789', '1cjy9188', '1czxr666888', '1chhq3349', '1caaa8866', '1chujing', '1cscy777', '1cligq741', '1caa777777', '1ca664752865', '1cyy711018', '1cwmm123', '1cwwxx141319', '1cchenge7293', '1clxw8624', '1cjoker88888', '1cyun20198', '1ctcx2020', '1cty2020', '1clichun8116', '1clcb168', '1ca18290885116', '1clzyh8888', '1czl667788', '1camll131419', '1cwyr131419', '1cxzf1686', '1cssss123', '1czsjjj666', '1czym201912', '1cyhsu802', '1cxp549472290', '1cv9836960', '1ccyx201912', '1cbgq1329', '1cyzjqq369', '1cdj518518', '1chtx99999', '1cxq1989214', '1clijian7777', '1cgxq888', '1cdxc9413', '1clch88888', '1ccy5222', '1cweiwei0920', '1ca138077263667', '1clhx5566', '1cshaojie168', '1czm123888', '1cmxl518518', '1clhq8888', '1caa1314', '1cyun888', '1cwlm5686', '1ccd13331374888', '1chyn6789', '1ccc1306940', '1ckun0629', '1cxu1106', '1csenle99', '1ccsh3493', '1cyc15568890732', '1clkx6868', '1cmu13598546712', '1cjts1666', '1csyl888999', '1cwas1234561', '1ctmd158', '1clcy1704', '1chaijie334455', '1cwya131418', '1cab8889', '1cjn5566', '1cliying', '1cxj123456', '1cszx1314', '1clyc121314', '1ccheche888', '1chxf123', '1cyy1305', '1cwyj1221', '1ca910683346', '1cwhl131431', '1cjia001', '1czm518518', '1cl12345', '1cpcf15688', '1chu2020', '1csiquba', '1clg131313', '1cquanquan', '1chuluwa', '1cdfq998', '1cshaojie16888', '1cbwt8888', '1cyc18123712689', '1cwang18911653042', '1cmang2020', '1cshb6868', '1czjm35637', '1cwlj5293', '1cpxj123123', '1cr888666', '1cxbt888', '1ca18316467032', '1cshu6699', '1cg13713900475', '1csjj1577', '1cg123123', '1cqym123456', '1ccfzy1353', '1ckakak888', '1clzz1349', '1clwj123456', '1czhouyu001', '1ccyc852852', '1csong8200', '1cyiyi188', '1cshan1688', '1cqq993919', '1cxpp8888', '1clx0311', '1cabc147258abc', '1cyang18877777', '1cqqqppp1223'
        ];
    }

    private function check_frozen_user_fund_xingchen()
    {
        if (get_config('app_ident', '') != 'xc') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataXingchen());
    }

    private function clear_frozen_user_fund_xingchen()
    {
        if (get_config('app_ident', '') != 'xc') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_clear_frozen_user_fund($this->getUserDataXingchen());
    }

    private function getUserDataXingchen()
    {
        return [
            'sdsd55', 'cvb2365', 'sishen1', 'yy16888', 'xc7930', 'cwp202088', 'admin9', 'yixin888', 'sss8888', 'zry573', 'yuqing0815', 'guo1998', 'yby1313', 'magang97', 'ydjyc1713202', 'vip131313', 'rty578', 'arui188', 'njf123', 'ww1688', 'kkaacc66', 'lxx9999', 'h666888', 'xin080721', 'qiang2233', 'wen478101046', 'xh1238', 'star666', 'bbb189', 'hongjie001', 'liuzong123', 'zhao442199', 'zuo888', 'lyl0804', 'lin0311', 'lxq003', 'liang5201314', 'dan6688', 'anan110424', 'yhy920505', 'daichao', 'ping8778', 'zss520', 'maoey987', 'lv299000', 'yahui168', 'zbf1314', 'xiaotian199', 'zhw8899', 'vin16888', 'qe1122', 'hehuayuan', 'zpw888', 'fengshao15', 'dcu4466', 'bbd1818', 'x123456x', 'jiao88', 'shanji888', 'hrkj888', 'aa135135', 'zyy521', 'sujun153', 'pr1818', 'lizhiqiang', 'q142154589', 'xiaohui168', 'qiu35683', 'a403151229', 'kent8888', 'ztg3579', 'ben7758521', 'lxc_999', 'toto888', 'yan61118', 'lei7758521', 'cb1987', 'fd789789', 'wrx6688', 'kulou888', 'jie0569', 'mumu888', 'junruy12', 'mofei888', 'wxp001', 'lvh888', 'wxy888', 'lvh666', 'liuye888', 'jjj88688', 'kjh99888', 'pdsyang01', 'gdjbdg4', 'hong66', 'qaqa66', 'sjbd99', 'kftest001', 'fu1998', 'cong2020', 'zlp888'
        ];
    }

    private function check_frozen_user_fund_yibo()
    {
        if (get_config('app_ident', '') != 'YB') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataYibo());
    }

    private function clear_frozen_user_fund_yibo()
    {
        if (get_config('app_ident', '') != 'YB') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_clear_frozen_user_fund($this->getUserDataYibo());
    }

    private function getUserDataYibo()
    {
        return [
            'kk6667888', 'as919656555', 'qq153143', 'lkz5200', 'gg1205', 'fwz55555', 'fa8888', 'gg45646', 'kuku88', 'abc0700187', 'sdy15145887770', 'yu0371', 'zyl73312', 'shou6699', 'wum8888', 'csh1998', 'tt866633', 'lai1023', 'v773366', 'abc4466', 'liang66666', 'zfz188', 'kang158158', 'peng1993', 'wen158158', 'a195453202', 'lqq6666', 'qwsxc188', 'yb9997', 'llwwxx', 'waiwai55555', 'cf33333', 'zz1166', 'lhy886'
        ];
    }

    private function check_frozen_user_fund_duowan()
    {
        if (get_config('app_ident', '') != 'dw') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataDuowan());
    }

    private function clear_frozen_user_fund_duowan()
    {
        if (get_config('app_ident', '') != 'dw') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_clear_frozen_user_fund($this->getUserDataDuowan());
    }

    private function getUserDataDuowan()
    {
        return [
            'zxxhtt520', 'wak888', 'tuantuan930', 'zhuwanyan123', 'fx8888', 'gsyx520', 'liufeng2019', 'lietou168', 'eric1982', 'qj58889', 'lm11111', 'chen1288', 'lp1011', 'a1502386', 'zjw6677', 'xiaoran1120', 'ergouzi', 'xu121318', 'chen599', 'xing37021', 'hy123456789', 'www890', 'kangkang66', 'laoying118', 'lxy178', 'vip8003', 'zgy1314', 'a67922157', 'cctv88', 'yf85800', 'gerdf4154', 'xiaole', 'gk8899', 'lz4469', 'dabozi', 'liu1885', 'dakaka888', 'huyaze1122', 'aa2425', 'wanglei007', 'www1688', 'hjy123', 'zhc5858', 'abc3147', 'rhc123', 'qcy123', 'vip55555', 'zjm00001', 'zds177', 'wang77552', 'fsaa385264', 'yuru54343', 'aseq7468', 'wjj9018', 'xx55110', 'weixiaobao464', 'aenj3554', 'xn0808', 'f58585858', 'wy0213', 'w55888', 'ws962464', 'qwe4655', 'ak0021', 'guandi888', 'phh888', 'vip00001', 'js6868', 'zn_1030', 'lh615615', 'zsq1913', 'a199610', 'yyf755', 'wqs110', 'jiu527', 'lmf511040', 'spk1688', 'a112511', 'sisi1913', 'xxygy0129', 'jfj6667', 'hzz999', 'zxx1173', 'qq172839', 'aaa999', 'hc978948659', 'l346286', 'vip99999', 'lsl1314', 'whj208', 'facai123', 'zhishu123', 'zyz1730698', 'xiajiangbin', 'dwceo209', 'rx5510', 'ming68', 'xingqiji', 'aaa131419', 'z6164298', 'zhongy168', 'yhf888', 'qing5545', 'moyaowei52', 'heer77976', 'heguozhen8', 'a810666', 'aa444558', 'duoli235', 'hetianxia', 'wer14777', 'yaqian8866', 'gfd15de', 'hhb2019', 'opq9512', 'zimo585210', 'guajitest', 'qwe5151', 'zhao1668', 'zml720', 'cj66888', 'helixin0423', 'wsw139', 'jian5621', 'jiuye8', 'qi8888666', 'ysjt001', 'hm8678889', 'stwp888', 'zlxm888', 'dwj56789', 'yaoyan7777', 'lxq270191', 'yan002', 'jiaxing6', 'qian006', 'bjy666888', 'qq199205', 'chenfei138', 'thm6688', 'a412299297', 'a587898', 'yanlin888', 'zxc121314', 'he98521', 'aiai168168', 'lwj131419', 'plm123', 'qunli123', 'af999662', 'xiang0912', 'ling138138138', 'll0333', 'pinger', 'li611314', 'xiaohui588', 'dsxl16888', 'jsz323', 'min331133', 'ff791214', 'zhongy16888', 'lj7788', 'wc6688', 'fa147258', 'li123456', 'zk7055', 'gdq521', 'hy8000', 'laoliu11', 'dy0099', 'ly6879', 'a739286047', 'a800600', 'he9999', 'jjk852', 'xxx731888', 'tangli', 'cff1686', 'chenchen88', 'gg8899', 'a1156037637', 'qibin1963', 'yl1996530', 'kakaka999', 'a15115875945', 'lwlulu', 'aa198912', 'wentuo', 'qhx168', 'xr520520', 'tielu5', 'xbh2540', 'lp6677', 'wyd3633', 'hgds888', 'qq277939205', 'ikhn26752', 'wangxinglong1986', 'wql1978', 'feis99s', 'wswbwps789', 'wsm1414', 'zl16888', 'jkdg6fg', 'jia199012', 'lishengfeng', 'lsq666', 'tj1943', 'fdvbg54d', 'wwx123', 'maoj666', 'q30737', 'yancong333', 'xin688688', 'a646541217', 'wang1993', 'qmbj2636', 'xiaosa999', 'hehhhe', 'dzq001', 'cczf2333', 'yuan201026', 'kkk777', 'xingxing68', 'dl8899', 'xixi200', 'sn8888', 'jubaopen', 'cplaoluo666', 'hch0111', 'xiaomeng168', 'f112233', 'lsf060708', 'oooi136', 'ps8585', 'yitianxin888', 'qwer5556', 'dfgh3210', 'mhc888888', 'lp6666', 'wxm8988', 'hzk123', 'hzxw123', 'zqz147258', 'a55688', 'kx1588', 'dw66171', 'zcy131419', 'zhuf8878', 'pingping', 'gong6688', 'fczz9876', 'scan9999', 'lichen9999', 'lsy123', 'dwb007', 'rhy000', 'a620202', 'jjj679', 'wr58588', 'kjud44d', 'mcdz9999', 'lhs1382', 'kx3366', 'dfh888', 'xiaofei666', 'fangguo520', 'zs666999', 'chen18888', 'ptzhaoshang1', 'xx1688', 'qb1234', 'huji334', 'zcff1288', 'wubf777888', 'zzcf1567', 'ffzc3556', 'xlw410909', 'uiop6543', 'zhang18888', 'hjkl0123', 'fanfan18888', 'dd2222', 'caomei137', 'fy0333', 'ttvy3393', 'lan888', 'dw1992', 'qwe12388', 'ljm2567', 'dhdf55', 'gnbz2ahw', 'ghdg922', 'liwei1', 'syb1234567', 'wqs123456', 'testceshi3', 's521920', 'kk6huj69', 'yan123', 'zx1888', 'tj112013', 'xf5656', 'hc9262', 'dxf810916', 'feilong001', 'zhangpw123', 'gtt521', 'lemon0615', 'svip116888', 'jb3323', 'zch1968', 'wangge88', 'ceshi123', 'jie698698', 'zxp131419', 'liu314', 'tian1314', 'zxg001', 'abc118118', 'a08150', 'bb24957', 'a120355', 'qws110', 'qwqw676', 'ccc502', 'bf0001', 'hmhtoby88', 'vip1314888', 'hxj456', 'wlr777', 'syb6666', 'wjzw1991', 'a541303793', 'chun818', 'yang18888', 'a601844517', 'hzh16888', 'gdj1314', 'zgf3913', 'wzgl518', 'y584302651', 'abc168', 'ubb1234', 'qiu123', 'a333593', 'jian168', 'zzz0924', 'qq8200', 'lxy789', 'davis88', 'hanjiyu123', 'jiang19880220', 'osols3s', 'song666', 'caiyc4134', 'guoshuai555', 'rhw777', 'baofa666', 'lldj88888', 'nice88', 'pp99663', 'hg1222', 'dj6666', 'zzz3535426', 'xhjunjun', 'guo123456', 'duowan888', 'yxc168', 'fengzong168', 'hg7777', 'mrjia520', 'gq1234567', 'szp0632', 'lipeng1452456347', 'alian168', 'lyl1314', 'gaoyue168', 'xxx888', 'yan168168', 'a616319', 'dongyiyi20001026', 'ygg1342001711', 'wen332898', 'yishou9998', 'kaka888', 'zzs5521', 'zz889900', 'a18944694686', 'hgq6666', 'dmw111', 'yj4414931', 'nb888888', 'cm21899', 'sam8888', 'qqssccxzj2', 'dwylb16888', 'dt88888', 'ldq518', 'sjy258', 'whs5189', 'a15266', 'wp2222', 'tzby11', 'qq1086', 'menghuan1886', 'rhw888', 'mls888', 'xiaowan888', 'a5234667', 'liuzebang', 'lucky86', 'nan2019', 'zhaodi123', 'wudan520', 'ygafjfh', 't2544095787', 'sheng16688', 'njcc567', 'gg1788', 'zg34567', 'zhl67676788', 'duowan168', 'lji159123', 'yyy123456', 'bc2333188855', 'langzi1689', 'vayne888', 'trj8688', 'jjj123456', 'xinhua999', 'chuntian', 'xinxin888', 'lij147258036', 'fc898989', 'tao555000', 'www168', 'tt79698', 'suze8785', 'czk888888', 'sxx131419', 'oy18664348044', 'q87247525', 'zb394333', 'duo722', 'gsq1314', 'xx7788', 'fj8888', 'p123456', 'hlb4913', 'cdp888', 'cwm999', 'gl131419', 'wp8803', 'qy261607764', 'meng1889', 'yuying2088361', 'lxp3355', 'wzr8888', 'xt1818', 'index8', 'asd1213', 'jie7590263', 'lizihao168', 'ljy123456', 'admin8', 'a1349198302', 'lyy8888', 'iphoneapp', 'clown286', 'ballackdl6', 'ym333444', 'qx123456', 'f667788', 'qw1319', 'duocai2088', 'xjj8888', 'haohao888', 'zjt8980', 'czl123456', 'toto123', 'a6969789', 'zdl888', 'basil01', 'hxt7777', 'loveli', 'yh20191120', 'lgc1987', 'lc686868', 'lyy9999', 'kk9926730', 'dw1234', 'ws525231', 'vip7758521', 'zly666888', 'fff668', 'jkl850', 'ljz182283', 'gq968808', 'q147258', 'zhang6688', 'aa1852001', 'yyg611', 'jie134124', 'faker580', 'lihb988', 'lyyuyu88', 'dwdw818', 'lujie18735643521', 'jzy16888', 'jr14789', 'jj7771', 'diaodiao888', 'dsa1356', 'gq123456', 'year89', 'fc0001', 'zdl123', 'die520', 'wori345', 'tz88188', 'xyq1314', 'chenjie8899', 'a2413844820', 'v2x688334', 'haowan666', 'asd333', 'zxc68686', 'wy1231', 'yan520', 'aikor888', 'a2591544205', 'tc1111', 'yf1234', 'redhome11', 'fjh433123', 'a544374920', 'gxgx22', 'bn2xzdz', 'qz1223343974', 'nxbhw2g', 'laoban168', 'wang1011', 'yuer33', 'w88888', 'hjj666', 'sk888888', 'zzf131419', 'zhoujiankang', 'coconut', 'bf123456', 'qn1314', 'a7895311', 'zyl99899', 'zw6666', 'sunbin', 'plh888', 'xx962788', 'lin678', 'lijun66', 'meng010', 'baobao81', 'dh333789', 'nyj321', 'dengpao', 'xiao999', 'heifeng66', 'xuexue', 'aaa234', 'moon32', 'mnxb2z', 'gr6666', 'xinghun1688', 'hzh1976', 'jingai19149', 'bobo521', 'dm8888', 'xiaofei8080', 'aj11258', 'zyc0078', 'xggg1888', 'kf123456', 'eve999', 'hezi888', 'xyh3433', 'jingjingdi55', 'zhy8888', 'fufu1599', 'zxcv1258', 'mengqiqi123', 'gangzi888', 'ljy16888', 'abc84258', 'hhh888', 's321xun', 'yangyang1213', 'lzl8989', 'q12411', 'xiangxiang8', 'dan8081', 'yangdada88', 'zp168168', 'aq888666', 'qian131414', 'tym0577', 'yg1911', 'liujiar1986', 'yinghao2', 'xuxu55899', 'liumang123', 'appwas', 'ad8888', 'nongzizhwu', 'tqing112', 'xiaoxiuxxni111', 'jinghua025', 'jmm518', 'wuji520', 'zzz123', 'ace666', 'hbf6688', 'zhanguo888', 'ee1086', 'xc5858', 'wq5022', 'duowan02', 'zpp6879', 'wcf888666', 'a689689', 'wx1216', 'zjy6688', 'wwr156', 'laogui1', 'gsh666', 'yxd80036', 'lum1541', 'we887910', 'dan888', 'aaa211553', 'erg80202', 'yb666999', 'chang331', 'zhao784', 'myz521', 'libing089', 'mm6666', 'zhang209', 'lyh888', 'yfj789789', 'qq79242412', 'lzp88999', 'pixiu2913', 'donfen888', 'laoy666', 'zy345678', 'lqq8888', 'qr1818', 'yingli999', 'mwm1122', 'bing089', 'ls2589', 'zsl0604', 'zx1111', 'sm7700', 'sun6688', 'z212739651', 'zhen66666', 'jindan8888', 'jackdanny888', 'mm0123', 'wzyx060114', 'kuai627', 'lx1313', 'mlzz1989', 'xjj23456', 'xiaomei386', 'di1234', 'jh88888', 'a43763227', 'qq2580', 'duojinbao', 'ww518518', 'nbssc168', 'llyuxinll', 'lzy777', 'kxl123456', 'chan168', 'azhanghongjing', 'lx861205', 'lxs0011', 'wenwen14', 'ylbtest0', 'lf1110', 'wyg666', 'fff666666', 'h34569', 'ergou777', 'jiajia88888', 'ww6343', 'a3064858', 'xiaoxin888', 'feng123', 'hy369369', 'z56789', 'flash99', 'lizexin', 'do2028', 'qq52566', 'wang9828', 'dwyx111', 'cwl001', 'jianqian8', 'zhf188', 'qqw987', 'clup1688', 'tuoni888', 'xiaobao666', 'mcl0000', 'm2080342', 'fhlmnet', 'aa1996', 'lc175203355', 'congtou666', 'ab99899', 'dan0188', 'yy148148', 'lw9898', 'hp666889', 'yc1788', 'y998998', 'zhifan666', 'wuheng118', 'lll444', 'guozi520', 'qq4545888', 'swtc91', 'dwyl99', 'akvscvw1', 'dys888', 'dpp021619', 'sakalaka5', 'ddszj666', 'li521521', 'zhou9876543', 'w186186', 'xrz08008', 'zyh0001', 'cxcs999', 'a888868', 'xiannv888', 'ljh123', 'zgj2020', 'xiao1921', 'lzc070906', 'dj16888', 'zxdrt789', 'wufont', 'djc1314', 'yy1988', 'rh291485', 'wanghui', 'ft123456', 'slh88888', 'aa55883', 'opp123456', 'lsy666', 'zmn520', 'xiaomo88', 'lihua1', 'rxtao56868', 'hu6667777', 'sdd12345', 'hh6633', 'aimumu910', 'a202088', 'wangzi888', 'haonan888', 'long1314', 'jiedu888', 'cmx888', 'ningle888', 'yk6688', 'ping520', 'ry6868', 'win1314', 'gyl0115', 'jyz001', 'waninn', 'djw2222', 'kkl333', 'ws666888', 'xfx123', 'ttk112', 'z715808734', 'ping123', 'hxw888', 'qmz1xsw', 'lff666888', 'xm5280', 'cyl168', 'ly1980', 'w931104', 'bai1997', 'duo7999', 'w147258', 'dw1111', 'nicholaschiu', 'lqq1315', 'llw1231', 'www123456', 'wx1212', 'xyxy18', 'yang84568456', 'jianyun555', 'qqq3399', 'ywq8538', 'lbb5591', 'jianyun520', 'guomei', 'bn456789', 'ckc29948', 'xinyan0688', 'vivi911911', 'mengxiang1117', 'gftyzz', 'duowan818', 'hyx888888', 'qqq15428', 'jiansheng888', 'zhangqingzhi', 'seolbs', 'hys8688', 'bmw7777', 'ck16888', 'wangyang', 'mh6688', 'q13265540444', 'haizi013', 'tang1226', 'hr88888', 'gubai1990', 'qq0829', 'qazwsx123', 'mmm1122', 'blbl55867', 'xiaohei888', 'zwj10055', 'jinlei', 'fd0011', 'zaizaiai', 'you452', 'xwt001', 'aq0906', 'llmm1477', 'luck51888', 'lisufang', 'vip807', 'pap554', 'lisuxia', 'ggg653', 'gang1989', 'yongge', 'jianxih', 'wangxun', 'feng6666', 'chen8060', 'jiexi01', 'ddfh123', 'mxk0918', 'duowan2', 'abcabc', 'ts99399', 'hx5888', 'wuwu123', 'weed84714', 'dddd5555', 'hhty3361', 'guoniu719', 'sjz0311', 'tter1427', 'wcj8888', 'nn4567', 'ji12312399', 'vip33343', 'guanjia121', 'zhoumanlian666', 'cheng841', 'hppyy1', 'maosherou', 'wawa5896', 'rdha6584', 'gxh931108', 'eswa4712', 'fdaw5142', 'fhlq08275', 'outuotuo', 'zhen6756', 'bibi088', 'hytd9984', 'qwer88745', 'zhizun168', 'zxf66666', 'mm1567', 'maomao555', 'hh1234', 'tyu456', 'zth888', 'zx1990', 'mama2560', 'dpp0219', 'ly0000', 'cy2008', 'ttk654', 'ssd234', 'qingxiu', 'dw74254732', 'zhn168', 'rock02', 'rock03', 'zhuguan02', 'zhuguan01'
        ];
    }

    private function check_frozen_user_fund_ued()
    {
        if (get_config('app_ident', '') != 'ued') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataUed());
    }

    private function clear_frozen_user_fund_ued()
    {
        if (get_config('app_ident', '') != 'ued') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_clear_frozen_user_fund($this->getUserDataUed());
    }

    private function getUserDataUed()
    {
        return [
            'ccccc88888', 'zwq8888', 'xinghun1688', 'x163184709', 'a156434', 'yzjqq3699', 'lz1890', 'qaz123456', 'buxiuchuanqi', 'tt1188', 'sanhao88', 'bmr888', 'xiarong999', 'cw8085', 'wan888', 'xu10088', 'hu2080', 'woshei458', 'a1214087712', 'jj166166', 'hhh8878', 'bq8628', 'gzd888', 'ytt2015', 'hzg5188', 'aa20150802', 'czcc666', 'xinyi1022', 'whc9999', 'lzbqs818', 'm18387803574', 'chen1990', 'sy888888', 'nwy654', 'a1039448', 'ljh555', 'wys666666', 'yf11388', 'junleihan020', 'faith88', 'ys8888', 'lcl6789', 'fhl123456', 'hx8748', 'mmc123', 'yyy2001', 'hu66666', 'wyh0763', 'zwq814814', 'ggg002', 'hong99923', 'cong234', 'missmusic888', 'a953270', 'amb1413', 'bc18893446520', 'elyls520', 'yaj198346', 'ued768', 'wang321', 'yemmbys', 'ckl888', 'a255150', 'ydh9121', 'hys827968835', 'xmg2020', 'zh119119', 'lxm56789', 'mlgb123', 'hlw908', 'jiacheng01', 'yucheng', 'cc18886', 'lmm888', 'w200248', 'qw890107', 'suze8785', 'ailyh666', 'laoge888', 'ab666666', 'sp4948102', 'ss2000', 'cyy1981', 'mz1689', 'daliu666', 'law2019', 'yc13451', 'lmx66286', 'yuanmei98', 'tt202027', 'l1390695', 'jiangc888', 'lp1030', 'qian66999', 'v159446', 'a624258321', 'vip1999', 'y888888', 'hei888', 'jiji16888', 'qq111123456', 'ls1215', 'ying181818', 'tong8899', 'laogui0', 'zxn1308293676', 'a99988', 'lx8888888', 'hl5555', 'qft888', 'cmn582', 'a277968', 'hyt6688', 'xz245015469', 'guige666', 'rw1475', 'zj123123', 'xyff33521', 'tgvip2020', 'hh5689', 'xfu2018', 'ab138322ab', 'jmk001', 'sh630412', 'hxy520wje', 'pl0088', 'ch16888', 'wy1019', 'cp371329', 'hh3030', 'yuyi520', 'wangzhi', 'yinya1997', 'qdd890103', 'duozhaowang', 'cc1664', 'dd3000', 'xt0007', 'z8553436', 'yemaozi23', 'a751295768', 'a15272006465', 'zb18812', 'w888000', 'aha1998', 'aqw123', 'h505170324', 'tm3614', 'hg66666', 'tbq1313', 'czl6688', 'y17770', 'yct345688782', 'tsir888', 'yhp0464', 'daolang9', 'kai8888', 'aini888', 'yuna1368', 'wangjunyi', 'r760366', 'xiaobiao8', 'youyio5656', 'liupai5678', 'l88888', 'iaisz123', 'zs667788', 'lx888888', 'lzw1992', 'wx2015', 'qiang78910', 'weidong111', 'lwd99999', 'wudi918', 'w1156789', 'xianzi88', 'zxc5688', 'ccc26688', 'ceo2020', 'jiu888', 'h123456789', 'a354991585', 'ljy5201314', 'songwenxia', 'lj5002', 'ldseven', 'l88888a', 'shen1688', 'yq778899', 'xy588119', 'jhz2580', 'zksky134', 'fj46828132', 'liu554610', 'zdh88688', 'love888', 'w1883593', 'caishen2020', 'tjx1234', 'xcr6688', 'qm511888', 'dai19866', 'fr8888', 'q45546000', 'wjh88888', 'xs5678', 'wang1583367', 'lwt1688', 'zf862788', 'wybf777888', 'hua9886', 'panfanliang123', 'ycc123', 'qmj886622', 'hai3366', 'xjh141349', 'newzy001', 'zyh810275', 'chanxin6666', 'douyu4545', 'zzc918', 'zhl123', 'aimi520', 'qq669499425', 'asd2226', 'ldm1431', 'cb505533138', 'ss20l30', 'cck5678', 'f7758258', 'zy6868', 'hao688888', 'zyh0001', 'zf0001', 'jiao123456', 'xmy6688', 'a130023200', 'nidec2010', 'ah141349', 'a1670216838', 'taiyang1', 'a15612722635', 'jaguar928', 'bobo6688', 'zz8104', 'lcl1004', 'xiong1', 'w15988062974', 'wifi2019', 'changzhi0519', 'pp2020', 'zh5728', 'xl96168', 'xjj520', 'as1836057449', 'a13269514012', 'xcy1996', 'a18320803621', 'tt1990', 'dsn62401', 'a957929532', 'ran5331', 'xy445566', 'fb8899', 'fy20208888', '2licanjie', 'gxsq12', 'xi520528', 'yg6666', 'zx88888888', 'jjj225', 'pa2345', 'yonghong68', 'asdzxc1', 'guong1997', 'yzs123456', 'a114117', 'qwe90908', 'z228899', 'ajs304', 'zy99999', 'gh131420', 'guo888888', 'meireey', 'ly8899', 'w2920248723', 'jiangliang1990', 'qwe369', 'alun01', 'xzg888', 'tsw888', 'asd1689', 'xjh666', 'xjh4bb', 'lek2668371679', 'jjj678', 'qg16888', 'w9971207', 'han18803005229', 'dd1994', 'f116688', 'zhaomj1978', 'wqy19988', 'kylin000', 'syc999', 'zxc2580', 'qp120620', 'liujian123', 'a15324287744', 'm680716', 'ah346786', 'dafei9911', 'lwg6668', 'ma1951', 'lxl556677', 'hm6688', 'zq123456', 'll1761526653', 'a444256834', 'zz5345', 'sg212121', 'wh6868', 'wjt1012', 'likexin1234', 'pyj9866', 'x52000', 'lhz12345', 'pp123456', 'llcc888', 'zs1761290', 'my7890', 'meinv521', 'q259112', 'cptt666', 'lingan', 'yc24647756', 'wenjin123', 'z99999z', 'yanan888', 'w520312', 'lanting241', 'pp410792271', 'zjyzyx', 'zx17776682060', 'zhizhi668', 'huizhang888', 'jin147258', 'wenwu08', 'at2020', 'ww6126', 'syjy521', 'vip3366', 'q1466161839', 'ysl16888', 'aa521521', 'haoa361', 'qw369537', 'a1370850', 'c931812', 'sos111', 'oyy888', 'gtzh123', 'ls2462680834', 'hhh585858aaa', 'xuzixuan', 'ly19870423', 'why123', 'sym117040', 'zhangjie123', 'gangge888', 'def789', 'ding8888', 'laobiao888', 'w206677', 'asd689', 'wyzdsbb', 'lan2020', 'wcj88888888', 'xsp1314', 'lyc_120750', 'qq774964802', 'shaolang668', 'w491168', 'wyy88888', 'w18221542391', 'yearlangmanwudao', 'vip001100', 'yt666123', 'jcy_666888', 'q979617445', 'lxs008', 'l785240', 'zam666', 'qq546122', 'xiaohai', 'a15257799348', 'qa6688', 'lry19980123', 'xhy123456789', 'zs123456', 'a52710120', 'lt67888', 'guwangao1989', 'lyt598598', 'chen18379498999', 'rong666', 'gyl369', 'ff7070', 'hol1967', 'tt3030', 'cmx888888', 'fa383838', 'aa123124', 'a102179', 'yjfhv1', 'a8411338', 'mm8999', 'uedhuangk', 'cb708019', 'bj666888', 'tu2917', 'Jun-18', 'panlin168168', 'love18174853585', 'asd142536', 'll718268895', 'yanyan', 'bhdrf888', 'li2492877043', 'sxp77989016', 'xhj1451289542', 'ht1995', 'zyh74110', 'xyj_13313677675', 'liwenhui198340', 'q2860760929', 'l980794154', 'lanshijun33', 'lh988622', 'abc18437525865', 'any_wxy0122', 'guoxinchao1', 'aabb1212', 'guojun123456', 'djdzq781221', 'luojia0601', 'likkxlfei', 'lxs666', 'aa0045', 'uxgllwh', 'j147369', 'twz159623', 's9999s', 'h888880', 'm269355', 'hh1248', 'as258258', 'jianghui753', 'aa836700', 'wsh187', 'jmq520', 'xukang999', 'yxz888', 'tnana55555ok', 'dc1992', 'd91888', 'mxs202020', 'hyq518518', 'a45177718', 'qz8888', 'vip112233', 'czxisa', 'taojing5', 'hye7878', 'wt2ljf', 'lijing6189', 'sanping888', 'xuyan1234', 'zz37788', 's870321', 'py88888', 'yxq1919', 'zl99355087', 'qiang123123', 'yu2580', 'cvb5687', 'qp6999', 'q41554', 'l29111', 'yb2087', 'wt10086', 'yong369', 'gg8886', 'zh12345678', 'zhg8899', 'pei147258', 'ff6688', 'stl147258', 'bobo1015', 'aa12388', 'luo0001', 'szh123321', 'cc66888', 'wbq2013', 'zhang2001', 'ljf888888', 'qq182182', 'thy168', 'aa200264', 'zhy8898', 'zcm2020', 'luo0003', 'ck0369', 'luo0002', 'boss666', 'xnly2019', 'tingting188', 'luck666', 'llp2288', 'cl3981', 'yyylll11', 'xgm888888', 'vvscsc', 'boss777', 'wangbin8858', 'zhe585858', 'zym19912', 'll585858', 'lhj2020', 'lzz7931', 'woy7496', 'cb8899', 'ltt9110', 'zxw168', 'acm2288', 'wsx13144', 'lxp1990', 'lds8816', 'czy999', 'mq16888', 'lzq6700', 'haw123456', 'wh6345', 'qwe1268', 'cd2020', 'cqh69435', 'uedmm888', 'fwx0123', 'fanbb6699', 'ww1122333', 'lin9999', 'pang668', 'yc668899', 'lin0001', '2xkzlxm888', 'peng1993', '2kaisa888', '2gt456456', '2yy181188', '2qq874006677', '2jingcai06', '2long2020', '2wy28888', '2al128828', '2jia789', 'jddfuch2', '2yj19900629', '2youth666', 'wwdomg', 'zz6699', '2gd1818', '2li1233', 'aadf3321', 'kly1963', '2qq15874724213', 'lx202020', '2lu123456', '2gaobo0810', '2syzbbbsjhhai', '2ndlhz1', '2zz888888', 'a7355994', 'feier888', '2fang0820', '2sjp6183', '2erge8888', '2ly1234567', '2zr1919', '2cqy888888', '2zhengzb', '2qj188888', '2cai149131', '2hyc1314', '2huqiong238', '2a168861123', '2jcn413413', '2qq1932791794', '2asd1888', '2fei419', '2l99999', '2dandan123', '2jiayouba', '2a32112555', '2xxj666', '2l578863938', '2ly9129', '2vip1230', '2wh141319', 'q050608', '2qj141319', '2ge12304', '2weiyi20201', '2zhengshuang', '2hui8888', '2tang001', '2z632644334', '2ued2huangk', '2long888', '2zx1818', '2jf1234567', '2lyt2020', 'kang7890', '2txy3450419586', '2wshwsh', '2tianwang95', '2ycj188888', '2bxyhlz', '2xl727685', '2dpy1212', '2xp6999', '2a15227575722', '2dj3838438', '2zzf888888', '2junge99', '2tx777777', '2a1431887335', '2a1471914046', '2a18640031305', '2kele0217', '2tian12300', '2szh664455', '2k1195440929', 'zjj1588', '2ac161613', '2songxudong', '2qfr2020', '2adnacht', '2qwer123', '2zwm0413', '2lz2727', '2cai3913jie', '2a6231054', '2liangtong258000', '2xiesm123', '2li17835977449', 'lyq666', '2wjp15170177910', '2yxq1919', '2sheng2020', '2li245773030', '2xhnb888', '2w128584', '2guo77456', '2aaa8787', '2xuexi598', '2yunyou666', '2hc88888', '2nannan885', '2zhou116118', '2mayinjie', '2pengying', '2qimendunjia1', '2bbc12345', '2gwb2113', '2lv2000', '2xtg26868', '2x13125', '2wmhd117', '2q1609291', '2wqh65358', '2w888000', '2lzx690769275', '2lhj1992', '2a377752628', '2facai558', '2k23560', '2chenpengyu', '2yx1984', '2xiao18711811900', '2xiaorui14', '2q203453131', '2likuaile', '2qq198037', '2zl123871023', '2jihaiqiu110', '2j198788', '2l15141711665', '2xianyi3528', '2yjy931201', '2a777987', '2q17608429105', '2l521abcd', '2zhuifeng889', '2dj1217', '2nmy0168', '2mlx18188', '2yan1688', 'cheng8998', '2jjj888', 'yi3699', '2zhm3042', '2vip585858', '2ttt2020', '2jianyin', '2cjy134679', '2a18870714992', '2www8866', '2zaj666', '2chc021', '2waw1122', '2hmx1319', '2hxuan857', '2xlm2345xlm', '2q28867977', '2q87295880', '2lfp518518', '2zhz188188', '2yn141319', '2bbb001', '2jq0099', '2gao0321', '2ff2020', '2q16213266664', '2a13112508276', '2zhangqi5201314', '2binge6015', '2shuangww123', '2lixiao2345', '2mld666', '2yc269882', '2cc1166', '2q666666', '2yyy1888', '2zj1009', '2hzy520', '2qeertyuiop', '2fjy15963', '2ywsg789', '2ch326854768', '2zf1888', '2hx1888', '2app111111', '2q13229998845', '2zhy_15637352882', '2a6677988a', '2s15591782672', '2mm1106', '2yjy880424', '2zyccy8', '2djh134723', 'dsf98765', '2ppx578', '2mw1996', '2z584532714', '2wlb558', '2thtd0002', '2chan782868', '2ph123456789', '2xu940923', '2aazxzx', '2wxm9966', '2tgnb123', '2qq92991', '2minsheng78168', '2kele1471', '2liugf147', '2mm9999', '2lz88888', '2zy0517', '2axiaohu', '2bgszhuang', '2clncln', '2lili417', '2pp168168', '2dali123', '2zp667788', '2eed159', '2ppp9999', '2wyy3601', '2gu3573', '2wang138', '2a47806', '2hh23568', '2hua185381'
        ];
    }

    private function check_frozen_user_fund_youyi()
    {
        if (get_config('app_ident', '') != 'yy') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataYouyi());
    }

    private function clear_frozen_user_fund_youyi()
    {
        if (get_config('app_ident', '') != 'yy') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_clear_frozen_user_fund($this->getUserDataYouyi());
    }

    private function getUserDataYouyi()
    {
        return [
            'honglou', 'fff0807', 'xyy66992', 'lw123456789', 'hzc0712', 'hcf123456', 'zh1995', 'xiaoye2019', 'lzw52371', 'wangxiaotao', 'jcj0712', 'sky0001', 'hg6666', 'qwqw1319', 'guyuwu', 'y980604', 'wsz333', 'dxd888', 'yuyang990319', 'shiyong888', 'jhg963', 'sktt8899', 'heyong', 'yi6998', 'shashou338', 'mzf1314520', 'lyr4916', 'lgh1968', 'matt1818', 'lyh2020', 'zhouming', 'chenwm', 'yu5980', 'dvd666', 'hyh999', 'liuyong', 'chuan318', 'dengxh', 'zjy1234', 'x900203521', 'zhou88', 'huangxh', 'a825986157', 'pzw654', 'lv810909', 'dongge518', 'a18859136925', 'zhuzhu07', 'fyw66666', 'fy2020', 'dh0714', 'jqs819', 'gaosanming', 'nd8888', 'z1132605338', 'zhqw14778180260', 'shuye12345', 'al888888', 'ceo168', 'goldenj', 'a4578362', 'dinner', 'pf0001', 'c1501311255', 'xlp8888', 'a15335493567', 'xzk0806', 'yyj888', 'yui1365', 'sy5188', 'mapleopop', 'xuan0888', 'yh1818', 'yx051640', 'wsx111', 'ye0947', 'yy5188', 'kk369369', 'zl5566', 'zw1544', 'w498505p', 'ddw1989', 'pang199i', 'luo1987', 'hyj123', 'janko888', 'aaass00', 'yybf8888', 'jhongl', 'hjg888', 'feiyan999', 'hw198009', 'taotao123', 'nmmoi11', 'mmm12345', 'kk8891888', 'yyuu1234', 'donglin', 'yesheng', 'id88888', 'ss12333', 'as138201456', 'zznihao6', 'dao4620', 'xhgli25', 'peng2020', 'aaooo33', 'haio12', 'gff6555', 'fhgh36', 'fhgh3623', 'axuhuhuan', 'ghmm33', 'bajkhgi88', 'jhjsajh66', 'doap666', 'sdgdedr12', 'hx920101', 'guiying', 'haomei520', 'cgg888', 'jd6677', 'pwmxb4', 'wy0555', 'oulaiya68', 'cs55555', 'qionghui123', 'hong66', 'djw0223', 'aa66666', 'qwe885566', 'qaz25886', 'bibi8906', 'a18501469697', 'ljy000', 'lf77008', 'smm521', 'wlj2020518', 'lvyuxue', 'hangxin66', 'feifei77', 'lz0422'
        ];
    }

    private function check_frozen_user_fund_bile()
    {
        if (get_config('app_ident', '') != 'BL') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_check_frozen_user_fund($this->getUserDataBile());
    }

    private function clear_frozen_user_fund_bile()
    {
        if (get_config('app_ident', '') != 'BL') {
            $this->info('执行平台名称不匹配');
            return;
        }
        $this->_clear_frozen_user_fund($this->getUserDataBile());
    }

    private function getUserDataBile()
    {
        return [
            'tiancai112', 'wz1986', 'ttoo88', 'bile8988', 'mmnn666', 'ks00123', 'ka6555', 'xixi666', 'longge33', 'shanshanmei', 'xoxo5111', 'dandan145', 'hhbb123', 'aixiang66', 'guiying', 'cheng2', 'oopp75', 'gentang11', 'lixiaomei', 'xianghan21', 'lezhong2020', 'longlin25', 'mao2588', 'feifei', 'meimei987', 'jizhu36', 'fuhai22', 'a13781580517', 'yuping', 'chundong', 'yangmei', 'zhiyong66', 'w741852', 'yy321321', 'wjj7131', 'yefeng11', 'changtai', 'gang520', 'baoshun', 'xiaohuang88', 'dongxing789', 'meihua5151', 'kaifa88', 'liuqun', 'xueli55', 'cscs8777', 'dongge688', 'xujiao22', 'wanghui520', 'feifan66', 'xlg918', 'xiaobai88', 'tiantian521', 'facai2020', 'asd890', 'ylq4520', '6sc12345', 'caishen66', 'linadad', 'hong543', 'xiaoju96', 'ljy189', 'ctxjrm1', 'kl65785', 'bypnx81374', 'blxhy22207', 'ygkfo38285', 'bgyoc74534', 'lsihh97210', 'qlkra45766', 'zpfcj21235', 'rkznd82525', 'sblzf31288', 'nurmy51315', 'zrpqe57640', 'erszq61103', '6zhaoshang01', 'wmtrw11438', 'dxdld90964', 'liu147', 'kekou555', 'wushui99', 'dada1987', 'kkk9986', 'ruirui88', 'zz0909', 'tpmwv94524', 'sbhqe61436', 'zyy4360', 'sgfyd39174', 'liuyi1314', 'xiaoyu888', 'sx77656', 'cvqau66768', 'qxelj54558', 'zzz8141', 'hkety36505', 'nbivc79769', 'zdj1247', 'pk5789', 'tdvaq92663', 'ahxea86324', 'akfyw50830', 'rroqp16953', 'xzhen0125', 'wp8899', 'ygidk03917', 'zxx785963510', 'itcqd19718', 'apwkh43962', 'nanfeng1', 'daidai123', 'long520', 'gtimq92839', 'apaqo54317', 'uipbh24787', 'kmuvq51248', 'basqg89225', 'ffdia37340', 'hgyhs18150', 'kmbva00301', 'gmfpw89091', 'mfolf82453', 'mhkqc29993', 'ipakf92674', 'evlsm59428', 'ubwxv33496', 'kgpmv52383', 'eujso71554', 'qduty77693', 'ibvat27345', 'a66666666', 'liomx87790', 'aibtz56745', 'aonlu59211', 'sgzkp80233', 'dhvpv54336', 'zbbqt69617', 'zpvnq70020', 'dysxs28520', 'ztcab84992', 'qvzpy24088', 'exdyd28060', 'tvagl02017', 'abbbc95010', 'zxc11235', 'irkjt52510', 'hhryi89669', 'sxcqa66180', 'kgcpr28011', 'fkths06672', 'rilos86335', 'noycg97008', 'evkbv85998', 'tkuqn79555', 'vtabe84041', 'aszij21568', 'rvqdk21873', 'fidnp29979', 'dwfsm95027', 'nzysk58705', 'ggg777', 'hilos86206', 'ycgdh63351', 'mpmmh05738', 'wpiri42543', 'wktak31428', 'ccybh78739', 'xbhqw84897', 'qwulf91530', 'sskdj21145', 'slexd64512', 'hnflr95789', 'xibiv26776', 'ghtae59121', 'ygypz86000', 'wdjwq15467', 'wtess89977', 'rat44964', 'lxbfi34490', 'dynvp22288', 'deng520', 'abcd1985', 'ml168168', 'atm1122', 'qwsx188', 'anl1238', 'yl888888', 'baise1', 'liming666', 'chunyan1', 'lb66866', 'yanzi9565', 'fyw123456', 'z1336520340', 'chen1995', 'jinge168', 'vdhhsj66', 'lwenwu', 'tt741852', 'a787044464', 'c1991kd', 'a13643700810', 'bq8899', 'fang112233', 'pyx1234', 'wang1998', 'abc0700', 'jiao1995', 'a15349993', 'cjl110', 'zyl6586', 'lj6666', 'vip1606', 'ji1999', 'zhang6666', 'dongfang2528', 'book123', 'y471685976', 'gyq00035', 'qwas123456', 'a152131', 'jt7552', 'js7856', 'a15737025121', 'dongfang25281', 'zjy2620331', 'wp971006', 'df123456', 'zhao123456', 'ysj8866', 'wangmeng', 'kkkk429', '6duxiang888', '6zongdai666', '6cai111', '6fengyun158', '6pang980123', '6tiantianfa', '6xiaodao666888', '6famen5266', '6yun3578', '6jiejie5501', '6jianghuai666', '6zhangzhang11', '6ls8380', 'suahe83173', 'neocs92128', 'ssoov10574', 'bcvou67341', '6douyu8', 're878484', 'q2826322427', 'uhghx24531', 'webof15271', 'kaxxo36093', 'rkith66282', 'pmzmw97149', 'mvrwc71211', 'yokha59275', 'vichm42228', 'ayrkd35784', 'kwpxq71885', 'wrypi77325', 'ussfs37753', 'ahqnj18990', 'boocp32693', 'gqwdq14687', 'mnums52401', 'xwstm81322', 'fdcjx38072', 'rjxmw09346', 'jqtxk45223', 'hdkkv50379', 'fdpdd77675', 'kolsn79995', 'rprll76314', 'ojbhv74880', 'qyvnl13152', 'sdesc60891', 'erucd70488', 'asbfr81496', 'rchel66830', 'psfmk00503', 'lqfpq94947', '6zhuguan01', 'qyixj89952', 'bzkyc91821', '6dzg920', '6yy5201314', '6xiao9005', '6q592696709', '6facai15888', '6bcbc666', '6bobo666', '6zongdai66', '6vcx125', '6jianm898', '6zhang071', '6ghfr549', '6cong0272'
        ];
    }

    /**
     * 优化索引
     */
    public function update_kill_code_win_index()
    {
        $this->line('platform => ' . get_config('app_ident', ''));
        DB::beginTransaction();
        try {

            Schema::table('kill_code_log', function (Blueprint $table) {
                $table->index(['third_ident', 'third_lottery', 'mode', 'step', 'error', 'created_at']);
            });

            Schema::table('kill_code_log', function (Blueprint $table) {
                $table->dropIndex(['step']);
                $table->dropIndex(['error']);
                $table->dropIndex(['created_at']);
            });

            DB::commit();
            $this->info('GGWIN杀号队列索引调整成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('GGWIN杀号队列索引调整失败');
            $this->error($e->getMessage());
        }
    }

    public function create_kill_code_win()
    {
        $this->line('platform => ' . get_config('app_ident', ''));
        DB::beginTransaction();
        try {
            (new \CreateTableKillCodeLog())->up();
            //(new \CreateTableKillCodeProjects())->up();
            DB::commit();
            $this->info('WIN杀号日志表添加成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("WIN杀号日志表添加失败" . PHP_EOL . $e->getMessage());
        }
    }

    public function remove_kill_code_win()
    {
        $this->line('platform => ' . get_config('app_ident', ''));
        DB::beginTransaction();
        try {
            (new \CreateTableKillCodeLog())->down();
            //(new \CreateTableKillCodeProjects())->down();
            DB::commit();
            $this->info('WIN杀号日志表删除成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("WIN杀号日志表删除失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加配置 是否显示彩种介绍
    private function _add_config_lotteryintroduce_display()
    {
        $operation = DB::table('config')->where('key', 'operation')->first();

        DB::beginTransaction();
        try {
            DB::table('config')->insert([
                'parent_id' => $operation->id,
                'title' => '是否显示彩种介绍',
                'key' => 'lotteryintroduce_display',
                'value' => '0',
                'description' => '0:隐藏彩种介绍，1:显示彩种介绍。默认0',
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加彩种介绍
    private function _add_lottery_introduce_table()
    {
        DB::beginTransaction();
        try {
            Schema::table('lottery', function (Blueprint $table) {
                $table->tinyInteger('introduce_status')->default(0)->comment('彩种介绍状态：0 未设置，1 已设置，2 被禁用');
            });

            (new \CreateTableLotteryIntroduce())->up();
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //增加设备盈亏日报表
    private function _add_report_daily_report_table()
    {
        DB::beginTransaction();
        try {
            (new \CreateTableReportDailyDevice())->up();
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    // 欢乐分分彩
    private function _hlffc_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'hlffc',
                'name' => '欢乐分分彩',
                'official_url' => 'http://brssys.com/results.html',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{"flag": "0", "times": "5", "max_time": "5", "hand_coding": "0", "probability": "10", "request_time": "10", "sleep_seconds": "5", "default_method": ""}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'hlffc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ],
                [
                    'name' => '新加坡奖源',
                    'ident' => 'Singapore\\Hlffc',
                    'url' => 'http://infonetwork.cn',
                    'status' => 'f',
                    'rank' => 100,
                ],
                [
                    'name' => '新加坡奖源_备用',
                    'ident' => 'Singapore2\\Hlffc',
                    'url' => 'http://brssys.com',
                    'status' => 'f',
                    'rank' => 100,
                ],
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    //DS电竞
    private function _thirdGameDs()
    {
        DB::beginTransaction();
        try {
            //创建third_game_ds_bet表
            (new \CreateTableThirdGameDsBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                        INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                        ('DSCR', 'DS电竞存入', 1, 2, 0, 3, 'DS电竞存入'),
                        ('DSTQ', 'DS电竞提取', 1, 1, 0, 3, 'DS电竞提取'),
                        ('DSFS', 'DS电竞返水', 1, 1, 0, 3, 'DS电竞返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                        INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                        ('Ds', 'DS电竞', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                        INSERT INTO third_game (
                            third_game_platform_id,
                            ident,
                            name,
                            merchant,
                            merchant_key,
                            api_base,
                            merchant_test,
                            merchant_key_test,
                            api_base_test,
                            status,
                            login_status,
                            transfer_status,
                            transfer_type,
                            deny_user_group,
                            last_fetch_time
                        )
                        VALUES(
                            (SELECT id FROM third_game_platform WHERE ident = 'Ds'),
                            'CiaDs',
                            'DS电竞(cia)',
                            '',
                            '',
                            'http://api.wm-cia.com/api/',
                            '',
                            '',
                            'http://uat.wmcia.net/api',
                            1,
                            0,
                            0,
                            0,
                            '[3]',
                            now()
                        );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("DS电竞SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    //BG大游
    private function _thirdGameBg()
    {
        DB::beginTransaction();
        try {
            //创建third_game_bg_bet表
            (new \CreateTableThirdGameBgBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                        INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                        ('BGCR', 'BG大游存入', 1, 2, 0, 3, 'BG大游存入'),
                        ('BGTQ', 'BG大游提取', 1, 1, 0, 3, 'BG大游提取'),
                        ('BGFS', 'BG大游返水', 1, 1, 0, 3, 'BG大游返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                        INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                        ('Bg', 'BG大游', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                        INSERT INTO third_game (
                            third_game_platform_id,
                            ident,
                            name,
                            merchant,
                            merchant_key,
                            api_base,
                            merchant_test,
                            merchant_key_test,
                            api_base_test,
                            status,
                            login_status,
                            transfer_status,
                            transfer_type,
                            deny_user_group,
                            last_fetch_time
                        )
                        VALUES(
                            (SELECT id FROM third_game_platform WHERE ident = 'Bg'),
                            'CiaBg',
                            'BG大游(cia)',
                            '',
                            '',
                            'http://api.wm-cia.com/api/',
                            '',
                            '',
                            'http://uat.wmcia.net/api',
                            1,
                            0,
                            0,
                            0,
                            '[3]',
                            now()
                        );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("BG大游SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    // 澳洲幸运10
    private function _azxy10_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'azxy10',
                'name' => '澳洲幸运10',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "23", "end_sale": "10", "drop_time": "10", "end_minute": "59", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "04", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "1", "rule": "[n8]", "year": "1", "month": "1"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'azxy10' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    // 新加坡SG飛艇
    private function _sgpk10_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'sgpk10',
                'name' => '新加坡SG飛艇',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "00", "end_sale": "10", "drop_time": "10", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "05", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "1", "rule": "[n8]", "year": "1", "month": "1"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'sgpk10' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 't',
                    'rank' => 100,
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    //修改虚拟货币银行汇率模式 字串栏位形态
    private function update_bank_virtual_bank_virtual()
    {
        DB::beginTransaction();
        try {
            DB::statement('ALTER TABLE "bank_virtual" ALTER COLUMN "api_fetch" TYPE varchar(1) USING "api_fetch"::varchar(1)');
            DB::update('UPDATE bank_virtual SET "api_fetch" = \'1\' where "api_fetch" like \'t\'');
            DB::update('UPDATE bank_virtual SET "api_fetch" = \'0\' where "api_fetch" like \'f\'');
            DB::commit();
            $this->info('api_fetch 字段修改成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //查看各平台权限点
    private function _show_admin_role_permissions()
    {
        $result = \Service\Models\Admin\AdminRolePermission::select('rule', 'name')->orderBy('rule')->get();
        foreach ($result as $value) {
            echo "rule：{$value->rule}   name：{$value->name}\r\n";
        }
    }

    // 新增自动风控配置 流水检查总开关 三方游戏流水倍数
    private function _add_auto_risk_config()
    {
        $risk = Config::where('key', 'auto_risk')->first();

        if (is_null($risk)) {
            $this->info('执行失败，无自动风控配置');
            return;
        }

        DB::beginTransaction();

        try {
            Config::insert([
                [
                    'parent_id' => $risk->id,
                    'title' => '流水检查总开关',
                    'key' => 'risk_user_consume_check',
                    'value' => '0',
                    'description' => '流水检查总开关（默认0，0:关闭，1:启用）'
                ],
                [
                    'parent_id' => $risk->id,
                    'title' => '三方游戏最低流水倍数要求',
                    'key' => 'risk_user_third_consume',
                    'value' => '0',
                    'description' => '三方游戏最低流水倍数要求（默认0，设置0.3即30%）'
                ],
            ]);

            DB::commit();
            $this->info('执行成功');
        } catch (Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    // 新增自动风控配置总开关
    private function _add_auto_risk_check_config()
    {
        $risk = Config::where('key', 'auto_risk')->first();

        if (is_null($risk)) {
            $this->info('执行失败，无自动风控配置');
            return;
        }

        DB::beginTransaction();

        try {
            Config::insert([
                [
                    'parent_id' => $risk->id,
                    'title' => '自动风控开关',
                    'key' => 'auto_risk_check',
                    'value' => '0',
                    'description' => '自动风控开关（默认0，0:关闭，1:启用）'
                ]
            ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function update_auto_risk_config()
    {
        DB::beginTransaction();
        try {
            Config::where('key', 'risk_user_consume')
                ->update([
                    'value' => '0',
                    'description' => '彩票最低流水倍数设定（默认：0.3 即30%， 0: 关闭）'
                ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //个人三方日报表
    private function _create_table_report_third_game_user_profit()
    {
        DB::beginTransaction();
        try {
            $row = DB::table('admin_role_permissions')->where('name', '第三方游戏')
                ->where('parent_id', 0)
                ->first();

            DB::table('admin_role_permissions')->insert([
                [
                    'parent_id' => $row->id,
                    'rule' => 'thirdgameuserprofit/index',
                    'name' => '个人三方日报表',
                ]
            ]);

            (new \CreateTableReportThirdGameUserProfit())->up();
            DB::commit();
            $this->info('report_third_game_user_profit 表创建成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 添加帐变类型，任务单
     * http://jira.empiregroup.solutions/browse/AP-1888
     * @throws \Exception
     */
    public function _create_order_types_ap1888()
    {
        $this->line('platform => ' . get_config('app_ident', ''));
        $order_types = [[
            'name' => '系统佣金发放',
            'ident' => 'XTYJFF',
            'display' => 1,
            'operation' => 1,
            'hold_operation' => 0,
            'category' => 2,
            'description' => '系统佣金发放[+]',
        ], [
            'name' => '系统私返发放',
            'ident' => 'XTSFFF',
            'display' => 1,
            'operation' => 1,
            'hold_operation' => 0,
            'category' => 2,
            'description' => '系统私返发放[+]',
        ], [
            'name' => '三方人工充值',
            'ident' => 'SFRGCZ',
            'display' => 1,
            'operation' => 1,
            'hold_operation' => 0,
            'category' => 2,
            'description' => '三方人工充值[+]',
        ], [
            'name' => '系统经营扣款',
            'ident' => 'XTJYKK',
            'display' => 1,
            'operation' => 2,
            'hold_operation' => 0,
            'category' => 2,
            'description' => '系统经营扣款[-]',
        ], [
            'name' => '彩票抽水',
            'ident' => 'CPCS',
            'display' => 1,
            'operation' => 2,
            'hold_operation' => 0,
            'category' => 2,
            'description' => '彩票抽水扣款[-]',
        ], [
            'name' => '彩票返水',
            'ident' => 'CPFS',
            'display' => 1,
            'operation' => 1,
            'hold_operation' => 0,
            'category' => 2,
            'description' => '彩票返水加款[+]',
        ],];
        DB::beginTransaction();
        try {
            DB::table('order_type')->insert($order_types);
            DB::commit();
            $this->info('帐变类型添加成功！');
        } catch (\Exception $e) {
            $this->info('帐变类型添加失败！');
        }
    }

    /**
     * 自动风控通知
     */
    private function _auto_risk_add_config()
    {
        $this->line('platform => ' . get_config('app_ident', ''));
        DB::beginTransaction();
        try {
            // 风控通知表配置
            $this->info('开始配置 projects_alert 数据表');
            if (Schema::hasColumn('projects_alert', 'type')) {
                $this->info('风控通知表 projects_alert 字段 type 已经存在，不需要重复添加');
            } else {
                Schema::table('projects_alert', function (Blueprint $table) {
                    $table->tinyInteger('type')->default(0)->comment('通知类型: 0 高额中奖，1 久未活跃用户投注，2 重点观察用户上线，3 今日登录数过高');
                });
            }

            if (Schema::hasColumn('projects_alert', 'user_id')) {
                $this->info('风控通知表 projects_alert 字段 user_id 已经存在，不需要重复添加');
            } else {
                Schema::table('projects_alert', function (Blueprint $table) {
                    $table->integer('user_id')->default(0)->comment('用户ID');
                });
            }

            if (Schema::hasColumn('projects_alert', 'extend')) {
                $this->info('风控通知表 projects_alert 字段 extend 已经存在，不需要重复添加');
            } else {
                Schema::table('projects_alert', function (Blueprint $table) {
                    $table->jsonb('extend')->default('{}')->comment('扩展字段');
                });
            }
            $this->info('开始配置 config 数据表');
            $this->info('检查 config 数据表 auto_risk_notice 配置');
            if ($auto_risk_config = DB::table('config')->where('key', 'auto_risk_notice')->first()) {
                $this->warn('自动风控配置已经存在！[ID ' . $auto_risk_config->id . ' ]');
                $aotu_risk_config_parent_id = $auto_risk_config->id;
            } else {
                $this->warn('自动风控配置不存在！添加中');
                $aotu_risk_config_parent_id = DB::table('config')->insertGetId([
                    'parent_id' => 0,
                    'title' => '自动风控通知配置',
                    'key' => 'auto_risk_notice',
                    'value' => '#',
                    'description' => '',
                ]);
            }

            if (empty(DB::table('config')->where('key', 'auto_risk_enabled')->first())) {
                $this->info('确定 config 数据表 添加 auto_risk_enabled 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '是否启用后台推送通知',
                    'key' => 'auto_risk_enabled',
                    'value' => '0',
                    'description' => '自动风控功能开关，1:开启，0:关闭。默认：0',
                ]]);
            }

            if (empty(DB::table('config')->where('key', 'auto_risk_show_duration')->first())) {
                $this->info('确定 config 数据表 添加 auto_risk_show_duration 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '推播通知显示持续时间',
                    'key' => 'auto_risk_show_duration',
                    'value' => '5000',
                    'description' => '单位：毫秒 默认：5000',
                ]]);
            }

            if (empty(DB::table('config')->where('key', 'auto_risk_inactivity_bets_alert')->first())) {
                $this->info('确定 config 数据表 添加 auto_risk_inactivity_bets_alert 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '久未活跃用户投注通知',
                    'key' => 'auto_risk_inactivity_bets_alert',
                    'value' => '0',
                    'description' => '1:开启，0:关闭。默认：0',
                ]]);
            }

            if (empty(DB::table('config')->where('key', 'auto_risk_inactivity_bets_days')->first())) {
                $this->info('确定 config 数据表 添加 auto_risk_inactivity_bets_days 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '久未活跃用户投注前连续几日未投注',
                    'key' => 'auto_risk_inactivity_bets_days',
                    'value' => '15',
                    'description' => '单位：天。默认：15',
                ]]);
            }

            if (empty(DB::table('config')->where('key', 'auto_risk_inactivity_bets_balance')->first())) {
                $this->info('确定 config 数据表 添加 auto_risk_inactivity_bets_balance 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '久未活跃用户投注持有馀额上限',
                    'key' => 'auto_risk_inactivity_bets_balance',
                    'value' => '10000',
                    'description' => '单位：元。默认：10000。投注前彩票馀额。',
                ]]);
            }

            if (empty(DB::table('config')->where('key', 'auto_risk_observe_login_alert')->first())) {
                $this->info('确定 config 数据表 添加 auto_risk_observe_login_alert 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '重点观察用户上线通知',
                    'key' => 'auto_risk_observe_login_alert',
                    'value' => '0',
                    'description' => '1:开启，0:关闭。默认：0',
                ]]);
            }

            if (empty(DB::table('config')->where('key', 'auto_risk_logins_tomuch_alert')->first())) {
                $this->info('确定 config 数据表 添加 auto_risk_logins_tomuch_alert 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '今日登录用户数过高通知',
                    'key' => 'auto_risk_logins_tomuch_alert',
                    'value' => '0',
                    'description' => '1:开启，0:关闭。默认：0',
                ]]);
            }

            if (empty(DB::table('config')->where('key', 'auto_risk_logins_tomuch_count')->first())) {
                $this->info('确定 config 数据表 添加 auto_risk_logins_tomuch_count 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '今日登录用户数上限',
                    'key' => 'auto_risk_logins_tomuch_count',
                    'value' => '1000',
                    'description' => '单位：人。默认：1000 或者 格式：1000|2000|3000',
                ]]);
            }

            if (empty(DB::table('config')->where('key', 'auto_risk_high_bonus_alert')->first())) {
                $this->info('确定 config 数据表 添加 auto_risk_high_bonus_alert 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '高额中奖通知',
                    'key' => 'auto_risk_high_bonus_alert',
                    'value' => '0',
                    'description' => '1:开启，0:关闭。默认：0',
                ]]);
            }

            $this->info('调整配置 op_bonus_alert 位置');
            $op_bonus_alert = DB::table('config')->where('key', 'op_bonus_alert')->first();
            if (empty($op_bonus_alert)) {
                $this->info('确定 config 数据表 添加 op_bonus_alert 配置');
                DB::table('config')->insert([[
                    'parent_id' => $aotu_risk_config_parent_id,
                    'title' => '高额中奖提醒金额',
                    'key' => 'op_bonus_alert',
                    'value' => '50000',
                    'description' => '单位：元。默认：50000。',
                ]]);
            } else {
                if ($op_bonus_alert->parent_id != $aotu_risk_config_parent_id) {
                    $this->info('确定 config 数据表 调整 op_bonus_alert 配置需要');
                    DB::table('config')->where('key', 'op_bonus_alert')->update([
                        'parent_id' => $aotu_risk_config_parent_id
                    ]);
                } else {
                    $this->info('确定 config 数据表 op_bonus_alert 配置已调整，无需修改');
                }
            }

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }

        $this->info('开始添加 projects_alert 索引');
        DB::beginTransaction();
        try {
            Schema::table('projects_alert', function (Blueprint $table) {
                $table->index('project_id');
                $table->index(['type', 'user_id']);
            });
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    /**
     * 风控提示推送添加toast，删除 auto_risk_high_bonus_alert
     */
    public function _create_risk_notice_toast()
    {
        $app_ident = get_config('app_ident', '');
        $this->line('platform => ' . $app_ident);
        DB::beginTransaction();
        try {
            $id = 0;
            $delete_af = 0;
            if ($auto_risk_notice_toast = DB::table('config')->where('key', 'auto_risk_notice_toast')->first()) {
                $this->info('配置 auto_risk_notice_toast 已经存在，值为 ' . $auto_risk_notice_toast->value);
            } else {
                if (!$auto_risk_config = DB::table('config')->where('key', 'auto_risk_notice')->first()) {
                    $this->info('配置 auto_risk_notice_toast 的上级auto_risk_notice不存在！，不用操作');
                } else {
                    $this->info('配置 auto_risk_notice_toast 不存在！，执行添加，上级ID' . $auto_risk_config->id);
                    $id = DB::table('config')->insertGetId([
                        'parent_id' => $auto_risk_config->id,
                        'title' => '哪些风控提示类型开启弹窗',
                        'key' => 'auto_risk_notice_toast',
                        'value' => '0,1',
                        'description' => '默认0,1，多个业务用,隔开。0高额中奖，1久未活跃用户投注，2重点观察用户上线，3今日登录数过高',
                    ]);
                }
            }
            if ($auto_risk_high_bonus_alert = DB::table('config')->where('key', 'auto_risk_high_bonus_alert')->first()) {
                $this->info('配置 auto_risk_high_bonus_alert 已经存在，执行删除');
                $delete_af = DB::table('config')->where('key', 'auto_risk_high_bonus_alert')->delete();
            }

            DB::commit();
            if ($id) {
                $this->info("执行成功，ID" . $id);
            } else {
                $this->info("事务成功，新增配置ID返回失败");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    /**
     * 风控弹窗添加已弹字段
     */
    private function add_create_risk_notice_toast_column()
    {
        $app_ident = get_config('app_ident', '');
        $this->line('platform => ' . $app_ident);
        if (\Schema::hasColumn('projects_alert', 'toast_admin_ids')) {
            $this->info('toast_admin_ids 字段已存在');
            return;
        } else {
            $this->info('toast_admin_ids 字段不存在，需要添加');
            try {
                DB::beginTransaction();
                Schema::table('projects_alert', function (Blueprint $table) {
                    $table->jsonb('toast_admin_ids')->default('[]')->comment('已弹管理员ID');
                });
                DB::commit();
                $this->info('toast_admin_ids 字段添加成功');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->info("执行失败" . PHP_EOL . $e->getMessage());
            }
        }
    }

    // 新增 WithdrawalRisk 自动风控审核备注栏位
    private function add_withdrawal_risk_remark_column()
    {
        try {
            if (\Schema::hasColumn('withdrawal_risks', 'auto_risk_remark')) {
                $this->info('auto_risk_remark 字段已存在');
                return;
            }
            DB::beginTransaction();
            DB::statement("ALTER TABLE public.withdrawal_risks ADD COLUMN IF NOT EXISTS auto_risk_remark VARCHAR(200) DEFAULT '' NOT NULL");
            DB::statement("COMMENT ON COLUMN \"public\".\"withdrawal_risks\".\"auto_risk_remark\" IS '自动风控备注'");
            DB::commit();
            $this->info('withdrawal_risks 表 auto_risk_remark 字段添加成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    //推荐彩种增加栏位
    private function add_lottery_recommend_column()
    {
        DB::beginTransaction();
        try {
            Schema::table('lottery_recommend', function (Blueprint $table) {
                $table->integer('interval_minutes')->default(0)->comment('重复弹出间隔时间(分)');
            });
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //九城2 24日25日浮动工资待发放的数据变更状态为已取消
    private function update_jiucheng2_floatwage_status()
    {
        //确认是必乐平台
        $platform_type_ident = get_config('dailywage_type_ident');
        if ($platform_type_ident != 'Jiucheng2') {
            $this->info('当前方法只允许 Jiucheng2 使用');
            return;
        }
        $dates = [
            '2021-01-24',
            '2021-01-25',
        ];
        $result = \Service\Models\FloatWages::whereIn('date', $dates)
            ->where('status', 1)
            ->update(['status' => 5]);
        $this->info(json_encode($dates) . "更新成功,共{$result} 条");
    }

    /**
     * 官彩休市
     */
    private function _lottery_closed_time()
    {
        $platform_ident = get_config('dividend_type_ident');
        $lottery_idents = [
            'fucai3d', //福彩3D
            'pl3pl5', //排列三五
        ];
        $up_data = [
            'closed_time_start' => '2021-02-09 00:00:00', //21年春节休市
            'closed_time_end' => '2021-02-18 23:59:59',
            //            'closed_time_start' => '2021-10-01 00:00:00',
            //            'closed_time_end' => '2021-10-04 23:59:59',
        ];
        $this->info('平台：' . $platform_ident . '，休市时间 ' . $up_data['closed_time_start'] . '  ~ ' . $up_data['closed_time_end']);
        DB::beginTransaction();
        try {
            //删除奖期
            $lottery_ids_query = \Service\Models\Lottery::select(['id'])->whereIn('ident', $lottery_idents);
            //$this->info($lottery_ids_query->toSql());
            $delete_builder = \Service\Models\Issue::whereIn('lottery_id', $lottery_ids_query)
                ->where('belong_date', '>=', $up_data['closed_time_start']);
            $delete_af = $delete_builder->delete();
            $this->info('奖期删除数：' . $delete_af);
            //$this->info($delete_builder->toSql());
            $update_builder = \Service\Models\Lottery::whereIn('ident', $lottery_idents);
            $update_af = $update_builder->update($up_data);
            $this->info('彩种更新数：' . $update_af);
            //$this->info($update_builder->toSql());
            DB::commit();
            $this->info('更新彩种 closed_time 成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('更新彩种 closed_time 失败：' . $e->getMessage());
        }
    }

    //增加配置 同IP登录帐号数限制
    private function _add_config_login_ip_limit()
    {
        $operation = DB::table('config')->where('key', 'reg')->first();

        DB::beginTransaction();
        try {
            DB::table('config')->insert([[
                'parent_id' => $operation->id,
                'title' => '同IP登录帐号数最大值',
                'key' => 'login_ip_limit_num',
                'value' => '30',
                'description' => '同IP登录帐号数检查时间内，同IP登录帐号最大值。默认：30个，设为0则不开启',
            ], [
                'parent_id' => $operation->id,
                'title' => '同IP登录帐号数检查时间',
                'key' => 'login_ip_limit_time',
                'value' => '60',
                'description' => '单位：分钟。默认：60',
            ]]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //修改活动异常盈利扣减账变类型
    private function modify_ycylkj_ordertype()
    {
        DB::beginTransaction();
        try {
            \Service\Models\OrderType::where("ident", "YCYLKJ")->update([
                'name' => '活动异常盈利扣减',
                'description' => '活动异常盈利扣减',
            ]);

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //报表单项查询添加菜单
    private function _create_report_single_menus()
    {
        DB::beginTransaction();
        try {
            $row = DB::table('admin_role_permissions')->where('name', '报表管理')
                ->where('parent_id', 0)
                ->first();

            DB::table('admin_role_permissions')->insert([
                [
                    'parent_id' => $row->id,
                    'rule' => 'reportsingle/index',
                    'name' => '报表单项查询',
                ]
            ]);
            DB::commit();
            $this->info('报表单项查询菜单创建成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }
    }

    //ued修改试玩用户昵称
    private function _ued_modify_shiwan_nickname()
    {
        $result = User::select('username', 'usernick')
            ->where('user_group_id', 3)
            ->orderBy('id')
            ->get();

        foreach ($result as $value) {
            if (preg_match('/^shiwan[\d]/', $value->username)) {
                $num = str_replace('shiwan', '', $value->username);
                $new_nickname = '试玩用户' . $num;
                if ($new_nickname !== $value->usernick) {
                    echo $value->username . ',(' . $value->usernick . '=>' . $new_nickname . ')';
                    $update_sign = 0;

                    $update_sign = User::where('user_group_id', 3)
                        ->where('username', $value->username)
                        ->update([
                            'usernick' => $new_nickname
                        ]);

                    echo ',result:' . $update_sign;

                    echo PHP_EOL;
                }
            }
        }
    }

    private function win_codes2_config()
    {
        $drawsource_config_parent_id = Config::where('key', 'drawsource_config')->value('id');
        if (empty($drawsource_config_parent_id)) {
            $this->info('失败：drawsource_config_parent_id 不存在');
            return;
        }
        DB::beginTransaction();
        try {
            Config::insert([
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
            ]);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function create_kill_code_jobs_table()
    {
        DB::beginTransaction();
        try {
            //添加表
            if (\Schema::hasTable('kill_code_jobs')) {
                $this->info('表 kill_code_jobs 已存在');
            } else {
                (new \CreateKillCodeJobsTable())->up();
            }
            DB::commit();
            $this->info('kill_code_jobs 表创建成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // 添加湖北11选5、极速11选5、上海快三、北京快三、极速快三、澳洲幸运5、凤凰腾讯PK10 七种彩种
    private function _add_seven_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['11x5']->id,
                'ident' => 'hb11x5',
                'name' => '湖北11选5',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "1200", "status": "1", "end_hour": "21", "end_sale": "120", "drop_time": "120", "end_minute": "55", "end_second": "00", "start_hour": "08", "start_minute": "35", "start_second": "00", "first_end_hour": "08", "input_code_time": "30", "first_end_minute": "55", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n2]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "sprepeat": "", "end_number": "11", "startrepeat": "", "spend_number": "", "start_number": "01", "spstart_number": ""}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* 0,8-22 * * *',
            ],
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['11x5']->id,
                'ident' => 'js16811x5',
                'name' => '极速11选5',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "75", "status": "1", "end_hour": "23", "end_sale": "8", "drop_time": "8", "end_minute": "59", "end_second": "15", "start_hour": "23", "start_minute": "59", "start_second": "15", "first_end_hour": "00", "input_code_time": "5", "first_end_minute": "00", "first_end_second": "30", "first_start_yesterday": "1"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "[n8]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "sprepeat": "", "end_number": "11", "startrepeat": "", "spend_number": "", "start_number": "01", "spstart_number": ""}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['k3']->id,
                'ident' => 'shk3',
                'name' => '上海快三',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "1200", "status": "1", "end_hour": "22", "end_sale": "180", "drop_time": "180", "end_minute": "19", "end_second": "00", "start_hour": "08", "start_minute": "39", "start_second": "00", "first_end_hour": "08", "input_code_time": "60", "first_end_minute": "59", "first_end_second": "00", "first_start_yesterday": "1"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* 8-23 * * *',
            ],
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['k3']->id,
                'ident' => 'bjk3',
                'name' => '北京快三',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "1200", "status": "1", "end_hour": "23", "end_sale": "180", "drop_time": "180", "end_minute": "40", "end_second": "00", "start_hour": "09", "start_minute": "00", "start_second": "00", "first_end_hour": "09", "input_code_time": "60", "first_end_minute": "20", "first_end_second": "00", "first_start_yesterday": "1"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['k3']->id,
                'ident' => 'js168k3',
                'name' => '极速快三',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "75", "status": "1", "end_hour": "23", "end_sale": "8", "drop_time": "8", "end_minute": "59", "end_second": "15", "start_hour": "23", "start_minute": "59", "start_second": "15", "first_end_hour": "00", "input_code_time": "5", "first_end_minute": "00", "first_end_second": "30", "first_start_yesterday": "1"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "[n8]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "3", "end_number": "6", "start_number": "1"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 't',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'azxy5',
                'name' => '澳洲幸运5',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "23", "end_sale": "0", "drop_time": "1", "end_minute": "58", "end_second": "40", "start_hour": "23", "start_minute": "58", "start_second": "40", "first_end_hour": "00", "input_code_time": "1", "first_end_minute": "03", "first_end_second": "40"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "[n8]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'fhtxpk10',
                'name' => '凤凰腾讯PK10',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort":"0","cycle":"60","status":"1","end_hour":"00","end_sale":"0","drop_time":"1","end_minute":"00","end_second":"00","start_hour":"00","start_minute":"00","start_second":"00","first_end_hour":"00","input_code_time":"0","first_end_minute":"01","first_end_second":"00","first_start_yesterday":"0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0200',
                'min_profit' => '0.0100',
                'issue_rule' => '{"day":"0","rule":"Ymd-[n4]","year":"0","month":"0"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '1',
                'special_config' => '{"times": 5, "probability": 10}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'hb11x5' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ],
            ],
            'js16811x5' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ],
            ],
            'shk3' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ],
            ],
            'bjk3' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ],
            ],
            'js168k3' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ],
            ],
            'azxy5' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ],
            ],
            'fhtxpk10' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ],
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    //activity_record表增加字段
    private function _add_activity_record_column()
    {
        DB::beginTransaction();
        try {
            Schema::table('activity_record', function (Blueprint $table) {
                $table->integer('relation_id')->default(0)->comment('相关id');
            });
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //查询activity_record表    rechargecommision     活动的领奖记录
    private function _query_activity_record()
    {
        $sql = "select id,activity_id,user_id,draw_money,extral,relation_id from activity_record
                where activity_id in (select id from activity where ident='rechargecommision')
                order by id";
        $result = DB::select($sql);
        foreach ($result as $row) {
            echo "id:{$row->id},activity_id:{$row->activity_id},user_id:{$row->user_id},draw_money:{$row->draw_money},extral:{$row->extral},relation_id:{$row->relation_id}" . PHP_EOL;
        }
    }

    /**
     * 添加抽水数据表，配置，菜单，添加注单表字段及索引
     */
    private function create_pumps_table_config()
    {
        $this->line('platform => ' . get_config('app_ident', ''));
        DB::beginTransaction();
        try {
            //添加表 pump_rules
            if (\Schema::hasTable('pump_rules')) {
                $this->warn('pump_rules 表 已存在');
            } else {
                (new \CreateTablePumpRules())->up();
                $this->info('pump_rules 表 添加成功');
            }
            //添加表 pumps
            if (\Schema::hasTable('pump_inlets')) {
                $this->warn('pump_inlets 表已存在');
            } else {
                (new \CreateTablePumpInlets())->up();
                $this->info('pump_inlets 表添加成功');
            }
            //添加表 pump_outlets
            if (\Schema::hasTable('pump_outlets')) {
                $this->warn('pump_outlets 表已存在');
            } else {
                (new \CreateTablePumpOutlets())->up();
                $this->info('pump_outlets 表添加成功');
            }
            $pump = Config::where('key', 'pump')->first();
            if ($pump) {
                $this->warn('pump 配置已存在');
            } else {
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
                        'description' => '抽水目录名',
                    ], [
                        'parent_id' => $pump_id,
                        'title' => '是否计算抽水',
                        'key' => 'pump_inlet_enabled',
                        'value' => '0',
                        'description' => '抽水开关，1:开启，0:关闭。默认：0',
                    ], [
                        'parent_id' => $pump_id,
                        'title' => '是否扣除抽水',
                        'key' => 'pump_inlet_order_enabled',
                        'value' => '0',
                        'description' => '扣除抽水开关，1:开启，0:关闭。默认：0',
                    ], [
                        'parent_id' => $pump_id,
                        'title' => '是否计算返水',
                        'key' => 'pump_outlet_enable',
                        'value' => '0',
                        'description' => '返水开关，1:开启，0:关闭。默认：0',
                    ], [
                        'parent_id' => $pump_id,
                        'title' => '是否发放返水',
                        'key' => 'pump_outlet_order_enable',
                        'value' => '0',
                        'description' => '发放返水开关，1:开启，0:关闭。默认：0',
                    ], [
                        'parent_id' => $pump_id,
                        'title' => '默认抽水配置',
                        'key' => 'pump_default_rule',
                        'value' => '{"enable":1,"conditions":["bonus"],"inlet":[{"scale":0.1,"bonus":10000,"outlet":[{"level":2,"scale":0.1}]},{"scale":0.05,"bonus":1000,"outlet":[{"level":2,"scale":0.05}]}]}',
                        'description' => '',
                    ], [
                        'parent_id' => $pump_id,
                        'title' => '指定抽水彩种',
                        'key' => 'pump_lottery_ident',
                        'value' => '',
                        'description' => '针对哪些彩种抽水，多个彩种标识用,隔开，默认为空，全部都抽',
                    ]
                ]);
                $this->info('pump 配置添加成功');
            }
            $order_type_cpcs = OrderType::where('ident', 'CPCS')->first();
            if ($order_type_cpcs) {
                $this->warn('彩票抽水帐变类型已经存在');
                if ($order_type_cpcs->category <> 1) {
                    $order_type_cpcs->category = 1;
                    $order_type_cpcs->save();
                    $this->warn('彩票抽水帐变分类已经改为彩票帐变');
                }
            } else {
                $order_type_cpcs_id = OrderType::insertGetId([
                    'name' => '彩票抽水',
                    'ident' => 'CPCS',
                    'display' => 1,
                    'operation' => 2,
                    'hold_operation' => 0,
                    'category' => 1,
                    'description' => '彩票抽水',
                ]);
                $this->info('彩票抽水帐变类型添加成功，ID' . $order_type_cpcs_id);
            }
            $order_type_cpfs = OrderType::where('ident', 'CPFS')->first();
            if ($order_type_cpfs) {
                $this->warn('彩票返水帐变类型已经存在');
                if ($order_type_cpfs->category <> 1) {
                    $order_type_cpfs->category = 1;
                    $order_type_cpfs->save();
                    $this->warn('彩票返水帐变分类已经改为彩票帐变');
                }
            } else {
                $order_type_cpfs_id = OrderType::insertGetId([
                    'name' => '彩票返水',
                    'ident' => 'CPFS',
                    'display' => 1,
                    'operation' => 1,
                    'hold_operation' => 0,
                    'category' => 1,
                    'description' => '彩票返水',
                ]);
                $this->info('彩票返水帐变类型添加成功，ID' . $order_type_cpfs_id);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('执行失败，原因：' . $e->getMessage());
        }
    }

    /**
     * 添加充入游戏币菜单
     */
    public function create_carry_menus()
    {
        //打印平台标识
        $this->line('platform => ' . get_config('app_ident', ''));
        $carry_row = DB::table('admin_role_permissions')->where('rule', 'deposit/carry')
            ->first();
        if ($carry_row) {
            $this->warn('权限 deposit/carry 已经存在，不要重复执行！');
            return false;
        }
        //添加菜单，并且同步，有审核权限的，也同样有充入游戏币权限
        DB::beginTransaction();
        try {
            $fa_row = DB::table('admin_role_permissions')->where('rule', 'withdrawal')
                ->where('parent_id', 0)
                ->first();
            $carry_has_data = [];
            $carry_row_id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => $fa_row->id,
                'rule' => 'deposit/carry',
                'name' => '[充值]加款到账',
            ]);
            $this->info('[充值]充入游戏币 菜单创建成功,ID ' . $carry_row_id);
            //查找人工审核纪录
            $row_deal = DB::table('admin_role_permissions')->where('rule', 'deposit/deal')
                ->where('parent_id', $fa_row->id)
                ->first();
            $this->info('后台权限 deposit/deal  的ID ' . $row_deal->id);
            $has_rows = DB::table('admin_role_has_permission')->leftJoin('admin_roles', 'admin_roles.id', 'admin_role_has_permission.role_id')->where('permission_id', $row_deal->id)
                ->select(['admin_role_has_permission.*', 'admin_roles.name as role_name'])
                ->get();
            if ($has_rows->isNotEmpty()) {
                $this->info('共 ' . $has_rows->count() . ' 个角色拥有 [充值]人工审核 权限');
                foreach ($has_rows as $has_row) {
                    $this->info('角色' . $has_row->role_name . ' [ ' . $has_row->role_id . ' ] 拥有 [充值]人工审核 权限');
                    $carry_has_data[] = [
                        'role_id' => $has_row->role_id,
                        'permission_id' => $carry_row_id,
                    ];
                }
                DB::table('admin_role_has_permission')->insert($carry_has_data);
                $this->info('后台权限 admin_role_has_permission 批量写入成功 ');
            }
            DB::commit();
            $this->info('操作完成');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('操作失败，原因：' . $e->getMessage());
        }
    }

    // 新增彩种 168系列 河腾系列
    private function _168_ht_lottery()
    {
        $categorys = \Service\Models\LotteryCategory::select(['id', 'ident'])->get()->keyBy('ident');
        $method_categorys = \Service\Models\LotteryMethodCategory::select(['id', 'ident'])->get()->keyBy('ident');

        $lottery_datas = [
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'xyft168b',
                'name' => '168幸运飞艇B',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "04", "end_sale": "30", "drop_time": "30", "end_minute": "04", "end_second": "00", "start_hour": "04", "start_minute": "04", "start_second": "00", "first_end_hour": "13", "input_code_time": "8", "first_end_minute": "09", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'jsft168b',
                'name' => '168极速飞艇B',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "1", "rule": "[n8]", "year": "1", "month": "1"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'jssc168b',
                'name' => '168极速赛车B',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "1", "rule": "[n8]", "year": "1", "month": "1"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'azxy5b',
                'name' => '澳洲幸运5B',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "23", "end_sale": "30", "drop_time": "30", "end_minute": "58", "end_second": "40", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "8", "first_end_minute": "03", "first_end_second": "40", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "1", "rule": "[n8]", "year": "1", "month": "1"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Lotto']->id,
                'lottery_method_category_id' => $method_categorys['pk10']->id,
                'ident' => 'azxy10b',
                'name' => '澳洲幸运10B',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "23", "end_sale": "30", "drop_time": "30", "end_minute": "59", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "8", "first_end_minute": "04", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "1", "rule": "[n8]", "year": "1", "month": "1"}',
                'number_rule' => '{"len": "10", "end_number": "10", "start_number": "01"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'htffc',
                'name' => '河腾分分彩',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "60", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "01", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n4]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'ht5fc',
                'name' => '河腾五分彩',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "300", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "05", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ],
            [
                'lottery_category_id' => $categorys['Digit']->id,
                'lottery_method_category_id' => $method_categorys['ssc']->id,
                'ident' => 'ht10fc',
                'name' => '河腾十分彩',
                'official_url' => '',
                'deny_method_ident' => '[]',
                'issue_set' => '[{"sort": "0", "cycle": "600", "status": "1", "end_hour": "00", "end_sale": "5", "drop_time": "5", "end_minute": "00", "end_second": "00", "start_hour": "00", "start_minute": "00", "start_second": "00", "first_end_hour": "00", "input_code_time": "2", "first_end_minute": "10", "first_end_second": "00", "first_start_yesterday": "0"}]',
                'week_cycle' => '127',
                'min_spread' => '0.0100',
                'min_profit' => '0.0200',
                'issue_rule' => '{"day": "0", "rule": "Ymd-[n3]", "year": "0", "month": "0"}',
                'number_rule' => '{"len": "5", "end_number": "9", "start_number": "0"}',
                'special' => '0',
                'special_config' => '{}',
                'closed_time_start' => '2017-01-01 00:00:00',
                'closed_time_end' => '2017-01-01 00:00:00',
                'status' => 'f',
                'cron' => '* * * * *',
            ]
        ];
        $drawsource_datas = [
            'xyft168b' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ]
            ],
            'jsft168b' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ]
            ],
            'jssc168b' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ]
            ],
            'azxy5b' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ]
            ],
            'azxy10b' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ]
            ],
            'htffc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ]
            ],
            'ht5fc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ]
            ],
            'ht10fc' => [
                [
                    'name' => 'Apollo奖源',
                    'ident' => 'Apollo\\Common',
                    'url' => 'http://www.gg-apollo.com',
                    'status' => 'f',
                    'rank' => 100,
                ]
            ]
        ];

        DB::beginTransaction();
        try {
            foreach ($lottery_datas as $lottery_data) {
                $lottery_ident = $lottery_data['ident'];
                $drawsource_data = isset($drawsource_datas[$lottery_ident]) ? $drawsource_datas[$lottery_ident] : [];
                $result = \Service\API\Lottery::quicklyAddLottery($lottery_data, $drawsource_data);
                $this->info($result['msg']);
            }
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('执行失败');
        }
    }

    //增加创赢专用活动: 取款佣金
    private function add_activity_withdrawcommission()
    {
        $app_ident = get_config('app_ident', '');
        if ($app_ident !== 'cy') {
            $this->info('这是创赢专用活动');
            return;
        }
        $contract = [
            ['consume' => 0, 'active' => 3, 'rate' => '0.30'],
            ['consume' => 20, 'active' => 6, 'rate' => '0.50'],
            ['consume' => 50, 'active' => 12, 'rate' => '0.80'],
            ['consume' => 100, 'active' => 24, 'rate' => '1.00'],
        ];
        $table_rows = '';
        $config = [];
        foreach ($contract as $row) {
            $config[] = [
                ['title' => "当日团队销量(万)", 'value' => $row['consume']],
                ['title' => "有效人数", 'value' => $row['active']],
                ['title' => "奖励充值总额比例(%)", 'value' => $row['rate']],
            ];
            $table_rows .= "<tr><td>{$row['consume']}</td><td>{$row['active']}</td><td>{$row['rate']}</td></tr>";
        }
        $data = [
            'ident' => 'cy_withdrawcommission',
            'name' => '取款佣金',
            'config' => '{"hide":"1","config":[[[{"title": "系统派发层级", "value": "2"},{"title": "日销量（扣返点）", "value": "500"}]],' . json_encode($config) . ']}',
            'config_ui' => '{}',
            'summary' => '取款佣金',
            'description' => "<p>参与资格：2级代理</p>
                              <p>佣金奖励计算：团队提现总金额 * 佣金比例</p>
                              <p>奖励条件如下</p>
                              <table width='100%'>
                                <tr>
                                <th>当日团队销量(万)</th>
                                <th>有效人数</th>
                                <th>奖励充值总额比例(%)</th>
                                </tr>
                                {$table_rows}
                              </table>
                            ",
            'start_time' => \Carbon\Carbon::today()->startOfDay(),
            'end_time' => \Carbon\Carbon::today()->endOfDay(),
            'draw_method' => 2,
            'status' => 0
        ];
        DB::beginTransaction();
        try {
            \Service\Models\Activity::insert($data);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    private function _pk10_dxds_4to10()
    {
        $data = [
            ['id' => '170701004', 'parent_id' => '170701000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_daxiao4', 'name' => '第四名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第四名"]', 'layout' => '
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第四名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第四名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170701005', 'parent_id' => '170701000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_daxiao5', 'name' => '第五名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第五名"]', 'layout' => '
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第五名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第五名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170701006', 'parent_id' => '170701000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_daxiao6', 'name' => '第六名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第六名"]', 'layout' => '
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第六名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第六名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170701007', 'parent_id' => '170701000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_daxiao7', 'name' => '第七名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第七名"]', 'layout' => '
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第七名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第七名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170701008', 'parent_id' => '170701000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_daxiao8', 'name' => '第八名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第八名"]', 'layout' => '
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第八名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第八名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170701009', 'parent_id' => '170701000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_daxiao9', 'name' => '第九名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第九名"]', 'layout' => '
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第九名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第九名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170701010', 'parent_id' => '170701000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_daxiao10', 'name' => '第十名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第十名"]', 'layout' => '
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第十名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第十名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],


            ['id' => '170801004', 'parent_id' => '170801000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_danshuang4', 'name' => '第四名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第四名"]', 'layout' => '
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第四名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第四名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170801005', 'parent_id' => '170801000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_danshuang5', 'name' => '第五名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第五名"]', 'layout' => '
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第五名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第五名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170801006', 'parent_id' => '170801000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_danshuang6', 'name' => '第六名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第六名"]', 'layout' => '
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第六名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第六名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170801007', 'parent_id' => '170801000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_danshuang7', 'name' => '第七名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第七名"]', 'layout' => '
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第七名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第七名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170801008', 'parent_id' => '170801000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_danshuang8', 'name' => '第八名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第八名"]', 'layout' => '
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第八名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第八名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170801009', 'parent_id' => '170801000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_danshuang9', 'name' => '第九名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第九名"]', 'layout' => '
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第九名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第九名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
            ['id' => '170801010', 'parent_id' => '170801000', 'lottery_method_category_id' => '71', 'ident' => 'pk10_danshuang10', 'name' => '第十名', 'draw_rule' => '{}', 'lock_table_name' => '', 'lock_init_function' => '', 'modes' => '[1,2,3,4,5,6,7,8]', 'prize_level' => '[4]', 'prize_level_name' => '["第十名"]', 'layout' => '
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第十名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第十名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort' => '0', 'status' => true, 'max_bet_num' => '0',],
        ];
        DB::beginTransaction();
        try {
            \Service\Models\LotteryMethod::insert($data);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }
    //添加虚拟货币USDTTRC20
    private function _add_bank_virtual_usdttrc20()
    {
        $bank_virtual = [
            [
                'ident' => 'USDTTRC20',
                'name' => 'TRC20虚拟钱包',
                'currency' => 'CNY',
                'rate' => '1',
                'channel_idents' => 'offlinerbcxpay,xinrbcxpay,rbcxpay',
                'withdraw' => 't',
                'disabled' => 'f',
                'amount_max' => '49999',
                'amount_min' => '5000',
                'start_time' => '09:00',
                'end_time' => '19:00',
                'api_fetch' => '1',
                'url' => 'https://apiv2.rbcx.io/tick',
            ]
        ];
        $banks = [
            [
                'name' => 'TRC20虚拟钱包',
                'ident' => 'USDTTRC20',
                'withdraw' => 't',
                'disabled' => 'f',
            ]
        ];
        DB::beginTransaction();
        try {
            \Service\Models\BankVirtual::insert($bank_virtual);
            \Service\Models\Bank::insert($banks);
            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }

    //必乐测试用户余额调整
    private function _check_test_user_balance()
    {
        if (get_config('app_ident') != 'BL') {
            $this->info('执行平台名称不匹配');
            return;
        }

        $user_name = 'bishengbei1';
        $new_balance = 20000;

        $user_id = User::where('username', $user_name)->where('user_group_id', 2)->value('id');

        $result = User::select('users.id', 'users.username', 'user_fund.balance')
            ->leftjoin('user_fund', 'user_fund.user_id', '=', 'users.id')
            ->where(function ($query) use ($user_id) {
                $query->where('users.parent_tree', '@>', $user_id)
                    ->orWhere('users.id', $user_id);
            })
            ->where('users.user_group_id', 2)
            ->orderBy('users.id')
            ->get();

        echo '开始修改测试用户余额' . PHP_EOL;

        $i = 0;
        $update_sign = 0;
        foreach ($result as $value) {
            //$update_sign = \Service\Models\UserFund::where('user_id', $value->id)->update(['balance' => $new_balance]);
            echo '用户ID：' . $value->id . ', 用户名：' . $value->username . ', 用户余额：' . $value->balance . ' => ' . $new_balance . ', 修改状态：' . $update_sign . PHP_EOL;
            $i++;
        }

        echo '结束修改测试用户余额' . PHP_EOL;
        echo '共修改用户数：' . $i . PHP_EOL;
    }


    /**
     * 华信抽返水帐变回滚

    private function _huaxin_pump_callback()
    {

        $app_ident = get_config('app_ident');
        $app_ident = get_config('app_ident');
        $this->info('平台 => '.$app_ident);
        if ($app_ident != 'huaxin') {
            $this->warn('只允许华信 huaxin 执行，当前平台为'.get_config('app_ident'));
            return;
        }
        //帐变时间范围
        $begin_time = '2021-04-16 00:00:00';
        $end_time = '2021-04-16 12:00:00';
        $last_time =  Carbon::now()->subHour(1);

        //获取帐变类型ID
        $cpcs_type_id = OrderType::where('ident','CPCS')->value('id');//彩票抽水
        $cpfs_type_id = OrderType::where('ident','CPFS')->value('id');//彩票返水
        $fhff_type_id = OrderType::where('ident','FHFF')->value('id');//分红发放
        $jykk_type_id = OrderType::where('ident','XTJYKK')->value('id');//经营扣款

        $this->info('抽返水时间范围  '.$begin_time
            .' ~ '.$end_time
            .' ,分红经营扣款开始时间  '.$last_time
        );


        $this->info('抽水类型 => '.$cpcs_type_id.
            ' ,返水类型 => '.$cpfs_type_id.
            ' ,分红类型 => '.$fhff_type_id.
            ' ,经营扣款类型 =>  '.$jykk_type_id
        );
        if(empty($cpcs_type_id)
            || empty($cpfs_type_id)
            || empty($fhff_type_id)
            || empty($jykk_type_id)
        ){
            $this->warn('缺乏必要的帐变类型');
            return;
        }

        $cpcs_count = 0;//抽水纪录数
        $cpcs_amount = 0;//抽水总金额
        $cpfs_count = 0;//返水纪录数
        $cpfs_amount = 0;//返水总金额
        $into_fhff = 0;//新增分红纪录数
        $into_fhff_amount = 0;//新增分红金额
        $into_jykk = 0;//新增扣款纪录数
        $into_jykk_amount = 0;//新增扣款金额

        //分红补发是否存在
        $fhff_count = Orders::orderBy('id','asc')
            ->where('order_type_id',$fhff_type_id)
            ->where('created_at', '>=',$last_time)
            ->count();
        $this->info('分红纪录数 => '.$fhff_count);
        if($fhff_count > 0){
            $this->warn($last_time.'之前已经存在分红发放纪录，不重复帐变');
        }

        //彩票抽水帐变扣的金额以分红发放形式加回
        $this->info('彩票抽水帐变扣的金额以分红发放形式返回');
        for ($i = 0 ; $i< 8;$i++){
            $orders = Orders::where('order_type_id',$cpcs_type_id)
                ->whereBetween('created_at', [$begin_time , $end_time])
                ->skip($i * 10000)->take(10000)
                ->select(['id','from_user_id','amount'])
                ->orderBy('id','asc')
                ->get();
            if($orders->isEmpty()){
                $this->info('退出循环，抽水扣款 '.$cpcs_count.' 条，共 '.$cpcs_amount.' 元');
                break;
            }else{
                foreach ($orders as $order){
                    if($fhff_count == 0){
                        $cb_order = new Orders();
                        $cb_order->from_user_id = $order->from_user_id;
                        $cb_order->admin_user_id = 0;
                        $cb_order->amount = $order->amount;
                        $cb_order->comment = json_encode([
                            'oid' => id_encode($order->id),
                        ]);
                        if(UserFund::modifyFund($cb_order, 'FHFF')){
                            $into_fhff++;
                            $into_fhff_amount += $cb_order->amount;
                        }
                    }
                    $cpcs_amount += $order->amount;
                    $cpcs_count++;
                }
                sleep(1);
                $this->info('第 '.$i.' 次循环结束，抽水金额累计'.$cpcs_amount);
            }
        }

        $fhff_sum = Orders::orderBy('id','asc')
            ->where('order_type_id',$fhff_type_id)
            ->where('created_at', '>=',$last_time)
            ->sum('amount');
        $this->info('分红补款 '.$fhff_sum.'元');

        //经营扣款是否存在
        $jykk_count = Orders::orderBy('id','asc')
            ->where('order_type_id',$jykk_type_id)
            ->where('created_at', '>=',$last_time)
            ->count();
        $this->info('经营扣款纪录数 => '.$jykk_count);
        if($jykk_count > 0){
            $this->warn($last_time.'之前已经存在经营扣款纪录，不重复帐变');
        }

        //彩票返水帐变加的金额以经营扣款的形式形式减去
        $this->info('彩票返水帐变加的金额以经营扣款的形式形式减去');
        for ($i = 0 ; $i< 8;$i++){
            $orders = Orders::where('order_type_id',$cpfs_type_id)
                ->whereBetween('created_at', [$begin_time , $end_time])
                ->skip($i * 10000)->take(10000)
                ->select(['id','from_user_id','amount'])
                ->orderBy('id','asc')
                ->get();
            if($orders->isEmpty()){
                $this->info('退出循环，返水加款 '.$cpfs_count.' 条，共 '.$cpfs_amount.' 元');
                break;
            }else{
                foreach ($orders as $order){
                    if($jykk_count == 0){
                        $cb_order = new Orders();
                        $cb_order->from_user_id = $order->from_user_id;
                        $cb_order->admin_user_id = 0;
                        $cb_order->amount = $order->amount;
                        $cb_order->comment = json_encode([
                            'oid' => id_encode($order->id),
                        ]);
                        if(UserFund::modifyFund($cb_order, 'XTJYKK')){
                            $into_jykk++;
                            $into_jykk_amount += $cb_order->amount;
                        }
                    }
                    $cpfs_amount += $order->amount;
                    $cpfs_count++;
                }
                sleep(1);
                $this->info('第 '.$i.' 次循环，返水金额累计'.$cpfs_amount);
            }
        }

        $jykk_sum = Orders::orderBy('id','asc')
            ->where('order_type_id',$jykk_type_id)
            ->where('created_at', '>=',$last_time)
            ->sum('amount');
        $this->info('经营扣款 '.$jykk_sum.'元');
        $this->info('抽水相关查询 '.
                '抽水扣款 '.$cpcs_count.' 条，共 '.$cpcs_amount.' 元；'
                .'分红发放 '.$fhff_count.'条，共 '.$fhff_sum.' 元，'
        );
        $this->info('返水相关查询 '
                .'返水加款 '.$cpfs_count.' 条，共 '.$cpfs_amount.' 元，'
                .'经营扣款 '.$jykk_count.' 条，共 '.$jykk_sum.' 元'
        );
        $this->info('新增帐变结果 '
                .'分红发放 '.$into_fhff.' 条，共 '.$into_fhff_amount.' 元；'
                .'经营扣款 '.$into_jykk.' 条，共 '.$into_jykk_amount.' 元'
        );
    }
     * */

    //添加SEA电游菜单
    private function _seady_front_menu()
    {
        if (\Service\Models\FrontMenu::where('ident', 'sea_game')->count()) {
            $this->info('SEA电游前台菜单已存在');
            return;
        }

        $date_time = date('Y-m-d H:i:s');
        $front_menu = new \CreateTableFrontMenu();

        DB::beginTransaction();
        try {
            DB::table('front_menu')->insert([
                [
                    'name' => 'SEA电游',
                    'ident' => 'sea_game',
                    'category' => 'common',
                    'data' => json_encode($front_menu->_seaGameMenuInit(), JSON_UNESCAPED_UNICODE),
                    'status' => 1,
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ],
            ]);
            DB::commit();
            $this->info('添加SEA电游前台菜单 SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            $this->info('添加SEA电游前台菜单 SQL执行失败！');
        }
    }

    //SEA电游
    private function _thirdGameSea()
    {
        DB::beginTransaction();
        try {
            //创建third_game_sea_bet表
            (new \CreateTableThirdGameSeaBet())->up();

            //order_type
            DB::unprepared(
                "SELECT SETVAL('order_type_id_seq',(SELECT MAX(id) FROM order_type));
                INSERT INTO order_type(ident, name, display, operation, hold_operation, category, description) VALUES
                ('SEACR', 'SEA电游存入', 1, 2, 0, 3, 'SEA电游存入'),
                ('SEATQ', 'SEA电游提取', 1, 1, 0, 3, 'SEA电游提取'),
                ('SEAFS', 'SEA电游返水', 1, 1, 0, 3, 'SEA电游返水');"
            );

            //platform
            DB::unprepared(
                "SELECT SETVAL('third_game_platform_id_seq',( SELECT MAX(id) FROM third_game_platform ));
                INSERT INTO third_game_platform(ident, name, sort, status, rebate_type) VALUES
                ('Sea', 'SEA电游', 0, 1, 0);"
            );

            //third_game
            DB::unprepared(
                "SELECT SETVAL('third_game_id_seq',(SELECT MAX(id) FROM third_game));
                INSERT INTO third_game (
                    third_game_platform_id,
                    ident,
                    name,
                    merchant,
                    merchant_key,
                    api_base,
                    merchant_test,
                    merchant_key_test,
                    api_base_test,
                    status,
                    login_status,
                    transfer_status,
                    transfer_type,
                    deny_user_group,
                    last_fetch_time
                )
                VALUES(
                    (SELECT id FROM third_game_platform WHERE ident = 'Sea'),
                    'CiaSea',
                    'SEA电游(cia)',
                    '',
                    '',
                    'http://api.wm-cia.com/api/',
                    '',
                    '',
                    'http://uat.wmcia.net/api',
                    1,
                    0,
                    0,
                    0,
                    '[3]',
                    now()
                );"
            );

            DB::commit();
            $this->info('SQL执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("SEA电游SQL执行失败,已回滚.\n" . $e->getMessage());
        }
    }

    private function fly_system_config_idents()
    {
        DB::beginTransaction();
        try {
            DB::unprepared('alter table fly_system_config alter column lottery_idents type varchar(256);');

            DB::commit();
            $this->info('执行成功');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info("执行失败" . PHP_EOL . $e->getMessage());
        }
    }
}
