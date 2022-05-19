@extends('layouts.base')

@section('title','受付银行管理')

@section('function','受付银行管理')
@section('function_link', '/bank/')

@section('here','银行列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

<div class="row page-title-row" id="dangqian" style="margin:5px;">
    <div class="col-md-6">

    </div>

</div>
<div class="row page-title-row" style="margin:5px;">
    <div class="col-md-6">
    </div>
    <div class="col-md-6 text-right">
    </div>
</div>

<div class="row">
    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6">

        </div>

        <div class="col-md-6 text-right">
            <a href="/bank/create" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加银行 </a>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">受付银行管理</h3>
            </div>
            <div class="panel-body">
                @include('partials.errors')
                @include('partials.success')

                <form class="form-horizontal" id="defaultForm" role="form" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                            <tr>

                                <th class="hidden-sm">编号</th>
                                <th data-sortable="false">名称</th>
                                <th data-sortable="false">唯一标示</th>
                                <th data-sortable="false">允许提现</th>
                                <th data-sortable="false">禁用</th>
                                <th data-sortable="false">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banks as $v)
                            <tr>
                                <th scope="row">{{$v->id}}</th>
                                <td>{{$v->name}}</td>
                                <td>{{$v->ident}}</td>
                                
                                <td>
                                    @if($v->withdraw)
                                    <span class="label label-success">是</span>
                                    @else
                                    <span class="label label-danger">否</span>
                                    @endif
                                </td>
                                <td>
                                    @if($v->disabled)
                                    <span class="label label-danger">禁用</span>
                                    @else
                                    <span class="label label-success">启用</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="/bank/edit?id={{$v->id}}" class="X-Small btn-xs text-primary">编辑</a>
                                    @if($v->disabled)
                                    <a href="javascript:;" attr="{{$v->id}}" data="{{$v->disabled?0:1}}" class="X-Small btn-xs text-success disabled"> <span class="text-success">启用</span></a>
                                    @else
                                    <a href="javascript:;" attr="{{$v->id}}" data="{{!$v->disabled}}" class="X-Small btn-xs text-danger disabled"><span class="text-danger">禁用</span></a>
                                    @endif
                                    <a href="javascript:;" attr="{{$v->id}}" class="X-Small btn-xs text-danger del">删除</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <th scope="row" colspan="8">空数据</th>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

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
                    确认要删除该银行吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="deleteForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times-circle"></i>确认
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
<div class="modal fade" id="modal-disabled" tabIndex="-1">
    <div class="modal-dialog modal-primary">
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
                    确认要<span class="row_verify_text"></span>该银行吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="verifyForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="disabled" value="0">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check-circle-o"></i> 确认
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
$(function () {

//审核
    $("table").delegate('.del', 'click', function () {
        var id = $(this).attr('attr');
        $('.deleteForm').attr('action', '/bank/?id=' + id);
        $("#modal-delete").modal();
    });
    $("table").delegate('.disabled', 'click', function () {
        var id = $(this).attr('attr');
        $("#modal-disabled .row_verify_text").html($(this).text());
        $('.verifyForm input[name="disabled"]').val($(this).attr('data'));
        $('.verifyForm').attr('action', '/bank/disabled/?id=' + id);
        $("#modal-disabled").modal();
    });
});
</script>
@stop