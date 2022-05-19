<?php

namespace App\Http\Requests;

use Carbon\Carbon;

class LotteryBonusDailyRequest extends Request
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
        $start_date       = $this->post('start_date');
        $start_limit_time = Carbon::now()->subYear()->toDateString();
        $end_limit_time   = Carbon::parse($start_date)->addYear()->toDateString();
        return [
            'start_date' => 'required|date|before:today|after:' . $start_limit_time . '|date_format:Y-m-d',//开始时间
            'end_date'   => 'required|date|after:start_date|before:' . $end_limit_time . '|date_format:Y-m-d',//开始时间
        ];
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages()
    {
        return [

            'start_date.date'        => '请输入正确的时间',
            'start_date.after'       => '查询间隔不得超过 1 年',
            'start_date.before'      => '查询开始时间不能晚于昨天',
            'start_date.date_format' => '时间格式为：YYYY-mm-dd',
            'end_date.date'          => '请输入正确的时间',
            'end_date.after'         => '结束时间不能晚于开始时间',
            'end_date.before'        => '结束时间不能晚于开始时间',
            'end_date.date_format'   => '时间格式为：YYYY-mm-dd',
        ];
    }
}
