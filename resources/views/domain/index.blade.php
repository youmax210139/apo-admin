@extends('layouts.base')

@section('title','域名管理')

@section('function','域名管理')
@section('function_link', '/notice/')

@section('here','域名管理')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

<div class="row page-title-row" id="dangqian" style="margin:5px;">
    <div class="col-md-12  text-right">
        @if(Gate::check('domain/create'))
        <a href="/domain/create" class="btn btn-primary btn-md  text-right"><i class="fa fa-plus-circle"></i> 添加域名 </a>
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
        <div class="box box-primary">
            @include('partials.errors')
            @include('partials.success')
            <form class="form-horizontal" id="search" method="post" action="/domain/recovery">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                        <thead>
                            <tr>
                                <th class="hidden-sm">总代</th>
                                <th data-sortable="false">已分配的域名</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($top_users as $u)
                        <tr>
                            <td>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name='user' value="{{$u->id}}">
                                    {{$u->username}}
                                </label></td>
                            <td>
                                @foreach($u->domains as $d)
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="user_domain[{{$u->id}}]" value="{{$d->domain_id}}">
                                    {{$domains[$d->domain_id]}}
                                </label>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    <div class="btn-group col-md-12">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-recycle" aria-hidden="true"></i> 回收</button>
                    </div>

                </div>
            </form>
        </div>
        <div class="box box-primary">
            <form class="form-horizontal" id="search" method="post" action="/domain/AssignAndDel">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <div class="box-header">
                    <!--BOX TITLE-->
                    <strong>可用域名</strong>
                </div>
                <div class="box-body" >
                    <div style="overflow-y:auto;  max-height:280px;">
                        <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                            <tbody>
                            @foreach($domains as $k=>$u)
                            <tr>
                                <td>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="domains[]" value="{{$k}}">
                                        {{$u}}
                                    </label>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="col-md-1">
                        <button type="submit" name="del" value="del" class="btn btn-danger" id="search_btn">
                            <i class="fa fa-times" aria-hidden="true"></i> 删除</button>
                    </div>
                    <div class="col-md-6">
                        <select name="top_user" class="form-control">
                            <option>选择总代</option>
                            @foreach($top_users as $v)
                            <option value='{{$v->id}}'>{{$v->username}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" name="assign" value="assign" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> 分配</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-show" tabIndex="-1">
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
                    确认要<span class="row_show_text"></span>该公告吗?
                </p>
            </div>
            <div class="modal-footer">
                <form class="showForm" method="POST">
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

@stop

@section('js')
<script src="/assets/js/app/common.js" charset="UTF-8"></script>
<script>
$(function () {

//隐藏
    $('input[name="user"]').click(function () {
        $(this).parent().parent().next().find(":checkbox").attr("checked", $(this).is(":checked"));
    });
});
</script>
@stop