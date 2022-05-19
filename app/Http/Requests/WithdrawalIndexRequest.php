<?php

namespace App\Http\Requests;

class WithdrawalIndexRequest extends Request
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
            'id' => 'integer',
            'ip' => 'ip',
            'cashier' => 'alpha_num|min:1|max:20',
            'status' => 'integer',
            'risk_status' => 'integer',
            'bank_id' => 'integer',
            'operate_type' => 'integer',
            'start_date' => 'date|date_format:Y-m-d H:i:s',//开始时间
            'end_date' => 'date|date_format:Y-m-d H:i:s',//结束时间
            'page' => 'integer',//当前页
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
            'id.integer' => '请输入正确的订单号',
            'start_date.date' => '请输入正确的时间',
            'start_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'end_date.date' => '请输入正确的时间',
            'end_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',

            'cashier.alpha_num' => '出纳用户名必须是字母和数字组合',
            'cashier.min' => '出纳用户名长度必须大于等于 1 个字符',
            'cashier.max' => '出纳用户名长度必须小于等于 20 个字符',

            'ip.ip' => 'IP 格式不正确',

            'status.integer' => '无效的出款状态类型',
            'risk_status.integer' => '无效的风控审核状态',
            'bank_id.integer' => '无效的银行名称',
            'operate_type.integer' => '无效的提现类型',

            'page.integer' => '无效的页数',
        ];
    }
}
