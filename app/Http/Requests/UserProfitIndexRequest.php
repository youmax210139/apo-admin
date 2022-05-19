<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfitIndexRequest extends FormRequest
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
            'start_time' => 'date|date_format:Y-m-d H:i:s',//开始时间
            'end_time' => 'date|date_format:Y-m-d H:i:s',//结束时间
            'deposit_min' => 'numeric',
            'deposit_max' => 'numeric',
            'rebate_min' => 'numeric',
            'rebate_max' => 'numeric',
            'profit_min' => 'numeric',
            'profit_max' => 'numeric',
        ];
    }

    public function messages()
    {
        return [
            'start_time.date' => '开始时间不正确',
            'start_time.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'end_time.date' => '结束时间不正确',
            'end_time.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'deposit_min.numeric' => '最小充值金额错误',
            'deposit_max.numeric' => '最大充值金额错误',
            'rebate_min.numeric' => '最小返点金额错误',
            'rebate_max.numeric' => '最大返点金额错误',
            'profit_min.numeric' => '最小盈亏金额错误',
            'profit_max.numeric' => '最大盈亏金额错误',
        ];
    }
}
