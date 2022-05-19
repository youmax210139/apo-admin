<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Service\API\UserFund;
use Service\Models\Orders;
use Service\Models\OrderType;

class ThirdGameSeeder extends Seeder
{
    protected $top_user = [];

    public function run()
    {
        $games = \Service\Models\ThirdGamePlatform::select('id', 'ident')->get();
        foreach ($games as $game) {
            if ($game->ident == 'Wmc' ||  $game->ident == 'AvCloud') {
                continue;
            }
            $order_type_id = OrderType::where('ident', strtoupper($game->ident) . 'FS')->value("id");
            Orders::where('order_type_id', $order_type_id)->delete();
            DB::table('third_game_' . strtolower($game->ident) . '_bet')->truncate();
        }
        DB::table('third_game_user')->truncate();
        DB::table('third_game_order')->truncate();
        foreach ([1, 2] as $zhong_dai) {
            //一级代理
            $yi_dai = [$zhong_dai * 20, $zhong_dai * 20 + 1];
            foreach ($yi_dai as $user_id) {
                $this->makeData($user_id);
            }
            //二级代理
            $er_dai = [$zhong_dai * 200, $zhong_dai * 200 + 1];
            foreach ($er_dai as $user_id) {
                $this->makeData($user_id);
            }
        }
    }

    protected function makeData($user_id)
    {
        $user = \Service\Models\User::find($user_id);
        $prefix = get_config('third_game_account_prefix');
        $games = \Service\Models\ThirdGamePlatform::select(
            'third_game_platform.id',
            'third_game_platform.ident',
            'third_game.id AS third_game_id'
        )
            ->leftJoin('third_game', function ($join) {
                $join->on('third_game.third_game_platform_id', 'third_game_platform.id');
                //$join->on('third_game.status', '=', DB::Raw('0'));
            })
            ->whereNotIn('third_game.ident', ['CiaWmc', 'CiaAvCloud'])
            ->get();
        $user_sql = $order_sql = $bet_sql = $choushui_sql = array();
        $counter = 0;
        foreach ($games as $game) {
            $user_sql[] = $this->genUserInsertData($user, $game->id, $prefix);
            if (!isset($this->top_user[$user->top_id . $game->ident])) {
                $top = \Service\Models\User::find($user->top_id);
                $rebate_sql[] = $this->genUserRebateData($top, $game->ident);
                $this->top_user[$user->top_id . $game->ident] = $user->top_id;
            }
            $rebate_sql[] = $this->genUserRebateData($user, $game->ident);
            for ($i = 0; $i < 2; $i++) {
                $date = ++$counter % 2 == 0 ? date('Y-m-d H:i:s') : date('Y-m-01 H:i:s');
                $order_sql[] = $this->genOrderData($user, $game->third_game_id, $game->ident, true, $date, $game->id);
                $order_sql[] = $this->genOrderData($user, $game->third_game_id, $game->ident, false, $date, $game->id);
                $bet_sql[$game->ident][] = $this->genBetData($user, $counter, $game->ident, $date, $prefix);
            }
        }
        DB::table('user_rebates')->insert($rebate_sql);
        DB::table('third_game_user')->insert($user_sql);
        DB::table('third_game_order')->insert($order_sql);
        foreach ($games as $game) {
            DB::table('third_game_' . strtolower($game->ident) . '_bet')->insert($bet_sql[$game->ident]);
        }
        //DB::table('third_game_lgqipai_commission')->insert($choushui_sql);
    }

