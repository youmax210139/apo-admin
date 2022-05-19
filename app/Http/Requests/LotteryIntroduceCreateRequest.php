<?php

namespace App\Http\Requests;

class LotteryIntroduceCreateRequest extends Request
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
            'subject' => 'required|max:64',
            'content' => 'required',
            'lottery_id' => 'required|int',
            'sort' => 'required|integer',
            'status' => 'integer|in:0,1',
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
            'subject.required' => '标题不能为空',
            'subject.max' => '标题不得超过 64 个字符',
            'content.required' => '内容不能为空',
            'sort.required' => '排序不能为空',
            'sort.integer' => '排序必须是数字',
            'status.integer' => '请选择正确的状态类型',
            'status.in' => '状态类型选择不正确',
        ];
    }
}
