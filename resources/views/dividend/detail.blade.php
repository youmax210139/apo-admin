<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        ×
    </button>
    <h4 class="modal-title">分红明细【#{{$detail->id}}】</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover">
        <tbody>
        <tr>
            <th>用户ID</th>
            <td>{{$detail->username}}[#{{$detail->user_id}}]</td>
            <th>用户级别</th>
            <td>{{$detail->user_type_name}}</td>
        </tr>
        <tr>
            <th>用户组</th>
            <td>
                {{$detail->user_group_name}}
            </td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th>分红类型</th>
            <td>
                @if($detail->type == 1)
                    <span class="label label-success">A线[佣金模式]</span>
                @elseif($detail->type == 2)
                    <span class="label label-warning">B线[比例模式]</span>
                @else
                    <span class="label label-danger">类型[{{$detail->type}}]</span>
                @endif
            </td>
            <th>分红模式</th>
            <td>
                @if($detail->model == 1)
                    <span class="label label-success">累计</span>
                @elseif($detail->model == 0)
                    <span class="label label-warning">不累计</span>
                @else
                    <span class="label label-danger">模式[{{$detail->model}}]</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>开始时间</th>
            <td>{{$detail->start_time}}</td>
            <th>结束时间</th>
            <td>{{$detail->end_time}}</td>
        </tr>
        <tr>
            <th>销量</th>
            <td>{{$detail->total_price}}</td>
            <th>奖金</th>
            <td>{{$detail->total_bonus}}</td>
        </tr>
        <tr>
            <th>返点</th>
            <td>{{$detail->total_rebate}}</td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th>充值平台手续费</th>
            <td>{{$detail->total_deposit_fee}}</td>
            <th>提现平台手续费</th>
            <td>{{$detail->total_withdrawal_fee}}</td>
        </tr>
        <tr>
            <th>日工资</th>
            <td>{{$detail->total_wage}}</td>
            <th>活动礼金</th>
            <td>{{$detail->total_activity}}</td>
        </tr>

        <tr>
            <th>盈亏</th>
            <td>{{$detail->total_profit}}</td>
            <th>总活跃人数</th>
            <td>{{$detail->total_daus}}</td>
        </tr>
        <tr>
            <th>上次分红比例</th>
            <td>{{$detail->last_rate}}</td>
            <th>上次分红金额</th>
            <td>{{$detail->last_amount}}</td>
        </tr>
        <tr>
            <th>分红比例</th>
            <td>{{$detail->rate}}</td>
            <th>实际分红金额</th>
            <td>{{$detail->amount}}</td>
        </tr>

        <tr>
            <th>扩展字段</th>
            <td style="WORD-WRAP: break-word" width="235">
                @if(!empty($detail->extra))
                    @foreach($detail->extra as $key => $value)
                        @if(in_array(get_config('dividend_type_ident'), ['Jiucheng2']))
                            @switch($key)
                                @case('loss')
                                    第三、四名亏损总金额 : {{$value}}<br />
                                    @break
                                @case('consume')
                                    月平均日销量 : {{$value}}<br />
                                    @break
                                @default
                                    {{$key}} : {{$value}}<br />
                            @endswitch
                        @elseif(in_array(get_config('dividend_type_ident'), ['Duowan2']))
                            @switch($key)
                                @case('real_profit')
                                    周期团队盈亏(含盈利会员) : {{$value}}<br />
                                    @break
                                @case('real_profit_amount')
                                    实际计算盈亏 : {{$value}}<br />
                                    @break
                                @default
                                    {{$key}} : {{$value}}<br />
                            @endswitch
                        @else
                            @if(is_array($value))
                                @foreach($value as $item_key => $item_value)
                                    {{$item_key}} : {{$item_value}}
                                @endforeach
                                <br />
                            @else
                                {{$key}} : {{$value}}<br />
                            @endif
                        @endif
                    @endforeach
                @endif
            </td>
            <th>状态</th>
            <td>
                @if($detail->status == 1)
                    <span class="label label-success">已发放</span>
                @elseif($detail->status == 2)
                    <span class="label label-warning">发放中</span>
                @elseif($detail->status == 3)
                    <span class="label label-default">上级审核</span>
                @elseif($detail->status == 4)
                    <span class="label label-danger">后台审核</span>
                @elseif($detail->status == 5)
                    <span class="label label-default">已取消</span>
                @elseif($detail->status == 6)
                    <span class="label label-default">不符合条件</span>
                @elseif($detail->status == 7)
                    <span class="label label-default">非结算日</span>
                @else
                    <span class="label ">状态[{{$detail->amount}}]</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>发放方式</th>
            <td>
                @if($detail->send_type == 1)
                    <span class="label label-primary">系统发放</span>
                @elseif($detail->send_type == 2)
                    <span class="label label-info">上级发放</span>
                @else
                    <span class="label label-warning">方式[{{$detail->send_type}}]</span>
                @endif
            </td>
            <th>发放时间</th>
            <td>{{$detail->send_at}}</td>
        </tr>

        <tr>
            <th>报表汇总状态</th>
            <td>
                @if($detail->report_status == 0)
                    <span class="label label-warning">未开始</span>
                @elseif($detail->report_status == 1)
                    <span class="label label-primary">进行中</span>
                @elseif($detail->report_status == 2)
                    <span class="label label-success">完成</span>
                @else
                    其他状态[{{$detail->report_status}}]
                @endif
            </td>
            <th>结算周期</th>
            <td>
                @if($detail->period == 1)
                    <span class="label label-warning">日结</span>
                @elseif($detail->period == 2)
                    <span class="label label-primary">半月结</span>
                @elseif($detail->period == 3)
                    <span class="label label-success">月结</span>
                @elseif($detail->period == 4)
                    <span class="label label-info">周结</span>
                @elseif($detail->period == 5)
                    <span class="label label-info">10日结</span>
                @elseif($detail->period == 11)
                    <span class="label label-info">浮动日结</span>
                @else
                    其他周期[{{$detail->period}}]
                @endif
            </td>
        </tr>

        <tr>
            <th>创建时间</th>
            <td>{{$detail->created_at}}</td>
            <th></th>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>