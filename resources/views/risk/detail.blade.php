<table class="table table-bordered">
    <tbody>
    <tr>
        <th width="20%" scope="row" class="text-right">风控状态:</th>
        <td width="30%">{{$status_label}}</td>
        <th width="20%" scope="row" class="text-right"></th>
        <td width="30%"></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">会员:</th>
        <td><span id="username">{{$username}}</span></td>
        <th scope="row" class="text-right">总代:</th>
        <td><span id="topuser">{{$top_username}}</span></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">申请时间:</th>
        <td><span id="topuser">{{$created_at}}</span></td>
        <th scope="row" class="text-right">申请IP:</th>
        <td><span id="username">{{$client_ip}}</span></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">提款金额:</th>
        <td>{{$amount}}</td>
        <th scope="row" class="text-right">手续费</th>
        <td>{{$user_fee}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">上次成功提款金额:</th>
        <td>{{$last_withdrawal_amount}}</td>
        <th scope="row" class="text-right">上次成提款功ID:</th>
        <td>{{$last_withdrawal_id}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">上次成功提款时间:</th>
        <td>{{$last_withdrawal_at}}</td>
        <th scope="row" class="text-right">上次成功提款IP:</th>
        <td>{{$last_withdrawal_ip}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">距上次成功提款累计充值金额:</th>
        <td>{{$deposit_total}}</td>
        <th scope="row" class="text-right">距上次成功提款累计充值次数:</th>
        <td>{{$deposit_times}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">距上次成功提款累计总投注金额:</th>
        <td>{{$bet_price}}</td>
        <th scope="row" class="text-right">距上次成功提款累计总返奖金额:</th>
        <td>{{$bet_bonus}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">距上次成功提款累计投注次数:</th>
        <td>{{$bet_times}}</td>
        <th scope="row" class="text-right">初步分析:</th>
        <td>平台 @if($bet_bonus > $bet_price) <span
                    class="text-danger"> 亏损 {{$bet_bonus - $bet_price }} </span> @else <span
                    class="text-success"> 盈利 {{$bet_price - $bet_bonus }} </span> @endif 元
        </td>
    </tr>
    <tr>
        <th scope="row" class="text-right">审核员:</th>
        <td>{{$verifier_username}}</td>
        <th scope="row" class="text-right">审核IP:</th>
        <td>{{$verifier_ip}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">审核时间:</th>
        <td>{{$verifier_at}}</td>
        <th scope="row" class="text-right">完成时间:</th>
        <td>{{$done_at}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">开户银行:</th>
        <td>{{$user_bank_name}}</td>
        <th scope="row" class="text-right">所在地:</th>
        <td>{{$province}} / {{$city}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">账户名:</th>
        <td>{{$account_name}}</td>
        <th scope="row" class="text-right">账号:</th>
        <td>{{$account}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">风控备注:</th>
        <td colspan="3">{{$risk_remark}}</td>
    </tr>
    <tr>
        <th scope="row" class="text-right">自动风控备注:</th>
        <td colspan="3">{{ $auto_risk_remark }}</td>
    </tr>
    @if (!empty($refused_msg))
        <tr>
            <th scope="row" class="text-right">风控拒绝提款原因:</th>
            <td colspan="3">{{$refused_msg}}</td>
        </tr>
    @endif
    </tbody>
</table>