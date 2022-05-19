<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLevelRequest extends FormRequest
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
            'name' => 'required|alpha_dash|max:32|unique:user_level',
            'register_start_time' => 'required|date',
            'register_end_time' => 'required|date',
            'deposit_times' => 'required|numeric',
            'deposit_count_amount' => 'required|numeric',
            'deposit_max_amount' => 'required|numeric',
            'withdrawal_times' => 'required|numeric',
            'withdrawal_count_amount' => 'required|numeric',
            'expense_count_amount' => 'required|numeric',
            'remark' => 'alpha_dash',
            'status' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => '层级名称已经存在',
            'name.required' => '层级名称不能为空',
            'name.alpha_dash' => '层级名称只能为中文字母数字组合',
            'name.max' => '层级名称最长为32个字符',
            'register_start_time.required' => '用户注册时间起始日不能为空',
            'register_start_time.date' => '用户注册日期起始日格式不正确',
            'register_end_time.required' => '用户注册日期截止日不能为空',
            'register_end_time.date' => '用户注册日期截止日格式不正确',
            'deposit_times.numeric' => '累计充值次数必须为数字',
            'deposit_count_amount.numeric' => '累计充值金额必须为数字',
            'deposit_max_amount.numeric' => '单次最大充值必须为数字',
            'withdrawal_times.numeric' => '提现次数必须为数字',
            'withdrawal_count_amount.numeric' => '累计提现必须为数字',
            'expense_count_amount.numeric' => '消费金额必须为数字',
            'remark.alpha_dash' => '备注只允许中文字母数字',
            'status.boolean' => '状态只允许开启、关闭',

        ];
    }
}