    /**
     * 生成投注数据
     */
    protected function genBetData($user, $counter, $game_code, $date, $prefix)
    {
        $data = [
            'user_id' => $user->id,
            'game' => mb_substr(md5(rand(0, 99999999)), 0, 6),
            'game_date' => $date,
            'game_type' => mb_substr(md5(rand(0, 99999999)), 0, 3),
            'bet_id' => $counter,
            'total_bets' => 100,
            'total_wins' => 100,
            'rebate_status' => 0,
            'remark' => md5(rand(0, 99999999)),
        ];
        if ($game_code == 'Vr') {
            $data = [
                'user_id' => $user->id,
                'player_name' => $prefix . $user->username,
                'merchant_prize' => '198',
                'player_prize' => '172',
                'loss_prize' => '0',
                'state' => '3',
                'sub_state' => '0',
                'unit' => '2',
                'multiple' => '1',
                'count' => '100',
                'cost' => '200',
                'odds' => '172',
                'number' => '0123456789,0123456789',
                'position' => '万,千',
                'channel_id' => rand(1, 99),
                'channel_name' => '遊戲頻道',
                'bet_type_name' => '前二直选复式',
                'issue_number' => '20170117625',
                'winning_number' => '9,0,9,4,4',
                'note' => '[\"count\":1,\"number\":\"9,0\",\"awardName\":\"u524du4e8cu76f4u9009\"]',
                'prize_detail' => '',
                'game' => 'VR1.5分彩',
                'game_type' => '前二直选复式',
                'game_date' => date('Y-m-d H:i:s'),
                'bet_id' => $counter . '_' . mb_substr(md5($counter), 0, 16),
                'status' => '3',
                'total_bets' => '200',
                'total_wins' => '-28',
                'rebate_status' => '1',
                'remark' => '单位:2 倍数:1 奖期:20170117625 中奖号码:9,0,9,4,4',
            ];
        } elseif ($game_code == 'FhLeli') {
            $data = [
                'user_id' => $user->id,
                'user_account' => $prefix . $user->username,
                'issue_no' => '20190521001',
                'nums' => '10',
                'count' => '1',
                'confirm_amount' => '100',
                'win_number' => '9,0,9,4,4',
                'win_count' => '1',
                'win_amount' => '100',
                'lottery_number' => '123',
                'unit' => '1.0000',
                'odds' => '1900',
                'content' => '[\"count\":1,\"number\":\"9,0\",\"awardName\":\"u524du4e8cu76f4u9009\"]',
                'cancel_status' => '0',
                'lottery_code' => 'cqssc',
                'game' => 'VR1.5分彩',
                'game_type' => '前二直选复式',
                'game_date' => date('Y-m-d H:i:s'),
                'bet_id' => $counter . '_' . mb_substr(md5($counter), 0, 16),
                'status' => '1',
                'total_bets' => '200',
                'total_wins' => '198',
                'rebate_status' => '0',
                'remark' => '单位:2 倍数:1 奖期:20190521001 中奖号码:9,0,9,4,4',
            ];
        }

        return $data;
    }


    /**
     * 生成转帐数据
     * @param $user
     * @param $third_code
     * @param bool $is_deposit
     * @return array
     */
    protected function genOrderData(
        $user,
        $third_game_id,
        $third_game_ident,
        $is_deposit = true,
        $date = null,
        $third_game_platform_id = 0
    ) {
        if ($is_deposit) {
            $from = 0;
            $to = $third_game_id;
            $from_platform = 0;
            $to_platform = $third_game_platform_id;
        } else {
            $from = $third_game_id;
            $to = 0;
            $from_platform = $third_game_platform_id;
            $to_platform = 0;
            $order = new Orders();
            $order->from_user_id = $user->id;
            $order->created_at = $date;
            $order->amount = 1;
            $order->comment = '反水测试';
            //$order_type_ident = strtoupper($third_game_ident) . 'FS';
            $order_type_ident = strtoupper($third_game_ident) . 'FS';
            if (!UserFund::modifyFund($order, $order_type_ident)) {
                echo "帐变失败{$order_type_ident}", UserFund::$error_msg, "\n";
            }
        }

        return [
            'order_num' => md5(rand(0, 99999999)),
            'user_id' => $user->id,
            'amount' => 1,
            'from' => $from,
            'to' => $to,
            'from_platform' => $from_platform,
            'to_platform' => $to_platform,
            'status' => rand(0, 6),
            'refund_status' => rand(0, 3),
            'created_at' => $date,
            'updated_at' => $date,
            'created_ip' => '192.168.203.' . rand(1, 254),
            'remark' => md5(rand(0, 99999999)),
        ];
    }

    /**
     * 生成用户数数据
     * @param \Illuminate\Database\Eloquent\Model $user
     * @param string $third_code
     * @param string $prefix 前缀
     * @return array
     */
    protected function genUserInsertData($user, $third_code, $prefix)
    {
        return [
            'user_id' => $user->id,
            'user_name_third' => $prefix . $user->username,
            'password' => mb_substr(md5(rand(0, 99999999)), 0, 6),
            'balance' => 0,
            'last_login_time' => date('Y-m-d H:i:s', (time() - rand(3600, 3600 * 10))),
            'last_login_ip' => '192.168.203.' . rand(1, 254),
            'is_lock' => rand(0, 1),
            'client_type' => rand(0, 1),
            'third_game_id' => $third_code,
            'created_at' => date('Y-m-d H:i:s', (time() - rand(3600 * 10, 3600 * 30))),
            'created_ip' => '192.168.203.' . rand(1, 254),
        ];
    }

    /**
     * 生成用户返点值数据
     * @param \Illuminate\Database\Eloquent\Model $user
     * @param string $third_code
     * @param string $prefix 前缀
     * @return array
     */
    protected function genUserRebateData($user, $third_code)
    {
        $parent_tree = json_decode($user->parent_tree, true);
        $user_level = count($parent_tree);
        $rebate_level_map = [
            '0' => 0.012,
            '1' => 0.012,
            '2' => 0.011,
            '3' => 0.010,
            '4' => 0.010
        ];
        $val = isset($rebate_level_map[$user_level]) ? $rebate_level_map[$user_level] : 0;

        return [
            'user_id' => $user->id,
            'type' => $third_code,
            'value' => $val,
        ];
    }
}
