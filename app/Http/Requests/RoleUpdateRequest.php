<?php

namespace App\Http\Requests;

class RoleUpdateRequest extends Request
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
            'id' => 'required|int',
            'name' => 'required|unique:admin_roles,name,' . $this->get('id') . '|max:64',
            'description' => 'max:255',
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
            'id.required' => '请输入要修改的角色ID',
            'name.required' => '角色名不能为空',
            'name.unique' => '角色名已存在',
            'name.max' => '角色名不得超过 64 个字符',
            'description.max' => '描述不得超过 255 个字符',
        ];
    }
}
