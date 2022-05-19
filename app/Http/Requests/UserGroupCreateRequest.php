<?php

namespace App\Http\Requests;

class UserGroupCreateRequest extends Request
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
            'name' => 'required|unique:user_group|max:16',
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
            'name.required' => '用户组名称不能为空',
            'name.unique' => '用户组名称已存在',
            'name.max' => '用户组名称不得超过 16 个字符',
        ];
    }
}
