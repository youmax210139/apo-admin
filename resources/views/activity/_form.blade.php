<div class="form-group">
    <label class="col-md-3 control-label">发放方式</label>
    <div class="col-md-6">
        <div class="col-md-6 form-control app_wauto_bnone">
            <label class="radio-inline">
                <input type="radio" name="draw_method" value="0" @if ($draw_method==0)checked @endif @if ($deny_modify==1) disabled @endif />
                用户领取
            </label>
            <label class="radio-inline">
                <input type="radio" name="draw_method" value="1" @if ($draw_method==1)checked @endif @if ($deny_modify==1) disabled @endif />
                管理员发放
            </label>
            <label class="radio-inline">
                <input type="radio" name="draw_method" value="2" @if ($draw_method==2)checked @endif @if ($deny_modify==1) disabled @endif />
                自动发放
            </label>
            <label class="radio-inline">
                <input type="radio" name="draw_method" value="3" @if ($draw_method==3)checked @endif @if ($deny_modify==1) disabled @endif />
                充值触发
            </label>
            <label class="radio-inline">
                <input type="radio" name="draw_method" value="4" @if ($draw_method==4)checked @endif @if ($deny_modify==1) disabled @endif />
                提现触发
            </label>
            @if ($deny_modify==1)<input type="hidden" name="draw_method" value="{{ $draw_method }}"> @endif
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">唯一标识</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="ident" placeholder="对应类名" value="{{ $ident }}" maxlength="64" autocomplete="off" @if ($deny_modify==1) readonly @endif >
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">活动名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" value="{{ $name }}" maxlength="64" autocomplete="off">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">排序</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="sort" value="{{ $sort }}" maxlength="64" autocomplete="off">
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label">开始时间</label>
    <div class="col-md-6">
        <input type="text" class="form-control" id="start_time" name="start_time" value="{{ $start_time }}"
               maxlength="64" autocomplete="off">
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label">结束时间</label>
    <div class="col-md-6">
        <input type="text" class="form-control" id="end_time" name="end_time" value="{{ $end_time }}" maxlength="64" autocomplete="off">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">禁止访问用户组</label>
    <div class="col-md-7">
        <div class="checkbox">
            <label class="checkbox-inline"><input type="checkbox" name="deny_user_group[]" value="1"
                                                  @if ( in_array(1,$deny_user_group) ) checked="checked"@endif />正式组</label>
            <label class="checkbox-inline"><input type="checkbox" name="deny_user_group[]" value="2"
                                                  @if ( in_array(2,$deny_user_group) ) checked="checked"@endif />测试组</label>
            <label class="checkbox-inline"><input type="checkbox" name="deny_user_group[]" value="3"
                                                  @if ( in_array(3,$deny_user_group) ) checked="checked"@endif />试玩组</label>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">允许访问总代线</label>
    <div class="col-md-6">
        <div class="col-md-6 form-control app_wauto_bnone">
            <label class="radio-inline">
                <input type="radio" name="ctl_user_top" value="0" @if (empty($allow_user_top_id))checked @endif />
                全部
            </label>
            <label class="radio-inline">
                <input type="radio" name="ctl_user_top" value="1" @if (!empty($allow_user_top_id))checked @endif />
                部分
            </label>
        </div>
    </div>
</div>
<div class="form-group ctl_user_top" style="@if(empty($allow_user_top_id)) display: none;@endif">
    <label class="col-md-3 control-label checkbox-inline text-bold">选择总代线</label>
    <div class="col-md-7">
        <div class="checkbox">
            @foreach($top_users as $item)
                <label class="checkbox-inline"><input type="checkbox" name="allow_user_top_id[]" value="{{ $item->id }}" {{ $item->checked ? 'checked':'' }}>{{ $item->username }}</label>
            @endforeach
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">允许访问层级</label>
    <div class="col-md-6">
        <div class="col-md-6 form-control app_wauto_bnone">
            <label class="radio-inline">
                <input type="radio" name="ctl_user_level" value="0" @if (empty($allow_user_level_id))checked @endif />
                全部
            </label>
            <label class="radio-inline">
                <input type="radio" name="ctl_user_level" value="1" @if (!empty($allow_user_level_id))checked @endif />
                部分
            </label>
        </div>
    </div>
