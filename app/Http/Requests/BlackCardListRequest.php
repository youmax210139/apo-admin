<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlackCardListRequest extends FormRequest
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
            'account_name' => 'required|max:16',
            'account' => 'required|max:20',
            'remark' => 'alpha_dash',
        ];
    }


    public function messages()
    {
        return [
            'account_name.required' => '请输入账户名',
            'account_name.max' => '账户名不得超过 16 个字符',
            'account.required' => '请输入卡号',
            'account.max' => '卡号格式不正确',
            'remark.alpha_dash' => '备注只能为中文字母或数字',

        ];
    }
}
