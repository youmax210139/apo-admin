<div class="form-group">
    <label for="tag" class="col-md-3 control-label">前台名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="front_name" value="{{ $front_name }}" placeholder="大客户直充" maxlength="64">
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">后台名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" value="{{ $name }}" placeholder="易宝-12345678" maxlength="64">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">限制平台</label>
    <div class="col-md-6 radio">
        <label><input type="radio" name="platform" value="0" {{ $platform == 0 ? 'checked':'' }}>不限</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="platform" value="1" {{ $platform == 1 ? 'checked':'' }}>PC</label>&nbsp;&nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="platform" value="2" {{ $platform == 2 ? 'checked':'' }}>手机</label>
    </div>
</div>

<div class="form-group" id="item_category">
    <label for="tag" class="col-md-3 control-label">支付渠道</label>
    <div class="col-md-6">
        <select name="payment_category_id" id="payment_category_id" class="form-control">
            <option value="0">请选择渠道</option>
            @foreach ($categories as $item)
                <option value="{{ $item->id }}" {{ $item->id == $payment_category_id ? 'selected':'' }}>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group" id="item_method">
    <label for="tag" class="col-md-3 control-label">支付类型</label>
    <div class="col-md-6">
        <select name="payment_method_id" id="payment_method_id" class="form-control">
            <option value="0">请选择类型</option>
            @foreach ($methods as $item)
                <option value="{{ $item->id }}" {{ $item->id == $payment_method_id ? 'selected':'' }}>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group payment_row netbank" id="item_banks" style="display: none;">
    <label for="tag" class="col-md-3 control-label">银行</label>
    <div class="col-md-6 checkbox">
        @foreach($banks as $item)
            <label><input type="checkbox" name="banks[]" value="{{ $item['code'] }}" {{ $item['checked'] ? 'checked':'' }}>{{ $item['name'] }}</label>&nbsp;&nbsp;&nbsp;&nbsp;
        @endforeach
    </div>
</div>

<div class="form-group payment_row netbank third_offline" id="item_domain">
    <label for="tag" class="col-md-3 control-label">支付域名</label>
    <div class="col-md-6">
        <select name="payment_domain_id" class="form-control">
            <option value="0">请选择域名</option>
            @foreach ($domains as $item)
                <option value="{{ $item->id }}" {{ $item->id == $payment_domain_id ? 'selected':'' }}>【{{ $item->payment_category_name or '不限渠道'}}】 {{ $item->domain }} 【{{ $item->intermediate_servers_name }}】
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">排序</label>
    <div class="col-md-6">
        <input type="number" class="form-control" name="sort" value="{{ $sort }}" maxlength="3" d>
    </div>
</div>

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

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">前台提示</label>
    <div class="col-md-6">
        <textarea class="form-control" name="front_tip_text" placeholder="自定义前台提示语句">{{ $front_tip_text }}</textarea>
    </div>
</div>

<div class="form-group payment_row transfer third_offline agent_weixin agent_qq agent_alipay agent_chat">
    <label class="col-md-3 control-label checkbox-inline text-bold">是否需要附言</label>
    <div class="col-md-6">
        <label class="radio-inline">
            <input type="radio" value="1" name="postscript_status" @if ($postscript_status != 0 ) checked @endif>
            开启
        </label>
        <label class="radio-inline">
            <input type="radio" value="0" name="postscript_status" @if ($postscript_status == 0) checked @endif>
            关闭
        </label>
    </div>
</div>

<div class="form-group payment_row agent_weixin agent_qq agent_alipay agent_chat">
    <label for="tag" class="col-md-3 control-label">举报有奖</label>
    <div class="col-md-6">
        <label class="radio-inline">
            <input type="radio" value="1" name="informants_available" @if ($informants_available != 0 ) checked @endif>
            开启
        </label>
        <label class="radio-inline">
            <input type="radio" value="0" name="informants_available" @if ($informants_available == 0) checked @endif>
            关闭
        </label>
    </div>
</div>

<hr>
<!-- 商户号信息 -->
<div class="form-group payment_row netbank transfer third_offline">
    <label for="tag" class="col-md-3 control-label">
        <span class="payment_row netbank">商户号</span>
        <span class="payment_row transfer third_offline">银行卡号</span>
    </label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="account_number" value="{{ $account_number }}" placeholder="" maxlength="64">
    </div>
</div>

<div class="form-group payment_row netbank third_offline">
    <label for="tag" class="col-md-3 control-label">商户号密钥</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="account_key" value="{{ $account_key }}" placeholder="">
    </div>
</div>

