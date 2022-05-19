<div class="form-group">
    @if($parent_id==0)
        <input type="hidden" class="form-control" name="parent_id" id="tag" value="{{ $parent_id }}">
    @else
        <label for="tag" class="col-md-3 control-label">配置分组</label>
        <div class="col-md-6">
            <select class="form-control" name="parent_id">
                {{--<option value="0">选择分组</option>--}}
                @foreach($parent_config as $v)
                    <option value="{{$v->id}}" @if($v->id == $parent_id) selected="" @endif>{{$v->title}}</option>
                @endforeach
            </select>
        </div>
    @endif
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">配置标题</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="title" value="{{ $title }}" maxlength="64" autocomplete="off">
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">配置名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="key" value="{{ $key }}" maxlength="64" autocomplete="off">
    </div>
</div>
@if($parent_id>0)
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">显示类型</label>
    <div class="col-md-6">
        <select name="input_type" class="form-control">
            <option value="0" {{$input_type==0?'selected':''}}>输入框</option>
            <option value="1" {{$input_type==1?'selected':''}}>下拉框</option>
            <option value="2" {{$input_type==2?'selected':''}}>复选框</option>
        </select>
    </div>
</div>
<div class="form-group" id="value_type" style="display: {{$input_type>0?'none':'block'}}">
    <label for="tag" class="col-md-3 control-label">值类型</label>
    <div class="col-md-6">
        <select name="value_type" class="form-control">
            <option value="0" {{$value_type==0?'selected':''}}>字符</option>
            <option value="1" {{$value_type==1?'selected':''}}>数字</option>
            <option value="2" {{$value_type==2?'selected':''}}>正数</option>
        </select>
    </div>
</div>
<div class="form-group" id="input_options" style="display: {{$input_type>0?'block':'none'}}">
    <label for="tag" class="col-md-3 control-label">输入选项</label>
    <div class="col-md-6">
        <textarea rows="3" placeholder="例如：0|关闭,1|开启" class="form-control" name="input_option">{{$input_option}}</textarea>
    </div>
</div>
@endif
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">配置值</label>
    <div class="col-md-6">
        @if(empty($title))
            <input type="text" class="form-control" name="value" value="{{ $value }}" maxlength="256" autocomplete="off">
        @else
            @if($input_type==1)
                @php
                    $input_option = explode(',',$input_option);
                @endphp
                <select name="value" class="form-control">
                    @foreach($input_option as $option)
                        @php
                            list($val,$name) = explode("|",$option);
                        @endphp
                        <option value="{{$val}}" {{$value==$val?'selected':''}}>{{$val}}-{{$name}}</option>
                    @endforeach
                </select>
            @elseif($input_type==2)
                @php
                    $input_option = explode(',',$input_option);
               $value = explode(',',$value);
                @endphp
                <select name="value[]" class="form-control" multiple>
                    @foreach($input_option as $option)
                        @php
                        list($val,$name) = explode("|",$option);
                        @endphp
                        <option value="{{$val}}" {{in_array($val,$value)?'selected':''}}>{{$val}}-{{$name}}</option>
                    @endforeach
                </select>
            @else
                @if(mb_strlen($value)>20)
                    <textarea name="value" class="form-control" rows="4">{{ $value }}</textarea>
                    @else
                <input type="text" class="form-control" name="value" value="{{ $value }}" maxlength="256" autocomplete="off">
                    @endif
            @endif
        @endif
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">配置描述</label>
    <div class="col-md-6">
        <textarea name="description" class="form-control" rows="3">{{ $description }}</textarea>
    </div>
</div>
@section('js')
    <script>
        $(function () {
            $("select[name='input_type']").change(function () {
                if($(this).val()==0){
                    $("#input_options").hide();
                    $("#value_type").show();
                }else{
                    $("#input_options").show();
                    $("#value_type").hide();
                }
            });
        });
    </script>

@stop