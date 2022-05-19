@extends('layouts.base')

@section('title','支付域名管理')

@section('function','支付域名管理')
@section('function_link', '/paymentdomain/')

@section('here','支付域名列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('paymentdomain/create'))
                <a href="/paymentdomain/create/" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加支付域名</a>
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
                            <div class='row payment-info '>
                                <div class="col-md-1 text-center">编号</div>
                                <div class="col-md-2 text-left">域名</div>
                                <div class="col-md-2 text-left">绑定的渠道</div>
                                <div class="col-md-2 text-left">绑定的服务器</div>
                                <div class="col-md-1 text-center">状态</div>
                                <div class="col-md-1 text-left">备注</div>
                                <div class="col-md-3 text-center">可选操作</div>
                            </div>
                        </li>
                        @forelse ($rows as $item)
                            <li class="list-group-item">
                                <div class='row payment-info '>
                                    <div class="col-md-1 text-center"><strong>{{ $item->id }}</strong></div>
                                    <div class="col-md-2 text-left">{{ $item->domain }}</div>
                                    <div class="col-md-2 text-left">{{ $item->payment_category_name or '不限渠道'}}</div>
                                    <div class="col-md-2 text-left">{{ $item->intermediate_servers_name }}</div>
                                    <div class="col-md-1 text-center">
                                        @if ($item->status == 1)
                                            <span class="label label-success">启用</span>
                                        @else
                                            <span class="label label-danger">禁用</span>
                                        @endif
                                    </div>
                                    <div class="col-md-1 text-left">{{ $item->remark }}</div>
                                    <div class="col-md-3 text-center">
                                        @if(Gate::check('paymentdomain/edit'))
                                            <a style="padding: 5px;" href="/paymentdomain/edit?id={{ $item->id }}"
                                               class="btn-sm  text-success"><i class="fa fa-edit"></i> 编辑</a>
                                        @endif
                                        @if(Gate::check('paymentdomain/delete'))
                                            <a style="padding: 5px;" href="javascript:;" onclick="deldomainServer({{$item->id}})"
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
                        确认要删除该支付域名吗?
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
        function deldomainServer(id){
            $('.deleteForm').attr('action', '/paymentdomain/delrecord?id=' + id);
            $('#modal-delete').modal();
        }
    </script>

@stop
