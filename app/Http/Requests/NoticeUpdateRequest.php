<?php

namespace App\Http\Requests;

class NoticeUpdateRequest extends Request
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
            'subject' => 'required|unique:notices,subject,' . $this->get('id') . '|max:64',
            'published_at' => 'required|date|date_format:Y-m-d H:i:s',
            'end_at' => 'required|date|date_format:Y-m-d H:i:s',
            'content' => 'required',
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
            'subject.required' => '公告标题不能为空',
            'subject.unique' => '公告标题已存在',
            'subject.max' => '标题不得超过 64 个字符',
            'published_at.required' => '发布时间不能为空',
            'published_at.date' => '请输入正确的发布时间',
            'published_at.date_format' => '时间格式为:yyyy-mm-dd hh:mm:ss',
            'end_at.required' => '结束时间不能为空',
            'end_at.date' => '请输入正确的结束时间',
            'end_at.date_format' => '时间格式为:yyyy-mm-dd hh:mm:ss',
            'content.required' => '公告内容不能为空',
        ];
    }
}
