<?php

namespace App\Http\Requests;

class UserDailyWageContractLineRequest extends Request
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
        $rules = [
            'name' => 'required|min:1|max:20',
            'user_id' => 'required|integer',
            'daily_wage_line_type' => 'required|integer|in:1,2,3,4,5,6,7,8,9,10',
            'content_title' => 'required|array',
            'content_key' => 'required|array',
            'content_value' => 'required|array',
        ];

        return $rules;
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '工资线名不能为空',
            'name.min' => '工资线名已存在',
            'name.max' => '工资线名不得超过 20 个字符',
            'user_id.required' => '用户ID不能为空',
            'user_id.integer' => '用户ID类型不正确',
            'daily_wage_line_type.required' => '工资线类型不能为空',
            'daily_wage_line_type.integer' => '请选择正确的工资线类型',
            'daily_wage_line_type.in' => '工资线类型选择不正确',
            'content_title.required' => '工资线配置名称不能为空',
            'content_title.array' => '工资线配置名称不正确',
            'content_key.required' => '工资线配置标识不能为空',
            'content_key.array' => '工资线配置标识不正确',
            'content_value.array' => '工资线配置内容不正确',
            'content_value.required' => '工资线配置内容不能为空',
        ];
    }
}
