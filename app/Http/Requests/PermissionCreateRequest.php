<?php

namespace App\Http\Requests;

class PermissionCreateRequest extends Request
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
            'rule' => 'required|unique:admin_role_permissions|max:64',
            'name' => 'required|max:128',
            'description' => 'max:256',
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
            'rule.required' => '权限规则不能为空',
            'rule.unique' => '权限规则已存在',
            'rule.max' => '权限规则不得超过 64 个字符',
            'name.required' => '权限名称不能为空',
            'name.max' => '权限名称不得超过 128 个字符',
            'description.max' => '权限概述不得超过 256 个字符'
        ];
    }
}
