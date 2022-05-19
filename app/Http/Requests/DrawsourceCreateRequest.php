<?php

namespace App\Http\Requests;

class DrawsourceCreateRequest extends Request
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
            'lottery_id' => 'required|int',
            'ident' => 'required|max:32',
            'name' => 'required|max:30',
            'url' => 'required|max:200',
            'status' => 'required|int',
            'rank' => 'required|int',
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
            'lottery_id.required' => '彩种ID 不能为空',
            'lottery_id.int' => '彩种ID 请输入整数',
            'ident.required' => '英文标识符不能为空',
            'ident.unique' => '英文标识符已存在',
            'ident.max' => '英文标识符不得超过 16 个字符',
            'name.required' => '号源名称 不能为空',
            'name.max' => '号源名称 不得超过 30 个字符',
            'url.required' => '号源api地址 不能为空',
            'url.max' => '号源api地址不得超过 200 个字符',
            'status.required' => '状态 不能为空',
            'status.int' => '状态 请输入整数',
            'rank.required' => '权重 不能为空',
            'rank.int' => '权重 请输入整数',
        ];
    }
}
