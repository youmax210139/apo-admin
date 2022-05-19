<table class="table table-bordered table-condensed" style="margin-bottom: 0">
    <tbody>
        <tr>
            <th scope="row" class="text-right">会员:</th>
            <td><span id="username">{{$username}}</span></td>
            <th scope="row" class="text-right">总代:</th>
            <td><span id="topuser">{{$topusername}}</span></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">申请时间:</th>
            <td><span id="created_at">{{$created_at}}</span></td>
            <th scope="row" class="text-right">客户端 IP:</th>
            <td><span id="ip">{{$ip}}</span></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">开户银行:</th>
            <td><span id="bank_name">{{$bank_name}}</span></td>
            <th scope="row" class="text-right">开户行所在:</th>
            <td><span id="bank_province">{{$province}} / {{$city}}</span></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">开户名:</th>
            <td colspan="2"><span id="account_name">{{$account_name}}</span></td>
            <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#account_name" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">银行卡号:</th>
            <td colspan="2"><span id="account_no">{{$account}}</span></td>
            <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#account_no" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">结算金额:</th>
            <td colspan="2"><code id="calculate_amount">{{ round($amount,2) + ($user_fee < 0 ? $user_fee : 0)  }}</code></td>
            <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#calculate_amount" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">提款金额:</th>
            <td colspan="2"><code id="amount">{{round($amount,2)}}</code></td>
            <td><button type="button" data-clipboard-action="copy" data-clipboard-target="#amount" class="btn btn-sm btn-default copy"><i class="fa fa-copy" aria-hidden="true"></i> 复制</button></td>
        </tr>
        <tr>
            <th scope="row" class="text-right">用户备注:</th>
            <td><span class="text-danger">{{$user_remark}}</span></td>
            <th scope="row" class="text-right">重点观察备注:</th>
            <td><span class="text-danger">{{$user_observe}}</span></td>
        </tr>
        <tr class="deal_action">

            <th scope="row" class="text-right">出款接口:</th>
            <td colspan="3">
                <select name="withdrawalapi" class="form-control">
                    <option value="">请选择出款接口</option>
                    @foreach($thirdapis as $b)
                    @if($amount >= $b['amount_min'] && $amount<=$b['amount_max'] && in_array($bank_id,explode(",",$b['banks'])))
                    <option value="{{$b['id']}}">{{$b['name']}}-{{$b['withdrawal_category_ident']}}(最小:{{$b['amount_min']}},最大：{{$b['amount_max']}},手续费:{{$b['fee_exp']}})</option>
                    @endif
                    @endforeach
                </select>
            </td>
        </tr>
    <input type="hidden" name="withdrawalid" value="{{$id}}">
    </tbody>
</table>
