<?php

namespace App\Http\Requests;

use Carbon\Carbon;

class IssuewagequeueIndexRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $end_date = $this->post('end_date');
        $end_date = strtotime($end_date) === false ? (string)Carbon::today()->endOfDay() : $end_date;
        $start_date = (string)((new Carbon($end_date))->subDays(90));
        return [
            'start_date' => 'date|after:' . $start_date . '|date_format:Y-m-d H:i:s',//开始时间
            'end_date' => 'date|date_format:Y-m-d H:i:s',   //结束时间
            'queue_status' => 'required|integer|in:1,2',    //对列状态
            'wage_type' => 'integer',                       //工资类型
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
            'wage_type.integer' => '队列状态格式错误',
            'queue_status.required' => '队列状态不能为空',
            'queue_status.integer' => '队列状态格式错误',
            'queue_status.in' => '队列状态内容错误',
        ];
    }
}
