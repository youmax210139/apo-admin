@extends('layouts.base')

@section('title','支付服务器管理')

@section('function','支付服务器管理')
@section('function_link', '/intermediateservers/')

@section('here','支付服务器列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('intermediateservers/create'))
                <a href="/intermediateservers/create/" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加支付服务器</a>
            @endif
            @if(Gate::check('intermediateservers/refreshserver'))
                <!--<a href="/intermediateservers/refreshserver/" class="btn btn-warning btn-md"><i class="fa fa-plus-circle"></i> 同步到服务器</a>-->
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
                <div class="box-body">
                    <ul class="list-group" style="margin:10px 0px;">
                        <li class="list-group-item active">
                            <div class='row payment-info'>
                                <div class="col-md-1 text-center">编号</div>
                                <div class="col-md-2 text-left">服务器名称</div>
                                <div class="col-md-2 text-left"> 服务器IP</div>
                                <div class="col-md-3 text-left">同步数据域名</div>
                                <div class="col-md-1 text-center">状态</div>
                                <div class="col-md-3 text-center">可选操作</div>
                            </div>
                        </li>
                        @forelse ($rows as $item)
                            <li class="list-group-item">
                                <div class='row payment-info '>
                                    <div class="col-md-1 text-center"><strong>{{ $item->id }}</strong></div>
                                    <div class="col-md-2 text-left"><strong>{{ $item->name }}</strong></div>
                                    <div class="col-md-2 text-left"><strong>{{ $item->ip }}</strong></div>
                                    <div class="col-md-3 text-left"><strong>{{ $item->domain }}</strong></div>
                                    <div class="col-md-1 text-center">
                                        @if ($item->status == 1)
                                            <span class="label label-success">启用</span>
                                        @else
                                            <span class="label label-danger">禁用</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3 text-center">
                                        @if(Gate::check('intermediateservers/edit'))
                                            <a style="padding: 5px;" href="/intermediateservers/edit?id={{ $item->id }}"
                                               class="btn-sm  text-success"><i class="fa fa-edit"></i> 编辑</a>
                                        @endif
                                        @if(Gate::check('intermediateservers/refreshserver'))
                                            <a style="padding: 5px;" href="/intermediateservers/refreshserver?id={{ $item->id }}"
                                               class="btn-sm  text-warning"><i class="fa fa-upload"></i> 同步</a>
                                        @endif
                                        @if(Gate::check('intermediateservers/delete'))
                                             <a style="padding: 5px;" href="javascript:;" onclick="delPayServer({{$item->id}})"
                                               class="btn-sm text-danger"><i class="fa fa-times-circle"></i>删除</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="panel  panel-default payment-account" style='margin: 10px 0px;display:none'></div>
                            </li>
                        @empty
                            <li class="list-group-item text-center">空数据</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--删除-->
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
                        确认要删除该支付服务器吗?
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

@stop

@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script>
        function delPayServer(id){
            $('.deleteForm').attr('action', '/intermediateservers/delrecord?id=' + id);
            $('#modal-delete').modal();
        }
    </script>

@stop