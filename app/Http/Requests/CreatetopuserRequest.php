<?php

namespace App\Http\Requests;

class CreatetopuserRequest extends Request
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
            'username' => 'required|unique:users|max:20',
            'password' => 'required|max:64',
            'usernick' => 'required|max:20',
            'user_group' => 'required|integer|in:1,2,3',
            'user_prize_level' => 'required|int',
            'rebates' => 'array',
            'rebates.*' => 'required|numeric',
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
            'username.unique' => '用户名已存在,不能重复',
            'username.max' => '用户名不得超过 20 个字符',
            'password.required' => '登录密码不能为空',
            'password.max' => '登录密码不得超过 64 个字符',
            'usernick.required' => '用户昵称不能为空',
            'usernick.max' => '用户昵称不得超过 20 个字符',
            'user_group.required' => '用户组不能为空',
            'user_group.integer' => '请选择正确的用户组类型',
            'user_group.in' => '用户组类型选择不正确',
            'play_mode.required' => '允许玩法类型不能为空',
            'play_mode.integer' => '请选择正确的允许玩法类型',
            'play_mode.in' => '允许玩法类型选择不正确',
            'user_prize_level.required' => '奖金级别不能为空',
            'user_prize_level.integer' => '请选择正确的奖金级别类型',
            'rebates.*.required' => '彩票或者第三方返点不能为空',
            'rebates.array' => '彩票或者第三方返点不正确',
            'rebates.*.numeric' => '彩票或者第三方返点格式不正确',
        ];
    }
}
