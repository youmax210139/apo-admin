@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','推广域名')

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
         @include('partials.errors')
                @include('partials.success')
        <div class="box box-primary">
            <form class="form-horizontal" id="search" method="post" action="/user/assigndomain?id={{$user->id}}">
                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                <div class="box-header with-border">
                    <h3 class="box-title">推广域名</h3>
                </div>
                <div class="box-body">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="domain_id" class="col-sm-3 control-label">域名</label>
                            <div class="col-sm-9">
                                <select name="domain_id" class="form-control">
                                    <option value="">请选择域名</option>
                                    @foreach($domains as $v)
                                    <option value="{{$v->id}}">{{$v->domain}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary col-sm-2" id="search_btn">
                                <i class="fa fa-search" aria-hidden="true"></i>分配</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                            <tr>
                                <th class="hidden-sm">编号</th>
                                <th data-sortable="false">域名</th>
                                <th data-sortable="false">回收</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user_domains as $v)
                            <tr>
                                <td scope="row">{{$v->id}}</td>
                                <td>{{$v->domain}}</td>
                                <td>
                                    <a href="#" data-id='{{$v->domain_id}}' class="X-Small btn-xs text-primary recover">回收域名</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">
                                    空数据
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-recover" tabIndex="-1">
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
                    确认要<span class="row_verify_text">回收</span>该域名吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="verifyForm" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="domain_id" value="">
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
    $("table").delegate('.recover', 'click', function () {
        var id = $(this).attr('data-id');
        $('.verifyForm input[name="domain_id"]').val(id);
        $('.verifyForm').attr('action', '/user/RecoveryDomain?id={{$user->id}}');
        $("#modal-recover").modal();
    });
});
</script>
@stop