<div class="form-group">
    <label for="name" class="col-md-3 control-label">中文名称</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="name" id="name" value="{{ $name }}" maxlength="32" />
    </div>
</div>
<div class="form-group">
    <label for="name" class="col-md-3 control-label">英文标识</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="ident" id="ident" value="{{ $ident }}" maxlength="32" placeholder="唯一标识" />
    </div>
</div>
<hr>
<div class="form-group">
    <label for="request_status" class="col-md-3 control-label">请求状态</label>
    <div class="col-md-5">
        <div class="radio">
            <label class="radio-inline"><input type="radio" name="request_status" value="1" @if ( $request_status==1 )checked="checked"@endif />启用</label>
            <label class="radio-inline"><input type="radio" name="request_status" value="0" @if ( $request_status==0 )checked="checked"@endif />禁用</label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="request_key" class="col-md-3 control-label">请求秘钥</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="request_key" id="request_key" value="{{ $request_key }}" maxlength="32" placeholder="32个字符" />
        <button type="button" onclick="generateKey('request_key')" class="btn">生成密钥</button><span class="">32个字符</span>
</div>
</div>
<div class="form-group">
    <label for="request_ips" class="col-md-3 control-label">请求IP</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="request_ips" id="push_url" value="{{ $request_ips }}" maxlength="256" />
        <span class="">多个IP用英文逗号,分隔。不限制IP填写：unlimited</span>
    </div>
</div>
<hr>
<div class="form-group">
    <label for="push_status" class="col-md-3 control-label">推送状态</label>
    <div class="col-md-5">
        <div class="radio">
            <label class="radio-inline"><input type="radio" name="push_status" value="1" @if ( $push_status==1 )checked="checked"@endif />启用</label>
            <label class="radio-inline"><input type="radio" name="push_status" value="0" @if ( $push_status==0 )checked="checked"@endif />禁用</label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="push_key" class="col-md-3 control-label">谁送秘钥</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="push_key" id="push_key" value="{{ $push_key }}" maxlength="32" placeholder="32个字符" />
        <button type="button" onclick="generateKey('push_key')" class="btn">生成密钥</button><span class="">32个字符</span>
    </div>
</div>
<div class="form-group">
    <label for="push_url" class="col-md-3 control-label">推送地址</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="push_url" id="push_url" value="{{ $push_url }}" maxlength="256" placeholder="http://www.abc.com" />
    </div>
</div>