@extends('layouts.base')

@section('title','权限管理')

@section('function','权限管理')
@section('function_link', '/permission/')

@section('here','权限列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">
        <div class="col-md-6">
            @if($parent_id==0)
                <span style="margin:3px;" id="parent_id" attr="{{$parent_id}}" class="btn-flat text-info">顶级菜单</span>
            @else
                <span style="margin:3px;" id="parent_id" attr="{{$parent_id}}" class="text-info">
                {{-- $data->display_name --}}下级菜单
                </span>
                <a style="margin:3px;" href="/permission/"
                   class="btn btn-warning btn-md animation-shake reloadBtn"><i class="fa fa-mail-reply-all"></i> 返回顶级菜单
                </a>
            @endif
        </div>

        <div class="col-md-6 text-right">
    @if(Gate::check('permission/create'))
        <a href="/permission/create/?parent_id={{$parent_id}}" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加权限 </a>
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
        <div class="box-body">
            <table id="tags-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th data-sortable="false" class="hidden-sm"></th>
                    <th class="hidden-sm">权限规则</th>
                    <th class="hidden-sm">权限名称</th>
                    <th class="hidden-sm">权限描述</th>
                    <th data-sortable="false">操作</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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
                确认要删除这个权限吗?
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
            $(function () {
                var parent_id = $('#parent_id').attr('attr');
                var table = $("#tags-table").DataTable({
                	language:app.DataTable.language(),
                    order: [[0, "asc"]],
                    serverSide: true,
                    pageLength:25,
                    // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                    // 要 ajax(url, type) 必须加这两参数
                    ajax: app.DataTable.ajax(),
                    "columns": [
                        {"data": "id"},
                        {"data": "rule"},
                        {"data": "name"},
                        {"data": "description"},
                        {"data": "action"}
                    ],
                    columnDefs: [
                        {
                            'targets': -1,
                            "render": function (data, type, row) {
                                var row_edit = {{ Gate::check('permission/edit') ? 1 : 0 }};
                                var row_delete = {{ Gate::check('permission/delete') ? 1 : 0 }};
                                var str = '';

                                //下级菜单
                                if (parent_id == 0) {
                                    str += '<a style="margin:3px;"  href="/permission/?parent_id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-adn"></i>下级菜单</a>';
                                }

                                //编辑
                                if (row_edit) {
                                    str += '<a style="margin:3px;" href="/permission/edit?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
                                }

                                //删除
                                if (row_delete) {
                                    str += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger"><i class="fa fa-times-circle"></i> 删除</a>';
                                }

                                return str;
                        	}
                        }
                    ]
                });

                table.on('preXhr.dt', function () {
                    loadShow();
                });

                table.on('draw.dt', function () {
                    table.column(0).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
                    loadFadeOut();
                });

                $("table").delegate('.delBtn', 'click', function () {
                    var id = $(this).attr('attr');
                    $('.deleteForm').attr('action', '/permission/?id=' + id);
                    $("#modal-delete").modal();
                });
            });
        </script>
@stop