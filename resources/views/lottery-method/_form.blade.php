<input type="hidden" name="parent_id" value="{{ $parent_id }}">
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">父级</label>
    <div class="col-md-5 control">
        @if ($parents)
            @foreach($parents as $p)
            {{ $loop->iteration }} 级：{{ $p->name }}（id：{{ $p->id }}）<br/>
            @endforeach
        @else
            无
        @endif
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">玩法 ID</label>
    <div class="col-md-5">
        <input type="text" class="form-control" id="tag" value="{{ $id }}" maxlength="8" placeholder="玩法 ID" @if ( $id ) disabled="disabled" @else name="id" @endif />
        8 位数字：第 1 位为玩法分类，第 2 - 3 位为 1 级，第 4 - 5 位为 2 级，第 6 - 8 位为 3 级
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">中文名称</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="name" id="tag" value="{{ $name }}" maxlength="32" />
    </div>
</div>
@if ( $this_level == 3 )
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">英文标识</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="ident" id="tag" value="{{ $ident }}" maxlength="32" />
    </div>
</div>
@endif
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">所属玩法分类</label>
    <div class="col-md-5">
        <select name="lottery_method_category_id" class="form-control" @if ( $lottery_method_category_id ) disabled="disabled" @endif>
            <option value="">选择玩法分类</option>
             @foreach($lottery_method_category as $v)
            <option value="{{ $v->id }}"  @if ( $v->id == $lottery_method_category_id )selected="selected"@endif>{{ $v->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">菜单排序</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="sort" id="tag" value="{{ $sort }}" maxlength="2" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">是否开启</label>
    <div class="col-md-5">
        <div class="radio">
            <label class="radio-inline"><input type="radio" name="status" value="1" @if ( $status==true )checked="checked"@endif />是</label>
            <label class="radio-inline"><input type="radio" name="status" value="0" @if ( $status==false )checked="checked"@endif />否</label>
        </div>
    </div>
</div>
@if ( $this_level == 3 )
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">封锁表名</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="lock_table_name" id="tag" value="{{ $lock_table_name }}" maxlength="100" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">封锁初始化函数</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="lock_init_function" id="tag" value="{{ $lock_init_function }}" maxlength="100" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">金额模式</label>
    <div class="col-md-5">
        <div class="checkbox" id="modes">
            @foreach($modes_list as $m)
            <label class="checkbox-inline"><input type="checkbox" name="modes[]" @if(in_array($m['id'],$modes)) checked="checked" @endif value="{{$m['id']}}">{{$m['name']}}</label>
                @endforeach
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">前端渲染参数</label>
    <div class="col-md-5">
        <textarea class="form-control" name="layout" id="tag">{{ $layout }}</textarea>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">开奖规则JSON</label>
    <div class="col-md-5">
        <textarea class="form-control" name="draw_rule" id="tag">{{ $draw_rule }}</textarea>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">奖金设置</label>
    <div class="col-md-5">
        <div id="prizes">
        </div>
        <a href="javascript:void(0)" onclick="addPrizes()"><i class="fa fa-plus"></i></a>
    </div>
</div>
@endif
@section('js')
<script type="text/javascript">
$(function(){
    //addModes();
    var prize_level_json = {!! empty($prize_level) ? '[]' : $prize_level !!};
    var prize_level_name_json = {!! empty($prize_level_name) ? '[]' : $prize_level_name !!};
    for(var i=0; i<prize_level_json.length; i++) {
        addPrizes(prize_level_json[i], prize_level_name_json[i]);
    }
});
function addPrizes(prize_level, prize_level_name) {
    var ob = $("#prizes");
    var len = ob.find("div").length;
    if(typeof(prize_level) == 'undefined') prize_level = '';
    if(typeof(prize_level_name) == 'undefined') prize_level_name = '';
    var prize_line = '<div>['+ len +']<br/>奖项名称：<input type="text" name="prize_level_name[]" value="'+ prize_level_name +'" style="width: 250px;"><br/>奖金：<input type="text" name="prize_level[]" value="'+ prize_level +'" style="width: 100px;"> <a href="javascript:void(0)" onclick="deletePrize(this)" title="删除该行"><span class="fa fa-minus"></span></a></div>';
    ob.append(prize_line);
}
function deletePrize(ob) {
    $(ob).parent().remove();
}
</script>
@stop