<form id="chat_deposit_config_from" method="post">
    <div style="max-height: 380px;overflow: hidden;overflow-y: auto">
        <table class="table table-striped table-condensed table-bordered table-hover no-footer">
            <tbody>
            <tr>
                <th scope="row">编号</th>
                <th scope="row">专员</th>
                <th scope="row">类型</th>
                <th scope="row">代理渠道</th>
                <th scope="row">姓名</th>
                <th scope="row">银行卡号/支付宝账号</th>
                <th scope="row">银行</th>
                <th scope="row">支行</th>
                <th scope="row">状态</th>
                <th scope="row">操作</th>
            </tr>
            @foreach($payments as $payment)
                <tr>
                    <td>{{$payment->id}}</td>
                    <td>专员{{$payment->kefu}}</td>
                    <td>
                        @if($payment->type == 'bank')
                            银行卡
                        @elseif($payment->type == 'alipay')
                            支付宝
                        @elseif($payment->type == 'wechat')
                            微信
                        @else
                            USDT
                        @endif
                    </td>
                    <td>{{$payment->channel_name}}</td>

                    <td>
                        {{$payment->name}}
                    </td>
                    <td>
                        {{$payment->account}}
                    </td>
                    <td>{{$payment->bank_name}}</td>
                    <td>
                        {{$payment->bank_branch}}
                    </td>
                    <td>
                        @if($payment->enabled)
                            <small class="label bg-green">正常</small>
                        @else
                            <small class="label bg-red">停用</small>
                        @endif
                    </td>
                    <td>
                        @if($payment->enabled)
                            <a href="javascript:;" class="btn-xs enabled" data-id="{{$payment->id}}" data="0">停用</a>
                        @else
                            <a href="javascript:;" class="btn-xs enabled" data-id="{{$payment->id}}" data="1">启用</a>
                        @endif

                        <a href="javascript:;" class="btn-xs edit" data-id="{{$payment->id}}">编辑</a> <a href="javascript:;" class="btn-xs del" data-id="{{$payment->id}}" >删除</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</form>
