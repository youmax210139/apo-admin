<form class="form-horizontal" id="setconfig-form" role="form" method="POST"
      action="/config/set">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="id" value="{{ $id }}">
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">配置分组</label>
        <div class="col-md-6 control-label">
            <p class=" text-left">{{$parent_config->title}}</p>
        </div>
    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">配置标题</label>
        <div class="col-md-6 control-label text-left">
            <p class=" text-left">{{ $title }}</p>
        </div>
    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">配置名称</label>
        <div class="col-md-6 control-label text-left">
            <p class=" text-left">{{ $key }}</p>
        </div>
    </div>
    @if($input_type==0)
        <div class="form-group">
            <label for="tag" class="col-md-3 control-label">设置类型</label>
            <div class="col-md-6 control-label text-left">
                <p class=" text-left">
                    @if($value_type==0)
                        字符
                    @elseif($value_type==1)
                        数字
                    @else
                        正数
                    @endif
                </p>
            </div>
        </div>
    @endif
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">配置值</label>
        <div class="col-md-8">
            @if($input_type==1)
                @php
                    $input_option = explode(',',$input_option);
                @endphp
                <select name="value" class="form-control">
                    @foreach($input_option as $option)
                        @php
                            list($val,$name) = explode("|",$option);
                        @endphp
                        <option value="{{$val}}" {{$value==$val?'selected':''}}>{{$name}}</option>
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
                        <option value="{{$val}}" {{in_array($val,$value)?'selected':''}}>{{$name}}</option>
                    @endforeach
                </select>
            @else
                @if(mb_strlen($value)>40)
                    <textarea name="value" class="form-control"
                              rows="8">{{ $value }}</textarea>
                @else
                    <input type="text" class="form-control" name="value"
                           value="{{ $value }}" maxlength="256" autocomplete="off">
                @endif
            @endif
        </div>
    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">配置描述</label>
        <div class="col-md-6 control-label text-left">
            <p class=" text-left">{{ $description }}</p>
        </div>
    </div>
</form>

