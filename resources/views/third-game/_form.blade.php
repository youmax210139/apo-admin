
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">所属游戏平台</label>
    <div class="col-md-5">
		<select name="third_game_platform_id">
			<option value="">选择所属游戏平台</option>
			@foreach ($platforms as $platform)
				<option value="{{ $platform->id }}" @if($platform->id == $third_game_platform_id) selected="selected" @endif>{{ $platform->name }}</option>
			@endforeach
		</select>
    </div>
</div>
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
    <label for="tag" class="col-md-3 control-label">商户号</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="merchant" value="{{ $merchant }}" maxlength="50" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">商户密钥</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="merchant_key" value="{{ $merchant_key }}" maxlength="100" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">商户测试号</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="merchant_test" value="{{ $merchant_test }}" maxlength="50" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">商户测试密钥</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="merchant_key_test" value="{{ $merchant_key_test }}" maxlength="100" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">正式环境api基础地址</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="api_base" value="{{ $api_base }}" maxlength="255" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">测试环境api基础地址</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="api_base_test" value="{{ $api_base_test }}" maxlength="255" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">最后抓取时间</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="last_fetch_time" value="{{ $last_fetch_time }}" maxlength="255" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label text-right app_label_pt9px">是否允许使用</label>
    <div class="col-md-5">
        <div class="col-md-6 form-control app_wauto_bnone">
            <label class="radio-inline"><input type="radio" name="status" value="0" @if ( $status==0 )checked="checked"@endif />是</label>
            <label class="radio-inline"><input type="radio" name="status" value="1" @if ( $status==1 )checked="checked"@endif />否</label>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label text-right app_label_pt9px">是否允许登入</label>
    <div class="col-md-5">
        <div class="col-md-6 form-control app_wauto_bnone">
            <label class="radio-inline"><input type="radio" name="login_status" value="0" @if ( $login_status==0 )checked="checked"@endif />是</label>
            <label class="radio-inline"><input type="radio" name="login_status" value="1" @if ( $login_status==1 )checked="checked"@endif />否</label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label text-right app_label_pt9px">是否允许转帐</label>
    <div class="col-md-5">
        <div class="col-md-6 form-control app_wauto_bnone">
            <label class="radio-inline"><input type="radio" name="transfer_status" value="0" @if ( $transfer_status==0 )checked="checked"@endif />是</label>
            <label class="radio-inline"><input type="radio" name="transfer_status" value="1" @if ( $transfer_status==1 )checked="checked"@endif />否</label>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="special" class="col-md-3 control-label checkbox-inline text-bold">禁止访问用户组</label>
    <div class="col-md-7">
        <div class="checkbox">
            <label class="checkbox-inline">
                <input type="checkbox" name="deny_user_group[]" value="1" @if ( in_array(1,$deny_user_group) ) checked="checked"@endif />正式组
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" name="deny_user_group[]" value="2" @if ( in_array(2,$deny_user_group) ) checked="checked"@endif />测试组
            </label>
            {{--<label class="checkbox-inline">
                <input type="checkbox" name="deny_user_group[]" value="3" @if ( in_array(3,$deny_user_group) ) checked="checked"@endif />试玩组
            </label>--}}
            <label class="checkbox-inline" style="color: #b0b0b0;">
                <input type="checkbox" name="deny_user_group[]" value="3" onclick="return false;" checked="checked" />试玩组
            </label>
        </div>
    </div>
</div>


<div class="form-group">
    <label for="tag" class="col-md-3 control-label text-right app_label_pt9px">转帐方式</label>
    <div class="col-md-5">
        <div class="col-md-6 form-control app_wauto_bnone">
            <label class="radio-inline"><input type="radio" name="transfer_type" value="0" @if ( $transfer_type==0 )checked="checked"@endif />通过平台转帐</label>
            <label class="radio-inline"><input type="radio" name="transfer_type" value="1" @if ( $transfer_type==1 )checked="checked"@endif />直接从平台扣款</label>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label"></label>
    <div class="col-md-5">
        <a href="javascript:void(0);" id="add_extend">添加扩展属性</a>
    </div>
</div>
<div  id="extend_tmp" style="display:none;">
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label"></label>
        <div class="col-md-6" style="margin-left: -15px;">
            <div class="col-md-3"><input type="text" class="form-control" name="extend_name[]" placeholder="中文名称" maxlength="32"></div>
            <div class="col-md-3"><input type="text" class="form-control" name="extend_ident[]" placeholder="唯一标识" maxlength="32" ></div>
            <div class="col-md-4"><input type="text" class="form-control" name="extend_value[]" placeholder="值" maxlength="250" ></div>
            <div class="col-md-2"><a href="javascript:void(0);" class="del_item">删除</a></div>
        </div>
    </div>
</div>
<div id="extend_list"></div>

@section('js')
<script>
    var items = new Array();

    @foreach($extend as $item)
        items.push({
            id:"{{ $item->id }}",
            name:"{{ $item->name }}",
            ident:"{{ $item->ident }}",
            value:"{{ $item->value }}"
        });
    @endforeach

    function addItem(item){
        var html = $('#extend_tmp').html();
        if(item){
            html = html.replace('name="extend_name[]"', 'name="extend_name[]"'+' value="'+item.name+'"');
            html = html.replace('name="extend_ident[]"', 'name="extend_ident[]"'+' value="'+item.ident+'"');
            html = html.replace('name="extend_value[]"', 'name="extend_value[]"'+' value="'+item.value+'"');
        }
        $('#extend_list').append(html);
    }
    for(var i=0; i<items.length; i++){
        addItem(items[i]);
    }

    $('#add_extend').bind('click',function () {
        addItem();
    });
    $('#extend_list').on('click','.del_item',function () {
        $(this).parent('div').parent('div').parent('div').remove();
    });
</script>
@stop
