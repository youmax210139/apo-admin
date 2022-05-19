<?php

namespace App\Http\Requests;

class IntermediateServersRequest extends Request
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
            'name' => 'required|alpha_dash|max:40',
            'ip' => 'required|ip',
            'domain' => 'required',
            'status' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.alpha_dash' => '名称只能为中文、字母、数字、破折号（ - ）以及下划线（ _ ）',
            'name.required' => '名称不能为空',
            'name.max' => '名称长度在 40 个字符以内',
            'ip.required' => '服务器IP不能为空',
            'ip.ip' => 'IP 格式不正确',
            'domain.required' => '同步网址不能为空',
            'status.boolean' => '支付方式状态只能为 开启和禁用',
        ];
    }
}
