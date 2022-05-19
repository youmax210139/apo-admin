<?php

namespace App\Http\Requests;

class BankUpdateRequest extends Request
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
            'name' => 'required|max:64',
            'ident' => 'required|unique:banks,ident,' . $this->get('id'),
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
            'name.required' => '请输入银行名称',
            'name.max' => '名称不得超过 64 个字符',
            'ident.required' => '请输入接口标示',
            'ident.unique' => '标示重复',
        ];
    }
}
