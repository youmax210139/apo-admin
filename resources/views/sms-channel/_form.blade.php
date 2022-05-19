<div class="form-group">
    <label for="tag" class="col-md-3 control-label">中文名称</label>
    <div class="col-md-5">
        <input required type="text" class="form-control" name="name" id="name" value="{{ $name }}" maxlength="16"/>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">所属渠道</label>
    <div class="col-md-5">
        <select class="form-control" name="category_id">
            @foreach($categories as $v)
                <option value="{{$v->id}}" @if($category_id==$v->id) selected @endif>{{$v->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">账号/标识</label>
    <div class="col-md-5">
        <input required type="text" class="form-control" name="account" id="account" value="{{ $account }}" maxlength="32"/>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">秘钥</label>
    <div class="col-md-5">
        <input required type="text" class="form-control" name="key" id="key" value="{{ $key }}" maxlength="255" placeholder="必填"/>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">秘钥2</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="key2" id="key2" value="{{ $key2 }}" maxlength="255" placeholder="没有就留空"/>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">短信签名</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="signature" id="signature" value="{{ $signature }}" maxlength="32" placeholder="内容包含【】"/>
    </div>
</div>
<div class="form-group">
    <label for="special" class="col-md-3 control-label checkbox-inline text-bold">可用</label>
    <div class="col-md-7">
        <div class="radio">
            <label class="radio-inline"><input type="radio" name="enabled" value="1"
                                               @if ($enabled == 1 ) checked="checked"@endif />可用</label>
            <label class="radio-inline"><input type="radio" name="enabled" value="0"
                                               @if ( $enabled == 0 ) checked="checked"@endif />禁用</label>
        </div>
    </div>
</div>
