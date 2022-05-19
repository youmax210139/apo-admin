<div class="form-group">
    <label for="tag" class="col-md-3 control-label">通道中文名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" value="{{ $name }}" maxlength="64">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">通道英文唯一标识</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="ident" value="{{ $ident }}" maxlength="64" >
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">支付类型</label>
    <div class="col-md-6 checkbox">
        @foreach ($methods as $method)
            @if ($method['checked'] == false)
                <label><input type="checkbox" name="methods[]" value="{{ $method['ident'] }}"> {{ $method['name'] }}&nbsp;&nbsp;</label>
            @else
                <label><input type="checkbox" name="methods[]" value="{{ $method['ident'] }}" checked> {{ $method['name'] }}&nbsp;&nbsp;</label>
            @endif
        @endforeach
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