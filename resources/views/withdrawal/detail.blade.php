<table class="table table-striped table-bordered table-hover">
    <tbody>
    <tr>
        <th width="20%" scope="row" class="text-right">状态:</th>
        <td width="30%">
            <span class="label @if($status == 1) label-success @elseif(@status == 2)  label-danger @elseif(@status == 3)  label-warning  @else label-info  @endif ">{{$status_label}}</span>
        </td>
        <th width="20%" scope="row" class="text-right"></th>
        <td width="30%"></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">会员:</th>
        <td>{{$username}}</td>
        <th scope="row" class="text-right">总代:</th>
        <td>{{$topusername}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">申请时间:</th>
        <td>{{$created_at}}</td>
        <th scope="row" class="text-right">申请IP:</th>
        <td>{{$ip}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">开户银行:</th>
        <td>{{$user_bank_name}}</td>
        <th scope="row" class="text-right">所在地:</th>
        <td>{{$province}} / {{$city}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">开户名:</th>
        <td>{{hide_str($account_name,0,-1)}}</td>
        <th scope="row" class="text-right">银行卡号:</th>
        <td>{{hide_str($account,3,-4)}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">提款金额:</th>
        <td class="text-danger">{{round($amount,2)}}</td>
        <th scope="row" class="text-right">用户手续费:</th>
        <td class="text-danger">{{$user_fee}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">平台手续费:</th>
        <td class="text-danger">{{$platform_fee}}</td>
        <th scope="row" class="text-right">本次属{{$this_withdrawal_date}}次数:</th>
        <td class="text-danger">{{$this_withdrawal_times}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">处理时间:</th>
        <td>{{$cashier_at}}</td>
        <th scope="row" class="text-right">完成时间:</th>
        <td>{{$done_at}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">出纳员:</th>
        <td>{{$cashier_username}}</td>
        <th scope="row" class="text-right">出纳 IP:</th>
        <td>{{$cashier_ip}}</td>
    </tr>
    @if(!empty($remark))
        <tr>
            <th scope="row" class="text-right">出纳备注:</th>
            <td colspan="3">{{$remark}}</td>
        </tr>
    @endif
    <tr class="deal_action">
        <th scope="row" class="text-right">出款方式:</th>
        <td>
            @if($operate_type == 2)
                第三方出款 @if( !empty($third_ident) ) [{{$third_name}}.{{$third_ident}}]   @endif
            @elseif ($operate_type == 3)
                软件
            @else
                人工 @if(!empty($cashier_bank_name))[ 银行：{{$cashier_bank_name}} . {{$cashier_bank_ident}} ] @endif
            @endif
        </td>
        <th scope="row" class="text-right">外部流水</th>
        <td>{{$bank_order_no}}</td>
    </tr>
    @if( !empty($third_ident) )
        <tr>
            <th scope="row" class="text-right">提交时间:</th>
            <td>{{$third_add_at}}</td>
            <th scope="row" class="text-right">提交次数:</th>
            <td>{{$third_add_count}}</td>
        </tr>
        <tr>
            <th scope="row" class="text-right">确认时间:</th>
            <td>{{$third_check_at}}</td>
            <th scope="row" class="text-right">确认次数:</th>
            <td>{{$third_check_count}}</td>
        </tr>
        <tr>
            <th scope="row" class="text-right">{{$third_name}}返回:</th>
            <td colspan="3" style="word-break: break-all;">{{$third_response}}</td>
        </tr>
    @endif
    </tbody>
</table>