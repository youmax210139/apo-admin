<?php

namespace App\Http\Requests;

class AdminCreateRequest extends Request
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
            'username' => 'required|unique:admin_users|max:20',
            'usernick' => 'required|unique:admin_users|max:20',
            'password' => 'required|max:20|confirmed',
            'password_confirmation' => 'min:6|max:20'
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
            'username.required' => '用户名不能为空',
            'username.unique' => '用户名已存在',
            'username.max' => '用户名不得超过 20 个字符',
            'usernick.required' => '用户昵称不能为空',
            'usernick.unique' => '用户昵称已存在',
            'usernick.max' => '用户昵称不得超过 20 个字符',
            'password.required' => '密码不能为空',
            'password.max' => '密码不得超过 20 个字符',
            'password.confirmed' => '确认密码必须一致',
        ];
    }
}
