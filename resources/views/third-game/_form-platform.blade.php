

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">英文标识</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="ident" value="{{ $ident }}" maxlength="16" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">中文名称</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="name" value="{{ $name }}" maxlength="32" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label text-right app_label_pt9px">状态</label>
    <div class="col-md-5">
        <div class="col-md-6 form-control app_wauto_bnone">
            <label class="radio-inline"><input type="radio" name="status" value="0" @if ( $status==0 )checked="checked"@endif />启用</label>
            <label class="radio-inline"><input type="radio" name="status" value="1" @if ( $status==1 )checked="checked"@endif />禁用</label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label text-right app_label_pt9px">返点类型</label>
    <div class="col-md-5">
        <div class="col-md-6 form-control app_wauto_bnone">
            <select name="rebate_type">
                <option value="0" @if ( $rebate_type==0 )selected="selected"@endif>使用第三方返点</option>
                <option value="1" @if ( $rebate_type==1 )selected="selected"@endif>使用彩票返点</option>
                <option value="2" @if ( $rebate_type==2 )selected="selected"@endif>不返点</option>
            </select>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">显示顺序</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="sort" value="{{ $sort }}" maxlength="3" >
    </div>
</div>