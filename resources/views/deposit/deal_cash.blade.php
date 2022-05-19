<table class="table table-bordered table-condensed" style="margin-bottom: 0">
    <tbody>
    <tr>
        <th scope="row" class="text-right">会员:</th>
        <td><span id="username">{{$username}}</span></td>
        <th scope="row" class="text-right">总代:</th>
        <td><span id="topuser">{{$topusername}}</span></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">受付渠道:</th>
        <td>{{$payment_category_name}}</td>
        <th scope="row" class="text-right">受付帐号:</th>
        <td>{{$account_number}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">充值时间:</th>
        <td>{{$created_at}}</td>
        <th scope="row" class="text-right">充值地址:</th>
        <td></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">充值金额:</th>
        <td style="font-size: 18px;font-weight: bold;color: red">{{$amount}}</td>
        <th scope="row" class="text-right">用户手续费:</th>
        <td>{{$user_fee}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">人工确认充值金额:</th>
        <td>{{$manual_amount}}</td>
        <th scope="row" class="text-right">人工确认手续费:</th>
        <td>{{$manual_fee}}</td>
    </tr>
    <tr class="deal_action">
        <th scope="row" class="text-right">人工确认附言:</th>
        <td>{{$manual_postscript}}</td>
        <th scope="row" class="text-right">外部交易ID:</th>
        <td>{{$bank_order_no}}</td>
    </tr>
    <tr class="deal_action">
        <th scope="row" class="text-right">会计:</th>
        <td>{{$accountant_admin}}</td>
        <th scope="row" class="text-right">受理时间:</th>
        <td>{{$deal_at}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">审核结果:</th>
        <td colspan="2">
            <label class="radio-inline" style="font-size: 22px; color: green">
                <input type="radio" onclick="$('#refused_option').hide()" name="deal_result" value="passed"> 通过
            </label>
            <label class="radio-inline" style="font-size: 22px; color: red">
                <input type="radio" onclick="$('#refused_option').show()" name="deal_result" value="refused"> 拒绝
            </label></td>
        <td></td>
    </tr>
    <tr id="refused_option" style="display: none">
        <th scope="row" class="text-right">拒绝原因:</th>
        <td colspan="3">
            <div class="radio">
                <label>
                    <input type="radio" name="refused_option" onclick="$('#refused_reason').val($(this).attr('data'))" data="附言违规，不予受理。" value="option1">附言违规，不予受理。
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="refused_option" onclick="$('#refused_reason').val($(this).attr('data'))" data="金额不正确,不予受理" value="option2">金额不正确,不予受理。
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="refused_option" onclick="$('#refused_reason').val($(this).attr('data'))" data="无效的外部交易流水,不予受理。" value="option2">无效的外部交易流水,不予受理。
                </label>
            </div>
            <div class="form-group">
                <input type="input" class="form-control" id="refused_reason" name="refused_reason" placeholder="自定原因">
            </div>
        </td>
    </tr>
    <tr class="deal_action">
        <th scope="row" class="text-right">操作备注:</th>
        <td colspan="3">
            <textarea class="form-control" placeholder="请填写备注" name="remark"></textarea>
        </td>
    </tr>
    <input type="hidden" name="deposit_status" value="{{$status}}">
    <input type="hidden" name="deposit_id" value="{{$id}}">
    </tbody>
</table>
