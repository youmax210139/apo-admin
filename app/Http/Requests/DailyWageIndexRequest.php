<?php

namespace App\Http\Requests;

class DailyWageIndexRequest extends Request
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
        $type_page = $this->get('type_page', get_config('dailywage_default_type', 1));

        $format = 'Y-m-d';
        if ($type_page == 2) {
            $format = 'Y-m-d H:i:s';
        } elseif ($type_page == 3) {
            $format = 'Y-m-d H:i:s';
        }
        return [
            'amount_min' => 'Numeric',
            'amount_max' => 'Numeric',
            'created_start_date' => 'date|date_format:' . $format,
            'page' => 'integer',
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

            'created_start_date.date' => '请输入正确的计算日期',
            'created_start_date.date_format' => '计算日期格式为：YYYY-mm-dd',
            'amount_min.Numeric' => '派现最小金额必须是数字',
            'amount_max.Numeric' => '派现最大金额必须是数字',
            'page.integer' => '无效的页数',

        ];
    }
}
