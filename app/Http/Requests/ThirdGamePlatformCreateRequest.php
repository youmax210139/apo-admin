<?php

namespace App\Http\Requests;

class ThirdGamePlatformCreateRequest extends Request
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
            'ident' => 'required|unique:third_game_platform|max:16',
            'name' => 'required|unique:third_game_platform|max:32',
            "status" => 'int',
            "sort" => 'required|integer',
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
            'ident.required' => '英文标识 不能为空',
            'ident.max' => '英文标识 不能超出16个字符',
            'ident.unique' => '英文标识 已存在',
            'name.required' => '中文名称 不能为空',
            'name.max' => '中文名称 不能超出32个字符',
            'name.unique' => '中文名称 已存在',
            'status.int' => '是否允许登入状态 类型不正确',
            'sort.required' => '显示顺序 不能为空',
            'sort.integer' => '显示顺序 必须为整数',
        ];
    }
}
