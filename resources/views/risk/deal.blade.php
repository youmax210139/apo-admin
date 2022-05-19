<table class="table table-bordered table-condensed" style="margin-bottom: 0">
    <tbody>
    <tr>
        <th scope="row" class="text-right">会员:</th>
        <td>
            <span id="username">{{$username}}</span>
            @if($user_group_id==1)
                <span class="label label-success">正式组</span>
                @elseif($user_group_id==2)
                <span class="label label-warning">测试组</span>
                @else
                <span class="label label-danger">试玩组</span>
                @endif
        </td>
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
        <td class="text-bold" style="font-size: 22px">{{$amount}}</td>
        <th scope="row" class="text-right"></th>
        <td></td>
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
        <td>平台 @if($bet_bonus > $bet_price) <span class="text-bold"> 亏损 {{$bet_bonus - $bet_price }} </span> @else <span class="text-white"> 盈利 {{$bet_price - $bet_bonus }} </span> @endif 元
        </td>
    </tr>
    <tr>
        <th scope="row" class="text-right">用户备注:</th>
        <td><span class="text-danger">{{$user_remark}}</span></td>
        <th scope="row" class="text-right">重点观察备注:</th>
        <td><span class="text-danger">{{$user_observe}}</span></td>
    </tr>
    <tr>
        <th scope="row" class="text-right">审核结果:</th>
        <td colspan="2">
            <label class="radio-inline" style="font-size: 22px;">
                <input type="radio" onclick="$('#refused_option').hide()" name="status" value="passed"> 通过
            </label>
            <label class="radio-inline" style="font-size: 22px;">
                <input type="radio" name="status" onclick="$('#refused_option').show()" value="refused"> 拒绝
            </label></td>
        <td></td>
    </tr>
    <tr id="refused_option" style="display: none">
        <th scope="row" class="text-right">拒绝原因:</th>
        <td colspan="3">
            <select class="form-control" name="refused_option" onchange="$('#refused_reason').val($(this).val())">
                <option value="">请选择原因</option>
                @if ($refused_reason->isNotEmpty())
                    @foreach($refused_reason as $reason)
                        <option value="{{$reason->text}}">{{$reason->text}}</option>
                    @endforeach
                @else
                <option value="您目前的流水不满足提款所需要求，请您在满足提款所需流水后再次申请提款。">您目前的流水不满足提款所需要求，请您在满足提款所需流水后再次申请提款。</option>
                <option value="由于您的注单注码超过活动最高限码，所以在扣除无效流水后目前您的有效流水不满足所需要求。">由于您的注单注码超过活动最高限码，所以在扣除无效流水后目前您的有效流水不满足所需要求。</option>
                <option value="您当日的提款已达最高上限，目前无法为您出款，请您明日再进行提款。">您当日的提款已达最高上限，目前无法为您出款，请您明日再进行提款。</option>
                <option value="由于风控部门检测出您的账户存在对打、套利行为所以目前无法为您出款。">由于风控部门检测出您的账户存在对打、套利行为所以目前无法为您出款。</option>
                <option value="由于风控部门查询出您的游戏账户存在异常，所以目前无法为您出款。">由于风控部门查询出您的游戏账户存在异常，所以目前无法为您出款。</option>
                <option value="由于目前平台处于维护状态所以您的提款暂时无法查询， 请您在维护结束后再次申请提款。">由于目前平台处于维护状态所以您的提款暂时无法查询， 请您在维护结束后再次申请提款。</option>
                @endif
            </select>

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
    <tr class="deal_action">
        <th scope="row" class="text-right">自动风控备注:</th>
        <td colspan="3">
            {{ $auto_risk_remark }}
        </td>
    </tr>
    <input type="hidden" name="risk_id" value="{{$id}}">
    </tbody>
</table>
