<div class="form-group">
    <label for="name" class="col-md-3 control-label">银行名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" value="{{ $name }}" maxlength="64" placeholder="输入银行名称">

    </div>
</div>

<div class="form-group">
    <label for="ident" class="col-md-3 control-label">唯一标示</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="ident" value="{{ $ident }}" placeholder="英文不可重复" onBlur="this.value=this.value.toUpperCase()">
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

@section('js')

@stop
