<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        ×
    </button>
    <h4 class="modal-title">抽返水明细【#{{$detail->project_id}}】</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover">
        <tbody>
        <tr>
            <th>用户ID</th>
            <td><a href="/user/detail?id={{$detail->user_id}}" mounttabs="" title="用户详情[{{$detail->username}}]">{{$detail->username}}</a> [#{{$detail->user_id}}]</td>
            <th>级别/组别</th>
            <td>{{$detail->user_type_name}} / {{$detail->user_group_name}}</td>
        </tr>
        <tr>
            <th>代理树</th>
            <td colspan="3">
                @foreach($parent_tree as $parent)
                    {{$parent->username}} /
                    @endforeach
            </td>
        </tr>
        <tr>
            <th>彩种</th>
            <td>{{$detail->lottery_name}}</td>
            <th>玩法</th>
            <td>{{$detail->method_name}}</td>
        </tr>
        <tr>
            <th>奖期</th>
            <td>{{$detail->issue}}</td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th>注单编号</th>
            <td><a href="/project/detail?id={{$detail->project_no}}" mounttabs="" title="{{$detail->username}}投注详情[{{$detail->project_no}}]">{{$detail->project_no}}</a></td>
            <th>状态</th>
            <td>
                @if ($detail->is_cancel==0)
                    @if ($detail->is_get_prize==0)
                        <span class="label label-default">未开奖</span>
                    @elseif ($detail->is_get_prize==2)
                        <span class="label label-danger">未中奖</span>
                    @elseif ($detail->is_get_prize==1)
                        @if ($detail->prize_status==0)
                            <span class="label label-info">未派奖</span>
                        @else
                            <span class="label label-success">已派奖</span>
                        @endif
                    @endif
                @elseif ($detail->is_cancel==1)
                    <span class="label label-warning">用户撤单</span>
                @elseif ($detail->is_cancel==2)
                    <span class="label label-warning">管理员撤单</span>
                @elseif ($detail->is_cancel==3)
                    <span class="label label-warning">开错奖撤单</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>投注金额</th>
            <td>{{$detail->total_price}}</td>
            <th>返奖金额</th>
            <td>{{$detail->bonus}}</td>
        </tr>
        <tr>
            <th>投注时间</th>
            <td>{{$detail->created_at}}</td>
            <th>返奖时间</th>
            <td>{{$detail->send_prize_at}}</td>
        </tr>

        <tr>
            <th>抽水ID</th>
            <td>{{$detail->pump_inlet_id}}</td>
            <th>抽水状态</th>
            <td>
                @if($detail->is_pump_paid == $detail->pump_inlet_status)
                    @if ($detail->pump_inlet_status==1)
                        <span class="label label-warning">已计算</span>
                    @elseif ($detail->pump_inlet_status==2)
                        <span class="label label-primary">已抽水</span>
                    @elseif ($detail->pump_inlet_status==3)
                        <span class="label label-success">已返水</span>
                    @else
                        <span class="label label-danger">未知状态</span>
                    @endif
                @else
                    <span class="label label-danger">状态同步失败( {{$detail->is_pump_paid}} - {{$detail->pump_inlet_status}} )</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>抽水基数</th>
            <td>{{$detail->pump_inlet_cardinal}}</td>
            <th>抽水比例</th>
            <td>{{$detail->pump_inlet_scale * 100}}%</td>
        </tr>
        <tr>
            <th>抽水金额</th>
            <td>{{$detail->pump_inlet_amount}}</td>
            <th>返水总金额</th>
            <td>{{$detail->pump_outlet_amount}}</td>
        </tr>
        <tr>
            <th>开始时间</th>
            <td>{{$detail->pump_inlet_created_at}}</td>
            <th>完成时间</th>
            <td>{{$detail->pump_inlet_updated_at}}</td>
        </tr>
        <tr>
            <th>备注</th>
            <td colspan="3">{{$detail->pump_inlet_extend}}</td>
        </tr>
        </tbody>
    </table>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>返水ID</th>
            <th>返水人</th>
            <th>返水基数</th>
            <th>返水比例</th>
            <th>返水金额</th>
            <th>状态</th>
            <th>备注</th>
        </tr>
        </thead>
        <tbody>
        @foreach($pump_outlets as $outlet)
            <tr>
                <td>{{$outlet->id}}</td>
                <td>{{$outlet->username}}</td>
                <td>{{$outlet->cardinal}}</td>
                <td>{{$outlet->scale * 100 }}%</td>
                <td>{{$outlet->amount}}</td>
                <td>
                    @if ($outlet->status==1)
                        <span class="label label-warning">已计算</span>
                    @elseif ($outlet->status==2)
                        <span class="label label-success">已抽水</span>
                    @elseif ($outlet->status==3)
                        <span class="label label-success">已返水</span>
                    @else
                        <span class="label label-danger">未知状态</span>
                    @endif
                </td>
                <td>
                    @if(!empty($outlet->extend))
                    {{var_export($outlet->extend)}}
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>