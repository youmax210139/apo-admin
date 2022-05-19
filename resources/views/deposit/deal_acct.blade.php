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
        <td>{{$ip}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">充值金额:</th>
        <td colspan="2"><span id="amount" style="font-size: 18px;font-weight: bold;color: red">{{$amount}}</span></td>
        <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#amount" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">用户手续费:</th>
        <td colspan="2"><span id="user_fee">{{$user_fee}}</span></td>
        <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#user_fee" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">平台手续费:</th>
        <td colspan="2"><span id="platform_fee">{{$platform_fee}}</span></td>
        <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#platform_fee" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
    </tr>
    @if($payment_method_ident == 'third_offline')
    <tr>
        <th scope="row" class="text-right">转账姓名:</th>
        <td colspan="2"><span id="account_name">{{$extra['account_name']}}</span></td>
        <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#account_name" class="btn btn-sm btn-default copy" @if ($extra['account_name'] == '') disabled @endif><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">转账卡号:</th>
        <td colspan="2"><span id="account_number">{{$extra['account_number']}}</span></td>
        <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#account_number" class="btn btn-sm btn-default copy" @if ($extra['account_number'] == '') disabled @endif><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
    </tr>
    @endif
    <tr>
        <th scope="row" class="text-right">充值备注:</th>
        <td colspan="2"><span id="remark">{{$remark}}</span></td>
        <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#remark" class="btn btn-sm btn-default copy" @if ($remark == '') disabled @endif><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
    </tr>
    <tr class="deal_action">
        <th scope="row" class="text-right">实际到帐金额:</th>
        <td><input type="text" class="form-control" name="deal_amount" value="{{$amount}}"></td>
        <th scope="row" class="text-right">用户手续费:</th>
        <td><input type="text" class="form-control" name="deal_fee" value="{{$user_fee}}"><span class="help-block">如果是扣除则为负数</span></td>
    </tr>
    <tr class="deal_action">
        <th scope="row" class="text-right">附言:</th>
        <td><input type="text" class="form-control" name="deal_postscript" value=""></td>
        <th scope="row" class="text-right">外部流水</th>
        <td><input type="text" class="form-control" name="bank_order_no" value=""></td>
    </tr>
    <input type="hidden" name="deposit_status" value="{{$status}}">
    <input type="hidden" name="deposit_id" value="{{$id}}">
    </tbody>
</table>
