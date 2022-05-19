<div class="form-group">
    <label for="tag" class="col-md-3 control-label">英文标识</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="ident" id="tag" value="{{ $ident }}" maxlength="32" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">中文名称</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="name" id="tag" value="{{ $name }}" maxlength="16" />
    </div>
</div>
<div class="form-group">
<label for="special" class="col-md-3 control-label checkbox-inline text-bold">可用</label>
<div class="col-md-7">
    <div class="radio">
        <label class="radio-inline"><input type="radio" name="enabled" value="1" @if ($enabled == 1 ) checked="checked"@endif />可用</label>
        <label class="radio-inline"><input type="radio" name="enabled" value="0" @if ( $enabled == 0 ) checked="checked"@endif />禁用</label>
    </div>
</div>
</div>