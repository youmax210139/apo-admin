@extends('layouts.base')
@section('title','飞单配置')
@section('function','飞单配置')
@section('function_link', '/flysystemconfig/')
@section('here','飞单配置列表')
@section('content')

    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6"></div>
        <div class="col-md-6 text-right">
            @if(Gate::check('flysystemconfig/create'))
                <a href="/flysystemconfig/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i>添加配置
                </a>
            @endif
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
                            <th class="hidden-sm" data-sortable="false"></th>
                            <th class="hidden-sm" data-sortable="false">英文标识</th>
                            <th class="hidden-sm" data-sortable="false">中文名称</th>
                            <th class="hidden-sm" data-sortable="false">推送域名</th>
                            <th class="hidden-sm" data-sortable="false">状态</th>
                            <th class="hidden-sm" data-sortable="false">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--禁用配置项-->
    <div class="modal fade" id="modal-status" tabIndex="-1">
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
                        确认要<span class="row_statustext"></span>该配置吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="statusForm" method="POST">
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
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                iDisplayLength: 25,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(),
                "columns": [
                    {"data": "id"},
                    {"data": "ident"},
                    {"data": "name"},
                    {"data": "domain"},
                    {"data": "status"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var row_edit = {{ Gate::check('flysystemconfig/edit') ? 1 : 0 }};
                            var row_status = {{ Gate::check('flysystemconfig/status') ? 1 : 0 }};
                            var row_statusclass = !row['status'] ? 'text-success' : 'text-danger';
                            var row_statusicon = !row['status'] ? 'fa-check-circle-o' : 'fa-ban';
                            var row_statustext = !row['status'] ? '启用' : '禁用';
                            var str = '';

                            //下级菜单
                            var a_attr = null;
                            var common_class = 'X-Small btn-xs ';

                            //编辑
                            if (row_edit) {
                                a_attr = {
                                    'class': common_class + 'text-success',
                                    'href': '/flysystemconfig/edit?id=' + row['id']
                                };
                                str += app.getalinkHtml('编辑', a_attr, 'fa-edit');
                            }
                            //是否禁用
                            if (row_status) {
                                a_attr = {
                                    'class': common_class + 'status ' + row_statusclass,
                                    'attr': row['id'],
                                    'href': '#',
                                    'status': row_statustext
                                };
                                //如果这里的html简单，直接写原生的html。如果html代码和js混一起比较复杂点的，使用app.getalinkHtml()
                                str += app.getalinkHtml(row_statustext, a_attr, row_statusicon);
                            }
                            return str;
                        },
                    },
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            return app.getLabelHtml(
                                row['status'] ? '启用' : '禁用',
                                'label-' + (row['status'] ? 'success' : 'danger')
                            );
                        }
                    },
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

            //禁用配置
            $("table").delegate('.status', 'click', function () {
                var id = $(this).attr('attr');
                $(".row_statustext").text($(this).attr('status'));
                $('.statusForm').attr('action', '/flysystemconfig/status?id=' + id);
                $("#modal-status").modal();
            });
        });
    </script>
@stop
