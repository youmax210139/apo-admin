@extends('layouts.base')

@section('title','提现渠道管理')

@section('function','提现渠道管理')
@section('function_link', '/withdrawalcategory/')

@section('here','提现渠道列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('withdrawalcategory/create'))
                <a href="/withdrawalcategory/create/" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加提现渠道</a>
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
                                <div class="col-md-3 text-center">名称</div>
                                <div class="col-md-3 text-center">英文标识</div>
                                <div class="col-md-3 text-center">状态</div>
                                <div class="col-md-3 text-center">可选操作</div>
                            </div>
                        </li>
                        @forelse ($categories as $cate)
                            <li class="list-group-item">
                                <div class='row payment-info '>
                                    <div class="col-md-3 text-center"><strong>{{ $cate->name }}</strong></div>
                                    <div class="col-md-3 text-center"><strong>{{ $cate->ident }}</strong></div>
                                    <div class="col-md-3 text-center">
                                        @if ($cate->status == 1)
                                            <span class="label label-success">启用</span>
                                        @else
                                            <span class="label label-danger">禁用</span>
                                        @endif
                                    </div>
                                    <div class="col-md-3 text-center">
                                        @if(Gate::check('withdrawalcategory/edit'))
                                            <a style="padding: 5px;" href="/withdrawalcategory/edit?id={{ $cate->id }}"
                                               class="btn-sm  text-success"><i class="fa fa-edit"></i> 编辑</a>
                                        @endif
                                        @if(Gate::check('withdrawalcategory/delete'))
                                            <a style="padding: 5px;" href="javascript:;" onclick="delWithdrawalCategory({{$cate->id}})"
                                               class="btn-sm text-danger"><i class="fa fa-times-circle"></i>删除</a>
                                        @endif
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
                        确认要删除该提现渠道吗?
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
        function delWithdrawalCategory(id){
            $('.deleteForm').attr('action', '/withdrawalcategory/?id=' + id);
            $('#modal-delete').modal();
        }
    </script>
@stop