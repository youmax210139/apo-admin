<?php

namespace App\Http\Requests;

use Carbon\Carbon;

class ActivityRecordRequest extends Request
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
        $start_date = (string)((new Carbon($end_date))->subDays(90));

        return [
            'ip' => 'ip',
            'start_date' => 'date|after:' . $start_date . '|date_format:Y-m-d H:i:s',//开始时间
            'end_date' => 'date|date_format:Y-m-d H:i:s',//结束时间
            'amount_min' => 'numeric',//投注最小金额
            'amount_max' => 'numeric',//投注最大金额
            'page' => 'integer',//当前页
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
            'start_date.date' => '请输入正确的时间',
            'start_date.after' => '查询间隔不得超过 90 天',
            'start_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'end_date.date' => '请输入正确的时间',
            'end_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'amount_min.numeric' => '投注最小金额输入错误',
            'amount_max.numeric' => '投注最大金额输入错误',
            'ip.ip' => 'IP 格式不正确',
            'page.integer' => '无效的页数',
        ];
    }
}
