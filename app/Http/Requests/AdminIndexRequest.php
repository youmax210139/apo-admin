<?php

namespace App\Http\Requests;

class AdminIndexRequest extends Request
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
            'username' => 'alpha_num|min:1|max:20',
            'ip' => 'ip',
            'role_id' => 'integer',
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
            'username.alpha_num' => '用户名必须是字母和数字组合',
            'username.min' => '用户名长度必须大于等于 1 个字符',
            'username.max' => '用户名长度必须小于等于 20 个字符',
            'ip.ip' => 'IP 格式不正确',
            'role_id.integer' => '请选择正确的角色类型',
        ];
    }
}
