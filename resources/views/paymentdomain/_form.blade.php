<div class="form-group">
    <label for="tag" class="col-md-3 control-label">域名</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="domain" value="{{ $domain }}" placeholder="例如：https://pay.xxx.com(需要加http头)" maxlength="128" >
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">绑定的渠道</label>
    <div class="col-md-6">
        <select name="payment_category_id" class="form-control">
            @if ($payment_category_id === 0)
                <option value="0" selected>不限渠道</option>
            @else
                <option value="0">不限渠道</option>
            @endif
            @foreach ($categories as $item)
                @if ($item->id == $payment_category_id)
                    <option value="{{ $item->id }}" selected>{{ $item->name }}</option>
                @else
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endif
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">绑定的服务器</label>
    <div class="col-md-6">
        <select name="intermediate_servers_id" class="form-control">
            <option value="">请选择服务器</option>
        @foreach ($servers as $item)
            @if ($item->id == $intermediate_servers_id)
                    <option value="{{ $item->id }}" selected>{{ $item->name }}</option>
            @else
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endif
        @endforeach
        </select>
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
    <label for="tag" class="col-md-3 control-label">备注</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="remark" value="{{ $remark }}" placeholder="" maxlength="128" >
    </div>
</div>