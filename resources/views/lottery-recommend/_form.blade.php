<div class="form-group">
    <label for="name" class="col-md-3 control-label">彩种</label>
    <div class="col-md-6">
        <select name="lottery_id" class="form-control">
            @foreach($lottery as $v)
                <option value="{{ $v->id }}" @if($v->id==$lottery_id) selected @endif>{{ $v->name }}</option>
            @endforeach
        </select>
    </div>
</div>

@foreach(range(1,4) as $idx)
    <div class="form-group">
        <label for="ident" class="col-md-3 control-label">推荐{{ $idx }}</label>
        <div class="col-md-6">
            <select name="lottery_ident{{$idx}}" class="form-control">
                <option value="" @if($v->ident == ${'lottery_ident'.$idx}) selected @endif>
                    请选择
                </option>
                @foreach($lottery as $v)
                    <option value="{{ $v->ident }}" @if($v->ident == ${'lottery_ident'.$idx}) selected @endif>
                        {{ $v->ident . '|' . $v->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
@endforeach

<div class="form-group">
    <label for="rate" class="col-md-3 control-label">提示文案</label>
    <div class="col-md-6">
        <textarea class="form-control" placeholder="提示文案" rows="3" name="tip">{{ $tip }}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="interval_minutes" class="col-md-3 control-label">重复弹出间隔时间(分)</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="interval_minutes" value="{{ $interval_minutes }}" maxlength="3"/>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label checkbox-inline text-bold">是否启用</label>
    <div class="col-md-6">
        <div class="radio">
            <label class="radio-inline">
                <input type="radio" name="status" value="0" @if(!$status) checked @endif>
                否
            </label>

            <label class="radio-inline">
                <input type="radio" name="status" value="1" @if($status) checked @endif>
                是
            </label>
        </div>
    </div>
</div>
