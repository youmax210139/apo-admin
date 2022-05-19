<?php

namespace App\Http\Requests;

class ActivityUpdateRequest extends Request
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
            'ident' => 'required|unique:activity,ident,' . $this->get('id') . '|max:64',
            'name' => 'required|unique:activity,name,' . $this->get('id') . '|max:64',
            'sort' => 'integer',
            'start_time' => 'required|date|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date|date_format:Y-m-d H:i:s|after:start_time',
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
            'ident.required' => '唯一标识不能为空',
            'ident.unique' => '唯一标识已存在,不能重复',
            'ident.max' => '唯一标识不得超过 64 个字符',
            'name.required' => '活动名称不能为空',
            'name.unique' => '活动名称已存在,不能重复',
            'name.max' => '活动名称不得超过 64 个字符',
            'sort.integer' => '排序必须是数字',
            'start_time.required' => '开始时间不能为空',
            'start_time.date' => '请输入正确的开始时间',
            'start_time.date_format' => '时间格式为:YYYY-mm-dd HH:MM:SS',
            'end_time.required' => '开始时间不能为空',
            'end_time.date' => '请输入正确的结束时间',
            'end_time.date_format' => '时间格式为:YYYY-mm-dd HH:MM:SS',
            'end_time.after' => '结束时间必须大于开始时间',
            'status.integer' => '请选择正确的活动状态类型',
            'status.in' => '活动状态类型选择不正确',
        ];
    }
}
