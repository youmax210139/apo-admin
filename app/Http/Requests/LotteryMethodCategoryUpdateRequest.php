<?php

namespace App\Http\Requests;

class LotteryMethodCategoryUpdateRequest extends Request
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
            'id' => 'required|int',
            'name' => 'required|max:32',
            'ident' => 'required|unique:lottery_method_category,ident,' . $this->get('id') . '|max:16',
            'drop_point' => 'required|numeric|between:0,500'
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
            'id.required' => 'ID 不能为空',
            'name.required' => '中文名不能为空',
            'name.max' => '中文名不得超过 32 个字符',
            'ident.required' => '英文标识不能为空',
            'ident.unique' => '英文标识已存在',
            'ident.max' => '英文标识不得超过 16 个字符',
            'drop_point.required' => '下降点数不能为空',
            'drop_point.numeric' => '下降点数必须为数字',
            'drop_point.between' => '下降点数范围必须在 :min - :max 之间',
        ];
    }
}