<div class="form-group payment_row netbank">
    <label for="tag" class="col-md-3 control-label">商户号密钥2</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="account_key2" value="{{ $account_key2 }}" placeholder="没有留空">
    </div>
</div>

<div class="form-group payment_row transfer ">
    <label for="tag" class="col-md-3 control-label">银行卡开户银行</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="account_bank_name" value="{{ $account_bank_name }}" placeholder="XX分行">
    </div>
</div>

<div class="form-group payment_row third_offline">
    <label for="tag" class="col-md-3 control-label">收款方式</label>
    <div class="col-md-6">
        <select name="account_bank_flag" class="form-control">
            <option value="">请选择收款方式...</option>
            <option value="ABC_农业银行" @if($account_bank_flag=='ABC_农业银行') selected @endif>农业银行</option>
            <option value="ICBC_工商银行" @if($account_bank_flag=='ICBC_工商银行') selected @endif>工商银行</option>
            <option value="BCM_交通银行" @if($account_bank_flag=='BCM_交通银行') selected @endif>交通银行</option>
            <option value="CCB_建设银行" @if($account_bank_flag=='CCB_建设银行') selected @endif>建设银行</option>
            <option value="CMB_招商银行" @if($account_bank_flag=='CMB_招商银行') selected @endif>招商银行</option>
            <option value="CMBC_民生银行" @if($account_bank_flag=='CMBC_民生银行') selected @endif>民生银行</option>
            <option value="HXB_华夏银行" @if($account_bank_flag=='HXB_华夏银行') selected @endif>华夏银行</option>
            <option value="PSBC_邮政储蓄银行" @if($account_bank_flag=='PSBC_邮政储蓄银行') selected @endif>邮政储蓄银行</option>
            <option value="CNCB_中信银行" @if($account_bank_flag=='CNCB_中信银行') selected @endif>中信银行</option>
            <option value="CIB_兴业银行" @if($account_bank_flag=='CIB_兴业银行') selected @endif>兴业银行</option>
            <option value="PAB_平安银行" @if($account_bank_flag=='PAB_平安银行') selected @endif>平安银行</option>
            <option value="SPDB_浦发银行" @if($account_bank_flag=='SPDB_浦发银行') selected @endif>浦发银行</option>
            <option value="NINEPAY_九付宝" @if($account_bank_flag=='NINEPAY_九付宝') selected @endif>九付宝</option>
            <option value="LEPOS_乐刷" @if($account_bank_flag=='LEPOS_乐刷') selected @endif>乐刷</option>
            <option value="CGB_广东发展银行" @if($account_bank_flag=='CGB_广东发展银行') selected @endif>广东发展银行</option>
            <option value="HRBB_哈尔滨银行" @if($account_bank_flag=='HRBB_哈尔滨银行') selected @endif>哈尔滨银行</option>
            <option value="CZB_浙商银行" @if($account_bank_flag=='CZB_浙商银行') selected @endif>浙商银行</option>
            <option value="QDCCB_青岛银行" @if($account_bank_flag=='QDCCB_青岛银行') selected @endif>青岛银行</option>
            <option value="ALIPAY_支付宝" @if($account_bank_flag=='ALIPAY_支付宝') selected @endif>支付宝</option>
            <option value="WebMM_微信" @if($account_bank_flag=='WebMM_微信') selected @endif>微信</option>
            <option value="BOC_中国银行" @if($account_bank_flag=='BOC_中国银行') selected @endif>中国银行</option>
            <option value="QCCMBC_民生一码通" @if($account_bank_flag=='QCCMBC_民生一码通') selected @endif>民生一码通</option>
            <option value="QR4XJL_收款小精灵" @if($account_bank_flag=='QR4XJL_收款小精灵') selected @endif>收款小精灵</option>
            <option value="QR4XEP_兴E付" @if($account_bank_flag=='QR4XEP_兴E付') selected @endif>兴E付</option>
            <option value="QR4QFT_中信全付通" @if($account_bank_flag=='QR4QFT_中信全付通') selected @endif>中信全付通</option>
            <option value="QR4HXF_恒丰恒星付" @if($account_bank_flag=='QR4HXF_恒丰恒星付') selected @endif>恒丰恒星付</option>
            <option value="QR4JLP_集利付" @if($account_bank_flag=='QR4JLP_集利付') selected @endif>集利付</option>
            <option value="QR4ZGM_掌柜买单" @if($account_bank_flag=='QR4ZGM_掌柜买单') selected @endif>掌柜买单</option>
            <option value="QR4ZBP_中百支付" @if($account_bank_flag=='QR4ZBP_中百支付') selected @endif>中百支付</option>
            <option value="QR4CEB_光大宝付通" @if($account_bank_flag=='QR4CEB_光大宝付通') selected @endif>光大宝付通</option>
            <option value="QR4MP_喵付" @if($account_bank_flag=='QR4MP_喵付') selected @endif>喵付</option>
            <option value="QR4WXP_微信买单" @if($account_bank_flag=='QR4WXP_微信买单') selected @endif>微信买单</option>
        </select>
    </div>
