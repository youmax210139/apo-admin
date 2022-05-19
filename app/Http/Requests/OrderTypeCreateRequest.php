<?php

namespace App\Http\Requests;

class OrderTypeCreateRequest extends Request
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
            'id' => 'required|unique:order_type|int',
            'name' => 'required|unique:order_type|max:64',
            'ident' => 'required|unique:order_type|max:16',
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
            'name.required' => '名称不能为空',
            'name.unique' => '名称已存在',
            'name.max' => '名称不得超过 64 个字符',
            'id.required' => 'ID不能为空',
            'id.unique' => 'ID已存在',
            'id.int' => 'ID必须为正整数',
            'ident.required' => '标识不能为空',
            'ident.unique' => '标识已存在',
            'ident.max' => '标识不得超过 16 个字符',
            'description.max' => '描述不得超过 255 个字符',
        ];
    }
}
