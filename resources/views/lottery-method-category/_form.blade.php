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
    <label for="tag" class="col-md-3 control-label">下降点数</label>
    <div class="col-md-5">
        <input type="number" class="form-control" name="drop_point" id="tag" value="{{ $drop_point }}" maxlength="3" />
    </div>
</div>