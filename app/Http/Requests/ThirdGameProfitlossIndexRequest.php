<?php

namespace App\Http\Requests;

class ThirdGameProfitlossIndexRequest extends Request
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
            'start_date' => 'required|date|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date|date_format:Y-m-d H:i:s',
            'user_group_id' => 'integer|in:,0,1,2,3',
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
            'start_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'start_date.required' => '开始时间不能为空',
            'end_date.date' => '请输入正确的时间',
            'end_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'end_date.required' => '结束时间不能为空',
            'user_group_id.integer' => '请选择正确的用户组别',
            'user_group_id.in' => '用户组别选择不正确',
        ];
    }
}
