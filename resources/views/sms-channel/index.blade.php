@extends('layouts.base')
@section('title','短信通道管理')
@section('function','短信通道管理')
@section('function_link', '/smschannel/')
@section('here','短信通道列表')
@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
            @if(Gate::check('smschannel/create'))
                <a href="/smschannel/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加短信通道
                </a>
            @endif
            @if(Gate::check('smschannel/refreshserver'))
                <a href="/smschannel/refreshserver" class="btn btn-warning btn-md">
                    <i class="fa fa-retweet"></i> 同步服务器
                </a>
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
        <div class="col-xs-12">
            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table"
                           class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">后台名称</th>
                            <th class="hidden-sm">渠道</th>
                            <th class="hidden-sm">账号/标识</th>
                            <th class="hidden-sm">同步情况</th>
                            <th class="hidden-sm">开启状态</th>
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
                        确认要删除该记录吗?
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
@stop

@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script>
        $(function () {
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(),
                "columns": [
                    {"data": "id"},
                    {"data": "name"},
                    {"data": "cate_name"},
                    {"data": "account"},
                    {"data": "sync_status"},
                    {"data": "enabled"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': -1, "render": function (data, type, row) {
                            var row_send = {{Gate::check('smschannel/send') ? 1 : 0}};
                            var row_edit = {{Gate::check('smschannel/edit') ? 1 : 0}};
                            var row_delete = {{Gate::check('smschannel/delete') ? 1 :0}};
                            var str = '';
                            //编辑
                            if (row_edit) {
                                str += '<a style="margin:3px;" href="/smschannel/send?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 发短信</a>';
                            }
                            //编辑
                            if (row_edit) {
                                str += '<a style="margin:3px;" href="/smschannel/edit?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';
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
                $('.deleteForm').attr('action', '/smschannel/?id=' + id);
                $("#modal-delete").modal();
            });

        });
    </script>
@stop
