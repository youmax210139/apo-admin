<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        ×
    </button>
    <h4 class="modal-title">私返明细【#{{$detail->id}}】</h4>
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
                <th>时间类型</th>
                <td>
                    <span class="label label-primary">{{$detail->time_type_label}}
                    </span>
                </td>
                <th>私返类型</th>
                <td>
                    <span class="label label-primary">{{$detail->condition_type_label}}
                    </span>
                </td>
            </tr>
            <tr>
                <th>私返基数</th>
                <td>
                    <span class="label label-primary">{{$detail->cardinal_type_label}}
                    </span>
                </td>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th>开始时间</th>
                <td>{{$detail->start_time}}</td>
                <th>结束时间</th>
                <td>{{$detail->end_time}}</td>
            </tr>
            @if(!empty($detail->lottery_name))
                <tr>
                    <th>彩种</th>
                    <td>{{$detail->lottery_name}}</td>
                    <th>奖期</th>
                    <td>{{$detail->issue}}</td>
                </tr>
            @endif
            <tr>
                <th>销量</th>
                <td>{{$detail->price}}</td>
                <th>奖金</th>
                <td>{{$detail->bonus}}</td>
            </tr>
            <tr>
                <th>返点</th>
                <td>{{$detail->rebate}}</td>
                <th>利润</th>
                <td>{{$detail->profit}}</td>
            </tr>
            <tr>
                <th>活跃用户数</th>
                <td>{{(int)$detail->active}}</td>
                <th>私返比例</th>
                <td>{{$detail->rate}}%</td>
            </tr>
            <tr>
                <th>施益用户</th>
                <td>{{$detail->source_name}}</td>
                <th>派发金额</th>
                <td>{{$detail->amount}}</td>
            </tr>
            <tr>
                <th>私返状态</th>
                <td>
                    @if($detail->status == 0)
                        <span class="label label-info">待审核</span>
                    @elseif($detail->status == 1)
                        <span class="label label-primary">待发放</span>
                    @elseif($detail->status == 2)
                        <span class="label label-success">已发放</span>
                    @elseif($detail->status == 3)
                        <span class="label label-danger">已拒绝</span>
                    @elseif($detail->status == 4)
                        <span class="label label-warning">未达标</span>
                    @else
                        <span class="label label-default">其他状态[{{$detail->status}}]</span>
                    @endif
                </td>
                <th>报表汇总状态</th>
                <td>
                    @if($detail->report_status == 0)
                        <span class="label label-info">未开始</span>
                    @elseif($detail->report_status == 1)
                        <span class="label label-primary">进行中</span>
                    @elseif($detail->report_status == 2)
                        <span class="label label-success">已发放</span>
                    @elseif($detail->report_status == 3)
                        <span class="label label-danger">不发放</span>
                    @elseif($detail->report_status == 4)
                        <span class="label label-warning">未达标</span>
                    @else
                        <span class="label label-default">其他状态[{{$detail->report_status}}]</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>计算时间</th>
                <td>{{$detail->calculate_at}}</td>
                <th>审核时间</th>
                <td>{{$detail->verified_at}}</td>
            </tr>
            <tr>
                <th>发放时间</th>
                <td>{{$detail->send_at}}</td>
                <th></th>
                <td></td>
            </tr>
            @if (!@empty($detail->remark))
                <tr>
                    <th>备注</th>
                    <td>{{@print_r($detail->remark)}}</td>
                    <th></th>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>
</div>