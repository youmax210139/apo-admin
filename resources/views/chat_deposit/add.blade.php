<form id="chat_deposit_config_add_from" method="post" class="form-horizontal">
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">类型</label>
        <div class="col-md-6">
            <select class="form-control" name="type" onchange="changeType($(this).val())">
                {{--<option value="0">选择分组</option>--}}
                @foreach($types as $key => $type)
                    <option value="{{$key}}" @if($key=='bank') selected @endif>{{$type}}</option>
                @endforeach
            </select>
        </div>

    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">专员</label>
        <div class="col-md-6">
            <select class="form-control" name="kefu">
                <option value="1">专员1</option>
                <option value="2">专员2</option>
                <option value="3">专员3</option>
                <option value="4">专员4</option>
                <option value="5">专员5</option>
                <option value="6">专员6</option>
                <option value="7">专员7</option>
                <option value="8">专员8</option>
                <option value="9">专员9</option>
                <option value="10">专员10</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">代理渠道</label>
        <div class="col-md-6">
            <select class="form-control" name="channel">
                {{--<option value="0">选择分组</option>--}}
                @foreach($payment_channels as $channel)
                    <option value="{{$channel->id}}">{{$channel->name}}({{$channel->front_name}})</option>
                @endforeach
            </select>
        </div>

    </div>
    <div class="form-group type bank">
        <label for="tag" class="col-md-3 control-label">开户姓名</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="bank_username" value="" maxlength="64" autocomplete="off">
        </div>
    </div>
    <div class="form-group type bank">
        <label for="tag" class="col-md-3 control-label">银行卡号</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="bank_account" value="" maxlength="64" autocomplete="off">
        </div>
    </div>
    <div class="form-group type bank">
        <label for="tag" class="col-md-3 control-label">银行名称</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="bank_name" value="" maxlength="64" autocomplete="off">
        </div>
    </div>
    <div class="form-group type bank">
        <label for="tag" class="col-md-3 control-label">支行</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="bank_branch" value="" maxlength="64" autocomplete="off">
        </div>
    </div>

    <div class="form-group type alipay" style="display: none">
        <label for="tag" class="col-md-3 control-label">收款二维码</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="alipay_qrcode" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group type alipay" style="display: none">
        <label for="tag" class="col-md-3 control-label">支付宝姓名</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="alipay_name" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group type alipay" style="display: none">
        <label for="tag" class="col-md-3 control-label">支付宝账号</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="alipay_account" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group type alipay" style="display: none">
        <label for="tag" class="col-md-3 control-label">支付宝姓</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="last_name" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group type alipay" style="display: none">
        <label for="tag" class="col-md-3 control-label">支付宝名</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="first_name" value="" autocomplete="off">
        </div>
    </div>

    <div class="form-group type wechat" style="display: none">
        <label for="tag" class="col-md-3 control-label">收款二维码</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="wechat_qrcode" value="" autocomplete="off">
        </div>
    </div>

    <div class="form-group type wechat" style="display: none">
        <label for="tag" class="col-md-3 control-label">微信姓名</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="wechat_name" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group type wechat" style="display: none">
        <label for="tag" class="col-md-3 control-label">微信账号</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="wechat_account" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group type wechat" style="display: none">
        <label for="tag" class="col-md-3 control-label">微信姓</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="last_name" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group type wechat" style="display: none">
        <label for="tag" class="col-md-3 control-label">微信名</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="first_name" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group type USDT" style="display: none">
        <label for="tag" class="col-md-3 control-label">收款链接</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="USDT_qrcode" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">状态</label>
        <div class="col-md-6">
        <div class="radio-inline">
            <label>
                <input value="1"  type="radio" name="enabled">
                正常
            </label>
        </div>

        <div class="radio-inline">
            <label>
                <input value="0" checked type="radio" name="enabled">
                停用
            </label>
        </div>
        </div>
    </div>
    <input type="hidden" name="act" value="add"/>
</form>