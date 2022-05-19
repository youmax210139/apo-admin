<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Service\API\Report;

class RechargereportController extends Controller
{
    public function getIndex(Request $request)
    {
        $data['start_date'] = $request->get('start_date', '');
        $data['end_date'] = $request->get('end_date', '');
        if (empty($data['start_date']) || empty($data['end_date'])) {
            $default_search_time = get_config('default_search_time', 0);
            $data = [
                'start_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::today()->addHours($default_search_time) :
                    Carbon::yesterday()->addHours($default_search_time),
                'end_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::tomorrow()->addHours($default_search_time)->subSecond(1) :
                    Carbon::today()->addHours($default_search_time)->subSecond(1),
            ];
        }
        $data['user_group'] = $request->get('user_group', 1);
        $data['frozen'] = $request->get('frozen', -1);
        $data['sum'] = [
            'sum_hand_cash_in' => 0.0,
            'sum_hand_cash_out' => 0.0,
            'sum_email_hand_cash_out' => 0.0,
            'sum_email_hand_cash_in' => 0.0,
            'sum_cash_in' => 0.0,
            'sum_cash_out' => 0.0,
            'sum_credit_in' => 0.0,
            'sum_credit_out' => 0.0,
            'cash_payment_out' => 0.0,
            'sum_lp_in' => 0.0,
            'sum_lp_out' => 0.0,
            'sum_fhff_in' => 0.0,//分红发放
            'sum_xtyjff_in' => 0.0,//系统佣金发放
            'sum_xtsfff_in' => 0.0,//系统私返发放
            'sum_xtjjkk_out' => 0.0,//系统经营扣款
            'sum_payment_in' => 0.0,
            'sum_payment_offline_in' => 0.0,
            'sum_payment_out' => 0.0,
            'sum_payment_fee_in' => 0.0,
            'sum_payment_fee_out' => 0.0,
            'sum_cash_diff' => 0
        ];
        $data['lists'] = Report::getTopProxyCashInOut($data['start_date'], $data['end_date'], $data['user_group'], $data['frozen']);
        foreach ($data['lists'] as &$v) {
            $v = (array)$v;
            $data['sum']['sum_hand_cash_in'] += $v['hand_cash_in'];
            $data['sum']['sum_hand_cash_out'] += $v['hand_cash_out'];
            $data['sum']['sum_email_hand_cash_in'] += $v['email_hand_cash_in'];
            $data['sum']['sum_cash_in'] += $v['cash_in'];
            $data['sum']['sum_cash_out'] += $v['cash_out'];
            $data['sum']['sum_credit_in'] += $v['credit_in'] ?? 0;
            $data['sum']['sum_credit_out'] += $v['credit_out'] ?? 0;
            $data['sum']['cash_payment_out'] += $v['cash_payment_out'];
            $data['sum']['sum_lp_in'] += $v['cash_lp_in'];
            $data['sum']['sum_lp_out'] += $v['cash_lp_out'];
            $data['sum']['sum_fhff_in'] += $v['cash_fhff_in'];
            $data['sum']['sum_xtyjff_in'] += $v['cash_xtyjff_in'];
            $data['sum']['sum_xtsfff_in'] += $v['cash_xtsfff_in'];
            $data['sum']['sum_xtjjkk_out'] += $v['cash_xtjjkk_out'];
            $v['cash_diff'] = ($v['cash_in']) - ($v['cash_out']);
            $data['sum']['sum_payment_in'] += ($v['cash_payment_in'] - $v['cash_payment_offline_in']);
            $data['sum']['sum_payment_offline_in'] += $v['cash_payment_offline_in'];
            $data['sum']['sum_payment_out'] += $v['cash_payment_out'];
            $data['sum']['sum_payment_fee_in'] += $v['cash_payment_fee_in'] - ($v['cash_email_and_hand_fee_in'] ?? 0);
            $data['sum']['sum_payment_fee_out'] += $v['cash_payment_fee_out'];
        }
        $data['sum']['sum_cash_diff'] = $data['sum']['sum_cash_in'] - $data['sum']['sum_cash_out'];
        return view('report.recharge-report', $data);
    }
}
