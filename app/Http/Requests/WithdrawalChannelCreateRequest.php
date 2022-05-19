<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalChannelCreateRequest extends FormRequest
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
        return [
            'name' => 'required|alpha_dash|max:30',
            'withdrawal_category_ident' => 'required|alpha_num|max:30|exists:withdrawal_category,ident',
            'merchant_id' => 'required|alpha_dash|max:128',
            'amount_min' => 'required|numeric',
            'amount_max' => 'required|numeric',
            'status' => 'required|in:0,1',
            'user_fee_status' => 'required|in:0,1',
            'user_fee_operation' => 'integer',
            'user_fee_step' => 'numeric',
            'user_fee_down_type' => 'integer',
            'user_fee_down_value' => 'numeric',
            'user_fee_up_type' => 'integer',
            'user_fee_up_value' => 'numeric',
            'platform_fee_status' => 'required|in:0,1',
            'platform_fee_operation' => 'integer',
            'platform_fee_step' => 'numeric',
            'platform_fee_down_type' => 'integer',
            'platform_fee_down_value' => 'numeric',
            'platform_fee_up_type' => 'integer',
            'platform_fee_up_value' => 'numeric',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '通道名称不能为空',
            'name.alpha_dash' => '通道名称格式只能为中文字幕数字组合',
            'name.max' => '通道名称最大长度为30个字符',
            'withdrawal_category_ident.required' => '提现渠道不能为空',
            'withdrawal_category_ident.alpha_num' => '提现渠道格式错误',
            'withdrawal_category_ident.max' => '提现渠道长度错误',
            'withdrawal_category_ident.unique' => '提现渠道不存在',
            'merchant_id.required' => '商户号不能为空',
            'merchant_id.alpha_dash' => '商户号只能为字母数字破折号（ - ）下划线（ _ ）组合',
            'merchant_id.max' => '商户号最大长度为128个字符',
            'amount_min.required' => '提现金额不能为空',
            'amount_min.numeric' => '提现金额格式只能为数字',
            'amount_max.required' => '提现金额不能为空',
            'amount_max.numeric' => '提现金额格式只能为数字',
            'status.required' => '状态不能为空',
            'status.in' => '状态只能为开启或关闭',
            'user_fee_status.required' => '用户手续费状态不能为空',
            'user_fee_status.in' => '用户手续费状态只能为开启或关闭',
            'user_fee_operation.integer' => '用户手续费操作只允许扣除和返还',
            'user_fee_step.numeric' => '用户手续费界定金额错误',
            'user_fee_down_type.integer' => '低于界定的手续费类型错误',
            'user_fee_down_value.numeric' => '低于界定线的手续费错误',
            'user_fee_up_type.integer' => '高于界定的手续费类型错误',
            'user_fee_up_value.numeric' => '高于界定线的手续费错误',
            'platform_fee_status.required' => '平台手续费状态不能为空',
            'platform_fee_status.in' => '平台手续费状态只能为开启或关闭',
            'platform_fee_step.numeric' => '平台手续费界定金额错误',
            'platform_fee_down_type.integer' => '低于界定的手续费类型错误',
            'platform_fee_down_value.numeric' => '低于界定线的手续费错误',
            'platform_fee_up_type.integer' => '高于界定的手续费类型错误',
            'platform_fee_up_value.numeric' => '高于界定线的手续费错误'
        ];
    }
}
