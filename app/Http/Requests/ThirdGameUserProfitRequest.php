<?php

namespace App\Http\Requests;
class ThirdGameUserProfitRequest extends Request
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
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d',
            'platform_id' => 'required|int',
            'amount_min' => 'numeric',
            'amount_max' => 'numeric',
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
            'start_date.required' => '开始日期不能为空',
            'start_date.date' => '请输入正确的时间',
            'start_date.date_format' => '时间格式为：YYYY-mm-dd',
            'end_date.required' => '结束日期不能为空',
            'end_date.date' => '请输入正确的结束时间',
            'end_date.date_format' => '时间格式为:YYYY-mm-dd',
            'amount_min.numeric' => '最小金额输入错误',
            'amount_max.numeric' => '最大金额输入错误',
        ];
    }
}
