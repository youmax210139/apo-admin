<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportDateUserIndexRequest extends FormRequest
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
            'start_time' => 'date|date_format:Y-m-d',//开始时间
            'end_time' => 'date|date_format:Y-m-d',//结束时间
            'export' => 'int',
        ];
    }

    public function messages()
    {
        return [
            'start_time.date' => '开始时间不正确',
            'start_time.date_format' => '时间格式为：YYYY-mm-dd',
            'end_time.date' => '结束时间不正确',
            'end_time.date_format' => '时间格式为：YYYY-mm-dd',
            'export.int' => 'export参数错误'
        ];
    }
}
