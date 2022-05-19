<?php

namespace App\Http\Requests;

class IssueCreateRequest extends Request
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
            'lottery_id' => 'required|int',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
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
            'lottery_id.required' => '彩种ID 不能为空',
            'lottery_id.int' => '彩种ID 请输入整数',
            'start_date.required' => '奖期生成日期开始时间 不能为空',
            'start_date.date' => '奖期生成日期开始时间 请输入时间格式',
            'end_date.required' => '奖期生成日期截至时间 不能为空',
            'end_date.date' => '奖期生成日期截至时间 请输入时间格式',
        ];
    }
}
