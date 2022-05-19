<?php

namespace App\Http\Requests;

class PaymentChannelRequest extends Request
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
        if ($this->get('set_status', '') == 1) {
            return [
                'status' => 'required|in:0,1',
            ];
        } else {
            return [
                'name' => 'required|alpha_dash|max:40' . (($this->method() == 'POST') ? '|unique:payment_channel' : ''),
                'front_name' => 'required|alpha_dash|max:40',
                'payment_category_id' => 'required|integer',
                'payment_method_id' => 'required|integer',
                'payment_domain_id' => 'integer',
                'status' => 'boolean',
                'amount_decimal' => 'boolean',
                'amount_fixed_list' => 'regex:/^[0-9,.]{0,200}$/',
                'register_time_limit' => 'required|integer|min:0|max:32767',
                'recharge_times_limit' => 'required|integer|min:0|max:32767',
                'recharge_amount_total_limit' => 'required|integer|min:0|max:2147483647',
                'invalid_times_limit' => 'required|integer|min:0|max:32767',
                'invalid_times_lock' => 'required|integer|min:0|max:100',
                'user_fee_line' => 'numeric',
                'user_fee_down_value' => 'numeric',
                'user_fee_up_value' => 'numeric',
                'platform_fee_line' => 'numeric',
                'platform_fee_down_value' => 'numeric',
                'platform_fee_up_value' => 'numeric',
            ];
        }
    }

    public function messages()
    {
        return [
            'name.alpha_dash' => '后台名称只能为中文、字母、数字、破折号（ - ）以及下划线（ _ ）',
            'name.required' => '后台名称不能为空',
            'name.max' => '后台名称长度在 40 个字符以内',
            'name.unique' => '后台名称已存在！',
            'front_name.alpha_dash' => '前台名称只能为中文、字母、数字、破折号（ - ）以及下划线（ _ ）',
            'front_name.required' => '前台名称不能为空',
            'front_name.max' => '前台名称长度在 40 个字符以内',
            'payment_category_id.integer' => '请选择支付渠道',
            'payment_method_id.integer' => '请选择支付类型',
            'payment_domain_id.integer' => '请选择支付域名',
            'status.boolean' => '支付方式状态只能为 开启和禁用',
            'amount_decimal.boolean' => '自动追加小数金额错误',
            'amount_fixed_list.regex' => '固定金额列表格式为 0-9数字，以及使用[,]分隔,例如：100,200,300,500,1000',
            'register_time_limit.required' => '用户注册时间限制不能为空',
            'register_time_limit.integer' => '用户注册时间限制只能为数字',
            'register_time_limit.min' => '用户注册时间限制不能小于0',
            'register_time_limit.max' => '用户注册时间限制不能大于32767',
            'recharge_times_limit.required' => '以往充值次数限制不能为空',
            'recharge_times_limit.integer' => '以往充值次数限制只能为数字',
            'recharge_times_limit.min' => '以往充值次数限制不能小于0',
            'recharge_times_limit.max' => '以往充值次数限制不能大于32767',
            'recharge_amount_total_limit.required' => '以往累计最少充值金额不能为空',
            'recharge_amount_total_limit.integer' => '以往累计最少充值金额只能为数字',
            'recharge_amount_total_limit.min' => '以往累计最少充值金额不能小于0',
            'recharge_amount_total_limit.max' => '以往累计最少充值金额不能大于2147483647',
            'invalid_times_limit.required' => '每天无效申请次数限制不能为空',
            'invalid_times_limit.integer' => '每天无效申请次数限制只能为数字',
            'invalid_times_limit.min' => '每天无效申请次数限制不能小于0',
            'invalid_times_limit.max' => '每天无效申请次数限制不能大于32767',
            'invalid_times_lock.required' => '10分钟无效申请次数限制不能为空',
            'invalid_times_lock.integer' => '10分钟无效申请次数限制只能为数字',
            'invalid_times_lock.min' => '10分钟无效申请次数限制不能小于0',
            'invalid_times_lock.max' => '10分钟无效申请次数限制不能大于100',
            'user_fee_line.integer' => '用户手续费界定线只能为数字',
            'user_fee_down_value.integer' => '用户低于界定线的手续费计算只能为数字',
            'user_fee_up_value.integer' => '用户高于界定线的手续费计算只能为数字',
            'platform_fee_line.integer' => '平台手续费界定线只能为数字',
            'platform_fee_down_value.integer' => '平台低于界定线的手续费计算只能为数字',
            'platform_fee_up_value.integer' => '平台高于界定线的手续费计算只能为数字',
        ];
    }
}
