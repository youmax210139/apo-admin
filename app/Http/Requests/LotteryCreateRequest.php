<?php

namespace App\Http\Requests;

class LotteryCreateRequest extends Request
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
            'lottery_category_id' => 'required|int',
            'lottery_method_category_id' => 'required|int',
            'ident' => 'required|unique:lottery|max:16',
            'name' => 'required|unique:lottery|max:32',
            'week_cycle' => 'required',
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
            'id.int' => 'ID 请输入整数',
            'lottery_category_id.required' => '彩种分类 ID 不能为空',
            'lottery_category_id.int' => '彩种分类 ID 请输入整数',
            'lottery_method_category_id.required' => '玩法分类 ID 不能为空',
            'lottery_method_category_id.int' => '玩法分类 ID 请输入整数',
            'ident.required' => '英文标识符不能为空',
            'ident.unique' => '英文标识符已存在',
            'ident.max' => '英文标识符不得超过 16 个字符',
            'name.required' => '中文名不能为空',
            'name.unique' => '中文名已存在',
            'name.max' => '中文名不得超过 32 个字符',
            'ident.required' => '英文标识不能为空',
            'ident.max' => '英文标识不得超过 16 个字符',
            'week_cycle.required' => '请选择开奖周期',
        ];
    }
}
