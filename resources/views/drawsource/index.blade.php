@extends('layouts.base')

@section('title','开奖管理')

@section('function','开奖管理')
@section('function_link', '/drawsource/')

@section('here','号源列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
    @if(Gate::check('drawsource/create'))
        <a href="/drawsource/create/" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加号源</a>
    @endif
</div>
</div>
<div class="row page-title-row" style="margin:5px;">
<div class="col-md-6">
</div>
<div class="col-md-6 text-right">
</div>
</div>
<div class="row">
<div class="col-sm-12">
    <div class="box  box-primary">
        @include('partials.errors')
        @include('partials.success')
        <style>
        .list-group-item{background: #fdfeff;}
        .list-group-item:hover{background: #faf9f2;}
        </style>
        <div class="box-body">
            <div>
                <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/drawsource/'" type="button" class="btn {{0==$mcid && 0==$special?'bg-primary':''}} " style="margin: 5px">全部</button>
            @foreach($method_category_rows as $v)
                <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/drawsource/?mcid={{$v->id}}'" type="button" class="btn {{$v->id==$mcid?'bg-primary':''}} " style="margin: 5px">{{$v->name}}</button>
            @endforeach
                <button style="width: 100px;margin-bottom: 5px;margin-left: 2px" onclick="location.href='/drawsource/?special=1'" type="button" class="btn {{$special > 0?'bg-primary':''}} " style="margin: 5px">自开彩</button>
            </div>
            <ul class="list-group text-center" style="margin:10px 0px;">
                <li class="list-group-item active">
                    <div class='row drawsource-info '>
                        <div class="col-md-2 ">编号</div>
                        <div class="col-md-5">彩种列表</div>
                        <div class="col-md-5">号源</div>
                    </div>
                </li>
                @forelse ($drawsource as $lottery)
                <li class="list-group-item">
                    <div class='row'>
                        <div class="col-md-2"><strong>{{ $lottery[0]->lottery_id }}</strong></div>
                        <div class="col-md-5"><strong>{{ $lottery[0]->lottery_name }} <span class="text-gray">{{ $lottery[0]->lottery_ident }}</span></strong></div>
                        <div class="col-md-5">
                            <a href="javascript:;" class="X-Small btn-xs text-primary show_sub" data-id="{{ $lottery[0]->lottery_id }}"><i class="fa fa-chevron-down"></i>显示</a>
                        </div>
                    </div>
                    <div class="panel  panel-info source-item-{{ $lottery[0]->lottery_id }}" style="display:none;margin: 10px 0px;">
                        <div class="panel-heading">
                            <div class="row">
                            <div class="col-md-2">号源名称</div>
                            <div class="col-md-2">号源标识</div>
                            <div class="col-md-3">号源 API 地址</div>
                            <div class="col-md-1">权重</div>
                            <div class="col-md-1">状态</div>
                            <div class="col-md-3">可选操作</div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <ul class="list-group">
                            @foreach ($lottery as $source)
                                    <li class="list-group-item"><div class="row">
                                        <div class="col-md-2"><strong>{{ $source->name }}</strong></div>
                                        <div class="col-md-2"><strong>{{ $source->ident }}</strong></div>
                                        <div class="col-md-3"><strong>{{ $source->url }}</strong></div>
                                        <div class="col-md-1"><strong>{{ $source->rank }}</strong></div>
                                        <div class="col-md-1">
                                            @if ($source->status == 1)
                                            <span class="label label-success" id="status_show_{{ $source->id }}">启用</span>
                                            @else
                                            <span class="label label-danger" id="status_show_{{ $source->id }}">禁用</span>
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            <a style="padding: 5px;" href="javascript:;" class="btn-sm  text-primary test" lottery-id="{{ $source->lottery_id }}" data-id='{{ $source->id }}' ident="{{ $source->ident }}"><i class="fa fa-hand-lizard-o"></i>测试</a>
                                            @if(Gate::check('drawsource/edit'))
                                                <a style="padding: 5px;" href="/drawsource/edit?id={{ $source->id }}" class="btn-sm  text-success"><i class="fa fa-edit"></i> 编辑</a>
                                                @if ($source->status == 1)
                                                <a style="padding: 5px;" href="javascript:;" data-id='{{ $source->id }}' data-set-status='0' class="disable btn-sm  text-danger set-status"><i class="fa fa-ban"></i> 禁用</a>
                                                @else
                                                <a style="padding: 5px;" href="javascript:;" data-id='{{ $source->id }}' data-set-status='1' class="disable btn-sm  text-success set-status"><i class="fa fa-check-circle-o"></i> 启用</a>
                                                @endif
                                            @endif
                                            @if(Gate::check('drawsource/delete'))
                                            <a style="padding: 5px;" href="javascript:;" class="del-btn btn-sm  text-danger" data-id='{{ $source->id }}' ><i class="fa fa-times-circle"></i> 删除</a>
                                            @endif
                                        </div>
                                    </div></li>
                            @endforeach
                            </ul>
                        </div>
                    </div>
                </li>
                @empty
                <li class="list-group-item text-center">空数据</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
</div>

<!--删除配置项-->
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
                确认要删除吗?
            </p>
        </div>
        <div class="modal-footer">
            <form class="deleteForm" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fa fa-times-circle"></i> 确认
                </button>
            </form>
        </div>
    </div>
</div>
</div>

<!--禁用配置项-->
<div class="modal fade" id="modal-disable" tabIndex="-1">
    <div class="modal-dialog modal-info">
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
                    确认要<span class="row_disable_text"></span>吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="disableForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times-circle"></i> 确认
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-msg" tabIndex="-1">
    <div class="modal-dialog">
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
                    <span id="tips_content"></span>
                </p>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
        <script>
        //测试
        $('.test').bind('click',function(){
            $("#tips_content").html("正在测试,请耐心等待结果返回");
            $("#modal-msg").modal();
            var ident = $(this).attr('ident');
            $.ajax({
                url:'/drawsource/index?test=1',
                type: "POST",
                data:{
                    lottery_id: $(this).attr('lottery-id'),
                    id: $(this).attr('data-id')
                },
                dataType:'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    if(typeof data[ident] != 'undefined' && data[ident].result){
                        msg = "测试成功：\n第 " + data[ident].data.issue + " 期，号码:" + data[ident].data.number;
                    }else{
                        msg = "测试失败";
                        console.log('测试抓号返回结果',data);
                    }
                    $("#tips_content").html(msg);
                    $("#modal-msg").modal();
                }
            });
        });
        
        //显示子分类
        $('.show_sub').bind('click',function(){
            var item = $(".source-item-"+$(this).attr('data-id'));
            if( item.css('display') == 'none' ){
                $(this).html('<i class="fa fa-chevron-up"></i> 隐藏');
            }else{
                $(this).html('<i class="fa fa-chevron-down"></i> 显示');
            }
            item.toggle();
        });
        
        //状态
        $('.set-status').bind('click',function(){
            var obj = $(this);
            var id = obj.attr('data-id');
            var show_obj = $("#status_show_"+id);
            var status = obj.attr('data-set-status');
            var status_txt = status == 1 ? '启用':'禁用';
            $.ajax({
                url: "/drawsource/edit",
                dataType: "json",
                method: "PUT",
                data:{"id":id, "set_status":1, "status":status,},
            }).done(function (json) {
                if(json.status == 0) {
                    if(status == '1') {
                        obj.attr('data-set-status', '0');
                        obj.removeClass('text-success');
                        obj.addClass('text-danger');
                        obj.html('<i class="fa fa-ban"></i> 禁用');
                        show_obj.removeClass('label-danger');
                        show_obj.addClass('label-success');
                        show_obj.html('启用');

                    } else {
                        obj.attr('data-set-status', '1');
                        obj.removeClass('text-danger');
                        obj.addClass('text-success');
                        obj.html('<i class="fa fa-check-circle-o"></i> 启用');
                        show_obj.removeClass('label-success');
                        show_obj.addClass('label-danger');
                        show_obj.html('禁用');
                    }
                    BootstrapDialog.alert({
                        title: status_txt + ' 奖源成功',
                        message: json.msg,
                        type: BootstrapDialog.TYPE_PRIMARY, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                        closable: true, // <-- Default value is false
                        draggable: true, // <-- Default value is false
                        buttonLabel: '关闭', // <-- Default value is 'OK',
                        callback: function(result) {
                            //document.location.reload();
                        }
                    });
                } else {
                    BootstrapDialog.alert({
                        title: '修改状态失败',
                        message: json.msg,
                        type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                        closable: true, // <-- Default value is false
                        draggable: true, // <-- Default value is false
                        buttonLabel: '关闭', // <-- Default value is 'OK',
                        callback: function(result) {

                        }
                    });
                }
            });
        });

        
        //删除
        $('.del-btn').bind('click',function(){
            var id = $(this).attr('data-id');
            var action = '/drawsource/?id='+id;
            $('.deleteForm').prop('action', action );
            $('#modal-delete').modal();
        });
        </script>
@stop