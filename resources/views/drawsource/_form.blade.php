<div class="form-group">
    <label for="lottery_id" class="col-md-3 control-label">所属彩种</label>
    <div class="col-md-5">
        <select name="lottery_id" id="lottery_id" class="form-control" />
            <option value="">选择彩种</option>
             @foreach($lottery as $v)
            <option value="{{ $v->id }}"  @if ( $v->id == $lottery_id )selected="selected"@endif>{{ $v->name }} 【{{ $v->ident }}】</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-md-3 control-label">号源名称</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="name" id="name" value="{{ $name }}" maxlength="30" />
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-md-3 control-label">号源标识</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="ident" id="ident" value="{{ $ident }}" maxlength="32" />
    </div>
</div>
<div class="form-group">
    <label for="url" class="col-md-3 control-label">号源API地址</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="url" id="url" value="{{ $url }}" maxlength="200" />
    </div>
</div>

<div class="form-group">
    <label for="rank" class="col-md-3 control-label">权重</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="rank" id="rank" value="{{ $rank }}" maxlength="3" />
    </div>
</div>


<div class="form-group">
    <label for="status" class="col-md-3 control-label">是否启用</label>
    <div class="col-md-5">
        <div class="radio">
            <label class="radio-inline"><input type="radio" id="status" name="status" value="1" @if ( $status==1 )checked="checked"@endif />是</label>
            <label class="radio-inline"><input type="radio" name="status" value="0" @if ( $status==0 )checked="checked"@endif />否</label>
        </div>
    </div>
</div>