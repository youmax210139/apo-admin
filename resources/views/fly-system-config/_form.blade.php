<div class="form-group">
    <label class="col-md-3 control-label">英文标识</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="ident" value="{{ $ident }}" maxlength="64" autocomplete="off" placeholder="英文标识不可重复">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">中文名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" value="{{ $name }}" maxlength="64" autocomplete="off" placeholder="中文名称不可重复">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">彩种标识</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="lottery_idents" value="{{ $lottery_idents }}" maxlength="256" autocomplete="off" placeholder="例如：cqssc,xjssc">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">推送域名</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="domain" value="{{ $domain }}" maxlength="128" autocomplete="off" placeholder="例如：https://www.xxx.com(需要加http头)">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">参数配置</label>
    <div class="col-md-6">
        <textarea class="form-control" id="config" rows="2" name="config">{{$config}}</textarea>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">状态</label>
    <div class="col-md-6">
        <div class="col-md-6 form-control app_wauto_bnone">
            <label class="radio-inline">
                <input type="radio" name="status" value="1" @if ($status==1)checked @endif />
                启用
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" value="0" @if ($status==0)checked @endif />
                禁用
            </label>
        </div>
    </div>
</div>
