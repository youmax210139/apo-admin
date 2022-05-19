<?php

namespace App\Http\Requests;

use Carbon\Carbon;

class LotterybonusIndexRequest extends Request
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
        $start_date = (string)((new Carbon($end_date))->subDays(365));
        return [
            'start_date' => 'date|after:' . $start_date . '|date_format:Y-m-d H:i:s',//开始时间
            'end_date' => 'date|date_format:Y-m-d H:i:s',       //结束时间
            'user_group_id' => 'integer|in:0,1,2,3',
            'lottery_id' => 'integer',              //彩种ID
            'lottery_method_id' => 'integer',       //玩法ID
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
            'start_date.after' => '查询间隔不得超过 1 年',
            'start_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'end_date.date' => '请输入正确的时间',
            'end_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'user_group_id.integer' => '请选择正确的用户组别',
            'user_group_id.in' => '用户组别选择不正确',
            'lottery_id.integer' => '无效的彩种 ID',
            'lottery_method_id.integer' => '无效的玩法 ID'
        ];
    }
}
