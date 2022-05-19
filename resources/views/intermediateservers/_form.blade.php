<div class="form-group">
    <label for="tag" class="col-md-3 control-label">服务器名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" value="{{ $name }}" maxlength="64">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">服务器IP</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="ip" value="{{ $ip }}" placeholder="" maxlength="15" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">同步数据URL</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="domain" value="{{ $domain }}" placeholder="http://www.xxx.com 要加http开头" maxlength="128" >
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