<div class="form-group">
    <label for="name" class="col-md-3 control-label">银行名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" value="{{ $name }}" maxlength="64" placeholder="输入银行名称" autocomplete="off">
    </div>
</div>

<div class="form-group">
    <label for="ident" class="col-md-3 control-label">唯一标识</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="ident" value="{{ $ident }}" placeholder="英文不可重复" autocomplete="off" onBlur="this.value=this.value.toUpperCase()">
    </div>
</div>

<div class="form-group">
    <label for="currency" class="col-md-3 control-label">币别</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="currency" value="{{ $currency }}" placeholder="币别" autocomplete="off" maxlength="64">
    </div>
</div>

<div class="form-group">
    <label for="rate" class="col-md-3 control-label">汇率</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="rate" value="{{ $rate }}" placeholder="汇率" @if($api_fetch) disabled @endif>
    </div>
</div>

<div class="form-group">
    <label for="url" class="col-md-3 control-label">汇率API</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="url" value="{{ $url }}" placeholder="汇率API URL" maxlength="200">
    </div>
</div>

<div class="form-group">
    <label for="start_time" class="col-md-3 control-label">提现开始:</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="start_time" value="{{ $start_time }}" placeholder="00:00" autocomplete="off">
    </div>
</div>

<div class="form-group">
    <label for="end_time" class="col-md-3 control-label">提现结束:</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="end_time" value="{{ $end_time }}" placeholder="00:00" autocomplete="off">
    </div>
</div>

<div class="form-group">
    <label for="amount_min" class="col-md-3 control-label">提现最小:</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="amount_min" value="{{ $amount_min }}" placeholder="0" autocomplete="off">
    </div>
</div>

<div class="form-group">
    <label for="amount_max" class="col-md-3 control-label">提现最大:</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="amount_max" value="{{ $amount_max }}" placeholder="0" autocomplete="off">
    </div>
</div>

<div class="form-group">
    <label for="channel_idents" class="col-md-3 control-label">使用渠道</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="channel_idents" value="{{ $channel_idents }}" placeholder="使用渠道,渠道英文标示,如有多个用”,”隔开">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">是否允许提现</label>
    <div class="col-md-6">
        <div class="radio">
            <label class="radio-inline">
                <input type="radio" name="withdraw" value="0" @if(!$withdraw) checked @endif>
                否
            </label>

            <label class="radio-inline">
                <input type="radio" name="withdraw" value="1" @if($withdraw) checked @endif>
                是
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">是否禁用</label>
    <div class="col-md-6">
        <div class="radio">
            <label class="radio-inline">
                <input type="radio" name="disabled" value="0" @if(!$disabled) checked @endif>
                否
            </label>

            <label class="radio-inline">
                <input type="radio" name="disabled" value="1" @if($disabled) checked @endif>
                是
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">汇率模式</label>
    <div class="col-md-6">
        <div class="radio">
            <label class="radio-inline">
                <input type="radio" name="api_fetch" value="0" @if(!$api_fetch) checked @endif>
                人工汇率
            </label>

            <label class="radio-inline">
                <input type="radio" name="api_fetch" value="1" @if($api_fetch==1) checked @endif>
                RBCX汇率
            </label>

            <label class="radio-inline">
                <input type="radio" name="api_fetch" value="2" @if($api_fetch==2) checked @endif>
                OTC365汇率
            </label>
        </div>
    </div>
</div>
@section('js')
    <script>
        $('input[type=radio][name=api_fetch]').change(function () {
            switch (this.value) {
                case"0":
                    $('input[type=text][name=rate]').prop('disabled', false);
                    $('input[type=text][name=url]').val('');
                    break;
                case"1":
                    $('input[type=text][name=rate]').val('');
                    $('input[type=text][name=rate]').prop('disabled', true);
                    $('input[type=text][name=url]').val('https://3d.rbcx.io/tick');
                    break;
                case"2":
                    $('input[type=text][name=rate]').val('');
                    $('input[type=text][name=rate]').prop('disabled', true);
                    $('input[type=text][name=url]').val('https://open-v2.otc365.com/cola/quotePriceBusiness/priceConfig/getPrice?coinType=cnyusdt');
                    break;
            }
        });
    </script>
@stop
