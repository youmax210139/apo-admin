<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UserReportIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $end_date = $this->post('end_date');
        $end_date = strtotime($end_date) === false ? (string)Carbon::today()->endOfDay() : $end_date;
        $start_date = (string)((new Carbon($end_date))->subDays(35));

        return [
            'start_time' => 'date|after:' . $start_date . '|date_format:Y-m-d H:i:s',//开始时间
            'end_time' => 'date|date_format:Y-m-d H:i:s',//结束时间
            'deposit_min' => 'numeric',
            'deposit_max' => 'numeric',
            'wage_min' => 'numeric',
            'wage_max' => 'numeric',
            'bet_min' => 'numeric',
            'bet_max' => 'numeric',
            'withdrawal_min' => 'numeric',
            'withdrawal_max' => 'numeric',
            'balance_min' => 'numeric',
            'balance_max' => 'numeric',
            'rebate_min' => 'numeric',
            'rebate_max' => 'numeric',
            'activity_min' => 'numeric',
            'activity_max' => 'numeric',
            'profit_min' => 'numeric',
            'profit_max' => 'numeric',
        ];
    }

    public function messages()
    {
        return [
            'start_time.date' => '开始时间不正确',
            'start_time.after' => '查询间隔不得超过 35 天',
            'start_time.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'end_time.date' => '结束时间不正确',
            'end_time.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'deposit_min.numeric' => '最小充值金额错误',
            'deposit_max.numeric' => '最大充值金额错误',
            'wage_min.numeric' => '最小日工资金额错误',
            'wage_max.numeric' => '最大日工资金额错误',
            'bet_min.numeric' => '最小投注金额错误',
            'bet_max.numeric' => '最大投注金额错误',
            'withdrawal_min.numeric' => '最小提现金额错误',
            'withdrawal_max.numeric' => '最小提现金额错误',
            'balance_min.numeric' => '最小金额错误',
            'balance_max.numeric' => '最大金额错误',
            'rebate_min.numeric' => '最小返点金额错误',
            'rebate_max.numeric' => '最大返点金额错误',
            'activity_min.numeric' => '最小活动金错误',
            'activity_max.numeric' => '最大活动金错误',
            'profit_min.numeric' => '最小盈亏金额错误',
            'profit_max.numeric' => '最大盈亏金额错误',
        ];
    }
}
