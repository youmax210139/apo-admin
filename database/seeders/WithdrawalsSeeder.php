<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WithdrawalsSeeder extends Seeder
{
    public function run()
    {
        $userBanks = DB::table('user_banks')->take(100)->get();
        $thirds = DB::table('withdrawal_channel')->get();
        $third_ids = [];
        $third_merchants = [];
        foreach ($thirds as $third) {
            $third_ids[] = $third->id;
            $third_merchants[] = $third->withdrawal_category_ident . '@' . $third->merchant_id;
        }
        foreach ($userBanks as $userBank) {
            $third_index = array_rand($third_ids);
            $third_id = $third_ids[$third_index];
            $third_merchant = $third_merchants[$third_index];
            $wData = $this->genWithdrawals($userBank->user_id, $userBank->id, $third_id, $third_merchant);
            $withdrawal_id = DB::table('withdrawals')->insertGetId($wData);
            $wrData = $this->genWithdrawalRisks($withdrawal_id, $wData['user_id'], $wData['amount']);
            $withdrawal_risk_id = DB::table('withdrawal_risks')->insertGetId($wrData);
            $this->genOrder($wData['user_id'], $wData['amount']);
        }
    }

    /**
     * 生成帐变数据
     */
    public function genOrder($uId, $amount)
    {
        $order = new \Service\Models\Orders();
        $order->from_user_id = $uId;
        $order->amount = $amount;
        $order->comment = '提现申请';
        $order->ip = '127.0.0.1';
        return \Service\API\UserFund::modifyFund($order, 'TKSQ');
    }

    /**
     * 生成风控数据
     */
    public function genWithdrawalRisks($wId, $uId, $amount)
    {
        $statusAll = [0, 1, 2, 3];
        $status = $statusAll[array_rand($statusAll)];
        $riskAdmin = 'apollo';
        $risk_ip = '192.168.1.' . rand(0, 254);
        $riskAt = date('Y-m-d H:i:s', strtotime("+1 minute"));
        $doneAt = date('Y-m-d H:i:s', strtotime("+2 minute"));
        switch ($status) {
            case 0:
                $riskAdmin = '';
                $riskAt = null;
                $doneAt = null;
                break;
            case 1:
                break;
            case 2:
                break;
            case 3:
                $doneAt = null;
                break;
        }
        $data = array(
            'withdrawal_id' => $wId,
            'verifier_username' => $riskAdmin,
            'last_withdrawal_amount' => rand(100, 500),
            'last_withdrawal_at' => date('Y-m-d H:i:s', strtotime("-2 day")),
            'last_withdrawal_id' => rand(1000, 2000),
            'deposit_total' => rand(100, 500),
            'deposit_times' => rand(1, 10),
            'bet_price' => rand(1000, 2000),
            'bet_bonus' => rand(5000, 1000),
            'bet_times' => rand(10, 100),
            'verifier_at' => $riskAt,
            'verifier_ip' => $risk_ip,
            'done_at' => $doneAt,
            'status' => $status,
        );
        return $data;
    }

    /**
     * 生成提现数据
     * @param $userId
     * @param $userBankId
     * @param $third_id
     * @param $third_merchant
     * @return array
     */
    public function genWithdrawals($userId, $userBankId, $third_id, $third_merchant)
    {
        //0:默认未处理；1=成功; 2=失败;3=操作中4=出款中5求人工介入
        //手工出款操作状态 11出款失败12出款成功
        //第三方出款操作状态：15、排队发送汇款信息中；14、发送汇款信息失败；13、排队确认汇款是否成功；12、对方确认汇款失败;11、对方确认汇款成功
        $status = 0;
        if (rand(1, 10) > 6) {
            $status = rand(1, 3);
        }
        $operate_type = rand(1, 2); //1手工2第三方
        $operate_status = 0;
        $third_add_count = rand(1, 5);
        $third_check_count = rand(1, 5);
        $third_response = '';
        $third_send_at = date('Y-m-d H:i:s', strtotime("+4 minute"));
        $third_check_at = date('Y-m-d H:i:s', strtotime("+5 minute"));
        $cashierUserName = '';
        $bankOrderNo = '';
        $cashierAt = null;
        $doneAt = null;
        switch ($status) {
            case 1: //成功
                if ($operate_type  == 2) {
                    $operate_status = 11;
                    $third_response = 'success';
                }
                $bankOrderNo = date('YmdH-') . rand(100000, 900000);
                $cashierUserName = 'apollo';
                $cashierAt = date('Y-m-d H:i:s', strtotime("3 minute"));
                $doneAt = date('Y-m-d H:i:s', strtotime("+6 minute"));
                break;
            case 2: //失败
                if ($operate_type  == 2) {
                    $operate_status = 12;
                    $third_response = 'fail';
                }
                $cashierUserName = 'apollo';
                $cashierAt = date('Y-m-d H:i:s', strtotime("3 minute"));
                $doneAt = date('Y-m-d H:i:s', strtotime("+6 minute"));
                break;
            case 3: //人工操作中
                if ($operate_type  == 2) {
                    $operate_status = (rand(1, 10) > 5) ? 14 : 12;
                    $third_response = 'wait';
                }
                break;
            case 4: //出款中
                if ($operate_type  == 2) {
                    $operate_status = (rand(1, 10) > 5) ? 15 : 13;
                    $third_response = 'wait';
                }
                break;
            case 5: //自动出款失败
                if ($operate_type  == 2) {
                    $operate_status = rand(12, 15);
                    $third_response = 'wait';
                    $third_add_count = rand(10, 50);
                    $third_check_count = rand(10, 50);
                }
                break;
            default:
                //第三方没有任何操作
                $third_add_count = 0;
                $third_check_count = 0;
                $third_id = 0;
                $third_send_at = null;
                $third_check_at = null;
                break;
        }
        $data = [
            'user_id' => $userId,
            'amount' => rand(100, 500) * 1.01,
            'user_fee'  => '0',
            'user_bank_id' => $userBankId,
            'cashier_username'    => $cashierUserName,
            'cashier_at'  => $cashierAt,
            'done_at'  => $doneAt,
            'bank_order_no'       => $bankOrderNo,
            'status'  => $status,
            'operate_type'       => $operate_type,
            'operate_status'       => $operate_status,
            'third_id'  => $third_id,
            'third_merchant' => $third_merchant,
            'third_add_at' => $third_send_at,
            'third_check_at' => $third_check_at,
            'third_response' => $third_response,
            'third_add_count' => $third_add_count,
            'third_check_count' => $third_check_count,
            'created_at' => now()
        ];
        return $data;
    }
}
