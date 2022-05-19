<table class="table table-bordered table-condensed" style="margin-bottom: 0">
    <tbody>
    <tr>
        <th scope="row" class="text-right">订单号:</th>
        <td><span id="username">{{$id}}</span></td>
        <th scope="row" class="text-right">状态:</th>
        <td>
            <span id="topuser">
                <span class="label
                    @if($status == 0) label-primary
                    @elseif($status == 1) label-warning
                    @elseif($status == 2) label-success
                    @elseif($status == 3) label-danger
                    @else label-info @endif ">{{$status_label}}</span>
            </span>
        </td>
    </tr>
    <tr>
        <th scope="row" class="text-right">会员:</th>
        <td><span id="username">{{$username}}</span></td>
        <th scope="row" class="text-right">总代:</th>
        <td><span id="topuser"></span></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">受付渠道:</th>
        <td>{{$payment_category_name}}</td>
        <th scope="row" class="text-right">受付帐号:</th>
        <td>{{$payment_channel_name}} [前台别名：{{$payment_channel_front_name}}]</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">充值金额:</th>
        <td ><span id="amount" style="font-size: 18px;font-weight: bold;color: red">{{$amount}}</span></td>
        <th scope="row" class="text-right">用户手续费:</th>
        <td>{{$user_fee}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right"></th>
        <td ><span id="amount"></span></td>
        <th scope="row" class="text-right">平台手续费</th>
        <td>{{$platform_fee}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">充值时间:</th>
        <td>{{$created_at}}</td>
        <th scope="row" class="text-right">充值地址:</th>
        <td>{{$ip}}</td>
    </tr>
    @if(!empty($manual_postscript))
    <tr>
        <th scope="row" class="text-right">人工输入金额:</th>
        <td>{{$manual_amount}}</td>
        <th scope="row" class="text-right">人工输入手续费:</th>
        <td>{{$manual_fee}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">人工输入附言:</th>
        <td>{{$manual_postscript}}</td>
        <th scope="row" class="text-right"></th>
        <td></td>
    </tr>
    @endif
    <tr>
        <th scope="row" class="text-right">审核时间:</th>
        <td>{{$deal_at}}</td>
        <th scope="row" class="text-right">会计:</th>
        <td>{{$accountant_admin}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">冲入时间:</th>
        <td>{{$deal_at}}</td>
        <th scope="row" class="text-right">出纳:</th>
        <td>{{$cash_admin}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">本站帐变流水:</th>
        <td>{{$order_id}}</td>
        <th scope="row" class="text-right">外部帐变流水:</th>
        <td>{{$bank_order_no}}</td>
    </tr>
    @if ($error_type)
    <tr>
        <th scope="row" class="text-right">违规类型:</th>
        <td colspan="3">{{$error_type}}</td>
    </tr>
    @endif
    @if ($remark)
    <tr>
        <th scope="row" class="text-right">管理员备注:</th>
        <td colspan="3">{{$remark}}</td>
    </tr>
    @endif
    </tbody>
</table>