</div>

<div class="form-group payment_row transfer third_offline">
    <label for="tag" class="col-md-3 control-label">银行卡开户姓名</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="account_full_name" value="{{ $account_full_name }}" placeholder="张三" maxlength="64">
    </div>
</div>

<div class="form-group payment_row transfer third_offline">
    <label for="tag" class="col-md-3 control-label">银行卡开户地址</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="account_address" value="{{ $account_address }}" placeholder="上海市" maxlength="100">
    </div>
</div>

<div class="form-group payment_row netbank third_offline">
    <label for="tag" class="col-md-3 control-label">API网关</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="api_gateway" value="{{ $api_gateway }}" placeholder="如果第三方需要指定的API接口地址，则填写到此处，没有则留空">
    </div>
</div>

<div class="form-group payment_row qrcode_offline offline_scan">
    <label for="tag" class="col-md-3 control-label">扫码方式</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="qrcode_type" value="{{ $qrcode_type }}" placeholder="支持的扫码方式：微信、支付宝、银联等，用/分隔，示例: 微信/支付宝">
    </div>
</div>

<div class="form-group payment_row qrcode_offline offline_scan">
    <label for="tag" class="col-md-3 control-label">扫码链接</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="qrcode_url" value="{{ $qrcode_url }}" placeholder="此处填写需要生成支付二维码的链接地址">
    </div>
</div>

@if(is_array($agent_account))
    @foreach( $agent_account as $key => $agent )
        <div class="form-group payment_row agent_weixin agent_qq agent_alipay agent_chat">
            <label for="tag" class="col-md-3 control-label">代理账户{{ $key+1 }}</label>
            <div class="col-md-6">
                <input type="text" class="form-control" name="agent_account[]" value="{{ $agent }}" placeholder="">
            </div>
        </div>
    @endforeach
@else
    @for ($i = 1; $i < 10; $i++)
        <div class="form-group payment_row agent_weixin agent_qq agent_alipay agent_chat">
            <label for="tag" class="col-md-3 control-label">代理账户{{ $i }}</label>
            <div class="col-md-6">
                <input type="text" class="form-control" name="agent_account[]" value="" placeholder="">
            </div>
        </div>
    @endfor
@endif

<div class="form-group payment_row agent_weixin agent_qq agent_alipay agent_chat">
    <label for="tag" class="col-md-3 control-label"></label>
    <div class="col-md-6" style="text-align: right;">
        <a href="javascript:;" onClick="AddNewAgentAccount(this)">+新增代理充值账户</a>
    </div>
</div>
@php
    $agent_payments = is_array($agent_payments)?$agent_payments:[];
@endphp
<div class="form-group payment_row agent_chat">
    <label class="col-md-3 control-label checkbox-inline text-bold">代理支付方式</label>
    <div class="col-md-6 checkbox">
        <label><input type="checkbox" class="choose_all">全选</label>&nbsp;&nbsp;&nbsp;&nbsp;
        @foreach($payments as $item)
            <label><input @if(in_array($item[1],$agent_payments)) checked @endif type="checkbox" name="agent_payments[]" value="{{ $item[1] }}">{{ $item[0] }}</label>&nbsp;&nbsp;&nbsp;&nbsp;
        @endforeach
    </div>
</div>

<div class="form-group payment_row agent_weixin agent_qq agent_alipay agent_chat">
    <label for="tag" class="col-md-3 control-label">举报专线微信</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="informants_wechat" value="{{ $informants_wechat }}" placeholder="">
    </div>
</div>

<div class="form-group payment_row agent_weixin agent_qq agent_alipay agent_chat">
    <label for="tag" class="col-md-3 control-label">举报专线QQ</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="informants_qq" value="{{ $informants_qq }}" placeholder="">
    </div>
</div>

