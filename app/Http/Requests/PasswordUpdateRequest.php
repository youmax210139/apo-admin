<?php

namespace App\Http\Requests;

class PasswordUpdateRequest extends Request
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
            'old_password' => 'required|alpha_num|min:6|max:20',
            'new_password' => 'required|alpha_num|min:6|max:20|confirmed',
            'new_password_confirmation' => 'required',
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
            'old_password.required' => '原密码不能为空',
            'old_password.alpha_num' => '原密码只能是数字或字母',
            'old_password.min' => '原密码不得少于 6 个字符',
            'old_password.max' => '原密码不得超过 20 个字符',

            'new_password.required' => '新密码不能为空',
            'new_password.alpha_num' => '新密码只能是数字或字母',
            'new_password.min' => '新密码不得少于 6 个字符',
            'new_password.max' => '新密码不得超过 20 个字符',
            'new_password.confirmed' => '确认新密码与新密码不一致',
            'new_password_confirmation.required' => '确认密码不能为空',
        ];
    }
}