</div>
<div class="form-group ctl_user_level" style="@if(empty($allow_user_level_id)) display: none;@endif">
    <label class="col-md-3 control-label checkbox-inline text-bold">选择层级</label>
    <div class="col-md-7">
        <div class="checkbox">
            @foreach($user_level as $level)
                <label class="checkbox-inline"><input type="checkbox" name="allow_user_level_id[]" value="{{ $level->id }}" {{ $level->checked ? 'checked':'' }}>{{ $level->name }}[{{ $level->id }}]</label>
            @endforeach
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">活动界面配置</label>
    <div class="col-md-6">
        <textarea class="form-control" id="config_ui" rows="2" name="config_ui">{{$config_ui}}</textarea>
    </div>
</div>
@if($config && isset($config['hide']))
    <div class="form-group">
        <label class="col-md-3 control-label">是否列表显示</label>
        <div class="col-md-6">
            <div class="col-md-6 form-control app_wauto_bnone">
                <label class="radio-inline">
                    <input type="radio" name="config[hide]" value="1" @if (!empty($config['hide']))checked @endif />
                    隐藏
                </label>
                <label class="radio-inline">
                    <input type="radio" data="{{$config['hide']}}" name="config[hide]" value="0" @if (empty($config['hide']))checked @endif />
                    显示
                </label>
            </div>
        </div>
    </div>
    @if(isset($config['config']) && is_array($config['config']))
        <div class="form-group">
            <label class="col-md-3 control-label">规则配置</label>
            <div class="col-md-6">
                @foreach($config['config'] as $key=>$configs)
                    <div>
                        @foreach($configs as $k1=>$items)
                            <p class="text-primary" style="padding-left: 10px">
                                @foreach($items as $k2=>$val)
                                    <input size="3" name="config[config][{{$key}}][{{$k1}}][{{$k2}}][title]" class="input_inline sort" style="width:62px;"
                                           type="hidden" value="{{$val['title']}}">
                                    {{$val['title']}}:<input size="3" name="config[config][{{$key}}][{{$k1}}][{{$k2}}][value]" class="input_inline sort" style="width:62px;"
                                                             type="text" value="{{$val['value']}}">
                                @endforeach
                            </p>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="form-group">
            <label class="col-md-3 control-label">活动配置</label>
            <div class="col-md-6">
                <textarea class="form-control" id="config" rows="8" name="config">{{$config}}</textarea>
            </div>
        </div>
        @endif
@else
    <div class="form-group">
        <label class="col-md-3 control-label">活动配置</label>
        <div class="col-md-6">
            <textarea class="form-control" id="config" rows="8" name="config">{{$config}}</textarea>
        </div>
    </div>
@endif
<div class="form-group">
    <label class="col-md-3 control-label">活动状态</label>
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
<div class="form-group">
    <label class="col-md-3 control-label">活动简介</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="summary" placeholder="列表使用描述" value="{{ $summary }}"
               maxlength="64" autocomplete="off">
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label">活动介绍</label>
    <div class="col-md-8">
        <textarea class="form-control" id="description" name="description">{{$description}}</textarea>
    </div>
</div>
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script src="/assets/plugins//ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        laydate.skin('lynn');
        laydate({
            elem: '#start_time',
            event: 'focus',
            format: 'YYYY-MM-DD 00:00:00',
            istime: false,
            istoday: true,
            zindex: 2
        });

        laydate({
            elem: '#end_time',
            event: 'focus',
            format: 'YYYY-MM-DD 23:59:59',
            istime: false,
            istoday: true,
            zindex: 2
        });
        $(function () {
            CKEDITOR.replace('description', {
                language: 'zh-cn',
                uiColor: '#9AB8F3'
            });

            //总代线选择操作
            $("input[name='ctl_user_top']").change(function () {
                if ($(this).val() == 1) {
                    $('.ctl_user_top').show();
                } else {
                    $('.ctl_user_top').hide();
                }
                $('input[name="allow_user_top_id[]"]').prop('checked', false);
            });

            //用户层级选择操作
            $("input[name='ctl_user_level']").change(function () {
                if ($(this).val() == 1) {
                    $('.ctl_user_level').show();
                } else {
                    $('.ctl_user_level').hide();
                }
                $('input[name="allow_user_level_id[]"]').prop('checked', false);
            });
        })
    </script>
@stop
