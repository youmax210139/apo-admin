@extends('layouts.base')

@section('title','审核拒绝原因')

@section('function','审核拒绝原因')
@section('function_link', '/riskrefusedreason/')

@section('here','原因列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')

    <div class="row page-title-row" id="dangqian" style="margin:5px;">

        <div class="col-md-12 text-right">
            @if(Gate::check('riskrefusedreason/create'))
                <a href="/riskrefusedreason/create" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加配置
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
        <div class="col-sm-12">
            <div class="box box-primary">
                @include('partials.errors')
                @include('partials.success')
                <div class="box-body">
                    <table id="tags-table" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="hidden-sm">ID</th>
                            <th class="hidden-sm">配置值</th>
                            <th class="hidden-sm">更新时间</th>
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

    <!--删除配置项-->
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
                        确认要删除该配置项吗?
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
            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[0, "asc"]],
                serverSide: true,
                iDisplayLength: 25,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type) 必须加这两参数
                ajax: app.DataTable.ajax(),
                "columns": [
                    {"data": "id"},
                    {"data": "text"},
                    {"data": "updated_at"},
                    {"data": "action"}
                ],
                columnDefs: [
                    // {
                    //     'target':5,
                    //     "render":function (data,type,row) {
                    //         console.log(data);
                    //          return '<div style="max-width: 220px;word-wrap:break-word">' + '123123123' + '</div>';
                    //
                    //     }
                    // },
                    {
                        'targets': 3,
                        "render": function (data, type, row) {
                            var row_edit = {{ Gate::check('riskrefusedreason/edit') ? 1 : 0 }};
                            var row_delete = {{ Gate::check('riskrefusedreason/delete') ? 1 : 0 }};
                            var str = '';

                            //下级菜单
                            var a_attr = null;
                            var common_class = 'X-Small btn-xs ';
                            //编辑
                            if (row_edit) {
                                a_attr = {
                                    'class': common_class + 'text-success',
                                    'href': '/riskrefusedreason/edit?id=' + row['id']
                                };
                                str += app.getalinkHtml('编辑', a_attr, 'fa-edit');
                            }

                            //删除
                            if (row_delete) {
                                a_attr = {
                                    'class': common_class + 'text-danger delBtn',
                                    'attr': row['id'],
                                    'href': '#',
                                };
                                str += app.getalinkHtml('删除', a_attr, 'fa-times-circle');
                            }

                            return str;
                        }
                    },
                    {
                        'targets': 2,
                        'render': function (data, type, row) {
                            return '<div style="max-width: 220px;word-wrap:break-word">' + data + '</div>';
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

            //删除
            $("table").delegate('.delBtn', 'click', function () {
                var id = $(this).attr('attr');
                $('.deleteForm').attr('action', '/riskrefusedreason/?id=' + id);
                $("#modal-delete").modal();
            });
        });
    </script>
@stop
