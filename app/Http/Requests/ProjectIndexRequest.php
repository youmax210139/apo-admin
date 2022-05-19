<?php

namespace App\Http\Requests;

use Carbon\Carbon;

class ProjectIndexRequest extends Request
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
        $end_date = strtotime($end_date) === false ? (string)Carbon::today()->endOfDay() : $end_date;
        $start_date = (string)((new Carbon($end_date))->subDays(90));
        return [
            'project_no' => 'alpha',//注单编号
            'start_date' => 'date|after:' . $start_date . '|date_format:Y-m-d H:i:s',//开始时间
            'end_date' => 'date|date_format:Y-m-d H:i:s',//结束时间
            'deduct_start_date' => 'date|date_format:Y-m-d H:i:s',//开始结算时间
            'deduct_end_date' => 'date|date_format:Y-m-d H:i:s',//結束结算时间
            'amount_min' => 'numeric',//投注最小金额
            'amount_max' => 'numeric',//投注最大金额
            'bonus_min' => 'numeric',//派奖最小金额
            'bonus_max' => 'numeric',//派奖最大金额
            'search_type' => 'integer|in:1,2',//用户查询类型，1为指定总代ID，2为手工输入
            'zongdai' => 'integer',//指定总代，总代ID
            'included_sub_agent' => 'integer',//是否包含下级
            'no_included_zongdai' => 'integer',//是否包含下级
            'user_group_id' => 'integer|in:0,1,2,3',
            'mode' => 'integer|in:1,2,3,4,5,6,7,8,9',//元角分厘模式，0不限，1元2角3分4厘
            'client_type' => 'in:-1,0,1,2,3,4,5',//来源 -1 不限 0 Unknown 1 WEB 2IOS 3 Android 4  AIR客户端 5 WAP
            'lottery_id' => 'integer',//彩种ID
            'lottery_method_id' => 'integer',//玩法ID
            'ip' => 'ip',
            'issue' => 'min:1|max:20',//奖期
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
            'start_date.date' => '请输入正确的时间',
            'start_date.after' => '查询间隔不得超过 90 天',
            'start_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'end_date.date' => '请输入正确的时间',
            'end_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',

            'deduct_start_date.date' => '请输入正确的时间',
            'deduct_start_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',
            'deduct_end_date.date' => '请输入正确的时间',
            'deduct_end_date.date_format' => '时间格式为：YYYY-mm-dd HH:MM:SS',

            'amount_min.numeric' => '投注最小金额输入错误',
            'amount_max.numeric' => '投注最大金额输入错误',

            'bonus_min.numeric' => '派奖最小金额输入错误',
            'bonus_max.numeric' => '派奖最大金额输入错误',

            'search_type.integer' => '请选择正确的查询类型',
            'search_type.in' => '查询类型必须选【手动输入】或【总代列表】其中之一',

            'zongdai.integer' => '请选择正确的总代',

            'mode.integer' => '请选择正确的元角分模式',
            'mode.in' => '只能是元角分毫其中之一',

            'client_type.in' => '来源限制',
            'user_group_id.integer' => '请选择正确的用户组别',
            'user_group_id.in' => '用户组别选择不正确',

            'lottery_id.integer' => '无效的彩种 ID',
            'lottery_method_id.integer' => '无效的玩法 ID',

            'issue.min' => '奖期长度必须大于等于 1 个字符',
            'issue.max' => '奖期长度必须小于等于 20 个字符',
            'ip.ip' => 'IP 格式不正确',
            'project_no.alpha' => '无效的注单编号',
            'page.integer' => '无效的页数',
        ];
    }
}
