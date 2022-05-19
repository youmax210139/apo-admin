@extends('layouts.base')

@section('title','第三方游戏平台管理')

@section('function','第三方游戏平台管理')
@section('function_link', '/thirdgame/')

@section('here','第三方游戏平台列表')

{{--@section('pageDesc','DashBoard')--}}

<style>
    .list-group-item{background: #fdfeff;}
    .list-group-item:hover{background: #faf9f2;}
</style>
@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
            @if(Gate::check('thirdgame/create'))
                <a href="/thirdgame/createplatform" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加游戏平台
                </a>
                <a href="/thirdgame/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加游戏平台接口
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">

                    <ul class="list-group text-center" style="margin:10px 0px;">
                        <li class="list-group-item active">
                            <div class='row drawsource-info '>
                                <div class="col-md-2 ">平台标识</div>
                                <div class="col-md-3">平台名称</div>
                                <div class="col-md-2">状态</div>
                                <div class="col-md-2">显示顺序</div>
                                <div class="col-md-3">状态</div>
                            </div>
                        </li>
                        @forelse ($platforms as $platform)
                            <li class="list-group-item">
                                <div class='row'>
                                    <div class="col-md-2"><strong>{{ $platform['ident'] }}</strong></div>
                                    <div class="col-md-3"><strong>{{ $platform['name'] }}</strong></div>
                                    <div class="col-md-2">
                                            @if ($platform['status'] == 0)
                                                <span class="label label-success">启用</span>
                                            @else
                                                <span class="label label-danger">禁用</span>
                                            @endif
                                    </div>
                                    <div class="col-md-2"><strong>{{ $platform['sort'] }}</strong></div>
                                    <div class="col-md-3">
                                        <a href="javascript:;" class="X-Small btn-xs text-primary show_sub" data-id="{{ $platform['ident'] }}"><i class="fa fa-chevron-down"></i>显示</a>
										@if(Gate::check('thirdgame/editplatform'))
											<a style="padding: 5px;" href="/thirdgame/editplatform?id={{ $platform['id'] }}" class="btn-sm  text-success"><i class="fa fa-edit"></i>编辑</a>
										@endif
                                    </div>
                                </div>
                                <div class="panel  panel-info source-item-{{ $platform['ident'] }}" style="display:none;margin: 10px 0px;">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-md-2">游戏接口标识</div>
                                            <div class="col-md-2">游戏接口名称</div>
                                            <div class="col-md-3">是否允许使用</div>
                                            <div class="col-md-1">是否允许登入</div>
                                            <div class="col-md-1">是否允许转帐</div>
                                            <div class="col-md-3">操作</div>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-group">
                                            @foreach ($platform['games'] as $game)
                                                <li class="list-group-item"><div class="row">
                                                        <div class="col-md-2"><strong>{{ $game->ident }}</strong></div>
                                                        <div class="col-md-2"><strong>{{ $game->name }}</strong></div>
                                                        <div class="col-md-3">
                                                            @if ($game->status == 0)
                                                                <span class="label label-success">是</span>
                                                            @else
                                                                <span class="label label-danger">否</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-1">
                                                            @if ($game->login_status == 0)
                                                                <span class="label label-success">是</span>
                                                            @else
                                                                <span class="label label-danger">否</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-1">
                                                            @if ($game->transfer_status == 0)
                                                                <span class="label label-success">是</span>
                                                            @else
                                                                <span class="label label-danger">否</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-3">
                                                            @if(Gate::check('thirdgame/edit'))
                                                                <a style="padding: 5px;" href="/thirdgame/test?id={{ $game->id }}" class="btn-sm  text-primary test"><i class="fa fa-hand-lizard-o"></i>测试API</a>
                                                                <a style="padding: 5px;" href="/thirdgame/edit?id={{ $game->id }}" class="btn-sm  text-success"><i class="fa fa-edit"></i>编辑</a>
                                                                <a style="padding: 5px;" href="/thirdgame/log?ident={{ $game->ident }}"><i class="fa fa-book"></i> 查看日志</a>
                                                            @endif
                                                            @if(Gate::check('thirdgame/delete'))
                                                                <a style="padding: 5px;" href="javascript:;" class="del-btn btn-sm  text-danger" data-id="{{ $game->id }}"><i class="fa fa-times-circle"></i> 删除</a>
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

    @stop

@section('js')

<script>
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

    //删除
    $('.del-btn').bind('click',function(){
        var id = $(this).attr('data-id');
        var action = '/thirdgame/index';
        BootstrapDialog.confirm({
            message: "确认要删除吗?",
            type: BootstrapDialog.TYPE_WARNING,
            closable: true,
            draggable: true,
            btnCancelLabel: '取消',
            btnOKLabel: '确认删除',
            btnOKClass: 'btn-warning',
            callback: function(result) {
                if (result) {
                    loadShow();
                    $.ajax({
                        url: action,
                        dataType: "json",
                        method: "DELETE",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data:{id:id},
                    }).done(function (json) {
                        loadFadeOut();
                        if (json.hasOwnProperty('code') && json.code == '302') {
                            window.location.reload();
                        }
                        var notify_type = 'danger';
                        if (json.status == 0) {
                            notify_type = 'success';
                        }
                        $.notify({
                            title: '<strong>提示!</strong>',
                            message: json.msg
                        },{
                            type: notify_type
                        });
                    });
                }
            }});
    });
</script>

@stop