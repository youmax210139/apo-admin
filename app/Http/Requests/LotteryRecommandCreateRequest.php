<?php

namespace App\Http\Requests;

class LotteryRecommandCreateRequest extends Request
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
            'lottery_id' => 'required|exists:lottery,id|unique:lottery_recommend',
            'lottery_ident1' => 'required|exists:lottery,ident|different:lottery_ident2|different:lottery_ident3|different:lottery_ident4',
            'lottery_ident2' => 'required|exists:lottery,ident|different:lottery_ident1|different:lottery_ident3|different:lottery_ident4',
            'lottery_ident3' => 'nullable|exists:lottery,ident|different:lottery_ident1|different:lottery_ident2|different:lottery_ident4',
            'lottery_ident4' => 'nullable|exists:lottery,ident|different:lottery_ident1|different:lottery_ident2|different:lottery_ident3',
            'tip' => 'required|string',
            'interval_minutes' => 'required|integer|gt:-1',
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
            'lottery_id.required' => '请输入彩种',
            'lottery_id.exists' => '彩种不存在',
            'lottery_id.unique' => '彩种重复',
            'lottery_ident1.required' => '推荐彩种1必须填写',
            'lottery_ident1.different' => '推荐彩种英文标示1重复',
            'lottery_ident1.exists' => '推荐彩种英文标示1不存在',
            'lottery_ident2.required' => '推荐彩种2必须填写',
            'lottery_ident2.different' => '推荐彩种英文标示2重复',
            'lottery_ident2.exists' => '推荐彩种英文标示2不存在',
            'lottery_ident3.required' => '推荐彩种3必须填写',
            'lottery_ident3.different' => '推荐彩种英文标示3重复',
            'lottery_ident3.exists' => '推荐彩种英文标示3不存在',
            'lottery_ident4.required' => '推荐彩种4必须填写',
            'lottery_ident4.different' => '推荐彩种英文标示4重复',
            'lottery_ident4.exists' => '推荐彩种英文标示4不存在',
            'tip.required' => '请输入提示文案',
            'interval_minutes.required' => '重复弹出间隔必须填写',
            'interval_minutes.integer' => '重复弹出间隔为正整数',
            'interval_minutes.gt' => '重复弹出间隔为正整数',
        ];
    }
}
