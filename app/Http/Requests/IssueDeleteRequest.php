<?php

namespace App\Http\Requests;

class IssueDeleteRequest extends Request
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
            'lottery_id' => 'int',
            'delete_by' => 'required|in:select,date',
            'start_time' => 'date',
            'end_time' => 'date|after:start_time',
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
            'delete_by.required' => '请选择删除方式',
            'delete_by.in' => '只允许以下2种方式：1、删除选中项;2、删除指定日期',
            'lottery_id.int' => '彩种ID 请输入整数',
            'start_time.date' => '开始时间 请输入时间格式',
            'end_time.date' => '截至时间 请输入时间格式',
            'end_time.after' => '截至时间应大于开始时间',
        ];
    }
}
