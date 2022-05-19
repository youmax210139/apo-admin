<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalCategoryUpdateRequest extends FormRequest
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
            'name' => 'required|alpha_dash|max:30',
            'ident' => 'required|alpha_num|max:30',
            'request_url' => 'url|max:128',
            'verify_url' => 'url|max:128',
            'notify_url' => 'url|max:128',
            'banks' => 'required|array',
            'status' => 'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '支付渠道名称不能为空',
            'name.alpha_dash' => '支付渠道名称格式为中文字幕数字组合',
            'name.max' => '支付渠道名称最大长度为30个字符',
            'ident.required' => '标识不能为空',
            'ident.alpha_num' => '标识只能是字幕数字组合',
            'ident.max' => '标识最大长度为30个字符',
            'request_url.url' => '请求URL格式错误',
            'request_url.max' => '请求URL最大长度为128个字符',
            'verify_url.url' => '确认URL格式错误',
            'verify_url.max' => '确认URL最大长度为128个字符',
            'notify_url.url' => '通知URL格式错误',
            'notify_url.max' => '通知URL最大长度为128个字符',
            'banks.required' => '支持银行参数不能为空',
            'banks.array' => '支持银行参数格式不正确',
            'status.required' => '状态码错误',
            'status.in' => '状态码只能为开启或关闭',
        ];
    }
}
