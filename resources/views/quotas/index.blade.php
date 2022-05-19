@extends('layouts.base')

@section('title','配额组管理')

@section('function','配额组管理')
@section('function_link', '/notice/')

@section('here','配额组管理')

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
            <a href="/quotas/create" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加配额 </a>
    </div>
</div>
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">配额列表</h3>
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
                                <th data-sortable="false">返点下限</th>
                                <th data-sortable="false">返点上限</th>
                                <th data-sortable="false">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotas as $v)
                            <tr>
                        <input type="hidden" name="id" value="{{$v->id}}">
                        <th scope="row">{{$v->id}}</th>
                        <td>{{$v->low}}</td>
                        <td>{{$v->high}}</td>
                        <td>
                         <a href="#" attr="{{$v->id}}" class="X-Small btn-xs text-primary del">删除</a>
                        </td>
                        </tr>
                        @endforeach
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
                        确认要删除该配额组吗?
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
@stop

@section('js')
<script src="/assets/js/app/common.js" charset="UTF-8"></script>
<script>
$(function () {

//审核
    $("table").delegate('.del', 'click', function () {
        var id = $(this).attr('attr');
        $('.deleteForm').attr('action', '/quotas/?id=' + id);
        $("#modal-delete").modal();
    });
;
});
</script>
@stop