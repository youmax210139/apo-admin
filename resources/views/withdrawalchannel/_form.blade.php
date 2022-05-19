<div class="form-group">
    <label for="tag" class="col-md-3 control-label">渠道中文名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" value="{{ $name }}" maxlength="64">
    </div>
</div>

<div class="form-group" id="item_method">
    <label for="tag" class="col-md-3 control-label">提现渠道类型</label>
    <div class="col-md-6">
        <select name="withdrawal_category_ident" id="withdrawal_category_ident" class="form-control">
            <option value="0">请选择类型</option>
            @foreach ($categorys as $item)
                <option value="{{ $item->ident }}" {{ $item->ident == $withdrawal_category_ident ? 'selected':'' }}>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group" id="item_method">
    <label for="tag" class="col-md-3 control-label">提现中间站域名</label>
    <div class="col-md-6">
        <select name="domain_id" class="form-control">
            <option value="0">请选择域名{{$domain_id}}</option>
            @foreach ($domains as $item)
                <option value="{{ $item->id }}" {{ $item->id == $domain_id ? 'selected':'' }}>【{{ $item->payment_category_name or '不限渠道'}}】 {{ $item->domain }} 【{{ $item->intermediate_servers_name }}】
            @endforeach
        </select>
    </div>
</div>

<hr/>

<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">状态</label>
    <div class="col-md-6">
        <label class="radio-inline">
            <input type="radio" value="1" name="status" @if ($status != 0 ) checked @endif>
            开启
        </label>
        <label class="radio-inline">
            <input type="radio" value="0" name="status" @if ($status == 0) checked @endif>
            关闭
        </label>
    </div>
</div>

<hr>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">商户号</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="merchant_id" value="{{ $merchant_id }}" maxlength="64">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">密钥1</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="key1" value="{{ $key1 }}" placeholder="提现秘钥">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">密钥2</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="key2" value="{{ $key2 }}" placeholder="如果是rsa加密，则此处为公钥，或者为支付秘钥">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">密钥3</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="key3" value="{{ $key3 }}"  placeholder="如果是rsa加密，则此处为私钥">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">提现金额</label>
    <div class="col-md-6 form-inline">
        最小：<input type="text" class="form-control" name="amount_min" value="{{ $amount_min }}" placeholder="最小" maxlength="64"> -
        最大：<input type="text" class="form-control" name="amount_max" value="{{ $amount_max }}" placeholder="最大" maxlength="64">
    </div>
</div>

<hr>

<!-- 用户手续费 -->
<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">用户手续费状态</label>
    <div class="col-md-6">
        <label class="radio-inline">
            <input type="radio" name="user_fee_status" value="1" @if($user_fee_status == 1) checked @endif>
            开启
        </label>
        <label class="radio-inline">
            <input type="radio" name="user_fee_status" value="0" @if($user_fee_status == 0) checked @endif>
            关闭
        </label>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">用户手续费界定线</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="user_fee_step" value="{{ $user_fee_step }}" placeholder="整数">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">用户手续费操作</label>
    <div class="col-md-6">
        <label class="radio-inline">
            <input type="radio" name="user_fee_operation" value="0" @if($user_fee_operation == 0) checked @endif>
            扣除
        </label>
        <label class="radio-inline">
            <input type="radio" name="user_fee_operation" value="1" @if($user_fee_operation == 1) checked @endif>
            返还
        </label>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">低于界定线的手续费计算</label>
    <div class="col-md-6">
        <div class="form-inline">
            <select name="user_fee_down_type" class="form-control" style="width: 100px;">
                <option value="0" @if ($user_fee_down_type == 0) selected @endif>百分比</option>
                <option value="1" @if ($user_fee_down_type == 1) selected @endif>固定值</option>
            </select>
            <input type="text" class="form-control" name="user_fee_down_value" value="{{ $user_fee_down_value }}">
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">高于界定线的手续费计算</label>
    <div class="col-md-6">
        <div class="form-inline">
            <select name="user_fee_up_type" class="form-control" style="width: 100px;">
                <option value="0" @if ($user_fee_up_type == 0) selected @endif>百分比</option>
                <option value="1" @if ($user_fee_up_type == 1) selected @endif>固定值</option>
            </select>
            <input type="text" class="form-control" name="user_fee_up_value" value="{{ $user_fee_up_value }}">
        </div>
    </div>
</div>

<hr>
<!-- 平台手续费 -->
<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">平台手续费状态</label>
    <div class="col-md-6">
        <label class="radio-inline">
            <input type="radio" name="platform_fee_status" value="1" @if($platform_fee_status == 1) checked @endif>
            开启
        </label>
        <label class="radio-inline">
            <input type="radio" name="platform_fee_status" value="0" @if($platform_fee_status == 0) checked @endif>
            关闭
        </label>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">平台手续费界定线</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="platform_fee_step" value="{{ $platform_fee_step }}" placeholder="整数">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">低于界定线的手续费计算</label>
    <div class="col-md-6">
        <div class="form-inline">
            <select name="platform_fee_down_type" class="form-control" style="width: 100px;">
                <option value="0" @if ($platform_fee_down_type == 0) selected @endif>百分比</option>
                <option value="1" @if ($platform_fee_down_type == 1) selected @endif>固定值</option>
            </select>
            <input type="text" class="form-control" name="platform_fee_down_value" value="{{ $platform_fee_down_value }}">
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">高于界定线的手续费计算</label>
    <div class="col-md-6">
        <div class="form-inline">
            <select name="platform_fee_up_type" class="form-control" style="width: 100px;">
                <option value="0" @if ($platform_fee_up_type == 0) selected @endif>百分比</option>
                <option value="1" @if ($platform_fee_up_type == 1) selected @endif>固定值</option>
            </select>
            <input type="text" class="form-control" name="platform_fee_up_value" value="{{ $platform_fee_up_value }}">
        </div>
    </div>
</div>