<div class="form-group payment_row agent_weixin agent_qq agent_alipay agent_chat">
    <label for="tag" class="col-md-3 control-label">举报奖金</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="informants_bonus" value="{{ $informants_bonus }}" placeholder="">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">充值金额</label>
    <div class="col-md-6 form-inline">
        最小：<input type="text" class="form-control" name="amount_min" value="{{ $amount_min }}" placeholder="最小" maxlength="64"> -
        最大：<input type="text" class="form-control" name="amount_max" value="{{ $amount_max }}" placeholder="最大" maxlength="64">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">金额类型</label>
    <div class="col-md-6 checkbox">
        <label style="padding: 10px 0px 10px 20px;">
            <input type="checkbox" value="1" name="amount_decimal" @php if(!empty($amount_decimal)) echo 'checked'; @endphp>自动追加小数金额
        </label>
        <div>
            <label>
                <input type="checkbox" value="1" name="amount_fixed" @php if(!empty($amount_fixed_list)) echo 'checked'; @endphp>固定金额列表
            </label>
            <input type="text" class="form-control" name="amount_fixed_list" value="@isset($amount_fixed_list){{ $amount_fixed_list }}@endisset" placeholder="用[,]号分隔,例：100,200,300,500,1000" style="width:50%;display: inline;">
        </div>

    </div>
</div>

<hr>
<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">启用的总代</label>
    <div class="col-md-6 checkbox">
        <label><input type="checkbox" class="choose_all">全选</label>&nbsp;&nbsp;&nbsp;&nbsp;
        @foreach($top_users as $item)
            <label><input type="checkbox" name="top_user_ids[]" value="{{ $item->id }}" {{ $item->checked ? 'checked':'' }}>{{ $item->username }}</label>&nbsp;&nbsp;&nbsp;&nbsp;
        @endforeach
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">层级</label>
    <div class="col-md-6 checkbox">
        <label><input type="checkbox" class="choose_all">全选</label>&nbsp;&nbsp;&nbsp;&nbsp;
        @foreach($user_level as $level)
            <label><input type="checkbox" name="user_level_ids[]" value="{{ $level->id }}" {{ $level->checked ? 'checked':'' }}>{{ $level->name }}[{{ $level->id }}]</label>&nbsp;&nbsp;&nbsp;&nbsp;
        @endforeach
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
    <label class="col-md-3 control-label">用户手续费界定线</label>
    <div class="col-md-6">
        <input type="number" class="form-control" step="0.1" name="user_fee_line" value="{{ $user_fee_line }}" placeholder="整数">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">用户低于界定线的手续费计算</label>
    <div class="col-md-6">
        <div class="form-inline">
            <select name="user_fee_down_type" class="form-control" style="width: 100px;">
                <option value="0" @if ($user_fee_down_type == 0) selected @endif>百分比</option>
                <option value="1" @if ($user_fee_down_type == 1) selected @endif>固定值</option>
            </select>
            <input type="number" class="form-control" step="0.1" name="user_fee_down_value" value="{{ $user_fee_down_value }}">
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">用户高于界定线的手续费计算</label>
    <div class="col-md-6">
        <div class="form-inline">
            <select name="user_fee_up_type" class="form-control" style="width: 100px;">
                <option value="0" @if ($user_fee_up_type == 0) selected @endif>百分比</option>
                <option value="1" @if ($user_fee_up_type == 1) selected @endif>固定值</option>
            </select>
            <input type="number" class="form-control" step="0.1" name="user_fee_up_value" value="{{ $user_fee_up_value }}">
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
        <input type="number" class="form-control" step="0.1" name="platform_fee_line" value="{{ $platform_fee_line }}" placeholder="整数">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">平台低于界定线的手续费计算</label>
    <div class="col-md-6">
        <div class="form-inline">
            <select name="platform_fee_down_type" class="form-control" style="width: 100px;">
                <option value="0" @if ($platform_fee_down_type == 0) selected @endif>百分比</option>
                <option value="1" @if ($platform_fee_down_type == 1) selected @endif>固定值</option>
            </select>
            <input type="number" class="form-control" step="0.1" name="platform_fee_down_value" value="{{ $platform_fee_down_value }}">
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">平台高于界定线的手续费计算</label>
    <div class="col-md-6">
        <div class="form-inline">
            <select name="platform_fee_up_type" class="form-control" style="width: 100px;">
                <option value="0" @if ($platform_fee_up_type == 0) selected @endif>百分比</option>
                <option value="1" @if ($platform_fee_up_type == 1) selected @endif>固定值</option>
            </select>
            <input type="number" class="form-control" step="0.1" name="platform_fee_up_value" value="{{ $platform_fee_up_value }}">
        </div>
    </div>
</div>
<hr>

