<?php

namespace App\Http\Requests;

class PointOrdersIndexRequest extends Request
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
            'order_no' => 'alpha',
            'admin_user_id' => 'integer',
            'start_date' => 'date|date_format:Y-m-d H:i:s',
            'end_date' => 'date|date_format:Y-m-d H:i:s',
            'amount_min' => 'numeric',
            'amount_max' => 'numeric',
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
            'admin_user_id.integer' => '请选择正确的管理员',
            'start_date.date' => '请输入正确的时间',
            'start_date.date_format' => '时间格式为:YYYY-mm-dd HH:MM:SS',
            'end_date.date' => '请输入正确的时间',
            'end_date.date_format' => '时间格式为:YYYY-mm-dd HH:MM:SS',
            'amount_min.numeric' => '金额输入错误',
            'amount_max.numeric' => '金额输入错误',
        ];
    }
}
