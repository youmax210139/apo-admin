<?php

namespace App\Http\Requests;

class BankVirtualCreateRequest extends Request
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
            'name' => 'required|max:64',
            'ident' => 'required|unique:bank_virtual',
            'currency' => 'required|max:64',
            'rate' => 'nullable|numeric',
            'url' => 'required|max:200',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'amount_min' => 'required|numeric',
            'amount_max' => 'required|numeric',
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
            'name.required' => '请输入银行名称',
            'name.max' => '名称不得超过 64 个字符',
            'ident.required' => '请输入接口标识',
            'ident.unique' => '标示重复',
            'currency.required' => '请输入币别',
            'rate.numeric' => '汇率需为数字',
            'url.required' => '汇率API地址 不能为空',
            'url.max' => '汇率API地址不得超过 200 个字符',
            'start_time.required' => '开始时间不能为空',
            'start_time.date_format' => '时间格式为HH:MM',
            'end_time.required' => '结束时间不能为空',
            'end_time.date_format' => '时间格式为:HH:MM',
            'amount_min.required' => '提现最小不能为空',
            'amount_max.required' => '提现最大不能为空',
        ];
    }
}
