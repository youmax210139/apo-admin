<?php

namespace App\Http\Requests;

class PaymentDomainRequest extends Request
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
            'domain' => 'required',
            'https' => 'boolean',
            'payment_category_id' => 'integer|required',
            'intermediate_servers_id' => 'required|integer',
            'status' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'domain.required' => '域名不能为空',
            'https.boolean' => 'https只能 是或否',
            'payment_category_id.required' => '请选择域名绑定的渠道',
            'payment_category_id.integer' => '请选择域名绑定的渠道',
            'intermediate_servers_id.required' => '请选择域名绑定的服务器',
            'intermediate_servers_id.integer' => '请选择域名绑定的服务器',
            'status.boolean' => '状态只能为 开启和禁用',
        ];
    }
}
