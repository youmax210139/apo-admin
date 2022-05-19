<?php

namespace App\Http\Requests;

class ThirdGameUpdateRequest extends Request
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
            'third_game_platform_id' => 'required|int',
            'ident' => 'required|max:16|unique:third_game,ident,' . $this->get('id'),
            'name' => 'required|max:32',
            "merchant" => 'max:50',
            "merchant_key" => 'max:100',
            "merchant_test" => 'max:50',
            "merchant_key_test" => 'max:100',
            'api_base' => 'required|max:255',
            "api_base_test" => 'max:255',
            "login_status" => 'int',
            "transfer_status" => 'int',
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
            'id.required' => 'ID 不能为空',
            'third_game_platform_id.required' => '游戏平台 不能为空',
            'ident.required' => '英文标识 不能为空',
            'ident.max' => '英文标识 不能超出16个字符',
            'ident.unique' => '英文标识 已存在',
            'name.required' => '中文名称 不能为空',
            'name.max' => '中文名称 不能超出32个字符',
            'merchant.max' => '商户号 不能超出50个字符',
            'merchant_key.max' => '商户密钥 不能超出100个字符',
            'merchant_test.max' => '测试商户号 不能超出50个字符',
            'merchant_key_test.max' => '测试商户密钥 不能超出100个字符',
            'api_base.max' => 'api地址 不能超出255个字符',
            'api_base_test.max' => '测试api地址 不能超出255个字符',
            'login_status.int' => '是否允许登入状态 类型不正确',
            'transfer_status.int' => '是否允许转帐状态 类型不正确',
        ];
    }
}
