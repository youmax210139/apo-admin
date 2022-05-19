@extends('layouts.base')
@section('title','用户日工资线')
@section('function','用户日工资线')
@section('function_link', '/userdailywagecontract/')
@section('here','设置用户日工资线')
@section('content')
    <div class="row page-title-row" style="margin:5px;">
        @if($wage_line_multi_available == 1 && $lines)
        <div class="col-md-12">
            @foreach($lines as $tmp_line)
                <a  href="{{url('userdailywagecontract/line')}}?user_id={{$tmp_line->top_user_id}}&daily_wage_line_type={{$tmp_line->type}}"
                    type="button" class="btn @if($line->type == $tmp_line->type) btn-primary @else btn-default  @endif ">{{__("wage.line_type_".$tmp_line->type)}}</a>
            @endforeach
            @if($wage_line_create)
            <div class="btn-group" style="float: right">
                <a href="{{url('userdailywagecontract/line')}}?user_id={{$user_id}}&daily_wage_line_type=999" class="btn btn-success btn-md"><i class="fa fa-plus-circle"></i>新增工资线</a>
            </div>
            @endif
        </div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">@if($is_edit)设置@else 新增 @endif 工资线</h3>
                </div>
                <div class="panel-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <form class="form-horizontal" role="form" method="POST" action="/userdailywagecontract/line">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label">线名</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="name" value="{{ $line->name }}" maxlength="16" >
                            </div>
                        </div>

                        <div class="form-group" id="item_category">
                            <label for="tag" class="col-md-3 control-label">工资类型</label>
                            <div class="col-md-5">
                                <select name="daily_wage_line_type" id="daily_wage_line_type" class="form-control" @if($is_edit) readonly @endif>
                                    @foreach($alternative_line_types as $alternative_line_type)
                                        <option value="{{$alternative_line_type}}" @if($line->type==$alternative_line_type) selected @endif>{{__("wage.line_type_".$alternative_line_type)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tag" class="col-md-3 control-label"></label>
                            <div class="col-md-5">
                                <a href="javascript:void(0);" id="add_content">添加配置</a>
                            </div>
                        </div>
                        <div  id="content_tmp" style="display:none;">
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label"></label>
                                <div class="col-md-6" style="margin-left: -15px;">
                                    <div class="col-md-4"><input type="text" class="form-control" name="content_title[]" placeholder="中文名称" maxlength="120"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="content_key[]" placeholder="配置标识" maxlength="120" ></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="content_value[]" placeholder="值"></div>
                                    <div class="col-md-1"><a href="javascript:void(0);" class="del_item">删除</a></div>
                                </div>
                            </div>
                        </div>
                        <div id="content_list"></div>

                    <div class="panel-footer text-center">
                        <button type="button" class="btn btn-warning btn-md" onclick="location.href='/userdailywagecontract/index'"><i
                                    class="fa fa-mail-reply-all"></i> 返回契约列表
                        </button>
                        <button type="submit" class="btn btn-primary btn-md save" style="margin-left:10px;"><i
                                    class="fa fa-plus-circle"></i> 保存
                        </button>
                        <!--created_at:{{ $line->created_at }}-->
                        <div  style="float: right">
                        <button type="button" class="btn btn-danger btn-md delete float-right"><i
                                    class="fa fa-minus-circle"></i> 清除工资线
                        </button>
                        </div>
                    </div>

                </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-delete" tabIndex="-1">
        <div class="modal-dialog modal-danger">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">提示</h4>
                </div>
                <div class="modal-body">
                    <p class="lead">
                        <i class="fa fa-question-circle fa-lg"></i>
                        确认要删除 【{{$user->username}}】 的 【{{__("wage.line_type_".$line->type)}}】 工资线么?
                        <br>警告：下级【{{__("wage.line_type_".$line->type)}}】契约将全部删除！
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="delete_line_id" value="{{$line->id}}">
                        <input type="hidden" name="delete_line_type" value="{{$line->type}}">
                        <input type="hidden" name="delete_line_top_user_id" value="{{$line->top_user_id}}">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        var items = new Array();

        @foreach($line->content as $_k=>$item)
        items.push({
            title:"{{ $item['title'] }}",
            key:"{{ $_k }}",
            value:"{{ $item['value'] }}"
        });
        @endforeach

        function addItem(item){
            var html = $('#content_tmp').html();
            if(item){
                html = html.replace('name="content_title[]"', 'name="content_title[]"'+' value="'+item.title+'"');
                html = html.replace('name="content_key[]"', 'name="content_key[]"'+' value="'+item.key+'"');
                html = html.replace('name="content_value[]"', 'name="content_value[]"'+' value="'+item.value+'"');
            }
            $('#content_list').append(html);
        }
        for(var i=0; i<items.length; i++){
            addItem(items[i]);
        }

        $('#add_content').bind('click',function () {
            addItem();
        });
        $('#content_list').on('click','.del_item',function () {
            $(this).parent('div').parent('div').parent('div').remove();
        });

        $('#daily_wage_line_type').change(function(){
            var type = $(this).val();
            $('button.save').prop('disabled','disabled');
            window.location.href = window.location.origin+window.location.pathname+'?user_id={{ $user_id }}&daily_wage_line_type='+type;
        });

        $('.delete').on('click',function () {
            $('.deleteForm').attr('action', '/userdailywagecontract/line');
            $("#modal-delete").modal();
        });
    </script>
@stop
