<form id="chat_deposit_config_edit_from" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">类型</label>
        <div class="col-md-6">
            @if($payment->type == 'bank')
                银行卡
            @elseif($payment->type == 'alipay')
                支付宝
            @elseif($payment->type == 'wechat')
                微信
            @else
                USDT
            @endif
        </div>

    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">专员</label>
        <div class="col-md-6">
            <select class="form-control" name="kefu">
                <option value="1" @if($payment->kefu==1) selected @endif>专员1</option>
                <option value="2" @if($payment->kefu==2) selected @endif>专员2</option>
                <option value="3" @if($payment->kefu==3) selected @endif>专员3</option>
                <option value="4" @if($payment->kefu==4) selected @endif>专员4</option>
                <option value="5" @if($payment->kefu==5) selected @endif>专员5</option>
                <option value="6" @if($payment->kefu==6) selected @endif>专员6</option>
                <option value="7" @if($payment->kefu==7) selected @endif>专员7</option>
                <option value="8" @if($payment->kefu==8) selected @endif>专员8</option>
                <option value="9" @if($payment->kefu==9) selected @endif>专员9</option>
                <option value="10" @if($payment->kefu==10) selected @endif>专员10</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">代理渠道</label>
        <div class="col-md-6">
            <select class="form-control" name="channel">
                {{--<option value="0">选择分组</option>--}}
                @foreach($payment_channels as $channel)
                    <option value="{{$channel->id}}" @if($payment->$channel==$channel->id) selected @endif>{{$channel->name}}({{$channel->front_name}})</option>
                @endforeach
            </select>
        </div>

    </div>
    @if($payment->type != 'USDT')
    <div class="form-group type bank">
        <label for="tag" class="col-md-3 control-label">
            @if($payment->type=='bank')
                银行卡姓名
            @elseif($payment->type=='alipay')
                支付宝姓名
            @else
                微信姓名
            @endif
        </label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="name" value="{{$payment->name}}" maxlength="64" autocomplete="off">
        </div>
    </div>
    <div class="form-group type bank">
        <label for="tag" class="col-md-3 control-label">
            @if($payment->type=='bank')
                银行卡号
            @elseif($payment->type=='alipay')
                支付宝账号
            @else
                微信账号
            @endif
        </label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="account" value="{{$payment->account}}" maxlength="64" autocomplete="off">
        </div>
    </div>
    @endif
    @if($payment->type=='bank')
    <div class="form-group type bank">
        <label for="tag" class="col-md-3 control-label">银行名称</label>
        <div class="col-md-6">
            <input type="text"  placeholder="非银行卡类型可不填" class="form-control" name="bank_name" value="{{$payment->bank_name}}" maxlength="64" autocomplete="off">
        </div>
    </div>
    <div class="form-group type bank">
        <label for="tag" class="col-md-3 control-label">支行</label>
        <div class="col-md-6">
            <input type="text" placeholder="非银行卡类型可不填" class="form-control" name="bank_branch" value="{{$payment->bank_branch}}" maxlength="64" autocomplete="off">
        </div>
    </div>
    @endif
    @if($payment->type=='alipay' || $payment->type=='wechat')
    <div class="form-group type alipay" >
        <label for="tag" class="col-md-3 control-label">收款二维码</label>
        <div class="col-md-6">
            <input type="text" placeholder="没有可不填"  class="form-control" name="qrcode" value="{{$payment->qrcode}}" autocomplete="off">
        </div>
    </div>
    <div class="form-group type alipay">
        <label for="tag" class="col-md-3 control-label">
            @if($payment->type=='alipay')
                支付宝姓
            @elseif($payment->type=='wechat')
                微信姓
            @endif
        </label>
        <div class="col-md-6">
            <input type="text" placeholder="非支付宝微信类型可不填" class="form-control" name="last_name" value="{{$payment->last_name}}" autocomplete="off">
        </div>
    </div>
    <div class="form-group type alipay" >
        <label for="tag" class="col-md-3 control-label">
            @if($payment->type=='alipay')
                支付宝名
            @elseif($payment->type=='wechat')
                微信名
            @endif
        </label>
        <div class="col-md-6">
            <input type="text" placeholder="非支付宝微信类型可不填" class="form-control" name="first_name" value="{{$payment->first_name}}" autocomplete="off">
        </div>
    </div>
    @endif
    @if($payment->type =='USDT')
        <div class="form-group type USDT">
            <label for="tag" class="col-md-3 control-label">收款链接</label>
            <div class="col-md-6">
                <input type="text" class="form-control" name="qrcode" value="{{$payment->qrcode}}" autocomplete="off">
            </div>
        </div>
    @endif
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">状态</label>
        <div class="col-md-6">
        <div class="radio-inline">
            <label>
                <input value="1" @if($payment->enabled) checked @endif type="radio" name="enabled">
                正常
            </label>
        </div>

        <div class="radio-inline">
            <label>
                <input value="0" @if(!$payment->enabled) checked @endif  type="radio" name="enabled">
                停用
            </label>
        </div>
        </div>
    </div>
    <input type="hidden" name="act" value="edit"/>
    <input type="hidden" name="id" value="{{$payment->id}}"/>
</form>