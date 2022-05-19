<?php

namespace App\Http\Requests;

class PaymentCategoryRequest extends Request
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
            'ident' => 'required|alpha_dash|max:20',
            'name' => 'required|alpha_dash|max:40',
            'methods' => 'required',
            'status' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'ident.alpha_dash' => '支付渠道英文标识要求字母、数字、破折号（ - ）以及下划线（ _ ）',
            'ident.max' => '支付渠道英文标识长度在 20 个字符以内',
            'ident.required' => '支付渠道英文标识不为空',
            'name.alpha_dash' => '支付渠道中文名只能为中文、字母、数字、破折号（ - ）以及下划线（ _ ）',
            'name.required' => '支付渠道中文名不能为空',
            'name.max' => '支付渠道中文名长度在 40 个字符以内',
            'methods.required' => '请选择支付类型',
            'status.boolean' => '支付渠道状态只能为 开启和禁用',
        ];
    }
}
