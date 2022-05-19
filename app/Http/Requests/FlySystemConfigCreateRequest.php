<?php

namespace App\Http\Requests;

class FlySystemConfigCreateRequest extends Request
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
            'ident' => 'required|unique:fly_system_config|max:64',
            'name' => 'required|unique:fly_system_config|max:64',
            'lottery_idents' => 'required|max:256',
            'domain' => 'required|max:128',
            'status' => 'integer|in:0,1',
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
            'ident.required' => '唯一英文标识不能为空',
            'ident.unique' => '唯一英文标识已存在,不能重复',
            'ident.max' => '唯一英文标识不得超过 64 个字符',
            'name.required' => '中文名称不能为空',
            'name.unique' => '中文名称已存在,不能重复',
            'name.max' => '中文名称不得超过 64 个字符',
            'lottery_idents.required' => '彩种标识不能为空',
            'lottery_idents.max' => '彩种标识不得超过 256 个字符',
            'domain.required' => '推送域名不能为空',
            'domain.max' => '推送域名不得超过 128 个字符',
            'status.integer' => '请选择正确的状态类型',
            'status.in' => '状态类型选择不正确',
        ];
    }
}
