<?php

namespace App\Http\Requests;

class ConfigUpdateRequest extends Request
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
            'parent_id' => 'required|int',
            'key' => 'required|unique:config,key,' . $this->get('id') . '|max:64',
            'value' => 'max:256',
            'title' => 'required|unique:config,title,' . $this->get('id') . '|max:64',
            'description' => 'max:128',
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
            'title.required' => '配置标题不能为空',
            'title.unique' => '配置标题已存在,不能重复',
            'title.max' => '配置标题不得超过 64 个字符',
            'key.required' => '配置名称不能为空',
            'key.unique' => '配置名称已存在,不能重复',
            'key.max' => '配置名称不得超过 64 个字符',
            'value.max' => '配置值不得超过 256 个字符',
            'description.max' => '配置描述不得超过 128 个字符'
        ];
    }
}
