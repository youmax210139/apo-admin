<?php

namespace App\Http\Requests;

class ThirdGameUserIndexRequest extends Request
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
            'created_start_date' => 'required|date|date_format:Y-m-d H:i:s', //开始时间
            'end_date' => 'date|date_format:Y-m-d H:i:s',   //结束时间
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
            'created_start_date.date' => '请输入正确的时间',
            'created_start_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'created_end_date.date' => '请输入正确的时间',
            'created_end_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
        ];
    }
}