<div class="form-group">
    <label for="date_limit" class="col-md-3 control-label">用户注册时间限制</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="register_time_limit" value="{{ $register_time_limit }}" placeholder="单位：小时">
        <p class="help-block">范围：0-32767 0:表示不做限制</p>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">以往充值次数限制</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="recharge_times_limit" value="{{ $recharge_times_limit }}">
        <p class="help-block">范围：0-32767 0:表示不做限制</p>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">以往累计最少充值金额</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="recharge_amount_total_limit" value="{{ $recharge_amount_total_limit }}">
        <p class="help-block">范围：0-2147483647 0:表示不做限制</p>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">每天无效申请次数限制</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="invalid_times_limit" value="{{ $invalid_times_limit }}">
        <p class="help-block">范围：0-32767 0:表示不做限制</p>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">10分钟无效申请次数限制</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="invalid_times_lock" value="{{ $invalid_times_lock }}">
        <p class="help-block">范围：0-100 0:表示不做限制</p>
    </div>
</div>

@section('css')
    <style>
        hr {
            border: 1px dotted #cccccc;
        }

        .payment_row {
            display: none
        }
    </style>
@stop

@section('js')
    <script>

        var methods = {!! $methods_json !!};

        function in_array(needle, data_array) {
            for (var i = 0; i < data_array.length; i++) {
                if (needle == data_array[i]) {
                    return true;
                }
            }
            return false;
        }

        function changeMethodOptions(payment_category_id, old_payment_method_id) {
            var html_options = '<option value="0">请选择类型</option>';
            var selected = '';
            var category_methods = new Array();
            @foreach ($categories as $item)
                category_methods[{{ $item->id }}] = {!! $item->methods !!};
            @endforeach
            if (payment_category_id === '') {
                return;
            }
            if (typeof category_methods[payment_category_id] != 'undefined') {
                for (var i = 0; i < methods.length; i++) {
                    if (in_array(methods[i].ident, category_methods[payment_category_id])) {
                        if (typeof old_payment_method_id != 'undefined' && methods[i].id == old_payment_method_id) {
                            selected = 'selected';
                        } else {
                            selected = '';
                        }
                        html_options += '<option value="' + methods[i].id + '"' + selected + '>' + methods[i].name + '</option>';
                    }
                }
            }
            $("#payment_method_id").html(html_options);
        }

        function methodControl() {
            var payment_method_id = $("#payment_method_id").val();

            var payment_method_ident = '';
            for (i = 0; i < methods.length; i++) {
                if (methods[i].id == payment_method_id) {
                    payment_method_ident = methods[i].ident;
                    break;
                }
            }
            $('.payment_row').css('display', 'none');

            if (payment_method_ident == 'transfer' ||
                payment_method_ident == 'qrcode_offline' ||
                payment_method_ident == 'netbank' ||
                payment_method_ident == 'third_offline' ||
                payment_method_ident == 'agent_weixin' ||
                payment_method_ident == 'agent_qq' ||
                payment_method_ident == 'agent_alipay' ||
                payment_method_ident == 'agent_chat' ||
                payment_method_ident == 'offline_scan'
            ) {
                $('.' + payment_method_ident).css('display', 'block');
            } else {
                $('.netbank').css({'display': 'block'});
                $('#item_banks').css('display', 'none');
            }
        }

        $(document).ready(function () {
            $("#payment_category_id").bind("change", function () {
                var payment_category_id = $(this).val();
                changeMethodOptions(payment_category_id, '{{ $payment_method_id }}');
            });
            changeMethodOptions($("#payment_category_id").val(), '{{ $payment_method_id }}');

            $("#payment_method_id").bind("change", function () {
                methodControl();
            })
            methodControl();
        })

        //全选
        $('.choose_all').click(function () {
            var checked = $(this).prop('checked');
            $(this).parent().siblings().each(function () {
                $(this).children('input').prop('checked', checked);
            });
        });

        $('input[name=amount_fixed_list]').focus(function () {
            $("input[name=amount_fixed]").prop('checked', true);
        });
        $('input[name=amount_fixed_list]').focusout(function () {
            $(this).val($(this).val().replace(/\s+/g, ""));
            if ($(this).val() == '') $("input[name=amount_fixed]").prop('checked', false);
        });

        // 新增代理充值账户
        function AddNewAgentAccount(obj) {
            var account_length = $('input[name="agent_account[]"]').size();
            var html = '<div class="form-group payment_row agent_weixin agent_qq agent_alipay agent_chat" style="display:block;">' +
                '<label for="tag" class="col-md-3 control-label">代理账户' + account_length + '</label>' +
                '<div class="col-md-6">' +
                '<input type="text" class="form-control" name="agent_account[]" value="" placeholder="">' +
                '</div>' +
                '</div>';

            $(obj).parents('.form-group').before(html);
        }
    </script>
@stop
