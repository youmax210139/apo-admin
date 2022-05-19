<?php

namespace App\Http\Requests;

use Carbon\Carbon;

class MethodAnalyseRequest extends Request
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
        $end_date = $this->post('end_date');
        $start_date = Carbon::parse($end_date)->subDays(32)->toDateString();

        return [
            'start_date' => 'required|date|after:' . $start_date . '|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d',
            'lottery_id' => 'int',
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
            'start_date.required' => '开始日期不能为空',
            'start_date.after' => '查询日期不能超过32天',
            'end_date.required' => '结束日期不能为空',
        ];
    }
}
