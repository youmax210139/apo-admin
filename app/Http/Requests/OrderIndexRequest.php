<?php

namespace App\Http\Requests;

use Carbon\Carbon;

class OrderIndexRequest extends Request
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
            'order_no' => 'alpha',
            'order_type_ids' => 'array',
            'order_type_ids.*' => 'integer',
            'admin_user_id' => 'integer',
            'ip' => 'ip',
            'start_date' => 'date|after:' . $start_date . '|date_format:Y-m-d H:i:s',//开始时间
            'end_date' => 'date|date_format:Y-m-d H:i:s',
            'amount_min' => 'numeric',
            'amount_max' => 'numeric',
            'mode' => 'integer|in:1,2,3,4,5,6,7,8,9',
            'play_source' => 'integer|in:0,1,2,3,4,5',
            'user_group_id' => 'integer|in:0,1,2,3',
            'lottery_id' => 'integer',
            'method_id' => 'integer',
            'included_sub_agent' => 'integer|in:0,1',
            'no_included_zongdai' => 'integer|in:0,1',
            'zongdai' => 'integer',
            'seach_type' => 'integer|in:1,2',
            'agent_user_id' => 'integer',
            'payment_account_id.regex' => '支付账户不正确',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'order_no.alpha' => '无效的订单编号',
            'order_type_ids.array' => '订单类型不正确',
            'order_type_ids.*.integer' => '订单类型格式不正确',
            'admin_user_id.integer' => '请选择正确的管理员',
            'ip.ip' => 'IP 格式不正确',
            'start_date.date' => '请输入正确的时间',
            'start_date.after' => '查询间隔不得超过 90 天',
            'start_date.date_format' => '时间格式为:YYYY-mm-dd HH:MM:SS',
            'end_date.date' => '请输入正确的时间',
            'end_date.date_format' => '时间格式为:YYYY-mm-dd HH:MM:SS',
            'amount_min.numeric' => '金额输入错误',
            'amount_max.numeric' => '金额输入错误',
            'mode.integer' => '请选择正确的投注模式类型',
            'mode.in' => '投注模式类型选择不正确',
            'play_source.integer' => '请选择正确的客户端来源类型',
            'play_source.in' => '客户端来源类型选择不正确',
            'user_group_id.integer' => '请选择正确的用户组别',
            'user_group_id.in' => '用户组别选择不正确',
            'lottery_id.integer' => '请选择正确的彩种类型',
            'lottery_id.in' => '彩种类型选择不正确',
            'method_id.integer' => '请选择正确的玩法类型',
            'method_id.in' => '玩法类型选择不正确',
            'included_sub_agent.integer' => '请选择正确的是否包含下级类型',
            'included_sub_agent.in' => '是否包含下级类型选择不正确',
            'no_included_zongdai.integer' => '请选择正确的是否不包含总代类型',
            'no_included_zongdai.in' => '是否不包含总代类型类型选择不正确',
            'search_type.integer' => '请选择正确的搜索类型',
            'search_type.in' => '搜索类型必须选[手动输入]或[总代列表]其中之一',
            'zongdai.integer' => '总代选择错误',
            'payment_account_id' => 'regex:/^[\d,]{1,}$/',
        ];
    }
}
