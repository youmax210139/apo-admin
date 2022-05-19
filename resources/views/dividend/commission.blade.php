<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        ×
    </button>
    <h4 class="modal-title">【{{$detail->username}}】佣金详情 ID:{{$detail->id}}</h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-condensed table-bordered table-hover " >
        <thead>
            <tr>
                <th class="text-center" >来源用户</th>
                <th class="text-center" >销量总额</th>
                <th class="text-center" >盈亏总额</th>
                <th class="text-center" >佣金比例</th>
                <th class="text-center" >佣金应派金额</th>
                <th class="text-center" >备注</th>
            </tr>
        </thead>
        <tbody>
            @foreach( $commission as $key => $value )
            <tr>
                <td class="text-center" >{{$value['username']}}</td>
                <td class="text-center" >{{$value['total_price']}}</td>
                <td class="text-center" >{{$value['total_profit']}}</td>
                <td class="text-center" >-</td>
                <td class="text-center" >{{$value['amount']}}</td>
                <td class="text-center" >奖励类型: 
                    @switch($value['reward_type'])
                        @case(1)
                            上级
                            @break
                        @case(2)
                            直属
                            @break
                        @case(3)
                            招商
                            @break
                        @default
                            自身
                            @break
                    @endswitch
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right"><b>总计： </b></th>
                <th colspan="3" class="text-center">{{$detail->amount}}</th>
            </tr>
        </tfoot>
    </table>
</div>