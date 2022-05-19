@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','用户配额')

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
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">用户配额设置</h3>
            </div>
            <div class="panel-body">
                @include('partials.errors')
                @include('partials.success')
                <div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong> <i class="fa fa-info fa-fw"></i> 提示！</strong>
                    <ul>
                        <li>此处增加配额不与上级配额关联，即增加配额不会扣除上级配额！</li>
                        <li>此处只能给上级曾经分配过给下级的配额组设置增加或扣减配额！</li>
                        <li>填写正数，负数或零,如：5,-5或0！</li>
                    </ul>
                </div>
                <form class="form-horizontal" id="defaultForm" role="form" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                            <tr>

                                <th class="hidden-sm">编号</th>
                                <th data-sortable="false">返点下限</th>
                                <th data-sortable="false">返点上限</th>
                                <th data-sortable="false">剩余配额</th>
                                <th data-sortable="false">增加</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotas as $v)
                            <tr>
                        <input type="hidden" name="uagid[]" value="@if(isset($v['user_quota_id'])){{$v['user_quota_id']}}@endif">
                        <th scope="row">@if(isset($v['user_quota_id'])){{$v['user_quota_id']}}@endif</th>
                        <td>{{$v['low']}}</td>
                        <td>{{$v['high']}}</td>
                        <td>@if(isset($v['num'])){{$v['num']}}@endif</td>
                        <td>
                            <input name="num[]" type="text" value="0"/>
                            <input type="hidden" name="agid[]" value="{{$v['id']}}">
                        </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="form-group">
                        <div class="col-md-7 col-md-offset-3">
                            <button type="submit" class="btn btn-primary btn-md">
                                <i class="fa fa-plus-circle"></i>
                                保存
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-verify" tabIndex="-1">
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
                    确认要<span class="row_verify_text"></span>该公告吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="verifyForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
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
    $("table").delegate('.verify', 'click', function () {
        var id = $(this).attr('attr');
        $(".row_verify_text").text($(this).attr('is_verified'));
        $('.verifyForm').attr('action', '/notice/verify?id=' + id);
        $("#modal-verify").modal();
    });
//隐藏
    $("table").delegate('.show', 'click', function () {
        var id = $(this).attr('attr');
        $(".row_show_text").text($(this).attr('is_show'));
        $('.showForm').attr('action', '/notice/show?id=' + id);
        $("#modal-show").modal();
    });
});
</script>
@stop