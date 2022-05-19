<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DespositSeeder extends Seeder
{
    public function run()
    {
        $users = DB::table('users')->take(100)->get();
        $payment_channels = DB::table('payment_channel')->get();
        $payment_channel_ids = [];
        foreach ($payment_channels as $channel) {
            $payment_channel_ids[] = $channel->id;
        }
        $insertData = array();
        foreach ($users as $user) {
            $payment_channel_id = $payment_channel_ids[array_rand($payment_channel_ids)];
            $insertData[] = $this->genInsertData($user->id, $payment_channel_id);
        }
        DB::table('deposits')->insert($insertData);
    }

    public function genInsertData($userId, $payment_channel_id)
    {
        $status = rand(0, 3);
        $errorType = 0;
        $remark = '';
        $cateAt = null;
        $hourRand = rand(1, 100);
        if (in_array($status, array(2, 3))) {
            $acctAdmin = 'apollo';
            $cashAdmin = 'apollo';
            $cateAt = date('Y-m-d H:i:s', strtotime("-" . $hourRand . " hours"));
            $dealAt = date('Y-m-d H:i:s', strtotime("-" . $hourRand . " hours +3 minutes"));
            $doneAt = date('Y-m-d H:i:s', strtotime("-" . $hourRand . " hours +6 minutes"));

            if ($status == 3) {
                $errorType = rand(1, 4);
                $remark = '无效商户ID';
            } else {
                $remark = '外部ID:' . rand(100000, 900000);
            }
        } elseif (in_array($status, array(1))) {
            $acctAdmin = 'apollo';
            $cashAdmin = '';
            $cateAt = date('Y-m-d H:i:s', strtotime("-" . $hourRand . " hours +3 minutes"));
            $dealAt = date('Y-m-d H:i:s', strtotime("-" . $hourRand . " hours +9 minutes"));
            $doneAt = null;
        } else {
            $acctAdmin = '';
            $cashAdmin = '';
            $cateAt = date('Y-m-d H:i:s', strtotime("- 9 minutes"));
            $dealAt = null;
            $doneAt = null;
        }
        $data = array(
            'user_id' => $userId,
            'amount' => rand(100, 9000),
            'user_fee'  => mt_rand(-10, 20),
            'platform_fee'  => mt_rand(-10, 20),
            'payment_channel_id' => $payment_channel_id,
            'payment_category_id' => mt_rand(1, 3),
            'account_number' => mt_rand(8, 10),
            'accountant_admin'  => $acctAdmin,
            'cash_admin'  => $cashAdmin,
            'status'       => $status,
            'error_type'  => $errorType,
            'remark'       => $remark,
            'deal_at'      => $dealAt,
            'done_at'      => $doneAt,
            'created_at'    => $cateAt,
        );

        return $data;
    }
}
