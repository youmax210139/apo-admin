<div class="form-group">
    <label for="tag" class="col-md-3 control-label">渠道中文名称</label>
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
    <label for="tag" class="col-md-3 control-label">请求接口地址</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="request_url" value="{{ $request_url }}" maxlength="128" >
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">查询接口地址</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="verify_url" value="{{ $verify_url }}" maxlength="128" >
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">第三方回调地址</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="notify_url" value="{{ $notify_url }}" maxlength="64" >
    </div>
</div>

<div class="form-group">
    <label for="banks" class="col-md-3 control-label">可提现银行（多选）</label>
    <div class="col-md-6">
        <div style="max-height: 360px;overflow-y: auto">
        <div class="bank_list">  
            <label class="checkbox"><input type="checkbox" class="choose_all">全选</label>
            @foreach($bank_list as $v)
                <label class="checkbox">
                    @if(in_array($v->id,$banks))
                        <input name="banks[]" checked="" type="checkbox" value="{{$v->id}}">
                    @else
                        <input name="banks[]" type="checkbox" value="{{$v->id}}">
                    @endif
                    {{$v->name}}
                </label>
            @endforeach
            </div>
        </div>
    </div>
</div>
@section('css')
<style>
    .checkbox {
        margin-left:20px;
        font-weight: normal;
    }
</style>
@stop
@section('js')
<script>
    //全选
    $('.choose_all').click(function(){
        var checked = $(this).prop('checked');
        $(this).parent().siblings().each(function(){
            $(this).children('input').prop('checked',checked);
        });
    });
</script>
@stop